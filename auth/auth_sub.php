<?php
define('CRLF',"\r\n");

$login=htmlspecialchars($_GET["login"]);
$client=htmlspecialchars($_GET["client"]);
$password=htmlspecialchars($_GET["password"]);

$email=$login."@".$client;

$fp=fsockopen("pop.".$client,110,$errno,$errstr,10);
echo fgets($fp,1024).'<br />';

/*fputs($fp,"STLS ".CRLF);
echo fgets($fp,1024).'<br />';*/

fputs($fp,"USER ".$login.CRLF);
echo fgets($fp,1024).'<br />';

fputs($fp,"PASS ".$password.CRLF);
echo fgets($fp,1024).'<br />';

fputs($fp,"QUIT ".CRLF);
echo fgets($fp,1024).'<br />';

fclose($fp);
?>
/**
 * Created by PhpStorm.
 * User: Александр
 * Date: 31.10.2016
 * Time: 17:38
 */