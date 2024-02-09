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

        if($_POST['email'] != "" && $_POST["nome"] != "" && $_POST['cognome'] != "" && $_POST["password"] != ""){
            try{
                $query = "INSERT INTO utente (email, nome, cognome, password) 
                            VALUES (?, ?, ?, ?)";

                $stmt = $connessione->prepare($query);
                $connessione->prepare($query);
                $stmt->bind_param("ssss", $_POST['email'],$_POST["nome"], $_POST['cognome'],$_POST["password"]);
                $stmt->execute();
                header("Location: ../index.php");

            }catch (Exception $e) {
                echo "<form action = '../index.php' method = 'post' id = 'send'> 
                        <input type = 'hidden' name = 'email' value = '" . $_POST['email'] . "'>
                    </form>";
        
            }
        }
        else{
            $campi = array();

            foreach ($_POST as $key => $value) {
                $campi[$key] = $value;
            }

            var_dump($campi);

            echo "<form action = 'registrazione.php' method = 'post' id = 'send'>";
            foreach ($campi as $key => $value) {
                echo "<input type = 'hidden' name = '" . $key . "' value = '" . $value . "'>";
            }  
            echo  "</form>";
        }
        

    }



    /*   LOGIN UTENTE   */

    if(isset($_POST["login"])){

        $query = "SELECT * FROM utente WHERE email = ? AND password = ?";

        $stmt = $connessione->prepare($query);
        $connessione->prepare($query);
        $stmt->bind_param("ss", $_POST['email'],$_POST["password"]);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        if($result != NULL){
            $_SESSION["logged_email"] = $result[0]["email"];
            echo $_SESSION["logged_email"];
            header("Location: ....");
        }

        $stmt->close();
        
    }
    
?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("send").submit();
    });
</script>