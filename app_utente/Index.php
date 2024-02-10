
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<link rel="stylesheet" href="stylesheets/login.css">
<body>
<div class="container">
    <div class="login-box">
        <h2>Login</h2>
        <form action="login_utente/check.php" method="post">

            <div class="input-box">
                <label>Email</label>
                <input type="text" name="email" value = "<?php if(isset($_POST["email"]) && $_POST["email"] != ""){echo $_POST["email"];} ?>">
            </div>
            <div class="input-box">
                <label>Password</label>
                <input type="password" name="password" style = "<?php if(isset($_POST["password"]) && $_POST["password"] == ""){echo "border: 2px solid red;";} ?>">
            </div>

            <div class="input-box" style = "text-transform: uppercase;">
                <a href = "login_utente/registrazione.php">non sei registrato? registrati</label>
            </div>

            <input id="login-button" type="submit" name = "login" value = "Login">
        </form>
    </div>
</div>

</body>
</html>