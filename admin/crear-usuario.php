<?php
	//Prueba
    require '../includes/funciones.php';
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

    //Importar la conexion

    require '../includes/config/database.php';
    $db = conectarDB();

    $consulta = "SELECT * FROM rrhhcomsys";
    $resultadoRrhh1 = mysqli_query($db,$consulta);
    $consulta = "SELECT * FROM areascomsys";
    $resultadoAreas = mysqli_query($db,$consulta);

    $nombre = '';
    $apellido = '';
    $iniciales = '';
    $cargo = '';
    $area = '';
    $correo = '';
    $requiereAcceso = '';
    $usuario = '';
    $password = '';
    $nivelAcceso = '';

    $errores = [];

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
    
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $iniciales = $_POST['iniciales'];
        $cargo = $_POST['cargo'];
        $area = $_POST['area'];
        $correo = $_POST['correo'];
        $requiereAcceso = $_POST['requiereAcceso'];
        $usuario = $_POST['usuario'];
        $password = $_POST['password'];
        $nivelAcceso = $_POST['nivelAcceso'];

        $consulta = "SELECT * FROM rrhhcomsys WHERE iniciales = '$iniciales'";
        $resultadoRrhh = mysqli_query($db,$consulta);
             
        if(!$nombre){
            $errores[] = "El nombre es obligatorio";
        }
        if(!$apellido){
            $errores[] = "El apellido es obligatorio";
        }   
        if(!$iniciales){
            $errores[] = "Debes especificar las iniciales";
        }
        if(!$cargo){
            $errores[] = "Debes de seleccionar el cargo";
        }
        if(!$area){
            $errores[] = "Debes de seleccionar el area";
        }
        
        if($requiereAcceso == 1){
            if(!$usuario){
                $errores[] = "El usuario es obligatorio";
            }
            if(!$password){
                $errores[] = "La contraseña es obligatorio";
            }   
            if(!$nivelAcceso){
                $errores[] = "Debes especificar el nivel de acceso";
            }
        }
        
        if( $resultadoRrhh->num_rows){
            $errores[] = "El Usuario ya existe en la base de datos";
        }elseif(empty($errores)){

            if($requiereAcceso == 0){
                $query = "INSERT INTO rrhhcomsys (nombre, apellido, iniciales, cargo, area, correo) 
                VALUES ('$nombre', '$apellido', '$iniciales', '$cargo', '$area', '$correo')";
   
                $resultado = mysqli_query($db, $query);
   
                if($resultado){              
                    header('Location: index.php?resultado=1');
                }
   
            }else{

                //Hasheamos password.

                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                $query = "INSERT INTO rrhhcomsys (nombre, apellido, iniciales, cargo, area, correo, usuario, password, nivelAcceso) 
                VALUES ('$nombre', '$apellido', '$iniciales', '$cargo', '$area', '$correo', '$usuario', '$passwordHash', '$nivelAcceso')";
    
                $resultado = mysqli_query($db, $query);
    
                if($resultado){              
                    header('Location: index.php?resultado=1');
                }
            }


         }
    }

    IncluirTemplate('../','header');
?>

    <main class="contenedor seccion">
        <h1>Crear Usuario</h1>

        <a href="index.php" class="boton boton-amarillo">Volver</a>

        <?php foreach ($errores as $error): ?>
            <div class="alerta error">
            <?php echo $error; ?>
            </div>
        <?php endforeach; ?>

        <form class="formulario-usuario" method="POST"> 
            <fieldset>
                                                                                                                    
                <legend>Datos Personales</legend>

                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" value="<?php echo $nombre;?>">

                <label for="apellido">Apellido</label>
                <input type="text" name="apellido" id="apellido" value="<?php echo $apellido;?>">

                <label for="iniciales">Iniciales</label>
                <input type="text" name="iniciales" id="iniciales" value="<?php echo $iniciales;?>">

                <label>Area</label>
                <select name="area">
                    <option value="">--Seleccione--</option>
                    <?php while ($areas = mysqli_fetch_assoc($resultadoAreas) ) : ?> <!-- si uso : tengo que usar el end while-->
                        <option  <?php echo $area === $areas['area'] ? 'selected' : ''; ?> value="<?php echo $areas['area']; ?>"> <?php echo $areas['area']; ?> </option> 
                    <?php endwhile; ?>                      
                </select>

                <label for="email">Correo</label>
                <input type="email" name="correo" id="email" value="<?php echo $correo;?>">

                <label>Cargo</label>
                <select name="cargo" id="cargo">
                    <option value="">--Seleccione--</option>
                    <option value="Gerencia" <?php echo $cargo == 'Gerencia' ? 'selected' : ''; ?>>Gerencia</option>
                    <option value="PM" <?php echo $cargo == 'PM' ? 'selected' : ''; ?>>PM</option>
                    <option value="PL" <?php echo $cargo == 'PL' ? 'selected' : ''; ?>>PL</option>
                    <option value="Tecnico" <?php echo $cargo == 'Tecnico' ? 'selected' : ''; ?>>Tecnico</option>
                </select>

                <label>Requiere usuario de sistema?</label>
                <select name="requiereAcceso">
                    <option value="0">NO</option>
                    <option value="1">SI</option>
                </select>
            
            </fieldset>
            <fieldset>
                                                                                                                    
                <legend>Datos para acceso sistema</legend>

                <label for="usuario">Usuario</label>
                <input type="text" name="usuario" id="usuario" value="<?php echo $usuario;?>">

                <label for="password">Password</label>
                <input type="text" name="password" id="password" value="<?php echo $password;?>">

                <label>Nivel de Acceso</label>
                <select name="nivelAcceso" id="nivelAcceso">
                    <option value="">--Seleccione--</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                </select>
            
            </fieldset>
            <input type="submit" value="Crear" class="boton boton-verde crear">
        </form>
    </main>
</body>
</html>