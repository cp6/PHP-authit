<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<?php
require_once('funcs.php');
ob_start();
session_start();
if (!isset($_SESSION['user'])) {//User not successfully logged in
$status_set = 0;
if (isset($_GET['status'])) {//Check for status code
    $status_set = 1;//Status is now set
}
?>
<head>
    <title>Login</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' href='bootstrap.min.css'>
    <link rel='stylesheet' href='login_style.css'>
</head>
<body>
<div class="container">
    <div class="row text-center">
        <div class="col-2"></div>
        <div class="col-8">
            <form method="post" action="auth.php">
                <?php if ($status_set == 1) {
                    if ($_GET['status'] == 0) {
                        echo "<div class='alert alert-danger' role='alert'>
                          Username or Password is incorrect!
                         </div>";
                    } elseif ($_GET['status'] == 1) {
                        echo "<div class='alert alert-danger' role='alert'>
                          Please enter captcha
                         </div>";
                    } elseif ($_GET['status'] == 2) {
                        echo "<div class='alert alert-danger' role='alert'>
                          Captcha was incorrect
                         </div>";
                    }
                } ?>
                <div class="form-group margin-below">
                    <input type="text" class="form-control" name="username" id="username"
                           aria-describedby="username"
                           placeholder="Username">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" name="pass" id="pass" placeholder="Password">
                </div>
                <div class="form-group"><label class="label">Captcha:</label>
                    <img id="captcha" alt="captcha" src=""/>
                    <input type="text" class="form-control" name="captcha" id="captcha"
                           aria-describedby="captcha"
                           placeholder="">
                </div>
                <button type="submit" value="submit" class="btn btn-primary btn-block">Login</button>
            </form>
        </div>
        <div class="col-2"></div>
    </div>
</div>
<script language="javascript" type="text/javascript">var d = new Date();
    document.getElementById("captcha").src = "captcha.png?ver=" + d.getTime();</script>
<?php
} else {//User is logged in (has session set)
?>
<head>
    <title>Dash - home</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' href='bootstrap.min.css'>
    <link rel='stylesheet' href='dash_style.min.css'>
</head>
<body>
<div class='container'>
    <h1>Logged in</h1>
    <p>Redirect to dash or have dash here</p>
</div>
<?php } ?>
</body>
</html>