

<?php
    $hostname = "localhost";
    $username = "root";
    $password = "";
    $database = "gestione_treni";

    $connessione = new mysqli($hostname, $username, $password, $database);


    echo "tratta selezionata: " . $_POST["tratta"] . "<br>";
    echo "partenza: " . $_POST["partenza"] . "<br>";
    echo "destinazione: " . $_POST["destinazione"] . "<br>";
    echo "capolinea: " . $_POST["capolinea"] . "<br>";

    $partenza = getStaz($_POST['partenza'], $connessione);;
    $tratta = $_POST["tratta"];

    while($partenza != $_POST["capolinea"]){
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