<?php 

    require 'includes/funciones.php'; // require es como un include, pero si no logra cargarlo va a marcar un error

    $auth=autenticacion();

    if(!$auth){
         header('Location: login.php');
    }elseif(!isset($_SESSION)){
        session_start();
    }else{
        $nivelAcceso = $_SESSION['nivelAcceso'];
    }

    incluirTemplate('','header');
?>

    <main class="contenedor PRUEBA">

        <?php if($nivelAcceso==1) : ?>
            <div class="grilla-principal">
                <div class="vinculos-principal">
                    <a href="admin/index.php">ADMINISTRADOR</a> 
					
                </div>
                <div class="vinculos-principal">
                    <a href="stock/index.php">STOCK</a>
                </div>
                
            </div>
        <?php endif;?>
        <?php if($nivelAcceso==2) : ?>
            <div class="grilla-principal">
                <div class="vinculos-principal">
                    <a href="stock/index.php">STOCK</a>
                </div>
                
            </div>
        <?php endif;?>

    </main>

    
</body>
</html>