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

if (isset ($_POST["sottotratta_raggiunta"])) {
    $_SESSION["sottotratta_raggiunta"] = $_POST["sottotratta_raggiunta"];
}
else if (!isset ($_SESSION["sottotratta_raggiunta"]) && !isset ($_SESSION["sottotratta_raggiunta"]) && isset ($_SESSION["prima_sottotratta"])) {
    $_SESSION["sottotratta_raggiunta"] = $_SESSION["prima_sottotratta"];
}

if (isset ($_POST["stazione_raggiunta"])) {
    $_SESSION["stazione_raggiunta"] = $_POST["stazione_raggiunta"];
    setStazione($_SESSION["treno"], $_SESSION["stazione_raggiunta"], $connessione);
    
}
else if(!isset ($_SESSION["stazione_raggiunta"]) && !isset($_POST["stazione_raggiunta"]) && isset ($_SESSION["prima_sottotratta"])) {
    echo "prima sottotratta: ";
    echo "<pre>" . var_export(getSottotratta($_SESSION["prima_sottotratta"], $connessione), true) . "</pre>";
    $_SESSION["stazione_raggiunta"] = getStaz(getSottotratta($_SESSION["prima_sottotratta"], $connessione)["prima_stazione"], $connessione);
}

if (isset ($_POST["ritardo"])) {
    //echo "ritardo: ";
    aggiornaRitardo($_POST["ritardo"], $_SESSION["treno"], $_SESSION["sottotratta_raggiunta"], $connessione);
}

foreach (getFasceOrarie($_SESSION["treno"], $connessione, null) as $fascia_oraria) {     //per ogni fascia oraria della tratta del $treno, se l'orario attuale è in una delle fasce orarie allora setta $_SESSION['prima_sottottratta']
    if (orarioInFasciaOraria("16:00:00", $fascia_oraria[0]["orario_partenza"], $fascia_oraria[count($fascia_oraria) - 1]["orario_arrivo"])) {
        $_SESSION["prima_sottotratta"] = $fascia_oraria[0]["id"];
    }

}

echo "<form action='view_macchinista.php' method='post'>
                  <label>Ritado: </label>
                  <input name='ritardo' type='number' min='0' step='5' placeholder='ritardo' value = '" . getRitardo($_SESSION["treno"], $_SESSION["sottotratta_raggiunta"], $connessione) . "'> 
                  <input name='aggiorna' type='submit' value='Invia'> 
          </form>";

if (isset ($_SESSION["prima_sottotratta"])) {
    $tratta = getFasceOrarie($_SESSION["treno"], $connessione, $_SESSION["prima_sottotratta"]);
    foreach ($tratta as $sottotratta) {
        //echo $_SESSION["stazione_raggiunta"];
        //questa parte è solo per la prima stazione della tratta
        if ($sottotratta == $tratta[0]) {
            $stazione = getStaz($sottotratta['prima_stazione'], $connessione);
            echo "<form action='view_macchinista.php' method='post'>";
            if ($stazione == $_SESSION["stazione_raggiunta"]) {
               
                echo "<input class='stazione_raggiunta' name='button' type='submit' value='" . $stazione . " - Ritardo: " . getRitardo($_SESSION["treno"], $sottotratta['id'], $connessione) . " - Ora: " . $sottotratta['orario_partenza'] . "'> <br>";
            } else {
                echo "<input class='stazione' name='button' type='submit' value='" . $stazione . " - Ritardo: " . getRitardo($_SESSION["treno"], $sottotratta['id'], $connessione) . " - Ora: " . $sottotratta['orario_partenza'] . "'> <br>";
            }
            echo "<input name = 'sottotratta_raggiunta' type = 'hidden' value = '" . $sottotratta['id'] . "'>";
            echo "<input name = 'stazione_raggiunta' type = 'hidden' value = '" . $stazione . "'>";
            //echo $sottotratta['id'];
            echo '</form>';
        }

        //questa parte è per tutte le stazioni della tratta tranne la prima
        $stazione = getStaz($sottotratta['ultima_stazione'], $connessione);
        echo "<form action='view_macchinista.php' method='post'>";
        if ($stazione == $_SESSION["stazione_raggiunta"]) {
            echo "<input class='stazione_raggiunta' name='button' type='submit' value='" . $stazione . " - Ritardo: " . getRitardo($_SESSION["treno"], $sottotratta['id'], $connessione) . " - Ora: " . $sottotratta['orario_partenza'] . "'> <br>";
        } else {
            echo "<input class='stazione' name='button' type='submit' value='" . $stazione . " - Ritardo: " . getRitardo($_SESSION["treno"], $sottotratta['id'], $connessione) . " - Ora: " . $sottotratta['orario_partenza'] . "'> <br>";
        }
        echo "<input name = 'sottotratta_raggiunta' type = 'hidden' value = '" . $sottotratta['sottotratta_successiva'] . "'>";
        echo "<input name = 'stazione_raggiunta' type = 'hidden' value = '" . $stazione . "'>";
        //echo $sottotratta['sottotratta_successiva'];
        echo '</form>';


    }
    //unset($_SESSION["prima_sottotratta"]);   //Ha senso?
}



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
<style>
    .stazione {
        background-color: #f2f2f2;
        padding: 10px;
        margin: 10px;
        border-radius: 10px;
    }

    .stazione_raggiunta {
        background-color: #f2f2f2;
        padding: 10px;
        margin: 10px;
        border-radius: 10px;
        color: red;

    }
</style>

<body>
</body>

</html>

<?php
// Se l'ora è dentro ad un range    
function orarioInFasciaOraria($orario, $inizio, $fine)
{
    $orarioTimestamp = strtotime($orario);
    $inizioTimestamp = strtotime($inizio);
    $fineTimestamp = strtotime($fine);

    return ($orarioTimestamp >= $inizioTimestamp && $orarioTimestamp <= $fineTimestamp);
}
// Dall'id della stazione, restituisce il nome della stazione, Dalla stringa del nome della stazione, restituisce l'id della stazione
function getStaz($var, $connessione)
{
    $query = "";
    $param_type = "";
    $what = "";
    if (is_int($var)) {
        $query = "SELECT nome FROM stazione WHERE id = ?";
        $param_type = "i";
        $what = "nome";
    } else {
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
function getDayOfWeek($date)
{
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
function getItalianDate($date)
{
    // Formattare la data nel formato italiano
    $italianDate = date('d/m/Y', strtotime($date));

    return $italianDate;
}
// Aggiorna il ritardo ($ritardo), del treno ($treno) sulla sottotratta ($sottotratta)
function aggiornaRitardo2($ritardo, $treno, $sottotratta, $connessione)
{
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

    if (empty ($result)) {
        $query = "INSERT INTO ritardo (minuti, treno, data, sottotratta) 
                        VALUES (?, ?, ?, ?)";
        $stmt = $connessione->prepare($query);
        $stmt->bind_param("isss", $ritardo, $treno, $currentDate, $sottotratta);
        $stmt->execute();
    } else {
        $query = "UPDATE ritardo SET minuti = ? WHERE id = ?";
        $stmt = $connessione->prepare($query);
        $stmt->bind_param("ii", $ritardo, $result[0]["id"]);
        $stmt->execute();
    }
}

function aggiornaRitardo($ritardo, $treno, $sottotratta, $connessione)
{
    date_default_timezone_set('Europe/Rome');
    $currentDate = date('Y-m-d');
    $ritardo = intval($ritardo);
    $sottotratta_successiva = 0;
    
    while ($sottotratta_successiva !== null) {
        //echo $sottotratta_successiva . "<br>";
        $query = "SELECT sottotratta_successiva FROM sottotratta 
                            WHERE id = ?";

        $stmt = $connessione->prepare($query);
        $stmt->bind_param("s", $sottotratta);
        $stmt->execute();
        $sottotratta_successiva = $stmt->get_result()->fetch_assoc();
        $sottotratta_successiva = $sottotratta_successiva["sottotratta_successiva"];



        $query = "SELECT * FROM ritardo 
                    WHERE treno = ?
                    AND data = ?
                    AND sottotratta = ?";

        $stmt = $connessione->prepare($query);
        $stmt->bind_param("sss", $treno, $currentDate, $sottotratta);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        //echo "<pre>" . var_export($result, true) . "</pre>";

        if (empty ($result) && $ritardo != 0) {
            $query = "INSERT INTO ritardo (minuti, treno, data, sottotratta) 
                        VALUES (?, ?, ?, ?)";
            $stmt = $connessione->prepare($query);
            $stmt->bind_param("isss", $ritardo, $treno, $currentDate, $sottotratta);
            $stmt->execute();
        } else {
            if($ritardo != 0){
                $query = "UPDATE ritardo SET minuti = ? WHERE id = ?";
                $stmt = $connessione->prepare($query);
                $stmt->bind_param("ii", $ritardo, $result[0]["id"]);
                $stmt->execute();
            }
            else {
                $query = "DELETE FROM ritardo WHERE id = ?";
                $stmt = $connessione->prepare($query);
                $stmt->bind_param("i", $result[0]["id"]);
                $stmt->execute();
            }

        }
        $sottotratta = $sottotratta_successiva;
    }




}
// Ritorna quanto ritardo c'è sul treno "$treno" sulla sottotratta "$sottotratta"
function getRitardo($treno, $sottotratta, $connessione)
{
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

    if (empty ($result)) {
        return "0";
    } else {
        return $result["minuti"];
    }
}
// Dall id del treno ($treno), restituisce la tratta del treno
function getTratta($treno, $connessione)
{
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
function getFasceOrarie($treno, $connessione, $id_sottotratta)
{
    $query = "SELECT s.id, orario_partenza, orario_arrivo, s.prima_stazione AS '1staz', s.ultima_stazione AS '2staz', s.sottotratta_successiva, t.ultima_stazione AS 'capolinea' FROM sottotratta s
        LEFT JOIN tratta t ON s.tratta = t.id
        WHERE s.tratta = (SELECT tratta FROM treno WHERE id = ?)
        AND s.prima_stazione = t.prima_stazione";

    $stmt = $connessione->prepare($query);
    $stmt->bind_param("s", $treno);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $fasce = array();

    if ($id_sottotratta != null) {
        $query = "SELECT * FROM sottotratta 
                WHERE id = ?";

        $stmt = $connessione->prepare($query);
        $stmt->bind_param("s", $id_sottotratta);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    foreach ($result as $sottotratta) {
        $prox = $sottotratta["id"];
        $fascia = array();
        while ($prox != null) {
            $query_sottotratta = "SELECT * FROM sottotratta 
                WHERE id = ?";

            $stmt = $connessione->prepare($query_sottotratta);
            $stmt->bind_param("s", $prox);
            $stmt->execute();
            $result_sottotratta = $stmt->get_result()->fetch_assoc();


            array_push($fascia, $result_sottotratta);
            $prox = $result_sottotratta["sottotratta_successiva"];
        }
        if ($id_sottotratta != null) {
            return $fascia;
        } else {
            array_push($fasce, $fascia);
        }
    }
    //echo '<pre>' . var_export($fasce, true) . '</pre>';
    return $fasce;
}
// Setta la stazione raggiunta dal treno nel database
function setStazione($treno, $stazione, $connessione)
{
    $stazione = getStaz($stazione, $connessione);

    $query = "UPDATE treno SET stazione = ? WHERE id = ?";
    $stmt = $connessione->prepare($query);
    $stmt->bind_param("ss", $stazione, $treno);
    $stmt->execute();
}
//ritorna tutte le informazioni riguardanti la sottotratta con id = $id_sottotratta
function getSottotratta($id_sottotratta, $connessione)
{
    $query = "SELECT * FROM sottotratta 
                WHERE id = ?";

    $stmt = $connessione->prepare($query);
    $stmt->bind_param("s", $id_sottotratta);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    return $result[0];
}

?>