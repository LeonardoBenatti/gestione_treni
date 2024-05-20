<?php 
session_start();

if(isset($_POST["partenza"])){
    $_SESSION["partenza"] = $_POST["partenza"];
    $partenza = $_SESSION["partenza"];
}

if(isset($_POST["destinazione"])){
    $_SESSION["destinazione"] = $_POST["destinazione"];
}

if(isset($_SESSION["partenza"])){
    $partenza = $_SESSION["partenza"];
}

if(isset($_SESSION["destinazione"])){
    $destinazione = $_SESSION["destinazione"];
}

//echo $partenza . "<br>";
//echo $destinazione . "<br>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<link rel="stylesheet" href="../stylesheets/soluzioni.css">
<body>
    <div class = "blur-overlay"></div>
    <div id = "container">
        <h1 id = "titolo">Soluzioni da <?php echo $partenza; ?> a <?php echo $destinazione; ?></h1>

        <div id = "soluzioni">
<?php

    $hostname = "localhost";
    $username = "root";
    $password = "";
    $database = "gestione_treni";

    $connessione = new mysqli($hostname, $username, $password, $database);

    $query_tratta = "SELECT * FROM tratta 
    WHERE id IN (
        SELECT tratta FROM sottotratta 
        WHERE prima_stazione = (SELECT id FROM stazione WHERE nome = ?)
    )
    AND id IN (
        SELECT tratta FROM sottotratta 
        WHERE ultima_stazione = (SELECT id FROM stazione WHERE nome = ?)
    )";

    $stmt = $connessione->prepare($query_tratta);
    $stmt->bind_param("ss", $partenza, $_POST['destinazione']);
    $stmt->execute();
    $result_tratta = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    //echo $partenza . "<br>";
    //echo $destinazione . "<br>";

    foreach($result_tratta as $row){
        //var_dump($row);
        //echo "tratta selezionata: " . $row["id"] . "<br>";

        $partenza_id = getStaz($partenza, $connessione);
        $tratta = $row["id"];

        $query_partenza = "SELECT * FROM sottotratta 
                            WHERE prima_stazione = ?
                            AND tratta = ?";

        $stmt = $connessione->prepare($query_partenza);
        $stmt->bind_param("ii", $partenza_id, $tratta);
        $stmt->execute();
        $result_partenza = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach($result_partenza as $sottottratta){

            echo "<div class = 'soluzione_row'>";

            $prox = $sottottratta["id"];
            $ultima_stazione = $sottottratta["ultima_stazione"];

            echo "<form action='tratta.php' method='post'>";
            
            echo "<button type='submit' name = 'tratta'>";

            $tratte_utili = array();

            do{

                $query_sottotratta = "SELECT * FROM sottotratta 
                            WHERE id = ?
                            AND tratta = ?";
                
                $stmt = $connessione->prepare($query_sottotratta);
                $stmt->bind_param("ii", $prox, $tratta);
                $stmt->execute();
                $result_sottotratta = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

                //echo "prox: " . $prox . "<br>";
                //echo "tratta: " . $tratta . "<br>";

                array_push($tratte_utili, $result_sottotratta[0]);


                if(getStaz($result_sottotratta[0]["prima_stazione"], $connessione) == $partenza ){
                    echo $partenza. " " . $result_sottotratta[0]["orario_partenza"];
                }
                
                if(getStaz($result_sottotratta[0]["ultima_stazione"], $connessione) == $destinazione ){
                    echo " - " . $destinazione . " " . $result_sottotratta[0]["orario_arrivo"] . "<br>";
                }
                
                $ultima_stazione = $result_sottotratta[0]["ultima_stazione"];
                $prox = $result_sottotratta[0]["sottotratta_successiva"];
                //echo "ulti_staz: " . getStaz($ultima_stazione, $connessione) . "<br>";
            }while(getStaz($ultima_stazione, $connessione) != $destinazione);

            echo "</button>";

            echo "<input type='hidden' name = 'tratta' value = '" . $tratta . "'>";
            echo "<input type='hidden' name = 'partenza' value = '" . $partenza . "'>";
            echo "<input type='hidden' name = 'destinazione' value = '" . $destinazione . "'>";
            echo "<input type='hidden' name = 'prima_sottotratta' value = '" . $sottottratta["id"] . "'>";   
            foreach($tratte_utili as $tratta_utile){
                echo "<input type='hidden' name = 'tratte_utili[]' value = '" . $tratta_utile["id"] . "'>";
            }

            echo "</form>";

            echo "<form class = 'ticket_button' action='acquisto.php' method='post'>";

            echo "<button id = 'acquista' type='submit' name = 'tratta'>Acquista \n Biglietto</button>";
            echo "<input type='hidden' name = 'destinazione' value = '" . $destinazione . "'>";
            echo "<input type='hidden' name = 'partenza' value = '" . $partenza . "'>";

            echo "</form>";

            echo "</div>";


        }

        
    }

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

    
?>
</div>
</div>

    
</body>
</html>