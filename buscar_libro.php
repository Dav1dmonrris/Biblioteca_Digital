<!--============================================================================================-->
<!--                                  Catálogo de Libros (php)                                  -->
<!--============================================================================================-->
<?php
session_start();
if(!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'usuario'){
    header("Location: login.php");
    exit;
}

include('Conexion.php');                       # <- Incluir el archivo de conexión a la base de datos
$conexion = conexion();                        # <- Establecer la conexión a la base de datos.

// OBTENER LIBROS PARA LA TABLA
//------------------------------*
$resultado_libros = $conexion->query("SELECT l.titulo, a.nombre as autor, l.año as fecha_publicacion 
                                     FROM libros l 
                                     JOIN autores a ON l.id_autor = a.id_autor");
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

    <!--============================================================================-->
    <!-- TABLA DE LIBROS  -->
    <!--
    <center>
    <h3>Catálogo de Libros</h3>
    <table border="1" style="margin: 20px auto;">
        <tr>
            <th>Libro</th>
            <th>Autor</th>
            <th>Fecha de Publicación</th>
        </tr>
        <?php //while($libro = $resultado_libros->fetch_assoc()): ?>
        <tr>
            <td><?php //echo $libro['titulo']; ?></td>
            <td><?php //echo $libro['autor']; ?></td>
            <td><?php //echo $libro['fecha_publicacion']; ?></td>
        </tr>
        <?php //endwhile; ?>
    </table>
    </center>
        -->
    <!--============================================================================-->
    <center>
    <h3>Catálogo de Libros</h3>
    <div class="libros-container">
        <?php while($libro = $resultado_libros->fetch_assoc()): ?>
        <div class="libro-card">
            <h4><?php echo $libro['titulo']; ?></h4>
            <p><strong>Autor:</strong> <?php echo $libro['autor']; ?></p>
            <p><strong>Año de publicación:</strong> <?php echo $libro['fecha_publicacion']; ?></p>
        </div>
        <?php endwhile; ?>
    </div>
    </center>


</body>
</html>