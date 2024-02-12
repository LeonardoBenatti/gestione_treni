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
    $connessione->prepare($query_tratta);
    $stmt->bind_param("ss", $_POST['partenza'], $_POST['destinazione']);
    $stmt->execute();
    $result_tratta = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    //echo $_POST["partenza"] . "<br>";
    //echo $_POST["destinazione"] . "<br>";

    foreach($result_tratta as $row){
        var_dump($row);
        //echo "tratta selezionata: " . $row["id"] . "<br>";

        $partenza = getStaz($_POST['partenza'], $connessione);
        $tratta = $row["id"];

        echo "<form action='tratta.php' method='post'>";
            
            echo "<button type='submit' name = 'tratta'>";

            while(getStaz($partenza, $connessione) != $_POST['destinazione']){

                $query_sottotratta = "SELECT * FROM sottotratta 
                            WHERE prima_stazione = ?
                            AND tratta = ?";
                
                $stmt = $connessione->prepare($query_sottotratta);
                $connessione->prepare($query_sottotratta);
                $stmt->bind_param("ii", $partenza, $tratta);
                $stmt->execute();
                $result_sottotratta = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

                //var_dump($result_sottotratta);
                if(getStaz($partenza, $connessione) == $_POST['partenza'] ){
                    echo $_POST['partenza']. " " . $result_sottotratta[1]["orario_partenza"];
                }
                
                if(getStaz($result_sottotratta[1]["ultima_stazione"], $connessione) == $_POST['destinazione'] ){
                    echo " - " . $_POST['destinazione']. " " . $result_sottotratta[1]["orario_arrivo"];
                }
                

                $partenza = $result_sottotratta[1]["ultima_stazione"];
                //echo "partenza: " . getStaz($partenza, $connessione) . "<br>";
            }
            echo "</button>";

            echo "<input type='hidden' name = 'tratta' value = '" . $row["id"] . "'>";
            echo "<input type='hidden' name = 'partenza' value = '" . $_POST["partenza"] . "'>";
            echo "<input type='hidden' name = 'destinazione' value = '" . $_POST["destinazione"] . "'>";
            echo "<input type='hidden' name = 'capolinea' value = '" . $row["ultima_stazione"] . "'>";
        
        echo "</form>";

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