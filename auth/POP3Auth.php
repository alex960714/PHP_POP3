<?php
class POP3Auth
{
    function _construct()
    {
        define('CRLF', "\r\n");
    }

    function _destruct()
    {}

    function connect($login,$client,$password,&$fp)
    {
        define('CRLF', "\r\n");

        //$email=$login."@".$client;
        $host = "pop." . $client;
        try {
            $fp = fsockopen($host, 110, $errno, $errstr, 10);
            if ($fp == false) {
                return "con_fail";
            }
            $connect = fgets($fp, 1024);
            if (substr($connect, 0, 4) == "-ERR") {
                fclose($fp);
                return "con_fail";
            }
        } catch (Exception $e) {
            return "con_fail";
        }

        /*fputs($fp,"STLS ".CRLF);
        echo fgets($fp,1024).'<br />';*/


        fputs($fp, "USER " . $login . CRLF);
        $get_log = fgets($fp, 1024);
        //echo $get_log . '<br />';

        fputs($fp, "PASS " . $password . CRLF);
        $get_pass = fgets($fp, 1024);
        //echo $get_pass . '<br />';

        if (substr($get_log, 0, 4) == "-ERR" || substr($get_pass, 0, 4) == "-ERR") {
            fclose($fp);
            return "log_or_pass_fail";
        }
        //session_start();
        $_SESSION['id']=$login."@".$client;
        $_SESSION['log']=$login;
        $_SESSION['client']=$client;
        $_SESSION['pass']=$password;
        return "success";
    }
    
    function sessionExit(&$fp)
    {
        fputs($fp, "QUIT " . "\r\n");
        echo fgets($fp, 1024) . '<br />';


        fclose($fp);
        return "success";
    }

    function printHeaders(&$fp) {
        fputs($fp,"STAT\r\n");
        $get_stat=fgets($fp,1024);
        $pos = strpos($get_stat, ' ',4)-4;
        $mesNum = substr($get_stat, 4 , $pos);
        //echo $mesNum.'<br />';
        //print_r($_SESSION).'<br />';
        //$subj=Array();
        $bodies=Array();


        for($i=1;$i<=$mesNum*2;$i++) {
            $comm = "RETR " . $i. "\r\n";
            fputs($fp, $comm);

            /*$comm="TOP 1\r\n";
            fputs($fp,$comm);
            echo fgets($fp,1024).'<br />';*/


            fputs($fp, $comm);
            $data = $this->get_data($fp);
            //$message=$this->compile_body($data, "base64","string" );
            $format = substr($data, strpos($data, "charset=\"") + 9, strpos($data, "\"", strpos($data, "charset=\"") + 9) - (strpos($data, "charset=\"") + 9));
            $data = quoted_printable_decode($data);
            if ($format != "utf-8") $data = iconv($format, "utf-8", $data);
            /*$format = substr($data, strpos($data, "base64"), 7);
            echo $format.'<br />';*/

            $data = $this->decode_mime_string($data);
            $data = $this->compile_body($data, "utf-8");
            $email = $this->fetch_structure($data);

            //echo $email['header'] . '<br />';

            $from_pos = strpos($email['header'], "From:") + 6;
            //echo $base64_num . '<br />';
            $from_name = substr($email['header'], $from_pos, strpos($email['header'], "To", $from_pos) - $from_pos);
            if(strlen($from_name)>28) {
                $from_name=substr($from_name,0,23)."...";
            }
            if(strlen($from_name)==0) {
                $from_name="no_name";
            }
            //echo "From: " . $text_base64 . '<br />' . '<br />';

            $base64_num = strpos($email['header'], "base64") + 7;
            $text_base64 = substr($email['header'], $base64_num, strpos($email['header'], "=", $base64_num) - $base64_num + 1);


            //if ($format != "utf-8") echo iconv($format,"utf-8",base64_decode($text_base64)).'<br />';
            // else echo base64_decode($text_base64).'<br />';
            /*$base64_num=0;
            while (strpos($email['body'], "base64", $base64_num)!=false) {*/
            $base64_num = strpos($email['body'], "base64") + 7;
            $text_base64 = substr($email['body'], $base64_num, strpos($email['body'], "=", $base64_num) - $base64_num + 1);
            $text_base64=base64_decode($text_base64);
            if ($format != "utf-8") $text_base64=iconv($format,"utf-8",$text_base64);
            $subject=substr($text_base64,0,80)."...";
            
            if($i%2==0) {
                $bodies[$i/2]=$email['body'];
                /*$mail_name="<dt><a href = \"../pageLoad/message.php\" id=$i value=$fp>$from_name</a></dt>";
                $subj_name="<dd><a href = \"../pageLoad/message.php\" id=$i value=$fp>$subject</a></dd>";*/
                $mail_name="<dt>".$from_name."</dt>";
                $subj_name="<dd>".$subject."</dd>";

                echo '<form method=\"get\" action="../pageLoad/message.php?' . htmlspecialchars('SID') . '"><label>'.
                    $mail_name.$subj_name."<input type='submit' name='mes' value=".($i/2)."></label></form>";
            }
            //echo base64_decode($text_base64) . '<br />';
            //}
            //echo $text_base64.'<br />';

            //echo $data.'<br />';

            /*$comm="TOP 2 1\r\n";
            fputs($fp,$comm);
            echo fgets($fp,1024).'<br />';*/
        }
        $_SESSION['body']=$bodies;
        
    }

    function decode_mime_string($subject) {
        $string = $subject;
        if(($pos = strpos($string,"=?")) === false) return $string;
        $newresult = '';
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
            if($enctype == "q") /*$mystring = quoted_printable_decode(ereg_replace("_"," ",$mystring))*/;
            else if ($enctype == "b") $mystring = base64_decode($mystring);
            $newresult .= $mystring;
            $pos = strpos($string,"=?");
        }

        $result = $newresult.$string;
        /*if(preg_match("koi8", $subject)) $result = convert_cyr_string($result, "k", "w");
        if(preg_match("KOI8", $subject)) $result = convert_cyr_string($result, "k", "w");*/
        return $result;
    }

// перекодировщик тела письма.
// Само письмо может быть закодировано и данная функция приводит тело письма в нормальный вид.
// Так же и вложенные файлы будут перекодироваться этой функцией.
    function compile_body($body,$enctype/*,$ctype*/) {
        $enctype = explode(" ",$enctype); $enctype = $enctype[0];
        if(strtolower($enctype) == "base64")
            $body = base64_decode($body);
        elseif(strtolower($enctype) == "quoted-printable")
            $body = quoted_printable_decode($body);
        //if(preg_match("koi8", $ctype)) $body = convert_cyr_string($body, "k", "w");
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
    function get_data($pop_conn)
    {
        $data="";
        while (!feof($pop_conn)) {
            $buffer = chop(fgets($pop_conn,1024));
            $data .= "$buffer\r\n";
            if(trim($buffer) == ".") break;
        }
        return $data;
    }
}
