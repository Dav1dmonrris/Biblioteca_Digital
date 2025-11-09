<?php
session_start();
include('Conexion.php');
$conexion = conexion();

$email = $_POST['email'];
$password = $_POST['password'];
$tipo = $_POST['tipo_usuario'];

if($tipo == 'usuario'){
    $sql = "SELECT * FROM usuarios WHERE email = ? AND password = ?";
    $redirect = 'buscar_libro.php';
} else {
    $sql = "SELECT * FROM administradores WHERE email = ? AND password = ?";
    $redirect = 'libros.php';
}

$stmt = $conexion->prepare($sql);
$stmt->bind_param("ss", $email, $password);
$stmt->execute();
$resultado = $stmt->get_result();

if($resultado->num_rows === 1){
    $usuario = $resultado->fetch_assoc();
    
    $_SESSION['usuario'] = $usuario['nombre'];
    $_SESSION['email'] = $usuario['email'];
    $_SESSION['tipo'] = $tipo;
    $_SESSION['id_usuario'] = $usuario['id_usuario']; // <- ESTA LÍNEA ES LA CLAVE
    
    header("Location: $redirect");
} else {
    echo "Usuario o contraseña incorrectos";
}

$conexion->close();
?>