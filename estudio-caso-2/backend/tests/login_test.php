<?php
require('../login.php');

if(login("user1@gmail.com","c")){
    echo 'Login exitoso' . PHP_EOL;
}else{
    echo 'Login incorrecto' . PHP_EOL;
}


if(login("asdadad", "asdads")){
    echo 'Login exitoso' . PHP_EOL;
}else{
    echo 'Login incorrecto' . PHP_EOL;
}