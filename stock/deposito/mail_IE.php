<?php

    require '../../includes/config/database.php';
    require '../../includes/funciones.php'; 
    $db = conectarDB();

    $GET_id = $_GET['id'];
    $GET_accion = $_GET['accion'];



    if ($GET_accion == 'nuevo_ingreso'){
        $consulta = "SELECT * FROM depositoingreso WHERE id = '$GET_id'";
        $resultadoIngreso = mysqli_query($db,$consulta);
    
        $ingreso = mysqli_fetch_assoc($resultadoIngreso);
        $pl = $ingreso['pl'];
    
        $rrhh1 = $ingreso['rrhh1'];
        $rrhh2 = $ingreso['rrhh2'];
        // $pl = $ingreso['pl'];
        // $area = $ingreso['area'];
        $cliente = $ingreso['cliente'];
        $clienteFinal = $ingreso['clienteFinal'];
        $fechaIngreso = $ingreso['fechaIngreso'];
        $proyecto = $ingreso['proyecto'];
        $origen = $ingreso['origen'];
        $bultos = $ingreso['bultos'];
        $comentario = $ingreso['comentario'];

    }elseif($GET_accion == 'nuevo_egreso'){

        $consulta = "SELECT * FROM depositoproyecto WHERE id = '$GET_id'";
        $resultadoDeposito = mysqli_query($db,$consulta); 
        $deposito = mysqli_fetch_assoc($resultadoDeposito);
        $proyectoId = $deposito['proyectoId'];
        $ingresoId = $deposito['depositoIngresoId'];

        $consulta = "SELECT * FROM proyecto WHERE id = '$proyectoId'";
        $resultadoProyecto = mysqli_query($db,$consulta);
        $proyecto = mysqli_fetch_assoc($resultadoProyecto);

        $consulta = "SELECT * FROM depositoingreso WHERE id = '$ingresoId'";
        $resultadoIngreso = mysqli_query($db,$consulta);
        $ingreso = mysqli_fetch_assoc($resultadoIngreso);
        $pl = $ingreso['pl'];

        $consulta = "SELECT * FROM depositoegreso ORDER BY id DESC";
        $resultadoEgreso = mysqli_query($db,$consulta);
        $egreso = mysqli_fetch_assoc($resultadoEgreso);

        $nombreProyecto = $proyecto['nombre'];
        $bultos = $egreso['bultos'];
        $retira = $egreso['retira'];
        $entrega = $egreso['entrega'];
        $fechaEntrega = $egreso['fechaEntrega'];
        $destino = $egreso['destino'];
        $comentario = $egreso['comentario'];
        
    }

    $consulta = "SELECT * FROM rrhhcomsys WHERE iniciales = '$pl'";
    $resultadorrhh = mysqli_query($db,$consulta);
    $coordinador= mysqli_fetch_assoc($resultadorrhh);

    $mailCoordinador = $coordinador['correo'];
    $nombre = $coordinador['nombre'];

    // Impotar algunas clases de phpmailer
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    // Cargar phpmailer via composer
    require '../../vendor/autoload.php';

    $mail = new PHPMailer(true);
    try {
        //Si el correo no te llega, quita el comentario
        //de la linea de abajo, para mas información
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host       = 'smtp.office365.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'gestionx@comsys.com.ar';
        $mail->Password   = 'Tgr45.-.';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->setFrom('gestionx@comsys.com.ar', 'Deposito Stock');

        //Destinatarios
        $mail->addAddress($mailCoordinador);

        // Contenido
        if ($GET_accion == 'nuevo_ingreso'){
        $cuerpo = '<html><head>';
        $cuerpo .= '<style type="text/css">';
        $cuerpo .= 'h1{ margin: 0 0 20px 0; color: #002973;}';
        $cuerpo .= 'h2{ margin: 0 0 20px 0; color: #002973;}';
        $cuerpo .= 'th{ text-align: left; background-color: #73a5ff; padding: 8px; width: 200px;}';
        $cuerpo .= 'td{ width: 200px; text-align: center; background-color: #cbe9fd; padding: 8px;}';
        $cuerpo .= '</style>';
        $cuerpo .= '</head>';
        $cuerpo .= '<body>';
        $cuerpo .= '<h1>Hola ' . $nombre . '!</h1>';
        $cuerpo .= '<h2>Acaba de ingresar el siguiente material:</h2>';
        $cuerpo .= '<table>';
        $cuerpo .= '<tbody>';
        $cuerpo .= '<tr><th>RRHH</th><td>' . $rrhh1 . '</td></tr>';
        $cuerpo .= '<tr><th>RRHH</th><td>' . $rrhh1 . '</td></tr>';
        $cuerpo .= '<tr><th>CLIENTE</th><td>' . $cliente . '</td></tr>';
        $cuerpo .= '<tr><th>CLIENTE FINAL</th><td>' . $clienteFinal . '</td></tr>';
        $cuerpo .= '<tr><th>FECHA DE INGRESO</th><td>' . $fechaIngreso . '</td></tr>';
        $cuerpo .= '<tr><th>PROYECTO</th><td>' . $proyecto . '</td></tr>';
        $cuerpo .= '<tr><th>ORIGEN</th><td>' . $origen . '</td></tr>';
        $cuerpo .= '<tr><th>CANTIDAD DE BULTOS</th><td>' . $bultos . '</td></tr>';
        $cuerpo .= '<tr><th>COMENTARIOS</th><td>' . $comentario . '</td></tr>';
        $cuerpo .= "</tbody>";
        $cuerpo .= "</table>";
        $cuerpo .= "</body></html>";

        $mail->isHTML(true);                          
        $mail->Subject = 'Material en deposito';
        $mail->Body    = $cuerpo;
        $mail->send();
        header('Location: entransito.php?resultado=1');

        }elseif ($GET_accion == 'nuevo_egreso'){
            $cuerpo = '<html><head>';   
            $cuerpo .= '<style type="text/css">';
            $cuerpo .= 'h1{ margin: 0 0 20px 0; color: #002973;}';
            $cuerpo .= 'h2{ margin: 0 0 20px 0; color: #002973;}';
            $cuerpo .= 'th{ text-align: left; background-color: #73a5ff; padding: 8px; width: 200px;}';
            $cuerpo .= 'td{ width: 200px; text-align: center; background-color: #cbe9fd; padding: 8px;}';
            $cuerpo .= '</style>';
            $cuerpo .= '</head>';
            $cuerpo .= '<body>';
            $cuerpo .= '<h1>Hola ' . $nombre . '!</h1>';
            $cuerpo .= '<h2>Acaban de realizar el siguiente retiro de material:</h2>';
            $cuerpo .= '<table>';
            $cuerpo .= '<tbody>';
            $cuerpo .= '<tr><th>PROYECTO</th><td>' . $nombreProyecto . '</td></tr>';
            $cuerpo .= '<tr><th>RETIRO</th><td>' . $retira . '</td></tr>';
            $cuerpo .= '<tr><th>ENTREGO</th><td>' . $entrega . '</td></tr>';
            $cuerpo .= '<tr><th>FECHA DE EGRESO</th><td>' . $fechaEntrega . '</td></tr>';
            $cuerpo .= '<tr><th>DESTINO</th><td>' . $destino . '</td></tr>';
            $cuerpo .= '<tr><th>CANTIDAD DE BULTOS</th><td>' . $bultos . '</td></tr>';
            $cuerpo .= '<tr><th>COMENTARIOS</th><td>' . $comentario . '</td></tr>';
            $cuerpo .= "</tbody>";
            $cuerpo .= "</table>";
            $cuerpo .= "</body></html>";
            $mail->isHTML(true);                          
            $mail->Subject = 'Egreso de material en deposito';
            $mail->Body    = $cuerpo;
            $mail->send();
            header('Location: deposito.php?estado=3'); 
        }

    } catch (Exception $e) {
        echo "Ocurrio un error: {$mail->ErrorInfo}";
        }