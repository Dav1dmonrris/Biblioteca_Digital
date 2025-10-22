<?php
include('Conexion.php');
$conexion = conexion();

$id = $_GET['id'];

// Cargar datos actuales
$sql = "SELECT * FROM autores WHERE id_autor = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$autor = $stmt->get_result()->fetch_assoc();

if($_POST){
    $nombre = $_POST['nombre'];
    $nacionalidad = $_POST['nacionalidad'];
    
    $sql = "UPDATE autores SET nombre=?, nacionalidad=? WHERE id_autor=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssi", $nombre, $nacionalidad, $id);
    $stmt->execute();
    
    header("Location: autores.php");
    exit;
}
?>

<form method="post">
    Nombre: <input type="text" name="nombre" value="<?php echo $autor['nombre']; ?>" required><br>
    Nacionalidad: <input type="text" name="nacionalidad" value="<?php echo $autor['nacionalidad']; ?>" required><br>
    <input type="submit" value="Actualizar Autor">
</form>