<!--============================================================================================-->
<!--                        Codigo PHP para mostrar el perfil del usuario                       -->
<!--============================================================================================-->
<?php
session_start();
if(!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'usuario'){
    header("Location: login.php");
    exit;
}

include('Conexion.php');                        # <- Incluir el archivo de conexión a la base de datos
$conexion = conexion();                         # <- Establecer la conexión a la base de datos.

$email = $_SESSION['email'];
$sql = "SELECT * FROM usuarios WHERE email = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
?>

<!--============================================================================================-->
<!--                                  Perfil de usuario (html)                                  -->
<!--============================================================================================-->
<!DOCTYPE html>
<html>  
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Libros">
        <link rel="stylesheet" href="CSS/perfil_usuario.css">
    </head>

    <body>
        <header><h1>LIBRERÍA EL INGE</h1></header>
        
        <div class="perfil-card">
            <h2>Mi Perfil</h2>

            <img src="Elementos/Ejemplo de diseño de perfil.png" alt="Avatar" class="perfil-avatar">

            <!-- Mostrar información correspondiente del usuario -->
            <!----------------------------------------------------->
            <p><strong>Nombre:</strong> <?php echo $usuario['nombre']; ?></p>
            <p><strong>Email:</strong> <?php echo $usuario['email']; ?></p>
            <p><strong>Fecha de registro:</strong> <?php echo $usuario['fecha_registro']; ?></p>

            <!-- Mostrar botones de opciones para el usuario -->
            <!------------------------------------------------->
            <div class="perfil-buttons">
                <a href="actualizar_perfil.php" class="btn_EditarPerfil">Editar Perfil</a>
                <a href="buscar_libro.php" class="btn_volver">Volver</a>
                <a href="logout.php" class="btn_CerrarSesion">Cerrar Sesión</a>
            </div>
        </div>

    </body>
</html>