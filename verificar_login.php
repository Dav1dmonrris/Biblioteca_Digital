<?php
session_start();
include('Conexion.php');
$conexion = conexion();

$email = $_POST['email'];
$password = $_POST['password'];
$tipo = $_POST['tipo_usuario'];

if($tipo == 'usuario'){
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $tabla = 'usuarios';
    $redirect = 'perfil_usuario.php';
} else {
    $sql = "SELECT * FROM administradores WHERE email = ?";
    $tabla = 'administradores';
    $redirect = 'libros.php';
}

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$resultado = $stmt->get_result();

if($resultado->num_rows === 1){
    $usuario = $resultado->fetch_assoc();
    
    // Verificar contraseña (en texto plano por ahora)
    if($password == $usuario['password']){
        $_SESSION['usuario'] = $usuario['nombre'];
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['tipo'] = $tipo;
        header("Location: $redirect");
    } else {
        echo "Contraseña incorrecta";
    }
} else {
    echo "Usuario no encontrado";
}

$conexion->close();
?>