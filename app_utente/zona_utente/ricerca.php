<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cerca il tuo treno</title>
</head>
<link rel="stylesheet" href="../stylesheets/ricerca.css">
<body>
    <form action="soluzioni.php" method="post">

    <input id = "partenza" type="text" name = "partenza" placeholder="partenza">
    <br>
    <input id = "destinazione" type="text" name = "destinazione" placeholder="destinazione">
    <br>
    <button type="submit" name = "cerca">cerca</button>

    </form>
    <img id="img" width="30vh" onclick="invertiContenuti()" src="../Images/invert.png">

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