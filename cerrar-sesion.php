<?php
    session_start();

    $_SESSION = [];

    header('Location: /gestionx/login.php');
    var_dump($_SESSION);