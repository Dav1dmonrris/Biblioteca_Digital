<?php
include('Conexion.php');
$conexion = conexion();

$id = $_GET['id'];

$sql = "DELETE FROM autores WHERE id_autor = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: autores.php");
exit;
?>