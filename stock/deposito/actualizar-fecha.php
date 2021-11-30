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

    // echo $GET_Id;

    //Consultar la base 

    $consulta = "SELECT * FROM depositoproyecto WHERE id = $GET_Id";
    $resultadoDeposito = mysqli_query($db,$consulta); 
    $deposito = mysqli_fetch_assoc($resultadoDeposito);

    $proximoUso = '';
    // Incluye Header
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        
        $proximoUso = $_POST['proximoUso'];
        echo "<pre>";
        var_dump($proximoUso);
        echo "</pre>";

        $query =  "UPDATE depositoproyecto SET fechaUsoFuturo = '$proximoUso' WHERE id = $GET_Id";
        $resultado = mysqli_query($db,$query); 
        echo $query;
            
        if($resultado){
            header('Location: deposito.php?estado=2');
        }   


    }
    
    incluirTemplate('../../','header');
?>
<main class="contenedor">

    <h1>Actualizar Fecha</h1>
    <a href="deposito.php" class="boton boton-amarillo">Volver</a>
    <form method="POST">
    <table class="listado-tabla">
        <thead>
            <th>Estante</th>
            <th>Proyecto</th>
            <th>Area</th>
            <th>Condicion</th>
            <th>Proximo Uso</th>
            <th>Bultos</th>
            <th>Acciones</th>
        </thead>
            <tbody class="item-tabla">
                <tr>
                    <td> <?php echo $deposito['ubicacion']; ?> </td>
                    <td> 
                        <a href="historico.php?id=<?php echo $id =$deposito['proyectoId']; ?>">
                            <?php 
                                $id =$deposito['proyectoId'];
                                $consulta = "SELECT * FROM proyecto WHERE id = $id";
                                $proyecto = mysqli_fetch_assoc(mysqli_query($db,$consulta));
                                echo $proyecto['nombre']; 
                            ?> 
                        </a>
                    </td>
                    <td> 
                        <?php 
                            $id =$deposito['proyectoId'];
                            $consulta = "SELECT * FROM proyecto WHERE id = $id";
                            $proyecto = mysqli_fetch_assoc(mysqli_query($db,$consulta));
                            echo $proyecto['area']; 
                        ?> 
                    </td>
                    <td> 
                        <?php 
                            echo $deposito['condicion'];
                        ?> 
                    </td>
                    <td> 
                        <input type="date" name="proximoUso" value="<?php echo $deposito['fechaUsoFuturo'];?>">
                    </td>
                    <td> 
                        <?php 
                            $id =$deposito['depositoIngresoId'];
                            $consulta = "SELECT * FROM depositoingreso WHERE id = $id";
                            $ingreso = mysqli_fetch_assoc(mysqli_query($db,$consulta));
                            echo $ingreso['bultos'];                
                        ?> 
                    </td>
                    <td>
                        <input type="submit" class="boton boton-verde" value="Actualizar Fecha">                           
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</main>
</body>
</html>