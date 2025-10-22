<?php
session_start();
if(!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'admin'){
    header("Location: login.php");
    exit;
}

include('Conexion.php');
$conexion = conexion();

$id_libro = $_GET['id'];

$sql = "DELETE FROM libros WHERE id_libro = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_libro);
$stmt->execute();

header("Location: libros.php");
exit;
?>