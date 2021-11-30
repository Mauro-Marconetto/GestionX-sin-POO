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

    //Consultar la base de contactoCliente

    $consulta = "SELECT * FROM contactocliente";
    $resultadoContacto = mysqli_query($db,$consulta);

    //Consultar la base de Cliente final

    $consulta = "SELECT * FROM clientefinal";
    $resultadoCliente = mysqli_query($db,$consulta);
    
    //Consultar la base de Proyectos

    $consulta = "SELECT * FROM proyecto";
    $resultadoProyecto = mysqli_query($db,$consulta);

    //Consultar la base de Areas

    $consulta = "SELECT * FROM areascomsys";
    $resultadoAreas = mysqli_query($db,$consulta);
      
    //Tomamos el resultado de la pagina de crear contacto

    $GET_Contacto = $_GET['contacto'] ?? NULL;
        
    $GET_Id = $_GET['id'];
      
    //Arreglo con mensajes de errores 

    $errores = []; 

    //Creo estas variables vacias para poder asignarlas como value y que guarden los valores por si da error

    if($GET_Contacto == 'new'){

        $consulta = "SELECT * FROM contactocliente ORDER BY id DESC LIMIT 1";
        $resultado = mysqli_query($db,$consulta);
        $contacto = mysqli_fetch_assoc($resultado);
        $contactoClienteId = $contacto['id'];
        
    }else{
        $contactoClienteId = '';
    }
    
    $nombre = '';
    $areaNombre = '';
    $clienteFinal = '';
    
    //Ejecutar el codigo despues de que el usuario envia el formulario

    if($_SERVER['REQUEST_METHOD'] === 'POST'){


        $nombre = mysqli_real_escape_string($db, $_POST['nombre']);
        $contactoClienteId = $_POST['contactoClienteId'];
        $clienteFinal = $_POST['clienteFinal'];
        $areaNombre = $_POST['area'];
        $fechaCreacion = date('Y-m-d');
        $estado = 'abierto';

        //Corroboramos todos los campos completos

        if(!$nombre){
            $errores[] = "El nombre es obligatorio"; //agrego el mensaje al final del arreglo
        }
        if(!$contactoClienteId){
            $errores[] = "Debes seleccionar un contacto"; //agrego el mensaje al final del arreglo
        }   
        if(!$areaNombre){
            $errores[] = "Debes seleccionar un area"; //agrego el mensaje al final del arreglo
        }
        if(!$clienteFinal){
            $errores[] = "Debes seleccionar el cliente final"; //agrego el mensaje al final del arreglo
        }
        
        if(empty($errores)){ //si errores esta vacio

            /** SUBIDA DE ARCHIVOS */

            //Insertar en la base de datos

            $query = "INSERT INTO proyecto (nombre, contactoClienteId, clienteFinal, fechaCreacion, area, estado) 
                      VALUES ('$nombre', '$contactoClienteId', '$clienteFinal', '$fechaCreacion', '$areaNombre', '$estado')";

            $resultado = mysqli_query($db, $query);



            if($resultado){
                //Redireccionamos al Usuario
                $location = "Location: nuevo-deposito.php?id=$GET_Id&proyecto=new";
                header($location);
            }

        }
    }
    // Incluye Header
    
    incluirTemplate('../../','header');
?>

<main class="contenedor seccion">

    <h1>Nuevo Proyecto</h1>

    <?php if($resultadoContacto == 'new') : ?>
        <p class="alerta exito">Contacto generado correctamente</p>
    <?php endif ?>

    <a href="./nuevo-deposito.php?id=<?php echo $GET_Id; ?>" class="boton boton-amarillo">Volver</a>

    <?php foreach ($errores as $error): ?>
        <div class="alerta error">
        <?php echo $error; ?>
        </div>
    <?php endforeach; ?>

    <form class="formulario-proyecto" method="POST" enctype="multipart/form-data"> <!--el enctype lo uso para poder sacar info de como almacenar las imagenes-->
        <fieldset>                                                                                          <!--el action es para definir a que archivo se van a enviar los datos para ser procesados-->
                                                                                                                
            <legend>Datos del proyecto</legend>

            <label for="proyecto">Nombre del proyecto</label>
            <input type="text" name="nombre" id="proyecto" value="<?php echo $nombre;?>">

            <label>Area</label>
            <select name="area">
                <option value="">--Seleccione--</option>
                <?php while ($area = mysqli_fetch_assoc($resultadoAreas) ) : ?> <!-- si uso : tengo que usar el end while-->
                    <option  <?php echo $areaNombre == $area['area'] ? 'selected' : ''; ?> value="<?php echo $area['area']; ?>"> <?php echo $area['area']; ?> </option> 
                <?php endwhile; ?>                      
            </select>

            <label>Contacto</label>
                    
            <div class="select-boton">

                <select name="contactoClienteId">
                    <option value="">--Seleccione--</option>

                    <?php while ($contacto = mysqli_fetch_assoc($resultadoContacto) ) : ?> 
                        <option <?php echo $contactoClienteId == $contacto['id'] ? 'selected' : ''; ?> value="<?php echo $contacto['id']; ?>">
                        <?php echo $contacto['empresa'] . "-" . $contacto['nombre'] . " " . $contacto['apellido']; ?> 
                        </option> 
                    <?php endwhile; ?>       

                </select>

                <a href="./nuevo-contacto.php?id=<?php echo $_GET['id']; ?>" class="boton boton-amarillo">Crear Contacto</a>

            </div>

            <label>Empresa</label>
            <select name="clienteFinal">
                <option value="">--Seleccione--</option>

                <?php while ($cliente = mysqli_fetch_assoc($resultadoCliente) ) : ?> 
                    <option  <?php echo $clienteFinal === $cliente['nombre'] ? 'selected' : ''; ?> value="<?php echo $cliente['nombre']; ?>"> 
                    <?php echo $cliente['nombre']; ?> 
                    </option> 
                <?php endwhile; ?>   
                        
            </select>
            
        </fieldset>

        <input type="submit" value="Crear" class="boton boton-verde crear">
    </form>
        
</main>
</body>
</html>