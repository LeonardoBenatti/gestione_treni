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
    $stmt->bind_param("ss", $_POST['partenza'], $_POST['destinazione']);
    $stmt->execute();
    $result_tratta = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    //echo $_POST["partenza"] . "<br>";
    //echo $_POST["destinazione"] . "<br>";

    foreach($result_tratta as $row){
        //var_dump($row);
        //echo "tratta selezionata: " . $row["id"] . "<br>";

        $partenza = getStaz($_POST['partenza'], $connessione);
        $tratta = $row["id"];

        $query_partenza = "SELECT * FROM sottotratta 
                            WHERE prima_stazione = ?
                            AND tratta = ?";

        $stmt = $connessione->prepare($query_partenza);
        $stmt->bind_param("ii", $partenza, $tratta);
        $stmt->execute();
        $result_partenza = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach($result_partenza as $sottottratta){

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


                if(getStaz($result_sottotratta[0]["prima_stazione"], $connessione) == $_POST['partenza'] ){
                    echo $_POST['partenza']. " " . $result_sottotratta[0]["orario_partenza"];
                }
                
                if(getStaz($result_sottotratta[0]["ultima_stazione"], $connessione) == $_POST['destinazione'] ){
                    echo " - " . $_POST['destinazione']. " " . $result_sottotratta[0]["orario_arrivo"] . "<br>";
                }
                
                $ultima_stazione = $result_sottotratta[0]["ultima_stazione"];
                $prox = $result_sottotratta[0]["sottotratta_successiva"];
                //echo "ulti_staz: " . getStaz($ultima_stazione, $connessione) . "<br>";
            }while(getStaz($ultima_stazione, $connessione) != $_POST['destinazione']);

            echo "</button>";

            echo "<input type='hidden' name = 'tratta' value = '" . $tratta . "'>";
            echo "<input type='hidden' name = 'partenza' value = '" . $_POST["partenza"] . "'>";
            echo "<input type='hidden' name = 'destinazione' value = '" . $_POST["destinazione"] . "'>";
            echo "<input type='hidden' name = 'prima_sottotratta' value = '" . $sottottratta["id"] . "'>";   
            foreach($tratte_utili as $tratta_utile){
                echo "<input type='hidden' name = 'tratte_utili[]' value = '" . $tratta_utile["id"] . "'>";
            }
        
        echo "</form>";


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