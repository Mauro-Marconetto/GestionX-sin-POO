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

    //Consultar la base de RRHH

    $depositoId = $_GET['id'];

    $consulta = "SELECT * FROM depositoproyecto WHERE id = $depositoId";
    $resultadoDeposito = mysqli_query($db,$consulta); 
    $deposito = mysqli_fetch_assoc($resultadoDeposito);
    $proyectoId = $deposito['proyectoId'];
    // $ingresoId = $deposito['depositoIngresoId'];

    $consulta = "SELECT * FROM proyecto WHERE id = $proyectoId";
    $resultadoProyecto = mysqli_query($db,$consulta); 
    $proyecto = mysqli_fetch_assoc($resultadoProyecto);
    $proyectoNombre = $proyecto['nombre'];
    
    // $consulta = "SELECT * FROM depositoingreso WHERE id = $ingresoId";
    // $resultadoIngreso = mysqli_query($db,$consulta); 
    // $ingreso = mysqli_fetch_assoc($resultadoIngreso);
    // $depositoIngreso = $ingreso['pl'];

    // echo $proyectoId;
    // echo $ingresoId;
    // echo $depositoIngreso;

    // exit;

    $consulta = "SELECT * FROM rrhhcomsys";
    $resultadoRrhh = mysqli_query($db,$consulta);
    $consulta = "SELECT * FROM rrhhcomsys WHERE area = 'STOCK'";
    $resultadoStock = mysqli_query($db,$consulta);
    $consulta = "SELECT * FROM depositoegreso";
    $resultadoEgreso = mysqli_query($db,$consulta);
    
    $errores = []; 

    $bultos = '';
    $retira = '';
    $entrega = '';
    $fechaEntrega = '';
    $destino = '';
    $comentario = '';

    if($_SERVER['REQUEST_METHOD'] === 'POST'){

        $proyecto =$_GET['id'];
        $bultos = $_POST['bultos'];
        $retira =$_POST['retira'];
        $entrega = $_POST['entrega'];
        $fechaEntrega =date('Y-m-d');
        $destino = $_POST['destino'];
        $comentario =$_POST['comentario'];

        
        if(!$bultos){
            $errores[] = "Debes seleccionar la cantidad de bultos a retirar"; //agrego el mensaje al final del arreglo
        }
        if(!$retira){
            $errores[] = "Debes seleccionar quien retira"; //agrego el mensaje al final del arreglo
        }   
        if(!$entrega){
            $errores[] = "Debes seleccionar quien entrega"; //agrego el mensaje al final del arreglo
        }
        if(!$destino){
            $errores[] = "Debes especificar el destino"; //agrego el mensaje al final del arreglo
        }   
        
        // echo "<pre>";
        // var_dump($_POST);
        // echo "</pre>";
        // exit;
        if(empty($errores)){ 
            
            $query = "INSERT INTO depositoegreso (proyectoId, bultos, retira, entrega, fechaEntrega, destino, comentario) 
            VALUES ('$proyectoId', '$bultos', '$retira', '$entrega', '$fechaEntrega', '$destino', '$comentario') ";

            echo $query;

            $resultado = mysqli_query($db, $query);

            if($resultado){     
                $location = "Location: mail_IE.php?id=$depositoId&accion=nuevo_egreso";
                header($location);            
            }

        }
    }
    
    incluirTemplate('../../','header');
?>

<main class="contenedor seccion">
    <h1>Egreso de Materiales</h1>

    <a href="./deposito.php" class="boton boton-amarillo">Volver</a>

    <?php foreach ($errores as $error): ?>
        <div class="alerta error">
        <?php echo $error; ?>
        </div>
    <?php endforeach; ?>

    <form class="formulario-ingreso" method="POST" >
        <fieldset>
                                                                                                                
            <label>Proyecto: <?php echo $proyectoNombre; ?></label>

            <label>RETIRA:</label>
            <select name="retira">
                <option value="">--Seleccione--</option>
                <?php while ($rrhh = mysqli_fetch_assoc($resultadoRrhh) ) : ?>
                    <option  <?php echo $retira === $rrhh['iniciales'] ? 'selected' : ''; ?> value="<?php echo $rrhh['iniciales']; ?>"> <?php echo $rrhh['iniciales']; ?> </option> 
                <?php endwhile; ?>            
            </select>
            <label>ENTREGA:</label>
            <select name="entrega">
                <option value="">--Seleccione--</option>
                <?php while ($stock = mysqli_fetch_assoc($resultadoStock) ) : ?> <!-- si uso : tengo que usar el end while-->
                    <option  <?php echo $entrega === $stock['iniciales'] ? 'selected' : ''; ?> value="<?php echo $stock['iniciales']; ?>"> <?php echo $stock['iniciales']; ?> </option> 
                <?php endwhile; ?>                             
            </select>
            
            <label for="destino">Destino</label>
            <input type="text" name="destino" id="destino" value="<?php echo $destino;?>">
 
            <label for="bultos">Cantidad de bultos:</label>
            <input type="number" name="bultos" id="bultos" min=0 value="<?php echo $bultos;?>">

            <label for="comentario">Comentarios:</label>
            <textarea id="comentario" name="comentario" ><?php echo $comentario;?></textarea> 

        </fieldset>


        <input type="submit" value="Realizar Egreso" class="boton boton-verde crear">
    </form>
        
</main>
</body>
</html>