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

// Procesar préstamo de libro.
// --------------------------------
if(isset($_POST['prestar_libro'])){
    $id_libro = $_POST['id_libro'];
    $id_usuario = $_SESSION['id_usuario'];
    
    $fecha_prestamo = date('Y-m-d');
    $fecha_devolucion = date('Y-m-d', strtotime('+15 days'));
    
    $sql_prestar = "INSERT INTO prestamos (id_usuario, id_libro, fecha_prestamo, fecha_devolucion, estado) 
                   VALUES ($id_usuario, $id_libro, '$fecha_prestamo', '$fecha_devolucion', 'prestado')";
    
    if($conexion->query($sql_prestar)){
        echo "<script>alert('Libro prestado exitosamente'); window.location='buscar_libro.php'; </script>";
    } else {
        echo "<script>alert('Error al prestar el libro');</script>";
    }
}

// Filtrar géneros. -------------------------------------------
$filtro_genero = isset($_GET['genero']) ? $_GET['genero'] : '';

// Obtener lista de géneros para el seleccionado. -----------------------------------------
$generos_result = $conexion->query("SELECT nombre FROM generos ORDER BY nombre ASC");

// Realizar consulta de libros (sin excluir prestados). -----------------------------------------------------
$sql_libros = "SELECT l.id_libro, l.titulo, a.nombre AS autor, l.año AS fecha_publicacion, g.nombre AS genero
               FROM libros l
               JOIN autores a ON l.id_autor = a.id_autor
               JOIN generos g ON l.id_genero = g.id_genero
               ";

// Si hay filtro de género, lo agregamos a la consulta
if ($filtro_genero != '') {
    $sql_libros .= " AND g.nombre = '$filtro_genero'";
}

// Ordenar por título
$sql_libros .= " ORDER BY l.titulo ASC";

// Ejecutar la consulta final
$resultado_libros = $conexion->query($sql_libros);
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
    <!-- Menú de usuario | Se posicionará en la parte superior derecha. -->
    <!-------------------------------------------------------------------->
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

    <!--========================================================================================-->
    <!--                                    FILTROS DE BÚSQUEDA                                 -->
    <!--========================================================================================-->
    <section class="filtros">
        <form method="GET" class="form-filtros">
            <input type="text" id="busqueda" name="busqueda" placeholder="Buscar libro o autor...">
            
            <select name="genero" onchange="this.form.submit()">
                <option value="">Todos los géneros</option>
                <?php while($gen = $generos_result->fetch_assoc()): ?>
                    <option value="<?php echo $gen['nombre']; ?>" 
                            <?php echo ($filtro_genero == $gen['nombre']) ? 'selected' : ''; ?>>
                        <?php echo $gen['nombre']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <noscript><button type="submit">Filtrar</button></noscript>
        </form>
    </section>

    <!--============================================-->
    <!--           LISTA DE LIBROS                 -->
    <!--============================================-->
    <main class="libros-container" id="listaLibros">
        <h2>Catálogo de Libros Digitales</h2>

        <?php if($resultado_libros->num_rows > 0): ?>
            <?php while($libro = $resultado_libros->fetch_assoc()): ?>

                <?php
                // Verificar si el usuario ya tiene este libro prestado
                // ----------------------------------------------------
                $id_usuario = $_SESSION['id_usuario'];
                $id_libro_actual = $libro['id_libro'];
                $ya_prestado = $conexion->query("
                    SELECT 1 FROM prestamos 
                    WHERE id_usuario = $id_usuario 
                      AND id_libro = $id_libro_actual 
                      AND estado = 'prestado'
                ")->num_rows > 0;
                ?>

                <!-- Generar "tarjeta" donde se visualizarán los libros. -->
                <!--------------------------------------------------------->
                <section class="libro-card">
                    <div class="info-libro">
                        <h3><?php echo $libro['titulo']; ?></h3>
                        <p><strong>Autor:</strong> <?php echo $libro['autor']; ?></p>
                        <p><strong>Género:</strong> <?php echo $libro['genero']; ?></p>
                        <p><strong>Año:</strong> <?php echo $libro['fecha_publicacion']; ?></p>
                    </div>

                    <?php if($ya_prestado): ?>
                        <!-- Si el libro ya está prestado -->
                        <!---------------------------------->
                        <?php if(!empty($libro['archivo_url'])): ?>
                            <a href="<?php echo $libro['archivo_url']; ?>" target="_blank" class="btn-leer">Leer en línea</a>
                        <?php else: ?>
                            <a href="Mis_Libros.php" class="btn-leer">Leer en línea</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <!-- Si el usuario aún no lo ha prestado -->
                        <!----------------------------------------->
                        <form method="POST">
                            <input type="hidden" name="id_libro" value="<?php echo $libro['id_libro']; ?>">
                            <button type="submit" name="prestar_libro" class="btn-prestar">Prestar</button>
                        </form>
                    <?php endif; ?>
                </section>

            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center;">No hay libros disponibles en este momento.</p>
        <?php endif; ?>
    </main>

    <!--====================================================================-->
    <!--                         Búsqueda (JavaScript)                      -->
    <!--====================================================================-->
    <script>
    const inputBusqueda = document.getElementById('busqueda');
    const libros = document.querySelectorAll('.libro-card');

    inputBusqueda.addEventListener('input', () => {
        const texto = inputBusqueda.value.toLowerCase();
        libros.forEach(libro => {
            const contenido = libro.textContent.toLowerCase();
            libro.style.display = contenido.includes(texto) ? 'flex' : 'none';
        });
    });
    </script>
</body>
</html>