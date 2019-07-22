<?php
require_once('funcs.php');//Get MySQL connection + functions

if (isset($_POST['username']) && isset($_POST['pass'])) {//Came here through the form
    $username = $_POST['username'];
    $password = $_POST['pass'];
} else {//Someone rolled here without going through the form...send em back
    header("Location: index.php");
    exit;
}

ob_start();
session_start();
$select = $db->prepare("SELECT `uid`, `username`, `password` FROM `users` WHERE `username` = :username;");
$result = $select->fetch($select->execute(array(':username' => $username)));
if ($select->rowCount() == 1 && password_verify($password, $result['password'])) {//Row found for username and password is verified as correct
    check_auth(1, $result['uid']);
} else {//Bad password or user not found
    check_auth(0, $result['uid']);
}