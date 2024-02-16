<?php
    session_start();
    date_default_timezone_set('Europe/Rome');

    $_SESSION["currentDate"] = date('Y-m-d');
    $_SESSION["currentTime"] = date('H:i:s');

    $hostname = "localhost";
    $username = "root";
    $password = "";
    $database = "gestione_treni";

    $connessione = new mysqli($hostname, $username, $password, $database);

    

    echo "SONO LE ORE: " . $_SESSION["currentTime"] . " DI " . getDayOfWeek($_SESSION["currentDate"]) . " " . getItalianDate($_SESSION["currentDate"]) . "<br>";

    
    if (isset($_POST["aggiorna"])){
        aggiornaRitardo($_POST["ritardo"], $_SESSION["treno"], 7, $connessione);
    }

    foreach(getFasceOrarie($_SESSION["treno"], $connessione, null) as $fascia_oraria){     //per ogni fascia oraria della tratta del $treno, se l'orario attuale è in una delle fasce orarie allora setta $_SESSION['prima_sottottratta']
        if(orarioInFasciaOraria("16:00:00", $fascia_oraria[0]["orario_partenza"], $fascia_oraria[count($fascia_oraria)-1]["orario_arrivo"])){
            $_SESSION["prima_sottotratta"] = $fascia_oraria[0]["id"];
        }

    }   

    echo "<form action='view_macchinista.php' method='post'>
                  <label>Ritado: </label>
                  <input name='ritardo' type='number' min='5' step='5' placeholder='ritardo' value='" . (isset($_SESSION['ritardo']) ? $_SESSION['ritardo'] : '0') . "'> 
                  <input name='aggiorna' type='submit' value='Invia'> 
          </form>"; 

    echo '<ul>';
        if(isset($_SESSION["prima_sottotratta"])){
            foreach(getFasceOrarie($_SESSION["treno"], $connessione, $_SESSION["prima_sottotratta"]) as $sottotratta){
                $stazione = getStaz($sottotratta['prima_stazione'], $connessione);
                echo '<li>' . $stazione . ' - Ritardo: ' . getRitardo($_SESSION["treno"], $sottotratta['id'], $connessione) . ' - Ora: ' . $sottotratta['orario_partenza'] . '</li>';
            }
            //unset($_SESSION["prima_sottotratta"]);   //Ha senso?
        }
    echo '</ul>';


    //$test = getFasceOrarie($_SESSION["treno"], $connessione, $_SESSION["prima_tratta"]);
    //echo '<pre>' . var_export($test, true) . '</pre>'
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>view_macchinista</title>
</head>
<body>
    
</body>
</html>



<?php
    // Se l'ora è dentro ad un range    
    function orarioInFasciaOraria($orario, $inizio, $fine) {
        $orarioTimestamp = strtotime($orario);
        $inizioTimestamp = strtotime($inizio);
        $fineTimestamp = strtotime($fine);

        return ($orarioTimestamp >= $inizioTimestamp && $orarioTimestamp <= $fineTimestamp);
    }
    // Dall'id della stazione, restituisce il nome della stazione, Dalla stringa del nome della stazione, restituisce l'id della stazione
    function getStaz($var, $connessione){
        $query = "";
        $param_type = "";
        $what = "";
        if(is_int($var)){
            $query = "SELECT nome FROM stazione WHERE id = ?";
            $param_type = "i";
            $what = "nome";
        }
        else{
            $query = "SELECT id FROM stazione WHERE nome = ?";
            $param_type = "s";
            $what = "id";
        }
        $stmt = $connessione->prepare($query);
        $stmt->bind_param($param_type, $var);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return $result[0][$what];
    }
    // Dalla data, restituisce il giorno della settimana in italiano formato "Lunedì"
    function getDayOfWeek($date) {     
        $daysOfWeekItalian = array(
            'Monday' => 'Lunedì',
            'Tuesday' => 'Martedì',
            'Wednesday' => 'Mercoledì',
            'Thursday' => 'Giovedì',
            'Friday' => 'Venerdì',
            'Saturday' => 'Sabato',
            'Sunday' => 'Domenica'
        );

        // Ottenere il giorno della settimana dalla data specificata
        $dayOfWeek = $daysOfWeekItalian[date('l', strtotime($date))];
        
        return $dayOfWeek;
    }
    // Dalla data, restituisce la data nel formato italiano
    function getItalianDate($date) {        
        // Formattare la data nel formato italiano
        $italianDate = date('d/m/Y', strtotime($date));
        
        return $italianDate;
    }
    // Aggiorna il ritardo ($ritardo), del treno ($treno) sulla sottotratta ($sottotratta)
    function aggiornaRitardo($ritardo, $treno, $sottotratta, $connessione) {      
        date_default_timezone_set('Europe/Rome');
        $currentDate = date('Y-m-d'); 
        $ritardo = intval($ritardo);

        $query = "SELECT * FROM ritardo 
                            WHERE treno = ?
                            AND data = ?";

        $stmt = $connessione->prepare($query);
        $stmt->bind_param("ss", $treno, $currentDate);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        if(empty($result)){
            $query = "INSERT INTO ritardo (minuti, treno, data, sottotratta) 
                        VALUES (?, ?, ?, ?)";
            $stmt = $connessione->prepare($query);
            $stmt->bind_param("isss", $ritardo, $treno, $currentDate, $sottotratta); 
            $stmt->execute();
        }
        else{
            $query = "UPDATE ritardo SET minuti = ? WHERE id = ?";
            $stmt = $connessione->prepare($query);
            $stmt->bind_param("ii", $ritardo, $result[0]["id"]);
            $stmt->execute();
        }
    }
    // Ritorna quanto ritardo c'è sul treno "$treno" sulla sottotratta "$sottotratta"
    function getRitardo($treno, $sottotratta, $connessione){
        date_default_timezone_set('Europe/Rome');
        $currentDate = date('Y-m-d'); 

        $query = "SELECT * FROM ritardo 
                            WHERE treno = ?
                            AND data = ?
                            AND sottotratta = ?";

        $stmt = $connessione->prepare($query);
        $stmt->bind_param("ssi", $treno, $currentDate, $sottotratta);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if(empty($result)){
            return "0";
        }
        else{
            return $result["minuti"];
        }
    }
    // Dall id del treno ($treno), restituisce la tratta del treno
    function getTratta($treno, $connessione){
        $query = "SELECT tratta FROM treno 
        WHERE id = ?";

        $stmt = $connessione->prepare($query);
        $stmt->bind_param("s", $treno);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return $result[0]["tratta"];
    }
    /* Dall id del treno ($treno), restituisce le fasce orarie del treno, 
    se $id_sottotratta è null restituisce tutte le fascie orarie/sottotratte", 
    altrimenti restituisce le sottotratte partendo da $id_sottotratta*/
    function getFasceOrarie($treno, $connessione, $id_sottotratta){
        $query = "SELECT s.id, orario_partenza, orario_arrivo, s.prima_stazione AS '1staz', s.ultima_stazione AS '2staz', s.sottotratta_successiva, t.ultima_stazione AS 'capolinea' FROM sottotratta s
        LEFT JOIN tratta t ON s.tratta = t.id
        WHERE s.tratta = (SELECT tratta FROM treno WHERE id = ?)
        AND s.prima_stazione = t.prima_stazione";

        $stmt = $connessione->prepare($query);
        $stmt->bind_param("s", $treno);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $fasce = array();

        if($id_sottotratta != null){
            $query = "SELECT * FROM sottotratta 
                WHERE id = ?";

            $stmt = $connessione->prepare($query);
            $stmt->bind_param("s", $id_sottotratta);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        
        foreach($result as $sottotratta){
            $prox = $sottotratta["id"];
            $fascia = array();
            while($prox != null){
                $query_sottotratta = "SELECT * FROM sottotratta 
                WHERE id = ?";

                $stmt = $connessione->prepare($query_sottotratta);
                $stmt->bind_param("s", $prox);
                $stmt->execute();
                $result_sottotratta = $stmt->get_result()->fetch_assoc();

                
                array_push($fascia, $result_sottotratta);
                $prox = $result_sottotratta["sottotratta_successiva"];
            } 
            if($id_sottotratta != null){
                return $fascia;
            }   
            else{
                array_push($fasce, $fascia);
            }
        }
        //echo '<pre>' . var_export($fasce, true) . '</pre>';
        return $fasce;
    }

    
?>


