<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<link rel="stylesheet" href="../stylesheets/login.css">
<body>
<div class="container">
    <div class="login-box">
        <h2>Registazione</h2>
        <form action="login/check.php" method="post" style = "height: 90vh;"> 

            <div class="input-box">
                <label>Email</label>
                <input type="text" name="email">
            </div>
            <div class="input-box">
                <label>Password</label>
                <input type="password" name="password">
            </div>
            <div class="input-box">
                <label>Nome</label>
                <input type="text" name="nome">
            </div>
            <div class="input-box">
                <label>Cognome</label>
                <input type="text" name="cognome">
            </div>

            <div class="input-box" style = "text-transform: uppercase;">
                <a href = "index.php">hai già un account? accedi</label>
            </div>

            <button id="login-button" type="submit" name = "registrazione">Registrati</button>
        </form>
    </div>
</div>


<style>



</style>

</body>
</html>