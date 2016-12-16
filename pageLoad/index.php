<?php
include "../auth/POP3Auth.php";
$connect=new POP3Auth();

if($connect->connect($_POST['login'],$_POST['client'] ,$_POST['password'] ) == "success") {
    $_SESSION['log'] = $_POST['login'] . '@' . $_POST['client'];
    require "../index.html";
}
else {
    require "../headers/auth_header.html";
    require "../auth/auth.php";
}
