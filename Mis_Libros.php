<?php
session_start();
if(!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'usuario'){
    header("Location: login.php");
    exit;
}

include('Conexion.php');
$conexion = conexion();

// OBTENER LIBROS PRESTADOS
$id_usuario = $_SESSION['id_usuario'];
$resultado_mis_libros = $conexion->query("
    SELECT l.titulo, a.nombre as autor, l.año, p.fecha_prestamo, p.fecha_devolucion, p.estado
    FROM prestamos p, libros l, autores a 
    WHERE p.id_libro = l.id_libro 
    AND l.id_autor = a.id_autor 
    AND p.id_usuario = $id_usuario 
    AND p.estado = 'prestado'
");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Libros - Libreria el inge</title>
    <link rel="stylesheet" href="CSS/buscar_libro.css">
</head>
<body>

    <header>
        <div class="user-menu">
            <?php echo $_SESSION['usuario']; ?> | 
            <a href="buscar_libro.php">Catalogo</a> |
            <a href="Mis_Libros.php">Mis Libros</a> |
            <a href="perfil_usuario.php">Perfil</a> |
            <a href="logout.php">Cerrar Sesión</a>
        </div>
        <h1 class="Titulo_Principal">Mis Libros Prestados</h1>
    </header>

    <center>
        <h3>Mis Libros Actualmente Prestados</h3>
        
        <div class="libros-prestados">
            <?php if($resultado_mis_libros->num_rows > 0): ?>
                <?php while($libro = $resultado_mis_libros->fetch_assoc()): ?>
                <div class="libro-prestado">
                    <h4><?php echo $libro['titulo']; ?></h4>
                    <p><strong>Autor:</strong> <?php echo $libro['autor']; ?></p>
                    <p><strong>Año:</strong> <?php echo $libro['año']; ?></p>
                    <p><strong>Fecha de préstamo:</strong> <?php echo $libro['fecha_prestamo']; ?></p>
                    <p><strong>Fecha de devolución:</strong> <?php echo $libro['fecha_devolucion']; ?></p>
                    <p><strong>Estado:</strong> <?php echo $libro['estado']; ?></p>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No tienes libros prestados actualmente.</p>
            <?php endif; ?>
        </div>
    </center>

</body>
</html>