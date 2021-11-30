<?php 

    require '../includes/funciones.php'; // require es como un include, pero si no logra cargarlo va a marcar un error

    $auth=autenticacion();

    if(!$auth){
         header('Location: ../login.php');
    }elseif(!isset($_SESSION)){
        session_start();
    }else{
        $nivelAcceso = $_SESSION['nivelAcceso'];
    }

    if($nivelAcceso!=1){
        header('Location: ../login.php');
    }

    $resultado = $_GET['resultado'] ?? null;

    incluirTemplate('../','header');
?>

    <main class="grilla-admin">
        <?php if($resultado == 1) : ?>
            <p class="alerta exito">Usuario generado correctamente</p>
        <?php endif ?>
        <?php if($resultado == 2) : ?>
            <p class="alerta exito">Usuario modificado correctamente</p>
        <?php endif ?>
        <div class="vinculos-grilla">
            <a href="crear-usuario.php">Crear Usuario</a>
        </div>
        <div class="vinculos-grilla">
            <a href="modificar-usuario.php">Modificar Usuario</a>
        </div>

    </main>

</body>

</html>