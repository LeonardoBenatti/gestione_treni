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

    if (isset($_POST["just_logged_in"])){
        echo "SELEZIONA UNA FASCIA ORARIA: <br>";
        getFasceOrarie($_SESSION["treno"], $connessione);
        echo "<form action = 'view_macchinista.php' method = 'post'>";

        echo "</form>";
    }

    if (isset($_POST["aggiorna"])){
        aggiornaRitardo($_POST["ritardo"], $_SESSION["treno"], 9, $connessione);
    }
    //echo getSottotratte(getTratta($_SESSION["treno"], $connessione), $connessione);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>view_macchinista</title>
</head>
<body>
    <form action = "view_macchinista.php" method = "post">
        <input name = "ritardo" type = "number" min = "5" step = "5" placeholder = "ritardo" value = <?php if(isset($_POST["ritardo"])){echo $_POST["ritardo"];} ?>> 
        <input name = "aggiorna" type = "submit" value = "Invia" > 
    </form>
</body>
</html>

<?php
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
    function getItalianDate($date) {        
        // Formattare la data nel formato italiano
        $italianDate = date('d/m/Y', strtotime($date));
        
        return $italianDate;
    }
    function aggiornaRitardo($ritardo, $treno, $sottotratta, $connessione) {      
        date_default_timezone_set('Europe/Rome');
        $currentDate = date('Y-m-d');
        $currentTime = date('H:i:s');  
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
    function getTratta($treno, $connessione){
        $query = "SELECT tratta FROM treno 
        WHERE id = ?";

        $stmt = $connessione->prepare($query);
        $stmt->bind_param("s", $treno);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return $result[0]["tratta"];
    }

    function getFasceOrarie($treno, $connessione){
        $query = "SELECT s.id, orario_partenza, orario_arrivo, s.prima_stazione AS '1staz', s.ultima_stazione AS '2staz', s.sottotratta_successiva, t.ultima_stazione AS 'capolinea' FROM sottotratta s
        LEFT JOIN tratta t ON s.tratta = t.id
        WHERE s.tratta = (SELECT tratta FROM treno WHERE id = ?)
        AND s.prima_stazione = t.prima_stazione";

        $stmt = $connessione->prepare($query);
        $stmt->bind_param("s", $treno);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $fasce = array();
        
        foreach($result as $sottotratta){
            $prox = $sottotratta["id"];
            
            while($prox != null){
                $query_sottotratta = "SELECT * FROM sottotratta 
                WHERE id = ?";

                $stmt = $connessione->prepare($query_sottotratta);
                $stmt->bind_param("s", $prox);
                $stmt->execute();
                $result_sottotratta = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

                
                echo "prox: " . $prox . "<br>";
                var_dump($result_sottotratta);
                
                //array_push($sottotratta, $fasce);
                $prox = $result_sottotratta["sottotratta_successiva"];
            }
        }
        return $fasce;
    }
    function getSottotratte($tratta, $connessione){
        $query = "SELECT * FROM tratta 
        WHERE id = ?";

        $stmt = $connessione->prepare($query);
        $stmt->bind_param("s", $tratta);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $prox = $result[0]["prima_stazione"];
        $ultima_stazione = $result[0]["ultima_stazione"];

        do{

            $query_sottotratta = "SELECT * FROM sottotratta 
                        WHERE id = ?
                        AND tratta = ?";
            
            $stmt = $connessione->prepare($query_sottotratta);
            $stmt->bind_param("ii", $prox, $tratta);
            $stmt->execute();
            $result_sottotratta = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            echo "prox: " . $prox . "<br>";
            echo "tratta: " . $tratta . "<br>";

            echo getStaz($result_sottotratta[0]["prima_stazione"], $connessione);
            
            $prox = $result_sottotratta[0]["sottotratta_successiva"];

        }while($prox != null);

        return $result[0]["tratta"];
    }
?>


