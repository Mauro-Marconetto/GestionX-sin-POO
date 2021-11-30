<?php 

    //Importar la conexion

    require 'includes/config/database.php';
    $db = conectarDB();

    //Autententicar Usuario

    $errores = [];

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        // echo "<pre>";
        // var_dump($_POST);
        // echo "</pre>";

        $usuario = mysqli_real_escape_string($db,filter_var ($_POST['usuario']) ) ;
        $password = mysqli_real_escape_string($db,$_POST['password']);

        if(!$usuario){
            $errores [] = "El usuario es Obligatorio" ;
        }
        if(!$password){
            $errores [] = "El password es Obligatorio" ;
        }

        if(empty($errores)){
        
            //revisamos si el usuario existe
            $query = "SELECT * FROM rrhhcomsys WHERE usuario ='${usuario}' ";
            $resultado = mysqli_query($db,$query);

            // var_dump($resultado); //aca vamos que es un objeto

            if( $resultado->num_rows){ //asi se acceden a las variables dentro de un objeto
                //Revisar si el password es correcto

                $usuario=mysqli_fetch_assoc($resultado);

                //Verificamos si el password es correcto

                $auth = password_verify($password,$usuario['password']);

                $nivelAcceso = $usuario['nivelAcceso'];



                if($auth){

                    //inicio Session
                    session_start();

                    $_SESSION['usuario'] = $usuario['usuario'];
                    $_SESSION['login'] = true;
                    $_SESSION['nivelAcceso'] = $nivelAcceso;

                    header('Location: index.php');

                }else{
                    $errores [] = "El Password es incorrecto";
                }

            }else {
                $errores [] = "El Usuario no Existe";

            }


        }

    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="build/css/app.css">
    <title>GestionX</title>
</head>
<body>

    <main class="contenedor contenido-centrado">
        <div class="recuadro-login">

            <div class="login-top">
                <p>Gestion<span>X</span></p>
            </div>
            <form method="POST" class="login">
                
                <fieldset>
                    <label for="usuario">Usuario</label>
                    <input type="text" name='usuario' placeholder="Usuario" id="usuario" required> <!-- El required es para que el front valide que le ponga datos-->
                    <?php foreach ($errores as $error): ?>
                        <div class="error-login">
                        <?php 
                            if($error=="El Usuario no Existe"){
                                echo $error; 
                            }
                        ?>
                        </div>
                    <?php endforeach; ?>
                    <label for="password">Contraseña</label>
                    <input type="password" name='password' placeholder="Contraseña" id="password" required>
                    <?php foreach ($errores as $error): ?>
                        <div class="error-login"> 
                            <?php 
                                if($error=="El Password es incorrecto"){
                                    echo $error; 
                                }
                            ?>
                        </div>
                    <?php endforeach; ?>
                
                    <input type="submit" value="Iniciar Sesion" class="boton boton-verde">
                </fieldset>  
    
            </form>
        </div>
    </main>
</body>
</html>
