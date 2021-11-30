<?php

    if(!isset($_SESSION)){ //si session no esta definida
        session_start();
    }

    $auth = $_SESSION['login'] ?? false;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/gestionx/build/css/app.css">
    <script src="https://kit.fontawesome.com/74e88e2369.js" crossorigin="anonymous"></script>
    <title>GestionX</title>
</head>
<body>
    <header class="header">
        <div class="contenedor barra-header">
            <a href="/gestionx/index.php" class="logo-header">
                <p>Gestion<span>X</span></p>
            </a>
            <?php if($auth): ?>
                <a href="/gestionx/cerrar-sesion.php">Cerrar Sesion</a>  
            <?php endif; ?>
        </div>
    </header>