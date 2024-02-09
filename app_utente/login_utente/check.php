<?php

    session_start();

    $hostname = "localhost";
    $username = "root";
    $password = "";
    $database = "gestione_treni";

    $connessione = new mysqli($hostname, $username, $password, $database);

    if ($connessione->connect_error) {
        die("Connessione fallita: " . $connessione->connect_error);
    }

    /*   REGISTRAZIONE UTENTE   */


    /*   LOGIN UTENTE   */

    if(isset($POST["login"])){

        $query = "SELECT * FROM utente WHERE email = ? AND password = ?";

        $stmt = $connessione->prepare($query);
        $connessione->prepare($query);
        $stmt->bind_param("ss", $_POST['email'],$_POST["password"]);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        if($result != NULL){
            $_SESSION["logged_email"] = $result[0]["email"];
            echo $_SESSION["logged_email"];
        }

        $stmt->close();
        
    }
    
?>