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

    $db = conectarDB();

    $resultado = $_GET['estado'] ?? null; // si get resultado no existe, le asigna null, para que no de error

    $fechaActual = date('Y-m-d');
    $fechaAvencer = date("Y-m-d",strtotime($fechaActual."+ 1 month"));

    if($resultado == 4){
        $consulta = "SELECT * FROM depositoproyecto WHERE ubicacion != '' &&  fechaUsoFuturo < '$fechaActual'";
        $resultadoProyecto = mysqli_query($db,$consulta);

    }elseif ($resultado == 5){
        $consulta = "SELECT * FROM depositoproyecto WHERE ubicacion != '' && fechaUsoFuturo BETWEEN '$fechaActual' AND '$fechaAvencer' ";
        $resultadoProyecto = mysqli_query($db,$consulta);
    }else{
        
        $consulta = "SELECT * FROM depositoproyecto WHERE ubicacion != '' ORDER BY ubicacion ";
        $resultadoProyecto = mysqli_query($db,$consulta);  
    }


    $todos = 0;

    $query = "SELECT * FROM depositoproyecto WHERE ubicacion != '' ORDER BY ubicacion ";
    $resultadoTotal = mysqli_query($db,$query);
    while ($deposito = mysqli_fetch_assoc($resultadoTotal)){
        $todos++;
    }

    $vencido = 0;

    $queryVencido = "SELECT * FROM depositoproyecto WHERE ubicacion != '' &&  fechaUsoFuturo < '$fechaActual'";
    $resultadoVencido = mysqli_query($db,$queryVencido);
    while ($depositoVencido = mysqli_fetch_assoc($resultadoVencido)){
        $vencido++;
    }

    $aVencer = 0;

    $consulta = "SELECT * FROM depositoproyecto WHERE ubicacion != '' && fechaUsoFuturo BETWEEN '$fechaActual' AND '$fechaAvencer' ";
    $resultadoAvencer = mysqli_query($db,$consulta);
    while ($depositoAvencer = mysqli_fetch_assoc($resultadoAvencer)){
        $aVencer++;
    }
        
    // Muestra mensaje condicional
   

    // $ubicacion = '';
    // $proyecto = '';
    // $area = '';
    // $condicion = '';
    // $fecha = '';
    // $bultos = 0;

    //Libero estante

    if($_SERVER['REQUEST_METHOD'] === 'POST'){

        $ubicacion = $_POST['ubicacion'];

        $query =  "UPDATE depositoubicacion SET estado = 'libre' WHERE ubicacion = $ubicacion";
        $resultado = mysqli_query($db,$query); 
        $query =  "UPDATE depositoproyecto SET ubicacion = '' WHERE ubicacion = $ubicacion";
        $resultado = mysqli_query($db,$query); 
            
        if($resultado){
            header('Location: deposito.php');
        }   
    }
        
    // Incluye Header
    
    incluirTemplate('../../','header');
?>

    <main class="contenedor">
        <?php if($resultado == 1) : ?>
            <p class="alerta exito">Deposito agregado correctamente</p>
        <?php endif ?>
        <?php if($resultado == 2) : ?>
            <p class="alerta exito">Fecha actualizada correctamente</p>
        <?php endif ?>
        <?php if($resultado == 3) : ?>
            <p class="alerta exito">Egreso realizado correctamente</p>
        <?php endif ?>

        <h1>Materiales en Desposito</h1>
        <div class="grilla-deposito">
            <a href="deposito.php?estado=6" class="grilla-deposito-bloque">
                <h3>Todos</h3>
                <p><?php echo $todos;?></p>
            </a>
            <a href="deposito.php?estado=4" class="grilla-deposito-bloque">
                <h3>Vencidos</h3>
                <p><?php echo $vencido;?></p>
            </a>
            <a href="deposito.php?estado=5" class="grilla-deposito-bloque">
                <h3>A Vencer 30 dias</h3>
                <p><?php echo $aVencer;?></p>
            </a>
        </div>
        <a href="entransito.php" class="boton boton-amarillo">Materiales en Transito</a>
        <a href="historial-proyectos.php" class="boton boton-amarillo">Historial de Proyectos</a>
        <table class="listado-tabla">
            <thead>
                <th>Estante</th>
                <th>Proyecto</th>
                <th>Area</th>
                <th>Condicion</th>
                <th>Proximo Uso</th>
                <th>Bultos</th>
                <th>Acciones</th>
            </thead>
                <tbody class="item-tabla">
                    <?php while ($depositoProyecto = mysqli_fetch_assoc($resultadoProyecto)) : ?>
                        <tr>
                            <td> <?php echo $depositoProyecto['ubicacion']; ?> </td>
                            <td> 
                                <a href="historico.php?id=<?php echo $id =$depositoProyecto['proyectoId']; ?>">
                                <?php 
                                    $id =$depositoProyecto['proyectoId'];
                                    $consulta = "SELECT * FROM proyecto WHERE id = $id";
                                    $proyecto = mysqli_fetch_assoc(mysqli_query($db,$consulta));
                                    echo $proyecto['nombre']; 
                                ?> 
                                </a>
                            </td>
                            <td> 
                                <?php 
                                    $id =$depositoProyecto['proyectoId'];
                                    $consulta = "SELECT * FROM proyecto WHERE id = $id";
                                    $proyecto = mysqli_fetch_assoc(mysqli_query($db,$consulta));
                                    echo $proyecto['area']; 
                                ?> 
                            </td>
                            <td> 
                                <?php 
                                    echo $depositoProyecto['condicion'];
                                ?> 
                            </td>
                            <td> 
                                <?php 
                                    echo $depositoProyecto['fechaUsoFuturo'];
                                ?> 
                            </td>
                            <td> 
                                <?php 
                                    $id =$depositoProyecto['depositoIngresoId'];
                                    $consulta = "SELECT * FROM depositoingreso WHERE id = $id";
                                    $ingreso = mysqli_fetch_assoc(mysqli_query($db,$consulta));
                                    echo $ingreso['bultos']; 

                                    
                                ?> 
                            </td>
                            <td>
                                <a href="actualizar-fecha.php?id=<?php echo $depositoProyecto['id']; ?>" class="boton boton-amarillo">Cambiar Fecha</a>
                                <a href="nuevo-egreso.php?id=<?php echo $depositoProyecto['id']; ?>" class="boton boton-amarillo">Egreso</a>
                                <form method="POST">
                                    <input type="hidden" name="ubicacion" value="<?php echo $depositoProyecto['ubicacion']; ?> ">   
                                    <input type="submit" class="boton boton-rojo" value="Liberar Estante">
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>
    </body>
</html>