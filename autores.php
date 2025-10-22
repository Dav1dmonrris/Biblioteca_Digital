<?php
include('Conexion.php');
$conexion = conexion();

$resultado = $conexion->query("SELECT * FROM autores");
?>

<h2>Lista de Autores</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Nacionalidad</th>
        <th>Acciones</th>
    </tr>
    <?php while($fila = $resultado->fetch_assoc()): ?>
    <tr>
        <td><?php echo $fila['id_autor']; ?></td>
        <td><?php echo $fila['nombre']; ?></td>
        <td><?php echo $fila['nacionalidad']; ?></td>
        <td>
            <a href="editar_autor.php?id=<?php echo $fila['id_autor']; ?>">Editar</a>
            <a href="eliminar_autor.php?id=<?php echo $fila['id_autor']; ?>">Eliminar</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<a href="agregar_autores.php">Agregar Autor</a>

<?php $conexion->close(); ?>