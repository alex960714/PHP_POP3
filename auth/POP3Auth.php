<?php
class POP3Auth
{
    function _construct()
    {}

    function _destruct()
    {}
    
    function connect($login,$client,$password)
    {
        define('CRLF',"\r\n");
        //$email=$login."@".$client;
        $host="pop.".$client;
        try {
            $fp = fsockopen($host, 110, $errno, $errstr, 10);
            if($fp==false)
            {
                return "con_fail";
            }
            $connect = fgets($fp, 1024);
            if (substr($connect, 0, 4) == "-ERR") {
                fclose($fp);
                return "con_fail";
            }
        }
        catch(Exception $e)
        {
            return "con_fail";
        }

        /*fputs($fp,"STLS ".CRLF);
        echo fgets($fp,1024).'<br />';*/

        
        fputs($fp,"USER ".$login.CRLF);
        $get_log = fgets($fp,1024);
        echo $get_log.'<br />';

        fputs($fp,"PASS ".$password.CRLF);
        $get_pass = fgets($fp,1024);
        echo $get_pass.'<br />';

        if(substr($get_log,0,4)=="-ERR" || substr($get_pass,0,4)=="-ERR") {
            fclose($fp);
            return "log_or_pass_fail";
        }

        fputs($fp,"QUIT ".CRLF);
        echo fgets($fp,1024).'<br />';

        fclose($fp);
        return "success";
    }
    
}
