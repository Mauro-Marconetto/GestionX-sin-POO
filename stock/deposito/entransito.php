<?php
    require '../../includes/config/database.php';
    require '../../includes/funciones.php'; 
    
    $auth=autenticacion();

    if(!$auth){
        header('Location: /gestionx/login.php');
    }elseif(!isset($_SESSION)){
        session_start();
    }else{
        $nivelAcceso = $_SESSION['nivelAcceso'];
    }
    // Base de Datos

    $db = conectarDB();

    //Consultar la base de Ingreso al deposito

    $consulta = "SELECT * FROM depositoingreso WHERE estado = 'transitorio'";
    $resultadoIngreso = mysqli_query($db,$consulta);
    
    // Muestra mensaje condicional
    $resultado = $_GET['resultado'] ?? null; // si get resultado no existe, le asigna null, para que no de error

    //Elimino el deposito

    if($_SERVER['REQUEST_METHOD'] === 'POST'){

        $id = $_POST['id'];
        $id = filter_var($id, FILTER_VALIDATE_INT); //convierto el string en un int
  
        if($id){
      
            //Elimino la propiedad
            $query = "UPDATE depositoingreso SET estado='cerrado' WHERE id = ${id} ";
            $resultado = mysqli_query($db,$query);
            header ('Location: entransito.php');

        }
    }    
    
    // Incluye Header
    
    incluirTemplate('../../','header');
?>

    <main class="contenedor">
        <?php if($resultado == 1) : ?>
            <p class="alerta exito">Ingreso generado correctamente</p>
        <?php endif ?>

        <h1>Materiales en transito</h1>
        <a href="nuevo-ingreso.php" class="boton boton-amarillo">Nuevo ingreso</a>
        <a href="deposito.php" class="boton boton-amarillo">Ver Deposito</a>
        <table class="listado-tabla">
            <thead>
                <th>Area</th>
                <th>Cliente</th>
                <th>Cliente Final</th>
                <th>Proyecto</th>
                <th>Coordinador</th>
                <th>Comentarios</th>
                <th>Acciones</th>
            </thead>
            <tbody class="item-tabla">
                <?php while ($depositoIngreso = mysqli_fetch_assoc($resultadoIngreso)) : ?>
                <tr>
                    <td> <?php echo $depositoIngreso['area']; ?> </td>
                    <td> <?php echo $depositoIngreso['cliente']; ?> </td>
                    <td> <?php echo $depositoIngreso['clienteFinal']; ?> </td>
                    <td> <?php echo $depositoIngreso['proyecto']; ?> </td>
                    <td> <?php echo $depositoIngreso['pl']; ?> </td>
                    <td> <?php echo $depositoIngreso['comentario']; ?> </td>
                    <td>
                        <a href="nuevo-deposito.php?id=<?php echo $depositoIngreso['id']; ?>" class="boton boton-amarillo">Pasar a deposito</a>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?php echo $depositoIngreso['id']; ?> ">   
                            <input type="submit" class="boton boton-rojo" value="Eliminar">
                        </form>
                    </td>
                <?php endwhile; ?>
                </tr>
            </tbody>
        </table>
    </main>
</body>
</html>