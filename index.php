<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Авторизация</title>
    <link rel="stylesheet" type="text/css" href="auth/auth_style.css" />
</head>
<?php
include "auth/POP3Auth.php";
$connect=new POP3Auth();
/*switch($connect->connect($_POST['login'],$_POST['client'] ,$_POST['password'] )) {
    case "con_fail":
    case "log_or_pass_fail":
        require "auth/auth.php";
        break;
    case "success":
        require "index.html";
}*/
if($connect->connect($_POST['login'],$_POST['client'] ,$_POST['password'] ) == "success")
    require "index.html";
else
    require "auth/auth.php";
?>
</html>