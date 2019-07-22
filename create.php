<?php
function create_db_connection($hostname, $user, $password, $filename)
{
    $content = '<?php
    $db = new PDO("mysql:host=' . $hostname . ';dbname=auth;charset=utf8mb4", "' . $user . '", "' . $password . '");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);';
    $the_file = fopen("$filename.php", "w") or die("Unable to open file!");
    fwrite($the_file, $content);
    fclose($the_file);
}

create_db_connection($_POST['db_host'], $_POST['db_user'], $_POST['db_password'], 'db_connect');

$db = new PDO("mysql:host=" . $_POST['db_host'] . ";charset=utf8mb4", $_POST['db_user'], $_POST['db_password']);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db_create = $db->prepare("CREATE DATABASE IF NOT EXISTS `auth`");//Create Database
$db_create->execute();

$db = new PDO("mysql:host=" . $_POST['db_host'] . ";dbname=auth;charset=utf8mb4", $_POST['db_user'], $_POST['db_password']);//Connect and use created database
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db_create_captcha = $db->prepare("CREATE TABLE IF NOT EXISTS `captcha` (
`string` varchar(255) DEFAULT 'GKMYK'
) ENGINE = InnoDB DEFAULT CHARSET = latin1;");
$db_create_captcha->execute();//Create captcha table

$db_create_users = $db->prepare("CREATE TABLE IF NOT EXISTS `users` (
`uid` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `attempts` int(11) DEFAULT '0',
  `locked` tinyint(4) DEFAULT '0',
  `lock_until` datetime DEFAULT '2012-12-12 02:12:12',
  PRIMARY KEY(`uid`),
  UNIQUE KEY `Index 2` (`username`)
) ENGINE = InnoDB DEFAULT CHARSET = latin1;");
$db_create_users->execute();//Create users table

$hash_password = password_hash($_POST['password'], PASSWORD_DEFAULT);//Hash the submitted password

$insert = $db->prepare('INSERT INTO `users` (`username`, `password`) VALUES (?, ?)');
$insert->execute([$_POST['username'], $hash_password]);//Create the user you defined in the form

if ($_POST['captcha'] == 0){//No
    unlink('tobe_index.php');
    rename('tobe_index_nocaptcha.php', 'index.php');
    unlink('auth.php');
    rename('auth_nocaptcha.php', 'auth.php');
    unlink('funcs.php');
    rename('funcs_nocaptcha.php', 'funcs.php');
} elseif ($_POST['captcha'] == 1){//Yes
    unlink('tobe_index_nocaptcha.php');
    rename('tobe_index', 'index.php');
    unlink('auth_nocaptcha.php');
    rename('auth_nocaptcha.php', 'auth.php');
    unlink('funcs_nocaptcha.php');
}
unlink('index.html');
unlink('create.php');