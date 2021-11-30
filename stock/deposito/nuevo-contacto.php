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
    $resultadocontacto = mysqli_query($db,$consulta);
    //Consultar la base de Cliente
    $consulta = "SELECT * FROM cliente";
    $resultadoCliente = mysqli_query($db,$consulta);
      
    $resultadoId = $_GET['id'];
    
    $errores = []; 

    //Creo estas variables vacias para poder asignarlas como value y que guarden los valores por si da error

    $nombre = '';
    $apellido = '';
    $telefono = '';
    $empresa = '';
    
    //Ejecutar el codigo despues de que el usuario envia el formulario

    if($_SERVER['REQUEST_METHOD'] === 'POST'){

        $nombre =mysqli_real_escape_string($db, $_POST['nombre']);
        $apellido =mysqli_real_escape_string($db, $_POST['apellido']);
        $telefono =mysqli_real_escape_string($db, $_POST['telefono']);
        $empresa =mysqli_real_escape_string($db, $_POST['empresa']);

        //Corroboramos todos los campos completos

        if(!$nombre){
            $errores[] = "El nombre es obligatorio"; //agrego el mensaje al final del arreglo
        }   
        if(!$apellido){
            $errores[] = "El apellido es obligatorio"; //agrego el mensaje al final del arreglo
        }
        if(!$telefono){
            $errores[] = "Debes agregar un numero de telefono"; //agrego el mensaje al final del arreglo
        }   
        if(!$empresa){
            $errores[] = "Debes seleccionar la empresa para la que trabaja"; //agrego el mensaje al final del arreglo
        }
        
        if(empty($errores)){ //si errores esta vacio

            //Insertar en la base de datos

            $query = "INSERT INTO contactocliente (nombre, apellido, telefono, empresa) 
                    VALUES ('$nombre', '$apellido', '$telefono', '$empresa')";

            $resultado = mysqli_query($db, $query);

            if($resultado){

                $location = "Location: nuevo-proyecto.php?id=$resultadoId&accion=new";
                header($location);
            }
        }
    }

    // Incluye Header
    
    incluirTemplate('../../','header');
?>

<main class="contenedor seccion">
    <h1>Nuevo Contacto</h1>

    <a href="./nuevo-proyecto.php?id=<?php echo $resultadoId; ?>" class="boton boton-amarillo">Volver</a>

    <?php foreach ($errores as $error): ?>
        <div class="alerta error">
        <?php echo $error; ?>
        </div>
    <?php endforeach; ?>

    <form class="formulario-contacto" method="POST" enctype="multipart/form-data"> <!--el enctype lo uso para poder sacar info de como almacenar las imagenes-->
        <fieldset>                                                                                          <!--el action es para definir a que archivo se van a enviar los datos para ser procesados-->
                                                                                                                
            <legend>Datos del contacto</legend>

            <label for="proyecto">Nombre</label>
            <input type="text" name="nombre" id="nombre" value="<?php echo $nombre;?>">
            
            <label for="proyecto">Apellido</label>
            <input type="text" name="apellido" id="apellido" value="<?php echo $apellido;?>">

            <label for="proyecto">Telefono</label>
            <input type="text" name="telefono" id="telefono" value="<?php echo $telefono;?>">

            <label>Empresa</label>
            <select name="empresa">
                <option disabled>--Seleccione--</option>
                <?php while ($cliente = mysqli_fetch_assoc($resultadoCliente) ) : ?> <!-- si uso : tengo que usar el end while-->
                    <option  <?php echo $empresa === $cliente['nombre'] ? 'selected' : ''; ?> value="<?php echo $cliente['nombre']; ?>"> <?php echo $cliente['nombre']; ?> </option> 
                <?php endwhile; ?>            
            </select>
            
        </fieldset>

        <input type="submit" value="Crear" class="boton boton-verde crear">
    </form>
        
</main>
</body>
</html>