<?php
    session_start();
    $hostname = "localhost";
    $username = "root";
    $password = "";
    $database = "gestione_treni";

    $connessione = new mysqli($hostname, $username, $password, $database);

    //echo "Tratta selezionata: " . $_POST["tratta"] . "<br><br>";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $tratte_utili = $_POST["tratte_utili"];
    }

    $tratta = $_POST["tratta"];

    $query = "SELECT * FROM sottotratta
        WHERE id = ?";
        
    $stmt = $connessione->prepare($query);
    $stmt->bind_param("i",$_POST["prima_sottotratta"]);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $sott_succ = $_POST["prima_sottotratta"];
    $partenza = $result[0]["prima_stazione"];
    $orario_partenza;

    while(true){
        $query = "SELECT id, prima_stazione FROM sottotratta
        WHERE sottotratta_successiva = ?";
        
        $stmt = $connessione->prepare($query);
        $stmt->bind_param("i", $sott_succ);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
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
    
    echo "<br>";

    $sottratte = array();

    while($partenza != $capolinea){
        $query_sottotratta = "SELECT s.id, s.tratta, s.orario_partenza, s.orario_arrivo, s. prima_stazione, s.ultima_stazione, s.sottotratta_successiva, r.minuti, r.treno, r.data 
                        FROM sottotratta s
                        LEFT JOIN ritardo r ON s.id = r.sottotratta
                            WHERE s.prima_stazione = ?
                            AND s.tratta = ?
                            AND s.id = ?";
        
        $stmt = $connessione->prepare($query_sottotratta);
        $stmt->bind_param("iii", $partenza, $tratta, $sott_succ);
        $stmt->execute();
        $result_sottotratta = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        //echo "<pre style = 'color: white'>" . var_export($result_sottotratta, true) . "</pre>";

        array_push($sottratte, $result_sottotratta[0]);
        $sott_succ = $result_sottotratta[0]["sottotratta_successiva"];

        $partenza = $result_sottotratta[0]["ultima_stazione"];
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tratta</title>
    <link rel="stylesheet" href="../stylesheets/tratta.css">
</head>
    <div class="blur-overlay"></div>
    <div id = "container">
    <table>
        <thead>
            <tr>
                <th>Stazione</th>
                <th>Orario partenza previsto</th>
                <th>Ritardo (minuti)</th>
                
            </tr>
        </thead>
        <tbody>
        <?php //var_dump($tratte_utili); ?>
        <?php //echo "<pre>" . var_export($sottratte, true) . "</pre>" ?>
        <?php $c = 0; ?>
            <?php foreach($sottratte as $sottotratta): ?>
                <tr>
                    <?php if(in_array($sottotratta["id"], $tratte_utili) || $c == count($tratte_utili)): ?>
                        <td style = "color: red"><?php echo getStaz($sottotratta["prima_stazione"], $connessione);
                        $c++;?>

                    <?php else: ?>
                        <td><?php echo getStaz($sottotratta["prima_stazione"], $connessione); 
                        $c ++;?>

                    <?php endif; ?>

                    <td><?php echo $sottotratta["orario_partenza"]; ?></td>
                    <td><?php echo $sottotratta["minuti"]; ?></td>
                    
                </tr>
                <?php  if($sottotratta["ultima_stazione"] == $capolinea): ?>
                        <tr>
                            <td><?php echo getStaz($sottotratta["ultima_stazione"], $connessione); ?></td>
                            <td> <?php echo $sottotratta["orario_arrivo"]  ?> </td>  
                            <td><?php echo $sottotratta["minuti"];endif;?></td>
                        </tr>
                            
            <?php endforeach; ?>
        </tbody>
    </table>
    </div> 
</body>
</html>

<?php
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
