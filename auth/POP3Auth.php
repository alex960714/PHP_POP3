<?php
class POP3Auth
{
    function _construct()
    {}

    function _destruct()
    {}

    function get_data(&$fp)
    {
        $data="";
        while (!feof($fp)) {
            $buffer = chop(fgets($fp,1024));
            $data .= "$buffer\r\n";
            if(trim($buffer) == ".") break;
        }
        return $data;
    }

    function connect($login,$client,$password,&$fp)
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

        //$mes_Num=fputs($fp,"STAT\r\n");
        $pos=strpos($get_pass,' ');
        $mesNum=substr($get_pass,0,$pos);

        /*for($i=1;$i<=$mesNum;$i++)
        {
            $comm="TOP ".$i." 0\r\n";
            fputs($fp,$comm);
            echo fgets($fp,1024).'<br />';
            //print get_data($fp);
        }*/
        /*$comm="TOP 1\r\n";
        fputs($fp,$comm);
        echo fgets($fp,1024).'<br />';*/

        $comm="RETR 5 3\r\n";
        fputs($fp,$comm);
        $data="";
        while (!feof($fp)) {
            $buffer = chop(fgets($fp,1024));
            $data .= "$buffer\r\n";
            if(trim($buffer) == ".") break;
        }
        //$message=$this->compile_body($data, "base64","string" );
        $format = substr($data, strpos($data, "charset=\"") + 9, strpos($data, "\"",strpos($data, "charset=\"") + 9) - (strpos($data, "charset=\"") + 9));
        $data=quoted_printable_decode($data);
        $data=iconv($format,"utf-8",$data);
        //$data=iconv("windows-1251","utf-8",$data);
        echo $data.'<br />';

        /*$comm="TOP 2 1\r\n";
        fputs($fp,$comm);
        echo fgets($fp,1024).'<br />';*/


        fputs($fp,"QUIT ".CRLF);
        echo fgets($fp,1024).'<br />';

        fclose($fp);
        return "success";
    }

    function decode_mime_string($subject) {
        $string = $subject;
        if(($pos = strpos($string,"=?")) === false) return $string;
        while(!($pos === false)) {
            $newresult .= substr($string,0,$pos);
            $string = substr($string,$pos+2,strlen($string));
            $intpos = strpos($string,"?");
            $charset = substr($string,0,$intpos);
            $enctype = strtolower(substr($string,$intpos+1,1));
            $string = substr($string,$intpos+3,strlen($string));
            $endpos = strpos($string,"?=");
            $mystring = substr($string,0,$endpos);
            $string = substr($string,$endpos+2,strlen($string));
            if($enctype == "q") $mystring = quoted_printable_decode(ereg_replace("_"," ",$mystring));
            else if ($enctype == "b") $mystring = base64_decode($mystring);
            $newresult .= $mystring;
            $pos = strpos($string,"=?");
        }

        $result = $newresult.$string;
        if(preg_match("koi8", $subject)) $result = convert_cyr_string($result, "k", "w");
        if(preg_match("KOI8", $subject)) $result = convert_cyr_string($result, "k", "w");
        return $result;
    }

// перекодировщик тела письма.
// Само письмо может быть закодировано и данная функция приводит тело письма в нормальный вид.
// Так же и вложенные файлы будут перекодироваться этой функцией.
    function compile_body($body,$enctype,$ctype) {
        $enctype = explode(" ",$enctype); $enctype = $enctype[0];
        if(strtolower($enctype) == "base64")
            $body = base64_decode($body);
        elseif(strtolower($enctype) == "quoted-printable")
            $body = quoted_printable_decode($body);
        if(preg_match("koi8", $ctype)) $body = convert_cyr_string($body, "k", "w");
        return $body;
    }

// Функция для выдергивания метки boundary из заголовка Content-Type
// boundary это разделитель между разным содержимым в письме,
// например, чтобы отделить файл от текста письма
    function get_boundary($ctype){
        if(preg_match('/boundary[ ]?=[ ]?(["]?.*)/i',$ctype,$regs)) {
            $boundary = preg_replace('/^\"(.*)\"$/', "\1", $regs[1]);
            return trim("--$boundary");
        }
    }

// если письмо будет состоять из нескольких частей (текст, файлы и т.д.)
// то эта функция разобьет такое письмо на части (в массив), согласно разделителю boundary
    function split_parts($boundary,$body) {
        $startpos = strpos($body,$boundary)+strlen($boundary)+2;
        $lenbody = strpos($body,"\r\n$boundary--") - $startpos;
        $body = substr($body,$startpos,$lenbody);
        return explode($boundary."\r\n",$body);
    }

// Эта функция отделяет заголовки от тела.
// и возвращает массив с заголовками и телом
    function fetch_structure($email) {
        $ARemail = Array();
        $separador = "\r\n\r\n";
        $header = trim(substr($email,0,strpos($email,$separador)));
        $bodypos = strlen($header)+strlen($separador);
        $body = substr($email,$bodypos,strlen($email)-$bodypos);
        $ARemail["header"] = $header;
        $ARemail["body"] = $body;
        return $ARemail;
    }

// разбирает все заголовки и выводит массив, в котором каждый элемент является соответсвующим заголовком
    function decode_header($header) {
        $headers = explode("\r\n",$header);
        $decodedheaders = Array();
        for($i=0;$i<count($headers);$i++) {
            $thisheader = trim($headers[$i]);
            if(!empty($thisheader))
                if(!preg_match("^[A-Z0-9a-z_-]+:",$thisheader))
                    $decodedheaders[$lasthead] .= " $thisheader";
                else {
                    $dbpoint = strpos($thisheader,":");
                    $headname = strtolower(substr($thisheader,0,$dbpoint));
                    $headvalue = trim(substr($thisheader,$dbpoint+1));
                    if($decodedheaders[$headname] != "") $decodedheaders[$headname] .= "; $headvalue";
                    else $decodedheaders[$headname] = $headvalue;
                    $lasthead = $headname;
                }
        }
        return $decodedheaders;
    }

// эта функция нам уже знакома. она получает данные и реагирует на точку, которая ставится сервером в конце вывода.
    /*function get_data($pop_conn)
    {
        $data="";
        while (!feof($pop_conn)) {
            $buffer = chop(fgets($pop_conn,1024));
            $data .= "$buffer\r\n";
            if(trim($buffer) == ".") break;
        }
        return $data;
    }*/
}
