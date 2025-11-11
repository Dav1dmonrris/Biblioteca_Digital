<!--============================================================================================-->
<!--                             Codigo PHP para la gestion de libros                           -->       
<!--============================================================================================-->
<?php
session_start();
if(!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'admin'){
    header("Location: login.php");
    exit;
}

include('Conexion.php');    # <------------------ Incluir el archivo de conexión a la base de datos
$conexion = conexion();     # <------------------ Establecer la conexión a la base de datos

// Agregar un nuevo libro.
// -----------------------
if($_POST){
    $titulo = $_POST['titulo'];
    $año = $_POST['año'];
    $id_autor = $_POST['id_autor'];
    $reseña = $_POST['reseña'];
    $ruta_portada = $_POST['portada'];
    $id_genero = $_POST['id_genero'];


    // Para subir portada.
    // -------------------
    $ruta_portada = '';
    if(isset($_FILES['portada']) && $_FILES['portada']['error'] == 0){
        $nombreArchivo = time() . "_" . basename($_FILES['portada']['name']);
        $rutaDestino = "Portadas/" . $nombreArchivo;

        if(move_uploaded_file($_FILES['portada']['tmp_name'], $rutaDestino)){
            $ruta_portada = $rutaDestino;
        }
    }
    
    $sql = "INSERT INTO libros (titulo, año, id_autor, reseña, portada, id_genero) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("siissi", $titulo, $año, $id_autor, $reseña, $ruta_portada, $id_genero);
    $stmt->execute();
    
    header("Location: libros.php");
    exit;
}

// Consultas.
// ------------------------------------------
$resultado_libros = $conexion->query("
    SELECT l.*, a.nombre as autor_nombre 
    FROM libros l 
    JOIN autores a ON l.id_autor = a.id_autor
    JOIN generos g ON l.id_genero = g.id_genero
    ORDER BY l.titulo ASC
");

//$resultado_libros = $conexion->query("SELECT l.*, a.nombre as autor_nombre FROM libros l JOIN autores a ON l.id_autor = a.id_autor");
$autores = $conexion->query("SELECT * FROM autores");
$generos = $conexion->query("SELECT * FROM generos");

// Libros más prestados
// --------------------
$sql_top_libros = "
    SELECT l.titulo, COUNT(p.id_libro) AS total_prestamos
    FROM prestamos p
    JOIN libros l ON p.id_libro = l.id_libro
    GROUP BY l.id_libro
    ORDER BY total_prestamos DESC
    LIMIT 5";
$top_libros = $conexion->query($sql_top_libros);

// Usuarios con más préstamos
// --------------------------
$sql_top_usuarios = "
    SELECT u.nombre, COUNT(p.id_prestamo) AS total
    FROM prestamos p
    JOIN usuarios u ON p.id_usuario = u.id_usuario
    GROUP BY u.id_usuario
    ORDER BY total DESC
    LIMIT 5";
$top_usuarios = $conexion->query($sql_top_usuarios);

// Géneros más prestados
// ---------------------
$sql_top_generos = "
    SELECT g.nombre AS genero, COUNT(p.id_prestamo) AS total
    FROM prestamos p
    JOIN libros l ON p.id_libro = l.id_libro
    JOIN generos g ON l.id_genero = g.id_genero
    GROUP BY g.id_genero
    ORDER BY total DESC
    LIMIT 5";
$top_generos = $conexion->query($sql_top_generos);
?>

<!--============================================================================================-->
<!--                         Formulario de gestión de libros (html)                              -->
<!--============================================================================================-->
<!DOCTYPE html>
<html>  
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Libros">
    <link rel="stylesheet" href="CSS/libros.css">
    <title>Libreria el inge</title>
</head>

<header><h1>LIBRERÍA EL INGE</h1></header>

<h2>Gestión de Libros</h2>

<!-- **---------------------------** Formulario para agregar libros **---------------------------** -->
<!---------------------------------------------------------------------------------------------------->
<form method="post" enctype="multipart/form-data">
    <h3>Agregar libros</h3>

    <span>Título: </span><input type="text" name="titulo" required><br>
    <span>Año: </span><input type="number" name="año" required><br>
    <span>Reseña: </span><textarea name="reseña"></textarea><br>

    <span>Autor:</span>
    <select name="id_autor" required>
        <option value="">Seleccionar autor</option>
        <?php while($autor = $autores->fetch_assoc()): ?>
            <option value="<?php echo $autor['id_autor']; ?>"><?php echo $autor['nombre']; ?></option>
        <?php endwhile; ?>
    </select><br>

    <span>Género:</span>
        <select name="id_genero" required>
        <option value="">Seleccionar género</option>
        <?php while($gen = $generos->fetch_assoc()): ?>
            <option value="<?php echo $gen['id_genero']; ?>"><?php echo $gen['nombre']; ?></option>
        <?php endwhile; ?>
        </select><br>

    <!-- Opción de agregar libro -->
    <span>Portada:</span>
    <input type="file" name="portada" accept="image/*"><br><br>

    <input type="submit" value="AgregarLibro">
</form>

<!-- **----------------------------------** Estadísticas **--------------------------------------** -->
<!-- ---------------------------------------------------------------------------------------------- -->
 <h2>Estadísticas de la Biblioteca</h2>

<div class="estadisticas-container">

    <!-- Libros más prestados -->
    <div class="estadistica">
        <h3>Libros más prestados</h3>
        <table>
            <tr><th>Título</th><th>Total de Préstamos</th></tr>
            <?php if ($top_libros->num_rows > 0): ?>
                <?php while($row = $top_libros->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['titulo']; ?></td>
                        <td><?php echo $row['total_prestamos']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="2">Sin registros aún</td></tr>
            <?php endif; ?>
        </table>
    </div>

    <!-- Usuarios con más préstamos -->
    <div class="estadistica">
        <h3>Usuarios con más préstamos</h3>
        <table>
            <tr><th>Usuario</th><th>Total</th></tr>
            <?php if ($top_usuarios->num_rows > 0): ?>
                <?php while($row = $top_usuarios->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['nombre']; ?></td>
                        <td><?php echo $row['total']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="2">Sin registros aún</td></tr>
            <?php endif; ?>
        </table>
    </div>

    <!-- Géneros más populares -->
    <div class="estadistica">
        <h3>Géneros más populares</h3>
        <table>
            <tr><th>Género</th><th>Total</th></tr>
            <?php if ($top_generos->num_rows > 0): ?>
                <?php while($row = $top_generos->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['genero']; ?></td>
                        <td><?php echo $row['total']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="2">Sin registros aún</td></tr>
            <?php endif; ?>
        </table>
    </div>

</div>

<!-- **---------------------------** Tabla de libros existentes **---------------------------** -->
<!---------------------------------------------------------------------------------------------------->
<h3>Libros Existentes</h3>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Portada</th>
        <th>Título</th>
        <th>Año</th>
        <th>Autor</th>
        <th>Reseña</th>
        <th>Acciones</th>
    </tr>
    <?php while($libro = $resultado_libros->fetch_assoc()): ?>
    <tr>
        <td><?php echo $libro['id_libro']; ?></td>

        <td>
            <img src="<?php echo !empty($libro['portada']) ? $libro['portada'] : 'Portadas/default.jpg'; ?>" 
                 alt="Portada" class="mini-portada">
        </td>

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

<!-- **---------------------------** Tabla de usuarios existentes **---------------------------** -->
<!---------------------------------------------------------------------------------------------------->
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

<!--- Enlace para cerrar sesión     -->
<!------------------------------------>
<br>
<a href="logout.php" class="btn_CerrarSesion">Cerrar Sesión</a>
</html>