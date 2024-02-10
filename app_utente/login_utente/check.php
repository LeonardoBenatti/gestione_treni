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

    if(isset($_POST["registrazione"])){

        if($_POST['email'] != "" && $_POST["nome"] != "" && $_POST['cognome'] != "" && $_POST["password"] != ""){   //SE TUTTI I CAMPI DEL FORM SONO PIENI
            try{
                $query = "INSERT INTO utente (email, nome, cognome, password)                  
                            VALUES (?, ?, ?, ?)";                                       //SI INSERISCE IL NUOVO UTENTE

                $stmt = $connessione->prepare($query);
                $connessione->prepare($query);
                $stmt->bind_param("ssss", $_POST['email'],$_POST["nome"], $_POST['cognome'],$_POST["password"]);
                $stmt->execute();
                header("Location: ../index.php");

            }catch (Exception $e) {
                echo "<form action = '../index.php' method = 'post' id = 'send'>                  
                        <input type = 'hidden' name = 'email' value = '" . $_POST['email'] . "'>
                    </form>";                                                                                 //SE L'UTENTE ESISTE GIA MANDA AL LOGIN CON L'EMAIL GIà INSERITA
        
            }
        }
        else{                       //SE NON TUTTI I CAMPI DEL FORM SONO PIENI
            $campi = array();

            foreach ($_POST as $key => $value) {    //campi con valore e chiave in una array
                $campi[$key] = $value;
            }

            echo "<form action = 'registrazione.php' method = 'post' id = 'send'>";
            foreach ($campi as $key => $value) {
                echo "<input type = 'hidden' name = '" . $key . "' value = '" . $value . "'>";   //campi vengono rimandati a registrazione (che li gestirà)
            }  
            echo  "</form>";
        }
        

    }



    /*   LOGIN UTENTE   */

    if(isset($_POST["login"])){

        $query = "SELECT * FROM utente WHERE email = ?";        //RICERCA L'UTENTE SE ESISTE

        $stmt = $connessione->prepare($query);
        $connessione->prepare($query);
        $stmt->bind_param("s", $_POST['email']);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        if($result != NULL){                                                       
            $query = "SELECT * FROM utente WHERE email = ? AND password = ?";     //SE L'UTENTE ESISTE NE VERIFICA LA PASSWORD

            $stmt = $connessione->prepare($query);
            $connessione->prepare($query);
            $stmt->bind_param("ss", $_POST['email'],$_POST["password"]);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            if($result != NULL){
                $_SESSION["logged_email"] = $result[0]["email"];   //SE L'UTENTE ESISTE E LA PASSWORD è CORRETTA SI PROCEDE
                //echo $_SESSION["logged_email"];
                header("Location: ....");
            }
            else{                                                     //SE L'UTENTE ESISTE E LA PASSWORD è SBAGLIATA SI MANDA AL LOGIN L'EMAIL CHE ANDRà RISCRITTA E LA PASSWORD VUOTA
                if($_POST['password'] != ""){$_POST['password'] = "";}
                echo "<form action = '../index.php' method = 'post' id = 'send'>  
                        <input type = 'hidden' name = 'email' value = '" . $_POST['email'] . "'>
                        <input type = 'hidden' name = 'password' value = '" . $_POST['password'] . "'>
                    </form>";
            }
        }
        else{                                      //SE L'UTENTE NON ESISTE RITORNA AL LOGIN
            header("Location: ../index.php");
        }

        $stmt->close();
        
    }
    
?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("send").submit();
    });
</script>