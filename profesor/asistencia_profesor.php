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
    die("Error de conexión: " . pg_last_error());
}

// Obtener la fecha actual
$fecha_actual = date("Y-m-d");

// Filtrar por búsqueda de nombre o apellido si se envía
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$query = "SELECT id, nombre, apellido FROM public.alumnos";
$params = [];

if ($search_term) {
    $query .= " WHERE nombre ILIKE $1 OR apellido ILIKE $1";
    $params[] = '%' . $search_term . '%';
}

$result = pg_query_params($conn, $query, $params);

if (!$result) {
    die("Error al realizar la consulta.");
}
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
    <style>
        .form-check-input[type="radio"] {
            border-radius: 50%;
            width: 20px;
            height: 20px;
        }
    </style>
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

    <!-- Formulario de búsqueda -->
    <form class="d-flex justify-content-center mb-4" method="GET" action="asistencia_profesor.php">
        <input type="text" name="search" class="form-control w-50" placeholder="Buscar por nombre o apellido" value="<?php echo htmlspecialchars($search_term); ?>">
        <button type="submit" class="btn btn-primary ms-2">Buscar</button>
    </form>

    <h2 class="text-center mt-5">Lista de Alumnos</h2>
    <form method="POST" action="procesar_asistencia.php">
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Asistencia</th>
                    <th>Estado Actual</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Mostrar los nombres y apellidos de los alumnos
                while ($row = pg_fetch_assoc($result)) {
                    $alumno_id = $row['id'];
                    $nombre = htmlspecialchars($row['nombre']);
                    $apellido = htmlspecialchars($row['apellido']);

                    // Verificar el estado actual de la asistencia
                    $query_asistencia = "SELECT estado FROM asistencia WHERE alumno_id = $1 AND fecha = $2";
                    $result_asistencia = pg_query_params($conn, $query_asistencia, array($alumno_id, $fecha_actual));
                    $estado_asistencia = ($result_asistencia && pg_num_rows($result_asistencia) > 0) ? pg_fetch_result($result_asistencia, 0, 'estado') : 'sin_asignar';

                    echo "<tr>";
                    echo "<td>$nombre</td>";
                    echo "<td>$apellido</td>";
                    echo "<td>
                            <div class='d-flex justify-content-around'>
                                <div class='form-check'>
                                    <input class='form-check-input' type='radio' name='asistencia[$alumno_id]' id='asistido_$alumno_id' value='asistido' " . ($estado_asistencia == 'asistido' ? 'checked' : '') . ">
                                    <label class='form-check-label text-success' for='asistido_$alumno_id'>Asistido</label>
                                </div>
                                <div class='form-check'>
                                    <input class='form-check-input' type='radio' name='asistencia[$alumno_id]' id='ausente_$alumno_id' value='ausente' " . ($estado_asistencia == 'ausente' ? 'checked' : '') . ">
                                    <label class='form-check-label text-danger' for='ausente_$alumno_id'>Ausente</label>
                                </div>
                                <div class='form-check'>
                                    <input class='form-check-input' type='radio' name='asistencia[$alumno_id]' id='sin_asignar_$alumno_id' value='sin_asignar' " . ($estado_asistencia == 'sin_asignar' ? 'checked' : '') . ">
                                    <label class='form-check-label text-secondary' for='sin_asignar_$alumno_id'>Sin Asignar</label>
                                </div>
                            </div>
                          </td>";
                    echo "<td><span class='badge bg-" . ($estado_asistencia == 'asistido' ? 'success' : ($estado_asistencia == 'ausente' ? 'danger' : 'secondary')) . "'>$estado_asistencia</span></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <div class="text-center">
            <button type="submit" class="btn btn-primary mt-4">Guardar Asistencia</button>
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
