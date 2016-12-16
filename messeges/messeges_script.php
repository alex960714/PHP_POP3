<?php

function otkr(){
    for ($i = 1; $i <= 10; $i++)
    {
        echo "<dt><a href = \"../auth/auth.html\">4.04.15 $i</a></dt>
        <dd><a href = \"../auth/auth.html\">День веб-мастера</a></dd>";
    }
}

switch ($_GET['func']) {
case 'otkr':
    otkr();
break;
default:;
}
