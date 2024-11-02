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

$usuario_actual = $_SESSION['usuario'];

// Verificar permisos
$query = "SELECT permiso FROM alumnos WHERE correo = $1"; 
$result = pg_query_params($conn, $query, array($usuario_actual));

if ($result && pg_num_rows($result) > 0) {
    $row = pg_fetch_assoc($result);
    $permiso = $row['permiso'];

    if (!$permiso) {
        echo "<script>alert('No tienes permiso para acceder a esta página.');</script>";
        header("Location: /Proyecto_titulo/sin_permiso.html");
        exit();
    }
} else {
    echo "Error al verificar el permiso.";
    exit();
}

// Obtener todos los archivos agrupados por unidad
$archivos = [];
$query = "SELECT * FROM primero_a ORDER BY unidad, fecha_subida DESC";
$result = pg_query($conn, $query);
if ($result) {
    while ($row = pg_fetch_assoc($result)) {
        $archivos[$row['unidad']][] = $row;
    }
}

pg_close($conn);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Visualización de Archivos - Alumno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg p-3 fixed-top" id="menu" style="background-color: black; padding: 10px;">
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

<div class="container" style="margin-top: 120px;">
    <h2>Archivos Disponibles - Curso Primero A</h2>

    <div class="accordion" id="courseAccordion" style="padding: 20px;">
        <?php for ($i = 1; $i <= 6; $i++): ?>
            <?php $unidad = "Unidad $i"; ?>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading<?= $i ?>">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $i ?>" aria-expanded="false" aria-controls="collapse<?= $i ?>">
                        <?= $unidad ?>
                    </button>
                </h2>
                <div id="collapse<?= $i ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $i ?>" data-bs-parent="#courseAccordion">
                    <div class="accordion-body">
                        <!-- Mostrar archivos de la unidad actual -->
                        <h5 class="mt-4">Archivos Subidos en <?= $unidad ?>:</h5>
                        <?php if (isset($archivos[$unidad])): ?>
                            <ul class="list-group">
                                <?php foreach ($archivos[$unidad] as $archivo): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?= htmlspecialchars($archivo['nombre_archivo']) ?></strong><br>
                                            <small><?= htmlspecialchars($archivo['descripcion']) ?></small><br>
                                            <small><em>Subido el: <?= $archivo['fecha_subida'] ?></em></small>
                                        </div>
                                        <div>
                                            <a href="<?= htmlspecialchars($archivo['ruta_archivo']) ?>" class="btn btn-sm btn-primary" download>Descargar</a>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>No hay archivos subidos para esta unidad.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endfor; ?>
    </div>
</div>

<footer class="fixed-bottom container-fluid bg-gray p-4">
    <div class="row">
        <div class="col-md-4">
            <h5 class="text-uppercase">Contacto</h5>
            <ul class="list-unstyled">
                <li><i class="fas fa-envelope"></i> <a href="mailto:info@eduadmin.com" class="text-secondary">info@eduadmin.com</a></li>
                <li><i class="fas fa-phone"></i> <a href="https://wa.me/56974394982" target="_blank" class="text-secondary">+56 9 7439 4982</a></li>
                <li><i class="fab fa-facebook-f"></i> <a href="https://www.facebook.com/eduadmin" target="_blank" class="text-secondary">Facebook</a></li>
                <li><i class="fab fa-instagram"></i> <a href="https://instagram.com/eduadmin" target="_blank" class="text-secondary">Instagram</a></li>
            </ul>
        </div>
        <div class="col-md-4">
            <h5 class="text-uppercase">Ayuda y Soporte</h5>
            <ul class="list-unstyled">
                <li><a href="#" class="text-secondary">Preguntas frecuentes</a></li>
                <li><a href="#" class="text-secondary">Manual de usuario</a></li>
                <li><a href="#" class="text-secondary">Soporte técnico</a></li>
            </ul>
        </div>
        <div class="col-md-4">
            <h5 class="text-uppercase">Desarrolladores</h5>
            <p class="text-secondary">Página desarrollada por David Luengo, Diego Lezana y Vicente Basaure</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
