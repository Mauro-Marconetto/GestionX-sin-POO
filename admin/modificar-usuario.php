<?php
    require '../includes/config/database.php';
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
    // Base de Datos

    $GET_id = $_GET['id'] ?? null;

    $db = conectarDB();

    $consulta = "SELECT * FROM areascomsys";
    $resultadoAreas = mysqli_query($db,$consulta);

    if ($GET_id == null){
        $query = "SELECT * FROM rrhhcomsys";
        $resultadoQuery = mysqli_query($db,$query); 
    }else{
        $query = "SELECT * FROM rrhhcomsys WHERE iniciales = '$GET_id'";
        $resultadoQuery = mysqli_query($db,$query); 
        $datosUsuario = mysqli_fetch_assoc($resultadoQuery);
        $nombre = $datosUsuario['nombre'];
        $apellido = $datosUsuario['apellido'];
        $iniciales = $datosUsuario['iniciales'];
        $area = $datosUsuario['area'];
        $correo = $datosUsuario['correo'];
        $cargo = $datosUsuario['cargo'];
        $usuario = $datosUsuario['usuario'];
        $nivelAcceso = $datosUsuario['nivelAcceso'];
    }

    $user = '';
    $password = '';
    $requiereAcceso = '';
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){

        if ($GET_id == null){
            $usuario = $_POST['user'];
            $location = "Location: modificar-usuario.php?id=$usuario";
            header($location);
 
        }else{
            $requiereAcceso = $_POST['requiereAcceso'];
            $nombre = $_POST['nombre'];
            $apellido = $_POST['apellido'];
            $iniciales = $_POST['iniciales'];
            $area = $_POST['area'];
            $correo = $_POST['correo'];
            $cargo = $_POST['cargo'];

            if($requiereAcceso == 0){

                $query = "UPDATE rrhhcomsys SET nombre = '$nombre', apellido = '$apellido', iniciales = '$iniciales', area = '$area', correo = '$correo', cargo = '$cargo'  
                WHERE iniciales = '$usuario'";   
                $resultado = mysqli_query($db, $query);
   
                if($resultado){              
                    header('Location: index.php?resultado=2');
                }
   
            }else{

                $usuario = $_POST['usuario'];
                $password = $_POST['password'];
                $nivelAcceso = $_POST['nivelAcceso'];

                if($password == ''){

                    $query = "UPDATE rrhhcomsys SET nombre = '$nombre', apellido = '$apellido', iniciales = '$iniciales', area = '$area', correo = '$correo', cargo = '$cargo', usuario = '$usuario', nivelAcceso = '$nivelAcceso'  
                    WHERE iniciales = '$usuario'";   
                    $resultado = mysqli_query($db, $query);
       
                    if($resultado){              
                        header('Location: index.php?resultado=2');
                    }
                }else{
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                    $query = "UPDATE rrhhcomsys SET nombre = '$nombre', apellido = '$apellido', iniciales = '$iniciales', area = '$area', cargo = '$cargo', usuario = '$usuario', password = '$passwordHash', nivelAcceso = '$nivelAcceso'  
                    WHERE iniciales = '$usuario'";   
                    $resultado = mysqli_query($db, $query);
       
                    if($resultado){              
                        header('Location: index.php?resultado=2');
                    }
                }

            }

        }
        
    }
    
    incluirTemplate('../','header');
?>
<main class="contenedor">

    <h1>Modificar Usuario</h1>
    <a href="index.php" class="boton boton-amarillo">Volver</a>
    <?php if ($GET_id == null) : ?>
    <fieldset>
        <form class="formulario-usuario" method="POST">
            <label>Usuario</label>
            <select name="user">
                <option value="">--Seleccione--</option>
                <?php while ($rrhh = mysqli_fetch_assoc($resultadoQuery) ) : ?>
                    <option  <?php echo $user === $rrhh['iniciales'] ? 'selected' : ''; ?> value="<?php echo $rrhh['iniciales']; ?>"> <?php echo $rrhh['iniciales']; ?> </option> 
                <?php endwhile; ?>            
            </select>
            <input type="submit" class="boton boton-verde" value="Seleccionar Usuario">                           
        </form>
    </fieldset>
    <?php endif; ?>
    <?php if ($GET_id != null) : ?>
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
                    <option value="PL" <?php echo $cargo == 'PL' ? 'selected' : ''; ?>>PL</option>
                    <option value="PM" <?php echo $cargo == 'PM' ? 'selected' : ''; ?>>PM</option>
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
                    <option value="1" <?php echo $nivelAcceso == '1' ? 'selected' : ''; ?>>1</option>
                    <option value="2" <?php echo $nivelAcceso == '2' ? 'selected' : ''; ?>>2</option>
                </select>
            
            </fieldset>
            <input type="submit" value="Actualizar" class="boton boton-verde crear">
        </form>
    <?php endif; ?>
</main>
</body>
</html>