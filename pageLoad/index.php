<?php
include "../auth/POP3Auth.php";
$connect=new POP3Auth();

if($connect->connect($_POST['login'],$_POST['client'] ,$_POST['password'], $fp ) == "success" || !empty($_SESSION)) {
    $_SESSION['log'] = $_POST['login'] . '@' . $_POST['client'];
    $_GET['connect']=$fp;
    ?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Mail</title>
    <link rel="stylesheet" type="text/css" href="../styles/style.css" />

    <script type="text/javascript" src="script.js"></script>
    
</head>

<body>

<div id="container">

    <div id="header"></div>

    <!-- <div id="navigation">Блок навигации </div> -->
    <?php
    /*<div id="menu">
        <table id = "table_menu">
            <tr><br><center><button id = "button1" onclick="">Входящие</button></center></br></tr>
            <tr><center><button id = "button2">Отправленные</button></center></tr>
            <tr><br><center><button id = "button3">Черновики</button></center><br></tr>
            <tr><center><button id = "button4">Архив</button></center></tr>
            <tr><br><center><button id = "button5">Спам</button></center></br></tr>
            <tr><center><button id = "button6">Корзина</button></center></tr>
        </table>

    </div>*/
    require "../headers/menu.html";
    ?>

    <div id="content">
        <div id="wrapper">
            <div id="scroll">
                <div id="scrollcontent">
                    <dl class="holiday">
                        <form method="post" >
                            <?php
                            //echo $fp.'<br />';
                            $connect->printHeaders($fp);
                            ?>
                        </form>

                    </dl>
                </div>
                <div id="scrollbar">
                    <div id="scroller" class="scroller"></div>
                </div>
            </div>
        </div>

    </div>

    <div id="clear">

    </div>

    <div id="footer">

    </div>
</div>

</body>

</html>
    <?php
    //require "../messeges/index.html";
    
}
else {
    require "../headers/auth_header.html";
    require "../auth/auth.php";
}
