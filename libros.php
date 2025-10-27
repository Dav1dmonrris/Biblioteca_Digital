<?php
session_start();
if(!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'admin'){
    header("Location: login.php");
    exit;
}

include('Conexion.php');
$conexion = conexion();

if($_POST){
    $titulo = $_POST['titulo'];
    $año = $_POST['año'];
    $id_autor = $_POST['id_autor'];
    $reseña = $_POST['reseña'];
    
    $sql = "INSERT INTO libros (titulo, año, id_autor, reseña) VALUES (?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("siis", $titulo, $año, $id_autor, $reseña);
    $stmt->execute();
    
    header("Location: libros.php");
    exit;
}

$resultado_libros = $conexion->query("SELECT l.*, a.nombre as autor_nombre FROM libros l JOIN autores a ON l.id_autor = a.id_autor");
$autores = $conexion->query("SELECT * FROM autores");
?>


<!DOCTYPE html>
<html>  
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Libros">
    <link rel="stylesheet" href="CSS/estilo.css">
    <title>Libreria el inge</title>

</head>
<h2>Gestión de Libros</h2>

<form method="post">
    Título: <input type="text" name="titulo" required><br>
    Año: <input type="number" name="año" required><br>
    Reseña: <textarea name="reseña"></textarea><br>
    Autor: 
    <select name="id_autor" required>
        <option value="">Seleccionar autor</option>
        <?php while($autor = $autores->fetch_assoc()): ?>
            <option value="<?php echo $autor['id_autor']; ?>"><?php echo $autor['nombre']; ?></option>
        <?php endwhile; ?>
    </select><br>
    <input type="submit" value="Agregar Libro">
</form>

<h3>Libros Existentes</h3>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Título</th>
        <th>Año</th>
        <th>Autor</th>
        <th>Reseña</th>
        <th>Acciones</th>
    </tr>
    <?php while($libro = $resultado_libros->fetch_assoc()): ?>
    <tr>
        <td><?php echo $libro['id_libro']; ?></td>
        <td><?php echo $libro['titulo']; ?></td>
        <td><?php echo $libro['año']; ?></td>
        <td><?php echo $libro['autor_nombre']; ?></td>
        <td><?php echo $libro['reseña']; ?></td>
        <td>
            <a href="editar_libro.php?id=<?php echo $libro['id_libro']; ?>">Editar</a>
            <a href="eliminar_libro.php?id=<?php echo $libro['id_libro']; ?>">Eliminar</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>


<!--- 20/10/22 -->

<h3>Usuarios existentes</h3>

<?php
// OBTENER USUARIOS PARA LA TABLA
$resultado_usuarios = $conexion->query("SELECT * FROM usuarios");
?>

<table border="1">
    <tr>

        <th>Nombre</th>
        <th>Email</th>
        <th>Fecha de registro</th>
        <th>Acciones</th>
        
    </tr>
    <?php while($usu = $resultado_usuarios->fetch_assoc()): ?>
    <tr>

        <td><?php echo $usu['nombre']; ?></td>
        <td><?php echo $usu['email']; ?></td>
        <td><?php echo $usu['fecha_registro']; ?></td>

        <td>

            <a href="eliminar_usuario.php?id=<?php echo $usu['id_usuario']; ?>">Eliminar</a>

        </td>
    </tr>

    <?php endwhile; ?>

</table>

<br>
<a href="logout.php">Cerrar Sesión</a>
</html>