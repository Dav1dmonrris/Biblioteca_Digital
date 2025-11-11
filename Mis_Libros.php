<!--=============================== php ======================================-->
<!--==========================================================================-->
<?php
session_start();
if(!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'usuario'){
    header("Location: login.php");
    exit;
}

include('Conexion.php');
$conexion = conexion();

// Obtener libros.
// -----------------------------------
$id_usuario = $_SESSION['id_usuario'];

// Devolver el libro.
// ---------------------------------
if(isset($_POST['devolver_libro'])){
    $id_libro = $_POST['id_libro'];

    // Buscar el préstamo activo de ese libro para este usuario
    // --------------------------------------------------------
    $sql_devolver = "UPDATE prestamos 
                     SET estado = 'devuelto' 
                     WHERE id_usuario = $id_usuario 
                     AND id_libro = $id_libro 
                     AND estado = 'prestado'";

    if($conexion->query($sql_devolver)){
        echo "<script>alert('Libro devuelto exitosamente.'); window.location='Mis_Libros.php';</script>";
    } else {
        echo "<script>alert('Error al devolver el libro.');</script>";
    }
}

// Obtener libros prestados.
// ------------------------------------------------------------
$sql = "SELECT l.id_libro, l.titulo, l.portada, a.nombre AS autor, l.año, 
               p.fecha_prestamo, p.fecha_devolucion, p.estado
        FROM prestamos p
        JOIN libros l ON p.id_libro = l.id_libro
        JOIN autores a ON l.id_autor = a.id_autor
        WHERE p.id_usuario = $id_usuario
        AND p.estado = 'prestado'
        ORDER BY p.fecha_prestamo DESC";

$resultado_mis_libros = $conexion->query($sql);
?>

<!--=============================== Html ======================================-->
<!--===========================================================================-->
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mis Libros - Libreria el inge</title>
        <link rel="stylesheet" href="CSS/Mis_Libros.css">
    </head>
    <body>

        <header>
            <!-- Menú de usuario -->
            <!--------------------->
            <div class="user-menu">
                <?php echo $_SESSION['usuario']; ?> | 
                <a href="buscar_libro.php">Catalogo</a> |
                <a href="Mis_Libros.php">Mis Libros</a> |
                <a href="perfil_usuario.php">Perfil</a> |
                <a href="logout.php">Cerrar Sesión</a>
            </div>
            <h1 class="Titulo_Principal">Mis libros prestados</h1>
        </header>

        <main class="libros-container">

        <?php if($resultado_mis_libros->num_rows > 0): ?>
            <?php while($libro = $resultado_mis_libros->fetch_assoc()): ?>

                <section class="libro-card">

                    <!-- Añadir la imagen del libro. -->
                    <div class="img-libro">
                        <img src="<?php echo !empty($libro['portada']) ? $libro['portada'] : 'Portadas/PorDefecto.jpg'; ?>" 
                             alt="Portada de <?php echo $libro['titulo']; ?>">
                    </div>

                    <!-- Seccion de la info del libro. -->
                    <div class="info-libro">
                        <h3><?php echo $libro['titulo']; ?></h3>
                        <p><strong>Autor:</strong> <?php echo $libro['autor']; ?></p>
                        <p><strong>Año:</strong> <?php echo $libro['año']; ?></p>
                        <p><strong>Fecha de préstamo:</strong> <?php echo $libro['fecha_prestamo']; ?></p>
                        <p><strong>Fecha de devolución:</strong> <?php echo $libro['fecha_devolucion']; ?></p>
                        <p><strong>Estado:</strong> <span class="estado"><?php echo ucfirst($libro['estado']); ?></span></p>
                    </div>

                    <!-- *** Apartado para añadir un url para leer los libros. *** -->
                    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->

                    <!-- Formulario (en forma de botón) para devolver el libro. -->
                    <form method="POST" onsubmit="return confirmarDevolucion();">
                        <input type="hidden" name="id_libro" value="<?php echo $libro['id_libro']; ?>">
                        <button type="submit" name="devolver_libro" class="btn-devolver">Devolver</button>
                    </form>
                </section>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-libros">No tienes libros prestados actualmente.</p>
        <?php endif; ?>

        </main>

        <!-- JavaScript -->
        <script>
        function confirmarDevolucion() 
        {
            return confirm("¿Segur@ que deseas devolver este libro?");
        }

        </script>
    </body>
</html>