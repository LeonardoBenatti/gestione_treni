<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cerca il tuo treno</title>
</head>
<link rel="stylesheet" href="../stylesheets/ricerca.css">
<body>
    <div class = "blur-overlay"></div>
    <div id = "container">
        
        <form action="soluzioni.php" method="post">
            <h1>Cerca una soluzione :</h1>

            <input id = "partenza" type="text" name = "partenza" placeholder="Partenza">
            <br>
            <input id = "destinazione" type="text" name = "destinazione" placeholder="Destinazione">
            <br>

            <div id = "submit_box">
                <button type="submit" name = "Cerca">Cerca</button>
            </div>

            <div id = "inverti">
                <img id="inverti_img" onclick="invertiContenuti()" src="../imgs/invert.png">
            </div>
        </form>
        
    </div>

    <script>
        function invertiContenuti() {
            
            var input1 = document.getElementById("partenza").value;
            var input2 = document.getElementById("destinazione").value;

            document.getElementById("partenza").value = input2;
            document.getElementById("destinazione").value = input1;
        }       
    </script>
</body>
</html>