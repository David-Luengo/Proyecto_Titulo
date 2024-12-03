<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: /Proyecto_Titulo/index.html"); 
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
    die("Error de conexión: " . pg_last_error());
}

$usuario_actual = $_SESSION['usuario'];

// Obtener el ID del alumno basado en su correo
$query = "SELECT id, nombre, apellido FROM alumnos WHERE correo = $1";
$result = pg_query_params($conn, $query, array($usuario_actual));

if ($result && pg_num_rows($result) > 0) {
    $row = pg_fetch_assoc($result);
    $alumno_id = $row['id'];
    $nombre_alumno = $row['nombre'];
    $apellido_alumno = $row['apellido'];
} else {
    echo "Error al obtener los datos del alumno.";
    exit();
}

// Obtener el estado de asistencia del alumno en las fechas disponibles
$query_asistencia = "SELECT fecha, estado FROM asistencia WHERE alumno_id = $1 ORDER BY fecha DESC";
$result_asistencia = pg_query_params($conn, $query_asistencia, array($alumno_id));

pg_close($conn);
?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="./alumnos.css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <title>Estado de Asistencia</title>
</head>

<body>

    <nav class="navbar navbar-expand-lg p-3 fixed-top " id="menu" style="background-color: black; padding: 10px;">
        <div class="container-fluid">
            <a class="navbar-brand" href="index_alumnos.php">
                <img src="../img/icono.png" alt="" width="30" height="30" class="d-inline-block align-top">
                <span class="custom-text fs-5 fw-bold" style="color: white;">EduAdmin</span>
            </a>
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="asistencia_alumno.php" style="color: white;">Asistencia</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="notas_alumnos.php" style="color: white;">Notas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="materias.php" style="color: white;">Materias</a>
                </li>
            </ul>
    
            <a class="nav-link nav-item fs-6" href="../logout.php" style="color: white;">Cerrar Sesión</a>
        </div>
    </nav>
    
    <div class="container" style="margin-top: 100px;">
        <h3 class="text-center mb-4">Estado de Asistencia</h3>
        <p class="text-center"><strong>Nombre del alumno:</strong> <?php echo htmlspecialchars("$nombre_alumno $apellido_alumno"); ?></p>

        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Materia</th>
                    <th>Estado de Asistencia</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_asistencia && pg_num_rows($result_asistencia) > 0) {
                    while ($asistencia = pg_fetch_assoc($result_asistencia)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($asistencia['fecha']) . "</td>";
                        echo "<td>Lenguaje</td>"; // Materia fija como ejemplo
                        echo "<td><span class='badge bg-" . 
                             ($asistencia['estado'] == 'asistido' ? 'success' : ($asistencia['estado'] == 'ausente' ? 'danger' : 'secondary')) .
                             "'>" . htmlspecialchars($asistencia['estado']) . "</span></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3' class='text-center'>No hay registros de asistencia.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <footer class="fixed-bottom container-fluid bg-gray p-4">
        <div class="row">
            <div class="col-md-4">
                <h5 class="text-uppercase">Contacto</h5>
                <ul class="list-unstyled">
                    <li><i class="fas fa-envelope"></i> <a href="mailto:info@eduadmin.com" class="text-secondary">info@eduadmin.com</a></li>
                    <li><i class="fas fa-phone"></i> <a href="https://wa.me/56974394982" target="_blank" class="text-secondary">+56 9 7439 4982</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5 class="text-uppercase">Ayuda y Soporte</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-secondary">Preguntas frecuentes</a></li>
                    <li><a href="#" class="text-secondary">Manual de usuario</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5 class="text-uppercase">Desarrolladores</h5>
                <p class="text-secondary">Página desarrollada por David Luengo, Diego Lezana y Vicente Basaure</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</body>

</html>
