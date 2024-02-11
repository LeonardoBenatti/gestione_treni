<?php

/* QUESTO CODICE, PARTENDO DALLA PARTENZA E DALLA DESTINAZIONE, RITORNA TUTTE LE TRATTE POSSIBILI E TUTTE LE SOTTOTRATTE ALL'INTERNO  */
    $hostname = "localhost";
    $username = "root";
    $password = "";
    $database = "gestione_treni";

    $connessione = new mysqli($hostname, $username, $password, $database);

    $query_tratta = "SELECT * FROM tratta 
    WHERE id IN (
        SELECT tratta FROM sottotratta 
        WHERE prima_stazione = (SELECT id FROM stazione WHERE nome = 'bologna')
    )
    AND id IN (
        SELECT tratta FROM sottotratta 
        WHERE ultima_stazione = (SELECT id FROM stazione WHERE nome = 'ferrara')
    )";

    $stmt = $connessione->prepare($query_tratta);
    $connessione->prepare($query_tratta);
    //$stmt->bind_param("s", $_POST['email']);
    $stmt->execute();
    $result_tratta = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    foreach($result_tratta as $row){
        echo "tratta selezionata: " . $row["id"] . "<br>";

        $partenza = $row["prima_stazione"];
        $tratta = $row["id"];

        while($partenza != $row["ultima_stazione"]){
            $query_sottotratta = "SELECT * FROM sottotratta 
                        WHERE prima_stazione = ?
                        AND tratta = ?";
            
            $stmt = $connessione->prepare($query_sottotratta);
            $connessione->prepare($query_sottotratta);
            $stmt->bind_param("ii", $partenza, $tratta);
            $stmt->execute();
            $result_sottotratta = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            var_dump($result_sottotratta);
            echo "<br>";

            $partenza = $result_sottotratta[0]["ultima_stazione"];

        }

    }
    
?>