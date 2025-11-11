<?php
session_start();
if(!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'admin'){
    header("Location: login.php");
    exit;
}

include('Conexion.php');
$conexion = conexion();

$id_libro = $_GET['id'];

// Cargar datos del libro
$sql = "SELECT * FROM libros WHERE id_libro = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_libro);
$stmt->execute();
$libro = $stmt->get_result()->fetch_assoc();

// Cargar autores para dropdown
$autores = $conexion->query("SELECT * FROM autores");

if($_POST){
    $titulo = $_POST['titulo'];
    $año = $_POST['año'];
    $id_autor = $_POST['id_autor'];
    $reseña = $_POST['reseña'];
    
    $sql = "UPDATE libros SET titulo=?, año=?, id_autor=?, reseña=? WHERE id_libro=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("siisi", $titulo, $año, $id_autor, $reseña, $id_libro);
    $stmt->execute();
    
    header("Location: libros.php");
    exit;
}
?>

<!DOCTYPE html>
<html>  
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Libros">
    <link rel="stylesheet" href="CSS/editar_libro.css">
    <title>Libreria el inge</title>

</head>

<h2>Editar Libro</h2>

<form method="post">
    Título: <input type="text" name="titulo" value="<?php echo $libro['titulo']; ?>" required><br>
    Año: <input type="number" name="año" value="<?php echo $libro['año']; ?>" required><br>
    Reseña: <textarea name="reseña"><?php echo $libro['reseña']; ?></textarea><br>
    Autor: 
    <select name="id_autor" required>
        <?php while($autor = $autores->fetch_assoc()): ?>
            <option value="<?php echo $autor['id_autor']; ?>" <?php if($autor['id_autor'] == $libro['id_autor']) echo 'selected'; ?>>
                <?php echo $autor['nombre']; ?>
            </option>
        <?php endwhile; ?>
    </select><br>
    <input type="submit" value="Actualizar Libro">
</form>
<a href="libros.php">Cancelar</a>

</html>