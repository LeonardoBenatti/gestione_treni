<?php
    
    session_start();
    // Impostare il fuso orario (opzionale)
    date_default_timezone_set('Europe/Rome'); // Sostituisci con il tuo fuso orario
    
    // Ottenere la data attuale
    $_SESSION["dataAttuale"] = date('Y-m-d');
    $_SESSION["data"] = date('Y-m-d');

    if (isset($_SESSION['logged'])) {
        unset($_SESSION['logged']);
    }
    if (isset($_SESSION['email_prof'])) {
        unset($_SESSION['email_prof']);
    }
    if (isset($_SESSION['email_tecnico'])) {
        unset($_SESSION['email_tecnico']);
    }
    

    $hostname = "localhost";
    $username = "root";
    $password = "";
    $database = "controllo_classi";

    $connessione = new mysqli($hostname, $username, $password, $database);

    if ($connessione->connect_error) {
        die("Connessione fallita: " . $connessione->connect_error);
    }

    $user_email = $_POST['email'];
    $pass = $_POST['password'];
    $docente = 0;
    $tecnico = 0;
    
    /*   CONTROLLO DOCENTE   */

    $query = "SELECT * FROM docente";
    $stmt = $connessione->prepare($query);
    $connessione->prepare($query);
    if ($stmt) {
        $stmt->execute();
        $stmt->bind_result($email, $password, $nome, $cognome);
        while ($stmt->fetch()) {
            if($email == $user_email){
                if($password == $pass){
                    $docente = 1;
                    $_SESSION["logged"] = $nome . " " . $cognome;
                    $_SESSION["email_prof"] = $email;
                }
            }
            
        }

        $stmt->close();
    }
    else {
        echo "Errore nella preparazione della query: " . $connessione->error;
    }

    /*   CONTROLLO TECNICO   */

    if($docente != 1){
        $query = "SELECT * FROM tecnico";
        $stmt = $connessione->prepare($query);
        $connessione->prepare($query);
        if ($stmt) {
            $stmt->execute();
            $stmt->bind_result($email, $password, $nome, $cognome, $zona);
            while ($stmt->fetch()) {
                if($email == $user_email){
                    if($password == $pass){
                        $tecnico = 1;
                        $_SESSION["logged"] = $nome . " " . $cognome;
                        $_SESSION["id"] = $zona;
                        $_SESSION["email_tecnico"] = $email;
                    }
                }
                
            }
    
            $stmt->close();
        }
        else {
            echo "Errore nella preparazione della query: " . $connessione->error;
        }
    }
    



    if($docente == 1)
        header("Location:http://localhost/DATABASE/gestione_carrelli/docente/views_docente.php");

    if($tecnico == 1)
        header("Location:http://localhost/DATABASE/gestione_carrelli/tecnico/views_tecnico.php");
    
    if($docente != 1 && $tecnico != 1)
        header("Location:http://localhost/DATABASE/gestione_carrelli/");
?>