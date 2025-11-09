<!--============================================================================================-->
<!--                                  Catálogo de Libros (php)                                  -->
<!--============================================================================================-->
<?php
session_start();
echo "<!-- DEBUG: id_usuario: " . $_SESSION['id_usuario'] . " -->";
echo "<!-- DEBUG: usuario: " . $_SESSION['usuario'] . " -->";
echo "<!-- DEBUG: email: " . $_SESSION['email'] . " -->";
if(!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'usuario'){
    header("Location: login.php");
    exit;
}

include('Conexion.php');
$conexion = conexion();

// PROCESAR PRÉSTAMO DE LIBRO
if(isset($_POST['prestar_libro'])){
    $id_libro = $_POST['id_libro'];
    $id_usuario = $_SESSION['id_usuario'];
    
    $fecha_prestamo = date('Y-m-d');
    $fecha_devolucion = date('Y-m-d', strtotime('+15 days'));
    
    // INSERTO DIRECTAMENTE SIN PREPARE
    $sql_prestar = "INSERT INTO prestamos (id_usuario, id_libro, fecha_prestamo, fecha_devolucion, estado) 
                   VALUES ($id_usuario, $id_libro, '$fecha_prestamo', '$fecha_devolucion', 'prestado')";
    
    if($conexion->query($sql_prestar)){
        echo "<script>alert('Libro prestado exitosamente');</script>";
    } else {
        echo "<script>alert('Error al prestar el libro');</script>";
    }
}

// OBTENER LIBROS DISPONIBLES (que no están prestados)
$resultado_libros = $conexion->query("SELECT l.id_libro, l.titulo, a.nombre as autor, l.año as fecha_publicacion 
                                     FROM libros l 
                                     JOIN autores a ON l.id_autor = a.id_autor
                                     WHERE l.id_libro NOT IN (
                                         SELECT id_libro FROM prestamos WHERE estado = 'prestado'
                                     )");
?>

<!--============================================================================================-->
<!--                                  Catálogo de Libros (html)                                  -->
<!--============================================================================================-->
<!DOCTYPE html>
<html>  
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Libros">
    <link rel="stylesheet" href="CSS/buscar_libro.css">
    <title>Libreria el inge</title>
</head>
<body>

    <!-- MENÚ DE USUARIO EN ESQUINA SUPERIOR DERECHA -->
    <header>
    <div class="user-menu">
         <?php echo $_SESSION['usuario']; ?> | 
        <a href="buscar_libro.php">Catalogo</a> |
        <a href="Mis_Libros.php">Mis Libros</a> |
        <a href="perfil_usuario.php">Perfil</a> |
        <a href="logout.php">Cerrar Sesión</a>
    </div>

    <h1 class="Titulo_Principal">Libreria el Inge</h1>
    </header>

    <nav>
        <center>
            <a href="autores.php">Autores</a> |
            <a href="generos.php">Géneros</a> |
        </center>
    </nav>

    <br>
    
    <center>
        <label for="busqueda">Buscar Libro:<br></label>
        <input type="text" id="busqueda" name="busqueda">
    </center>
    <br><br>

    <center>
    <h3>Catálogo de Libros Disponibles</h3>
    <div class="libros-container">
        <?php while($libro = $resultado_libros->fetch_assoc()): ?>
        <div class="libro-card">
            <h4><?php echo $libro['titulo']; ?></h4>
            <p><strong>Autor:</strong> <?php echo $libro['autor']; ?></p>
            <p><strong>Año de publicación:</strong> <?php echo $libro['fecha_publicacion']; ?></p>
            
            <!-- BOTÓN DE PRESTAR -->
            <form method="POST" style="margin-top: 10px;">
                <input type="hidden" name="id_libro" value="<?php echo $libro['id_libro']; ?>">
                <button type="submit" name="prestar_libro" class="btn-prestar">Prestar Libro</button>
            </form>
        </div>
        <?php endwhile; ?>
    </div>
    </center>

</body>
</html>