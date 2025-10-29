<?php
session_start();
if(!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'usuario'){
    header("Location: login.php");
    exit;
}

include('Conexion.php');
$conexion = conexion();

$email = $_SESSION['email'];

// Cargar datos actuales
$sql = "SELECT * FROM usuarios WHERE email = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

if($_POST){
    $nuevo_nombre = $_POST['nombre'];
    $nuevo_email = $_POST['email'];
    
    $sql = "UPDATE usuarios SET nombre=?, email=? WHERE email=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sss", $nuevo_nombre, $nuevo_email, $email);
    
    if($stmt->execute()){
        $_SESSION['usuario'] = $nuevo_nombre;
        $_SESSION['email'] = $nuevo_email;
        header("Location: perfil_usuario.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>  
    <head>
        <meta charset="UTF-8">                                                  <!-- Etiqueta meta para definir la codificación de caracteres -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Libros">
        <link rel="stylesheet" href="CSS/actualizar_perfil.css">
        <title>Libreria el inge</title>

    </head>


    <h2>Editar Perfil</h2>
    <form method="post">
        Nombre: <input type="text" name="nombre" value="<?php echo $usuario['nombre']; ?>" required><br>
        Email: <input type="email" name="email" value="<?php echo $usuario['email']; ?>" required><br>
        <input type="submit" value="Actualizar">
    </form>
<a href="perfil_usuario.php">Cancelar</a>
</html>