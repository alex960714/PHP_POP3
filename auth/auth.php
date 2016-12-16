<!DOCTYPE html>
<html lang="en">
<?php
require "../headers/auth_header.html";
?>

<body>
<div id="page">
    <div id="header">

    </div>

    <div id="authframe">

        <div id="column">
            <form action="../pageLoad/index.php" method="post">
                <label><p><b>Авторизация</b></p>
                    <p><input type="text" name="login" placeholder="логин" size="20" required></label>

                <select size="1" name="client" >
                    <option selected value="yandex.ru">@yandex.ru</option>
                    <option value="gmail.com">@gmail.com</option>
                    <option value="mail.ru">@mail.ru</option>
                    <option selected value="rambler.ru">@rambler.ru</option>
                </select></p>


                <p><input type="password" name="password" placeholder="пароль" size="35" required></p>
                <?php
                /*print_r($_POST);
                if(!empty($_POST))
                    echo "Post is not empty";
                else
                    echo "Post is empty";*/
                if(isset($_POST['submit'])) {
                    //$connect=new POP3Auth();
                    /*switch($connect->connect($_POST['login'],$_POST['client'] ,$_POST['password'] )) {
                        case "con_fail":
                            echo "<div style='font-size: small; float: left; color:red'>Ошибка соединения с сервером</div>";
                            break;
                        case "log_or_pass_fail":*/
                            echo "<div style='font-size: small; float: left; color:red'>Неверный логин или пароль</div>";
                            /*break;
                        case "success":
                            echo "success";

                    }*/
                }
                ?>

                <p><input type="submit" name="submit" value="Войти"></p>


            </form>
        </div>

    </div>

    <div id="footer">

    </div>
</div>

</body>
</html>