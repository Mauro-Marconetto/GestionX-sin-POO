<?php

function conectarDB() : mysqli { //: mysqli significa que la funcion va a retornar un valor del tipo mysqli

    $addres = 'localhost';
    $user = 'root';
    $password = '';
    $base = 'gestionx';

    $db = mysqli_connect($addres,$user,$password,$base); 

    //if ($db){
    //    echo "Conexion Exitosa";
    //}else{
    //    echo "Conexion Fallida";
    //}

    if(!$db){
        echo "Error de Conexion";
        exit;
    }

    return $db;
}