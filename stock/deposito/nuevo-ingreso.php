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

    $consulta = "SELECT * FROM rrhhcomsys ORDER BY iniciales";
    $resultadoRrhh1 = mysqli_query($db,$consulta);
    $consulta = "SELECT * FROM rrhhcomsys ORDER BY iniciales";
    $resultadoRrhh2 = mysqli_query($db,$consulta);
    $consulta = "SELECT * FROM rrhhcomsys WHERE cargo LIKE 'p%' ORDER BY iniciales";
    $resultadoPl = mysqli_query($db,$consulta); 
    //Consultar la base de Areas
    $consulta = "SELECT * FROM areascomsys ORDER BY area";
    $resultadoAreas = mysqli_query($db,$consulta);
    //Consultar la base de Cliente
    $consulta = "SELECT * FROM cliente";
    $resultadoCliente = mysqli_query($db,$consulta);
    //Consultar la base de Cliente Final
    $consulta = "SELECT * FROM clientefinal";
    $resultadoClienteFinal = mysqli_query($db,$consulta);
    //Consultar la base de Ingreso al deposito
    $consulta = "SELECT * FROM depositoingreso";
    $resultadoIngreso = mysqli_query($db,$consulta);
    
    $errores = []; 

    $rrhh1 = '';
    $rrhh2 = '';
    $pl = '';
    $area = '';
    $cliente = '';
    $clienteFinal = '';
    $proyecto = '';
    $origen = '';
    $bultos = '';
    $comentario = '';

    //Ejecutar el codigo despues de que el usuario envia el formulario

    if($_SERVER['REQUEST_METHOD'] === 'POST'){

        $rrhh1 =$_POST['rrhh1'];
        $rrhh2 =$_POST['rrhh2'];
        $pl = $_POST['pl'];
        $area = $_POST['area'];
        $cliente = $_POST['cliente'];
        $clienteFinal = $_POST['clienteFinal'];
        $proyecto =mysqli_real_escape_string($db, $_POST['proyecto']);
        $origen =mysqli_real_escape_string($db, $_POST['origen']);
        $bultos =mysqli_real_escape_string($db, $_POST['bultos']);
        $comentario =mysqli_real_escape_string($db, $_POST['comentario']);
        $fechaIngreso =date('Y-m-d');
        $estado = 'transitorio';

        //Corroboramos todos los campos completos

        if(!$rrhh1){
            $errores[] = "Debes seleccionar al menos un tecnico"; //agrego el mensaje al final del arreglo
        }   
        if(!$area){
            $errores[] = "Debes seleccionar el area"; //agrego el mensaje al final del arreglo
        }
        if(!$cliente){
            $errores[] = "Debes seleccionar el cliente"; //agrego el mensaje al final del arreglo
        }   
        if(!$clienteFinal){
            $errores[] = "Debes seleccionar el cliente final"; //agrego el mensaje al final del arreglo
        }
        if(!$proyecto){
            $errores[] = "Debes seleccionar el proyecto"; //agrego el mensaje al final del arreglo
        }
        if(!$origen){
            $errores[] = "Debes especificar el origen"; //agrego el mensaje al final del arreglo
        }
        if(!$bultos){
            $errores[] = "Debes seleccionar la cantidad de bultos"; //agrego el mensaje al final del arreglo
        }

        if(empty($errores)){ //si errores esta vacio

            /** SUBIDA DE ARCHIVOS */

            //Insertar en la base de datos

            $query = "INSERT INTO depositoingreso (rrhh1, rrhh2, pl, area, cliente, clienteFinal, fechaIngreso, proyecto, origen, bultos, comentario, estado) 
            VALUES ('$rrhh1', '$rrhh2', '$pl', '$area', '$cliente', '$clienteFinal', '$fechaIngreso', '$proyecto', '$origen', '$bultos','$comentario', '$estado' )";

            $resultado = mysqli_query($db, $query);

            if($resultado){

                $consulta = "SELECT * FROM depositoingreso ORDER BY id DESC";
                $resultadoIngreso = mysqli_query($db,$consulta);
                $ingreso = mysqli_fetch_assoc($resultadoIngreso);
                $id = $ingreso['id'];
                $location = "Location: mail_IE.php?id=$id&accion=nuevo_ingreso";
                header($location);              
                
            }

        }
    }
    // Incluye Header
    
    incluirTemplate('../../','header');
?>

<main class="contenedor seccion">
    <h1>Nuevo Ingreso</h1>

    <a href="./entransito.php" class="boton boton-amarillo">Volver</a>

    <?php foreach ($errores as $error): ?>
        <div class="alerta error">
        <?php echo $error; ?>
        </div>
    <?php endforeach; ?>

    <form class="formulario-ingreso" method="POST" action="nuevo-ingreso.php" enctype="multipart/form-data"> <!--el enctype lo uso para poder sacar info de como almacenar las imagenes-->
        <fieldset>                                                                                          <!--el action es para definir a que archivo se van a enviar los datos para ser procesados-->
                                                                                                                
            <legend>Tecnicos que ingresan material</legend>

            <label>RRHH</label>
            <select name="rrhh1">
                <option value="">--Seleccione--</option>
                <?php while ($rrhh = mysqli_fetch_assoc($resultadoRrhh1) ) : ?> <!-- si uso : tengo que usar el end while-->
                    <option  <?php echo $rrhh1 === $rrhh['iniciales'] ? 'selected' : ''; ?> value="<?php echo $rrhh['iniciales']; ?>"> <?php echo $rrhh['iniciales']; ?> </option> 
                <?php endwhile; ?>            
            </select>
            <select name="rrhh2">
                <option value="">--Seleccione--</option>
                <?php while ($rrhh = mysqli_fetch_assoc($resultadoRrhh2) ) : ?> <!-- si uso : tengo que usar el end while-->
                    <option  <?php echo $rrhh2 === $rrhh['iniciales'] ? 'selected' : ''; ?> value="<?php echo $rrhh['iniciales']; ?>"> <?php echo $rrhh['iniciales']; ?> </option> 
                <?php endwhile; ?>                             
            </select>
            <label>Coordinador</label>
            <select name="pl">
                <option value="">--Seleccione--</option>
                <?php while ($coordinador = mysqli_fetch_assoc($resultadoPl) ) : ?> <!-- si uso : tengo que usar el end while-->
                    <option  <?php echo $pl === $coordinador['iniciales'] ? 'selected' : ''; ?> value="<?php echo $coordinador['iniciales']; ?>"> <?php echo $coordinador['iniciales']; ?> </option> 
                <?php endwhile; ?>                             
            </select>

            <label>Area</label>
            <select name="area">
                <option value="">--Seleccione--</option>
                <?php while ($areas = mysqli_fetch_assoc($resultadoAreas) ) : ?> <!-- si uso : tengo que usar el end while-->
                    <option  <?php echo $area === $areas['area'] ? 'selected' : ''; ?> value="<?php echo $areas['area']; ?>"> <?php echo $areas['area']; ?> </option> 
                <?php endwhile; ?>                      
            </select>
        </fieldset>  
        <fieldset>  
            <legend>Detalles del proyecto</legend>

            <label>Cliente</label>

                <select name="cliente">
                    <option value="">--Seleccione--</option>
                    <?php while ($cliente = mysqli_fetch_assoc($resultadoCliente) ) : ?> <!-- si uso : tengo que usar el end while-->
                        <option  <?php echo $cliente === $cliente['nombre'] ? 'selected' : ''; ?> value="<?php echo $cliente['nombre']; ?>"> <?php echo $cliente['nombre']; ?> </option> 
                    <?php endwhile; ?>                        
                </select>

                <label>Cliente Final</label>
                <select name="clienteFinal">
                    <option value="">--Seleccione--</option>
                    <?php while ($clienteFinal = mysqli_fetch_assoc($resultadoClienteFinal) ) : ?> <!-- si uso : tengo que usar el end while-->
                        <option  <?php echo $clienteFinal === $clienteFinal['nombre'] ? 'selected' : ''; ?> value="<?php echo $clienteFinal['nombre']; ?>"> <?php echo $clienteFinal['nombre']; ?> </option> 
                    <?php endwhile; ?>                       
                </select>

                <label for="proyecto">Proyecto</label>
                <input type="text" name="proyecto" id="proyecto" value="<?php echo $proyecto;?>">

                <label for="origen">Origen</label>
                <input type="text" name="origen" id="origen" value="<?php echo $origen;?>">

                
                <label for="bultos">Cantidad de bultos:</label>
                <input type="number" name="bultos" id="bultos" min=0 value="<?php echo $bultos;?>">

                <label for="comentario">Comentarios:</label>
                <textarea id="comentario" name="comentario" ><?php echo $comentario;?></textarea> 

            </fieldset>


            <input type="submit" value="Crear" class="boton boton-verde crear">
        </form>
        
    </main>
    </body>
</html>