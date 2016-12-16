<?php
define('CRLF',"\r\n");

$login=htmlspecialchars($_GET["login"]);
$client=htmlspecialchars($_GET["client"]);
$password=htmlspecialchars($_GET["password"]);

$email=$login."@".$client;
$host="pop.".$client;
$fp=fsockopen($host,110,$errno,$errstr,10);
echo fgets($fp,1024).'<br />';

/*fputs($fp,"STLS ".CRLF);
echo fgets($fp,1024).'<br />';*/

fputs($fp,"USER ".$login.CRLF);
$get_log = fgets($fp,1024);
echo $get_log.'<br />';

fputs($fp,"PASS ".$password.CRLF);
$get_pass = fgets($fp,1024);
echo $get_pass.'<br />';

if(substr($get_log,0,4)=="-ERR" || substr($get_pass,0,4)=="-ERR")
{
 echo "err";
}

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