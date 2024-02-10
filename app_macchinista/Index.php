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
<link rel="stylesheet" href="stylesheets/login.css">
<body>
<div class="container">
    <div class="login-box">
        <h2>Login</h2>
        <form action="login_macchinista/check.php" method="post">

            <div class="input-box">
                <label>Codice Fiscale</label>
                <input type="text" name="CF" value = "<?php if(isset($_POST["CF"])){echo $_POST["CF"];} ?>">
            </div>
            <div class="input-box">
                <label>Password</label>
                <input type="password" name="password" value = "<?php if(isset($_POST["password"])){echo $_POST["password"];} ?>" style = "<?php if(isset($_POST["password"]) && $_POST["password"] == ""){echo "border: 2px solid red;";} ?>">
            </div>
            <div class="input-box">
                <label>A bordo del treno:</label>
                <input type="text" name="treno" value = "<?php if(isset($_POST["treno"])){echo $_POST["treno"];} ?>" style = "<?php if(isset($_POST["treno"]) && $_POST["treno"] == ""){echo "border: 2px solid red;";} ?>">
            </div>

            <input name = "login" id="login-button" type="submit" value = "login">
        </form>
    </div>
</div>


<style>



</style>

</body>
</html>