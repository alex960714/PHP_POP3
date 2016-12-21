<?php
include "../auth/POP3Auth.php";
$connect=new POP3Auth();

if($connect->connect($_POST['login'],$_POST['client'] ,$_POST['password'], $fp ) == "success") {
    $_SESSION['log'] = $_POST['login'] . '@' . $_POST['client'];
    $_GET['connect']=$fp;
    require "../messeges/index.html";
    
}
else {
    require "../headers/auth_header.html";
    require "../auth/auth.php";
}
