<?php
include "../auth/POP3Auth.php";
?>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Mail</title>
        <link rel="stylesheet" type="text/css" href="../styles/style.css" />

        <script type="text/javascript" src="script.js"></script>
        <?php //include_once 'messeges_script.php' ?>
    </head>

    <body>

    <div id="container">

        <div id="header"></div>

        <!-- <div id="navigation">Блок навигации </div> -->
        <?php
        require "../headers/menu.html";
        ?>

        <div id="content">
            <div id="wrapper">
                <div id="scroll">
                    <div id="scrollcontent">
                        <?php
                        session_start();
                        print_r($_SESSION).'<br />';
                        //print_r($_GET).'<br />';
                        $mesNum=$_GET['mes'];
                        $body=$_SESSION['body'][$mesNum];
                        echo $body.'<br />';
                        //$fp=substr($_GET['mes'],strpos($mesNum," ")+1);
                        /*if(!empty($_SESSION)) {
                            $fp = &$_SESSION['socket'];
                            echo $fp.'<br />';
                        }*/
                        /*echo $mesNum.'<br />';
                        echo $fp.'<br />';*/

                        /*$connect=new POP3Auth();
                        $connect->sessionExit($fp);*/
                        ?>
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

