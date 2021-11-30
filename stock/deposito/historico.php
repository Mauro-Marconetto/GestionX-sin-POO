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

    $GET_Id = $_GET['id'];

    //Consultar la base 

    $consulta = "SELECT * FROM depositoproyecto WHERE proyectoId = $GET_Id";
    $resultadoDeposito = mysqli_query($db,$consulta);
    $consulta = "SELECT * FROM depositoegreso WHERE proyectoId = $GET_Id";
    $resultadoEgreso = mysqli_query($db,$consulta);  
    $consulta = "SELECT * FROM proyecto";
    $resultadoProyecto = mysqli_query($db,$consulta); 
    
    $ubicacionTotal = []; //hago un arreglo con todos los nombres de los proyectos que estan abiertos
    $contador = 0;
    $consulta = "SELECT * FROM depositoproyecto WHERE proyectoId = $GET_Id && estado = 'abierto' ORDER BY ubicacion";
    $resultadoubicaciones = mysqli_query($db,$consulta);
    while ($depositoProyecto = mysqli_fetch_assoc($resultadoubicaciones)){
        $ubicacionTotal[] = $depositoProyecto['ubicacion'];
        $contador++;
    }

    $ubicacion = [];
    $ubicacion[0] = $ubicacionTotal[0];
    $j=0;
    for($i=0 ; $i<$contador ; $i++){
        if($ubicacion[$j]==$ubicacionTotal[$i]){
        }else{
            $ubicacion[] = $ubicacionTotal[$i];
            $j++;
        }

    }
 
          
    // Incluye Header
    
    incluirTemplate('../../','header');
?>

    <main class="contenedor">
        <!-- <?php if($resultado == 1) : ?>
            <p class="alerta exito">Deposito agregado correctamente</p>
        <?php endif ?> -->

        <h1>Historico - 
            <?php 
                // $deposito = mysqli_fetch_assoc($resultadoDeposito);
                // $depositoId =$deposito['proyectoId'];
                $consulta = "SELECT * FROM proyecto WHERE id = $GET_Id";
                $resultadoProyecto = mysqli_fetch_assoc(mysqli_query($db,$consulta));
                $area = $resultadoProyecto['area'];
                $clienteFinal = $resultadoProyecto['clienteFinal'];
                $clienteId = $resultadoProyecto['contactoClienteId'];
                echo $resultadoProyecto['nombre'];    
            ?>
        </h1>
        <table class="listado-tabla-historico">
            <thead>
                <th>AREA</th>
                <th>CLIENTE/CLIENTE FINAL</th>
                <th>PROYECTO</th>
                <th>CANTIDAD DE BULTOS</th>
            </thead>
            <tbody class="item-tabla-historico">
                <tr>
                    <td>
                        <?php
                            echo $area;    
                        ?>
                    </td>
                    <td>
                        <?php
                            $consulta = "SELECT * FROM contactocliente WHERE id = $clienteId";
                            $contactoCliente = mysqli_fetch_assoc(mysqli_query($db,$consulta));
                            $empresa = $contactoCliente['empresa'];
                            echo $empresa . "/" . $clienteFinal;    
                        ?>
                    </td>
                    <td>
                        <?php
                            echo $resultadoProyecto['nombre'];    
                        ?>
                    </td>
                    <td>
                        <?php
                            $bultos = 0;
                            while ($deposito = mysqli_fetch_assoc($resultadoDeposito)){
                                $id = $deposito['depositoIngresoId'];                  
                                $consulta = "SELECT * FROM depositoingreso WHERE id = $id";
                                $resultadoIngreso = mysqli_fetch_assoc(mysqli_query($db,$consulta));
                                $bultos +=  intval($resultadoIngreso['bultos']);
                            }
                            while ($egreso = mysqli_fetch_assoc($resultadoEgreso)){
                                $bultos -=  intval($egreso['bultos']);
                            }
                            echo $bultos;
                        ?>
                    </td>
                </tr>
            </tbody>
            <thead>
                <th>CONTACTO CLIENTE</th>
                <th>TELEFONO</th>
                <th>FECHA FIN PROYECTO</th>
                <th>ESTANTES</th>
            </thead>
            <tbody class="item-tabla">
                <tr>
                    <td>
                        <?php
                            echo $contactoCliente['nombre'] . " " . $contactoCliente['apellido'];
                        ?>
                    </td>
                    <td>
                        <?php
                            echo $contactoCliente['telefono'];
                        ?>
                    </td>
                    <td>
                        <?php
                            $consulta = "SELECT * FROM depositoproyecto WHERE proyectoId = $GET_Id && estado = 'abierto' ORDER BY fechaUsoFuturo";
                            $resultadoDeposito = mysqli_query($db,$consulta);
                            $fecha = mysqli_fetch_assoc($resultadoDeposito);
                            echo $fecha['fechaUsoFuturo'];
                        ?>
                    </td>
                    <td>
                        <?php foreach ($ubicacion as $estante): ?>
                            <?php echo $estante; ?>
                        <?php endforeach; ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <a href="deposito.php" class="boton boton-amarillo">Volver</a>
        <h2>Ingresos</h2>
        <table class="listado-tabla">
            <thead>
                <th>Fecha Ingreso</th>
                <th>RRHH</th>
                <th>Area</th>
                <th>Origen</th>
                <th>Bultos</th>
                <th>Comentarios</th>
            </thead>
            <tbody class="item-tabla">
                <?php 
                    $consulta = "SELECT * FROM depositoproyecto WHERE proyectoId = $GET_Id";
                    $resultadoDeposito = mysqli_query($db,$consulta); 
                    while ($deposito = mysqli_fetch_assoc($resultadoDeposito)) : ?>
                    <tr>                    
                        <td> 
                            <?php 
                                $id =$deposito['depositoIngresoId'];
                                $consulta = "SELECT * FROM depositoingreso WHERE id = $id";
                                $resultadoIngreso = mysqli_fetch_assoc(mysqli_query($db,$consulta));
                                echo $resultadoIngreso['fechaIngreso']; 
                            ?> 
                        </td>
                        <td> 
                            <?php
                                echo $resultadoIngreso['rrhh1'] . "/" . $resultadoIngreso['rrhh2']; 
                            ?>    
                        </td>
                        <td> 
                            <?php echo $resultadoIngreso['area']; ?> 
                        </td>
                        <td> 
                            <?php echo $resultadoIngreso['origen']; ?> 
                        </td>
                        <td> 
                            <?php echo $resultadoIngreso['bultos']; ?> 
                        </td>
                        <td> 
                            <?php echo $resultadoIngreso['comentario']; ?> 
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <h2>Egresos</h2>
        <table class="listado-tabla">
            <thead>
                <th>Fecha Egreso</th>
                <th>Retiro</th>
                <th>Entrego</th>
                <th>Destino</th>
                <th>Bultos</th>
                <th>Comentarios</th>
            </thead>
            <tbody class="item-tabla">
                <?php 
                    $consulta = "SELECT * FROM depositoegreso WHERE proyectoId = $GET_Id";
                    $resultadoEgreso = mysqli_query($db,$consulta);  
                    while ($egreso = mysqli_fetch_assoc($resultadoEgreso)) : 
                ?>
                    <tr>                    
                        <td> 
                            <?php echo $egreso['fechaEntrega'];?> 
                        </td>
                        <td> 
                            <?php echo $egreso['retira'];?>    
                        </td>
                        <td> 
                            <?php echo $egreso['entrega'];?> 
                        </td>
                        <td> 
                            <?php echo $egreso['destino'];?> 
                        </td>
                        <td> 
                            <?php echo $egreso['bultos'];?> 
                        </td>
                        <td> 
                            <?php echo $egreso['comentario'];?> 
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
                
        </table>
    </main>
</body>
</html>