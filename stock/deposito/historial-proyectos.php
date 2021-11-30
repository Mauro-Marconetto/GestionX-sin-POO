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
    $consulta = "SELECT * FROM proyecto ORDER BY estado ";
    $resultadoProyecto = mysqli_query($db,$consulta);    
      

    
    incluirTemplate('../../','header');
?>

    <main class="contenedor">

        <h1>Historial del Proyectos</h1>
        <a href="deposito.php" class="boton boton-amarillo">Volver</a>
        <table class="listado-tabla">
            <thead>
                <th>Nombre</th>
                <th>Area</th>
                <th>Fecha de Creacion</th>
                <th>Estado</th>
            </thead>
                <tbody class="item-tabla">
                    <?php while ($proyecto = mysqli_fetch_assoc($resultadoProyecto)) : ?>
                        <tr>
                            <td> 
                                <a href="historico.php?id=<?php echo $id = $proyecto['id']; ?>">
                                    <?php echo $proyecto['nombre']; ?> 
                                </a>
                            </td>
                            <td> 
                                <?php echo $proyecto['area'];?> 
                            </td>
                            <td> 
                                <?php echo $proyecto['fechaCreacion'];?> 
                            </td>
                            <td> 
                                <?php echo $proyecto['estado'];?> 
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>
    </body>
</html>