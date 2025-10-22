<?php
session_start();
if(!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'usuario'){
    header("Location: login.php");
    exit;
}

include('Conexion.php');
$conexion = conexion();

$email = $_SESSION['email'];
$sql = "SELECT * FROM usuarios WHERE email = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
?>

<h2>Mi Perfil</h2>
<p><strong>Nombre:</strong> <?php echo $usuario['nombre']; ?></p>
<p><strong>Email:</strong> <?php echo $usuario['email']; ?></p>
<p><strong>Fecha de registro:</strong> <?php echo $usuario['fecha_registro']; ?></p>

<a href="actualizar_perfil.php">Editar Perfil</a>
<a href="logout.php">Cerrar Sesión</a>