<?php
session_start();
if(isset($_SESSION['usuario'])){
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Biblioteca</title>
</head>
<body>
    <h2>Iniciar Sesión</h2>
    <form action="verificar_login.php" method="post">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Contraseña" required><br>
        <select name="tipo_usuario">
            <option value="usuario">Usuario</option>
            <option value="admin">Administrador</option>
        </select><br>
        <input type="submit" value="Ingresar">
    </form>
</body>
</html>