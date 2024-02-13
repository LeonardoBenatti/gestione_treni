

<?php
    $hostname = "localhost";
    $username = "root";
    $password = "";
    $database = "gestione_treni";

    $connessione = new mysqli($hostname, $username, $password, $database);


    echo "tratta selezionata: " . $_POST["tratta"] . "<br>";
    echo "<br>";
    //echo "partenza: " . $_POST["partenza"] . "<br>";
    //echo "destinazione: " . $_POST["destinazione"] . "<br>";
    //echo "sott_succ: " . $_POST["prima_sottotratta"] . "<br>";

    $partenza = getStaz($_POST['partenza'], $connessione);;
    $tratta = $_POST["tratta"];

    $sott_succ = $_POST["prima_sottotratta"];
    $partenza;

    //echo "sott_succ_iniziale: " . $sott_succ . "<br>";


    while(true){
        $query = "SELECT id, prima_stazione FROM sottotratta
        WHERE sottotratta_successiva = ?";
        
        $stmt = $connessione->prepare($query);
        $stmt->bind_param("i", $sott_succ);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
        //echo "sott_succ: " . $sott_succ . "<br>";
    
        if(!empty($result)){
            $sott_succ = $result[0]["id"];
            $partenza = $result[0]["prima_stazione"];
            
        }
        else{
            break;
        }
    }
    

    
    
    $query_partenza = "SELECT ultima_stazione FROM tratta 
                    WHERE id = ?";
        
    $stmt = $connessione->prepare($query_partenza);
    $connessione->prepare($query_partenza);
    $stmt->bind_param("i", $_POST["tratta"]);
    $stmt->execute();
    $result_partenza = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $capolinea = $result_partenza[0]["ultima_stazione"];
    //echo "partenza nome: " . getStaz($partenza, $connessione) . "<br>";
    //echo "partenza id: " . $partenza . "<br>";
    //echo "capolinea nome: " . getStaz($capolinea, $connessione) . "<br>";
    //echo "capolinea id: " . $capolinea . "<br>";

    echo "stazione: " . getStaz($partenza, $connessione) . "<br>";
    echo "<br>";

    while($partenza != $capolinea){
        $query_sottotratta = "SELECT * FROM sottotratta 
                    WHERE prima_stazione = ?
                    AND tratta = ?";
        
        $stmt = $connessione->prepare($query_sottotratta);
        $connessione->prepare($query_sottotratta);
        $stmt->bind_param("ii", $partenza, $tratta);
        $stmt->execute();
        $result_sottotratta = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        //echo "partenza: " . getStaz($partenza, $connessione) . "<br>";
        //echo "tratta: " . $tratta . "<br>";

        //var_dump($result_sottotratta);
        echo "stazione: " . getStaz($result_sottotratta[0]["ultima_stazione"], $connessione) . "<br>";
        echo "<br>";

        $partenza = $result_sottotratta[0]["ultima_stazione"];

    }

    function getStaz($var, $connessione){
        $query = "";
        $param_type = "";
        $what = "";
        if(is_int($var) || is_numeric($var)){
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

        return $result[0][$what] ?? null;
    }

    
?>