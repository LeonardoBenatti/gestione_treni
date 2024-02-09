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
        <form action="login/check.php" method="post">
            <select>
                <option></option>

            </select>

            <div class="input-box">
                <label>Email</label>
                <input type="text" name="email">
            </div>
            <div class="input-box">
                <label>Password</label>
                <input type="password" name="password">
            </div>

            <button id="login-button" type="submit">Login</button>
        </form>
    </div>
</div>


<style>



</style>

</body>
</html>