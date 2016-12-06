<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Авторизация</title>
    <link rel="stylesheet" type="text/css" href="auth_style.css" />
</head>
<body>
<?php
include_once "POP3Auth.php";
?>

<div id="page">
    <div id="header">

    </div>

    <div id="authframe">

        <div id="column">
            <form method="get">
                <label><p><b>Авторизация</b></p>
                    <p><input type="text" name="login" placeholder="логин" size="20"></label>

                <select size="1" name="client" >
                    <option selected value="yandex.ru">@yandex.ru</option>
                    <option value="gmail.com">@gmail.com</option>
                    <option value="mail.ru">@mail.ru</option>
                </select></p>


                <p><input type="password" name="password" placeholder="пароль" size="35"></p>

                <p><input type="submit" name="submit" value="Войти"></p>
                <?php
                if(isset($_GET['submit']))
                {
                    $connect=new POP3Auth();
                    switch($connect->connect($_GET['login'],$_GET['client'] ,$_GET['password'] )) {
                        case "con_fail":
                            echo "<div style='font-size: small; color:red'>Ошибка соединения с сервером</div>";
                            break;
                        case "log_or_pass_fail":
                            echo "<div style='font-size: small; color:red'>Неверный логин или пароль</div>";
                            break;
                        case "success":
                    }
                }
                ?>

            </form>
        </div>

    </div>

    <div id="footer">

    </div>
</div>

</body>
</html>