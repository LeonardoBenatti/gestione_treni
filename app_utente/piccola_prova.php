<?php
    $hostname = "localhost";
    $username = "root";
    $password = "";
    $database = "gestione_treni";

    $connessione = new mysqli($hostname, $username, $password, $database);

    $query = "SELECT * FROM tratta 
                WHERE id = (
                    SELECT tratta FROM sottotratta
                    WHERE prima_stazione = (SELECT id FROM stazione WHERE nome = 'ferrara')
                    AND ultima_stazione = (SELECT id FROM stazione WHERE nome = 'rovigo')
                    AND orario_partenza = '15:41:00'
                )";

    $stmt = $connessione->prepare($query);
    $connessione->prepare($query);
    //$stmt->bind_param("s", $_POST['email']);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    //var_dump($result);

    $partenza = $result[0]["prima_stazione"];
    $arrivo = "";
    $tratta = $result[0]["prima_stazione"];

    while($arrivo != $result[0]["ultima_stazione"]){
        $query = "SELECT * FROM sottotratta 
                    WHERE prima_stazione = ?
                    AND tratta = ?";
        
        $stmt = $connessione->prepare($query);
        $connessione->prepare($query);
        $stmt->bind_param("ii", $partenza, $tratta);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        var_dump($result);
        echo "<br>";

        $partenza = $result[0]["prima_stazione"];
        $arrivo = $result[0]["ultima_stazione"];
    }
    
?>