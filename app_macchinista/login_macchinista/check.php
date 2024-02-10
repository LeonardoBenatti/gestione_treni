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

    /*   LOGIN MACCHINISTA   */

    if(isset($_POST["login"])){

        $query = "SELECT * FROM macchinista WHERE CF = ?";        //RICERCA IL MACCHINISTA SE ESISTE

        $stmt = $connessione->prepare($query);
        $connessione->prepare($query);
        $stmt->bind_param("s", $_POST['CF']);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        if($result != NULL){                                                       
            $query = "SELECT * FROM macchinista WHERE CF = ? AND password = ?";     //SE IL MACCHINISTA ESISTE NE VERIFICA LA PASSWORD

            $stmt = $connessione->prepare($query);
            $connessione->prepare($query);
            $stmt->bind_param("ss", $_POST['CF'],$_POST["password"]);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            if($result != NULL){
                $query = "SELECT * FROM treno WHERE id = ?";     //SE IL MACCHINISTA ESISTE E LA PASSWORD È CORRETTA SI CONTROLLA L'ESISTENZA DEL TRENO

                $stmt = $connessione->prepare($query);
                $connessione->prepare($query);
                $stmt->bind_param("s", $_POST['treno']);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

                if($result != NULL){                                    //SE IL MACCHINISTA ESISTE, LA PASSWORD È CORRETTA E IL TRENO ESISTE SI PROCEDE
                    //echo "<br> macchinista trovato password corretta treno esistente <br>";
                    $_SESSION["logged_CF"] = $result[0]["CF"];     
                    $_SESSION["treno"] = $result[0]["id"];  
                    header("Location: ....");
                }
                else{                                       //SE IL MACCHINISTA ESISTE E LA PASSWORD è CORRETTA MA IL TRENO NON ESISTE SI MANDA AL LOGIN IL CF CHE ANDRà RISCRITTA
                    // "<br> macchinista trovato password corretta treno non esistente <br>";
                    if($_POST['treno'] != ""){$_POST['treno'] = "";}
                    echo "<form action = '../index.php' method = 'post' id = 'send'>  
                        <input type = 'hidden' name = 'CF' value = '" . $_POST['CF'] . "'>
                        <input type = 'hidden' name = 'password' value = '" . $_POST['password'] . "'>
                        <input type = 'hidden' name = 'treno' value = '" . $_POST['treno'] . "'>
                    </form>";
                }

                
            }
            else{                                                    //SE IL MACCHINISTA ESISTE E LA PASSWORD è SBAGLIATA SI MANDA AL LOGIN IL CF E LA PASSWORD CHE ANDRANNO RISCRITTI
                //echo "<br> macchinista trovato password sbagliata<br>";
                if($_POST['password'] != ""){$_POST['password'] = "";}
                echo "<form action = '../index.php' method = 'post' id = 'send'>  
                        <input type = 'hidden' name = 'CF' value = '" . $_POST['CF'] . "'>
                        <input type = 'hidden' name = 'password' value = '" . $_POST['password'] . "'>
                        <input type = 'hidden' name = 'treno' value = '" . $_POST['treno'] . "'>
                    </form>";
            }
        }
        else{                                      //SE IL MACCHINISTA NON ESISTE RITORNA AL LOGIN
            //echo "<br> macchinista non trovato <br>";
            header("Location: ../index.php");
        }

        $stmt->close();
        
    }
    else{
        //echo "<br> estraneo <br>";
        header("Location: ../index.php");
    }

?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("send").submit();
    });
</script>