<?php


function incluirTemplate ($directorio , $nombre) {
    include "${directorio}includes/templates/${nombre}.php";
    // echo "${directorio}includes/templates/${nombre}.php";
}


function autenticacion() : bool { //va a retornar un bool
    session_start();
    
    $auth = $_SESSION['login'] ?? false;

    if($auth){
        return true;
    }
    return false;
}



