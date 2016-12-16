

<?php

include('pop3.php');

// коннект к базе
mysql_connect('localhost', 'root', '') or die('Connect to mysql server failed');
mysql_select_db('mbox') or die('DB selection failed');

try {

    // читаем почту
    $pop = new pop3('pop.mail.ru', 110, 'xxx', '******');

    // получаем количество писем
    list($num) = $pop->count();

    // проходимся по всем письмам
    for ( $i = 1; $i <= $num; $i++ ) {

        list($hdr, $body) = $pop->retr($i); // письмо номер $i

        // mail.ru кодирует всё в base64 и кодировку koi8-r
        // обработка тела сообщения
        $body = base64_decode($body);
        $body = iconv('KOI8-R', 'WINDOWS-1251', $body);

        // поиск темы письма
        if ( preg_match("!Subject: ([^\n]+)!", $hdr, $m) ) {
            list(, $charset, , $subj) = explode('?', $m[1]);
            $subj = base64_decode($subj);
            $subj = iconv('KOI8-R', 'WINDOWS-1251', $subj);
        }

        // вставляем в базу
        $sql = 'INSERT INTO `mail` (`subj`, `body`) VALUES (.$subj., .$body.)';
    mysql_query($sql); 
    $pop->dele($i); // и удаляем письмо
  } 

} catch (Exception $e) { 
  print $e->getMessage(); 
}