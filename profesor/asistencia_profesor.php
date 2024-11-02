<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: /Proyecto_titulo/index.html");
    exit();
}

$host = "localhost";
$port = "5432";
$dbname = "Proyecto_Titulo";
$user = "postgres";
$password = "123456";

$conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password";
$conn = pg_connect($conn_string);

if (!$conn) {
    die("Error al conectar con la base de datos.");
}

// Obtener los nombres y apellidos de los alumnos
$query = "SELECT id, nombre, apellido FROM public.alumnos";
$result = pg_query($conn, $query);

if (!$result) {
    die("Error al realizar la consulta.");
}

// Obtener la fecha actual
$fecha_actual = date("Y-m-d");
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrar Asistencia</title>
    <link rel="stylesheet" href="./profesor.css/cursos.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg p-3 fixed-top" id="menu" style="background-color: black; padding: 10px;">
    <div class="container-fluid">
        <a class="navbar-brand" href="index_profesor.php">
            <img src="../img/icono.png" alt="" width="30" height="30" class="d-inline-block align-top">
            <span class="custom-text fs-5 fw-bold" style="color: white;">EduAdmin</span>
        </a>
        <ul class="navbar-nav mr-auto">
            <li class="nav-item"><a class="nav-link" href="cursos_profesor.php" style="color: white;">Cursos</a></li>
            <li class="nav-item"><a class="nav-link" href="reuniones_profesor.php" style="color: white;">Reuniones</a></li>
            <li class="nav-item"><a class="nav-link" href="asistencia_profesor.php" style="color: white;">Asistencia</a></li>
        </ul>
        <a class="nav-link nav-item fs-6" href="./ausencia_profesor.php" style="color: white;">En caso de ausencia click aquí</a>
        <a class="nav-link nav-item fs-6" href="../logout.php" style="color: white;">Cerrar Sesión</a>
    </div>
</nav>

<div class="container mt-5 pt-5">
    <h1 class="text-center mb-4">Registrar Asistencia</h1>

    <!-- Mostrar la fecha de hoy -->
    <h3 class="text-center text-muted">Fecha: <?php echo date("d-m-Y"); ?></h3>
    
    <h2 class="text-center mt-5">Lista de Alumnos</h2>
    <form method="POST" action="procesar_asistencia.php">
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Registrar Asistencia</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Mostrar los nombres y apellidos de los alumnos
                while ($row = pg_fetch_assoc($result)) {
                    $alumno_id = $row['id'];
                    $nombre = htmlspecialchars($row['nombre']);
                    $apellido = htmlspecialchars($row['apellido']);

                    // Verificar si ya existe un registro de asistencia para el alumno en la fecha actual (solo el día)
                    $query_asistencia = "SELECT * FROM asistencia WHERE alumno_id = $1 AND DATE(fecha) = CURRENT_DATE";
                    $result_asistencia = pg_query_params($conn, $query_asistencia, array($alumno_id));

                    $asistencia_registrada = pg_num_rows($result_asistencia) > 0;

                    echo "<tr>";
                    echo "<td>$nombre</td>";
                    echo "<td>$apellido</td>";
                    if ($asistencia_registrada) {
                        // Desactivar la casilla si ya se registró asistencia
                        echo "<td><input type='checkbox' disabled></td>";
                        echo "<td><span class='badge bg-success'>Asistencia registrada</span></td>";
                    } else {
                        echo "<td><input type='checkbox' name='alumnos[]' value='$alumno_id'></td>";
                        echo "<td><span class='badge bg-warning'>No registrada</span></td>";
                    }
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <div class="text-center">
            <button type="submit" class="btn btn-primary mt-4">Registrar Asistencia</button>
        </div>
    </form>
</div>

<footer class="sticky-footer container-fluid bg-gray p-4">
    <div class="row">
        <div class="col-md-4"><h5 class="text-uppercase">Contacto</h5><ul class="list-unstyled"><li><i class="fas fa-envelope"></i> <a href="mailto:info@eduadmin.com" class="text-secondary">info@eduadmin.com</a></li><li><i class="fas fa-phone"></i> <a href="https://wa.me/56974394982" target="_blank" class="text-secondary">+56 9 7439 4982</a></li></ul></div>
        <div class="col-md-4"><h5 class="text-uppercase">Ayuda y Soporte</h5><ul class="list-unstyled"><li><a href="#" class="text-secondary">Preguntas frecuentes</a></li><li><a href="#" class="text-secondary">Manual de usuario</a></li></ul></div>
        <div class="col-md-4"><h5 class="text-uppercase">Desarrolladores</h5><p class="text-secondary">Página desarrollada por David Luengo, Diego Lezana y Vicente Basaure</p></div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
