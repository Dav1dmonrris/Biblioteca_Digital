<!--============================================================================================-->
<!--                            Crear la conexión a la base de datos                            -->
<!--============================================================================================-->
<?php

// Crear la función de conexión --------------------------------------------------*
function conexion(){
    $mysqli_conexion = new mysqli("localhost", "root", "", "Biblioteca");

    if($mysqli_conexion->connect_errno){
        echo "Error de conexion con la base de datos: ". $mysqli_conexion->connect_errno;
        exit;
    }
    return $mysqli_conexion;
}

$conexion = conexion();
$conexion->close();
?>