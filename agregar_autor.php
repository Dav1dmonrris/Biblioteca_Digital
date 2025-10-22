<?php
include('Conexion.php');
$conexion = conexion();

if($_POST){
    $nombre = $_POST['nombre'];
    $nacionalidad = $_POST['nacionalidad'];
    
    $sql = "INSERT INTO autores (nombre, nacionalidad) VALUES (?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ss", $nombre, $nacionalidad);
    $stmt->execute();
    
    header("Location: autores.php");
    exit;
}
?>

<form method="post">
    Nombre: <input type="text" name="nombre" required><br>
    Nacionalidad: <input type="text" name="nacionalidad" required><br>
    <input type="submit" value="Agregar Autor">
</form>
<a href="autores.php">Volver a la lista</a>