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

    //Consultar la base de Ingreso
    $consulta = "SELECT * FROM depositoingreso";
    $resultadoIngreso = mysqli_query($db,$consulta);
    //Consultar la base de proyecto
    $consulta = "SELECT * FROM proyecto";
    $resultadoProyecto = mysqli_query($db,$consulta);
    //Consultar la base de Contacto Cliente
    $consulta = "SELECT * FROM contactocliente";
    $resultadoContacto = mysqli_query($db,$consulta);
    //Consultar la base de Ubicacion
    $consulta = "SELECT * FROM depositoubicacion";
    $resultadoubicacion = mysqli_query($db,$consulta);

    $GET_id = $_GET['id'];
    $GET_Proyecto = $_GET['proyecto'] ?? null;
         
    $errores = []; 

    //Creo estas variables vacias para poder asignarlas como value y que guarden los valores por si da error

    if($GET_Proyecto == 'new'){

        $consulta = "SELECT * FROM proyecto ORDER BY id DESC LIMIT 1";
        $resultado = mysqli_query($db,$consulta);
        $proyecto = mysqli_fetch_assoc($resultado);
        $proyectoId = $proyecto['id'];
        
    }else{
        $proyectoId = '';
    }

    $condicion = '';
    $fechaUsoFuturo = '';
    $ubicacion = '';
    
    //Ejecutar el codigo despues de que el usuario envia el formulario

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $ingresoId = $_GET['id'];
        $condicion = $_POST['condicion'];
        $proyectoId = $_POST['proyectoId'];
        $ubicacion = $_POST['ubicacion'];
        $estadoIngreso = 'deposito';
        $estadoDeposito = 'abierto';
        $estadoUbicacion = 'ocupado';
        if($condicion == 'Devolver al cliente'){
            $fechaUsoFuturo = $_POST['fechaUsoFuturo1'];
        }elseif ( $condicion == 'Uso futuro'){
            $fechaUsoFuturo = $_POST['fechaUsoFuturo2'];  
        }else{
            $fechaUsoFuturo = '';
        }
        
        //Corroboramos todos los campos completos
        
        if(!$proyectoId){
            $errores[] = "Debes de seleccionar un proyecto"; //agrego el mensaje al final del arreglo
        }   
        if(!$condicion){
            $errores[] = "Debes especificar una condicion"; //agrego el mensaje al final del arreglo
        }
        if(!$ubicacion){
            $errores[] = "Debes especificar una ubicacion"; //agrego el mensaje al final del arreglo
        }
        if(!$fechaUsoFuturo){
            if ( $condicion != 'Scrap'){
                $errores[] = "Debes de seleccionar una proxima fecha de uso"; //agrego el mensaje al final del arreglo
            }
        }   

        if(empty($errores)){ //si errores esta vacio

        /** SUBIDA DE ARCHIVOS */

        //Insertar en la base de datos
        echo "<pre>";
        var_dump($_POST) ;
        echo "</pre>";

        if($fechaUsoFuturo == ''){
            $query = "INSERT INTO depositoproyecto (depositoIngresoId, proyectoId, condicion, estado, ubicacion) 
            VALUES ('$ingresoId', '$proyectoId', '$condicion', '$estadoDeposito', '$ubicacion')";
    
        }else{

            $query = "INSERT INTO depositoproyecto (depositoIngresoId, proyectoId, condicion, fechaUsoFuturo, estado, ubicacion) 
                    VALUES ('$ingresoId', '$proyectoId', '$condicion', '$fechaUsoFuturo', '$estadoDeposito', '$ubicacion')";
        }

        $resultado = mysqli_query($db, $query);

        $query = "UPDATE depositoingreso SET estado = '$estadoIngreso' WHERE id = $ingresoId";
        mysqli_query($db, $query);

        $query = "UPDATE depositoubicacion SET estado = '$estadoUbicacion' WHERE ubicacion = $ubicacion";
        mysqli_query($db, $query);

            if($resultado){
                header('Location: deposito.php?estado=1');
            }

        }
    }
    // Incluye Header
    
    incluirTemplate('../../','header');
?>

<main class="contenedor seccion">
    <h1>Nuevo Deposito</h1>

    <?php if($GET_Proyecto === 'new') : ?>
        <p class="alerta exito">Proyecto generado correctamente</p>
    <?php endif ?>

    <a href="./entransito.php" class="boton boton-amarillo">Volver</a>

    <?php foreach ($errores as $error): ?>
        <div class="alerta error">
        <?php echo $error; ?>
        </div>
    <?php endforeach; ?>

    <form class="formulario-deposito" method="POST"> 
        <fieldset>                             
                                                                                                                
            <legend>Datos del deposito</legend>

            <label>Datos del Proyecto</label>

            <div class="select-boton">
                <select name="proyectoId">
                    <option value="">--Seleccione--</option>

                    <?php while ($proyecto = mysqli_fetch_assoc($resultadoProyecto) ) : ?> 
                        <option <?php echo $proyectoId == $proyecto['id'] ? 'selected' : ''; ?> value="<?php echo $proyecto['id']; ?>">
                        <?php
                            //$proyecto = mysqli_fetch_assoc($resultadoProyecto); 
                            $id = $proyecto['contactoClienteId'];
                            $fecha = explode ("-", $proyecto['fechaCreacion']);
                            $contacto = mysqli_fetch_assoc(mysqli_query ($db,"SELECT * FROM contactocliente WHERE id=$id"));
                            echo $proyecto['nombre'] . " - " . $contacto['empresa'] . "/" . $proyecto['clienteFinal']  . " Año ". $fecha[0]; 
                        ?> 
                        </option> 
                    <?php endwhile; ?>       

                </select>

                <a href='./nuevo-proyecto.php?id=<?php echo $_GET['id']; ?>' class="boton boton-amarillo">Nuevo Proyecto</a>

            </div>

            <label>Condicion</label>
            <div class="deposito-condicion">

                <p>
                    <input type="radio" name="condicion" id="condicion1" value="Devolver al cliente" required <?php echo $condicion == "Devolver al cliente" ? 'checked' : ''; ?>>
                    <label for="condicion1">Devolver al Cliente</label>
                    <input type="date" name="fechaUsoFuturo1">
                </p>
                <p>
                    <input type="radio" name="condicion" id="condicion2" value="Uso futuro" required <?php echo $condicion == "Uso futuro" ? 'checked' : ''; ?>>
                    <label for="condicion2">Uso futuro</label>
                    <input type="date" name="fechaUsoFuturo2">
                </p>
                <p>
                    <input type="radio" name="condicion" id="condicion3" value="Scrap" required <?php echo $condicion == "Scrap" ? 'checked' : ''; ?>>
                    <label for="condicion3">Scrap</label>
                </p>
            </div>

            <label>Ubicacion</label>
            <select name="ubicacion">
                <option disabled>--Seleccione--</option>
                <?php while ($ubicacionId = mysqli_fetch_assoc($resultadoubicacion) ) : ?> <!-- si uso : tengo que usar el end while-->
                    <option  <?php echo $ubicacion === $ubicacionId['ubicacion'] ? 'selected' : ''; ?> value="<?php echo $ubicacionId['ubicacion']; ?>">
                     <?php echo $ubicacionId['ubicacion'] . " - " . $ubicacionId['estado']; ?> 
                    </option> 
                <?php endwhile; ?>            
            </select>
                        
        </fieldset>

        <input type="submit" value="Crear" class="boton boton-verde crear">

    </form>
        
</main>
</body>
</html>