<?php //var_dump($_POST);  ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<link rel="stylesheet" href="../stylesheets/registrazione.css">
<body>
<div class = "blur-overlay"></div>
<div class="container">
    <div class="login-box">
        <h2>Registazione</h2>
        <form action="check.php" method="post"> 

            <div class="input-box">
                <label>Email</label>
                <input type="text" name="email" value = "<?php if(isset($_POST["email"]) && $_POST["email"] != ""){echo $_POST["email"];} ?>" style = "<?php if(isset($_POST["email"]) && $_POST["email"] == ""){echo "border: 2px solid red;";} ?>">
            </div>
            <div class="input-box">
                <label>Password</label>
                <input type="password" name="password" value = "<?php if(isset($_POST["password"]) && $_POST["password"] != ""){echo $_POST["password"];} ?>" style = "<?php if(isset($_POST["password"]) && $_POST["password"] == ""){echo "border: 2px solid red;";} ?>">
            </div>
            <div class="input-box">
                <label>Nome</label>
                <input type="text" name="nome" value = "<?php if(isset($_POST["nome"]) && $_POST["nome"] != ""){echo $_POST["nome"];} ?>" style = "<?php if(isset($_POST["nome"]) && $_POST["nome"] == ""){echo "border: 2px solid red;";} ?>">
            </div>
            <div class="input-box">
                <label>Cognome</label>
                <input type="text" name="cognome" value = "<?php if(isset($_POST["cognome"]) && $_POST["cognome"] != ""){echo $_POST["cognome"];} ?>" style = "<?php if(isset($_POST["cognome"]) && $_POST["cognome"] == ""){echo "border: 2px solid red;";} ?>">
            </div>

            <div class="input-box" style = "text-transform: uppercase;">
                <a href = "../index.php">hai già un account? accedi</label>
            </div>

            <input id="login-button" type="submit" name = "registrazione" value = "Registrazione">
        </form>
    </div>
</div>


<style>



</style>

</body>
</html>