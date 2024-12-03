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

// Consultar el permiso y el ID del alumno
$query_permiso = "SELECT permiso, id FROM alumnos WHERE correo = $1";
$result_permiso = pg_query_params($conn, $query_permiso, array($usuario_actual));

if ($result_permiso && pg_num_rows($result_permiso) > 0) {
    $row_permiso = pg_fetch_assoc($result_permiso);
    $permiso = $row_permiso['permiso'];
    $alumno_id = $row_permiso['id'];

    if (!$permiso) {
        echo "<script>alert('No tienes permiso para acceder a esta página.');</script>";
        header("Location: /Proyecto_titulo/sin_permiso.html");
        exit();
    }

    // Obtener las notas y el promedio almacenado del alumno
    $query_notas = "SELECT nota_1, nota_2, nota_3, nota_4, nota_5, nota_6, nota_7, promedio FROM notas WHERE alumno_id = $1";
    $result_notas = pg_query_params($conn, $query_notas, array($alumno_id));
    
    $notas = [];
    $promedio = 0;

    if ($result_notas && pg_num_rows($result_notas) > 0) {
        $row_notas = pg_fetch_assoc($result_notas);
        for ($i = 1; $i <= 7; $i++) {
            $notas[] = $row_notas["nota_$i"] ?? '-';
        }
        $promedio = $row_notas['promedio'];
    }
} else {
    echo "Error al verificar el permiso.";
    exit();
}

pg_close($conn);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notas del Alumno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg p-3 fixed-top " id="menu" style="background-color: black; padding: 10px;">
        <div class="container-fluid">
            <a class="navbar-brand" href="index_alumnos.php">
                <img src="../img/icono.png" alt="" width="30" height="30" class="d-inline-block align-top">
                <span class="custom-text fs-5 fw-bold" style="color:  white;">EduAdmin</span>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="asistencia_alumno.php" style="color:  white;">Asistencia</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="notas_alumnos.php" style="color:  white;">Notas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="materias.php" style="color:  white;">Materias</a>
                </li>
            </ul>
    
            <a class="nav-link nav-item fs-6" href="../logout.php" style="color: white;">Cerrar Sesión</a>
        </div>
    </nav>
    

    <div class="container" style="margin-top: 150px;">
        <h3>Notas del estudiante</h3>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Materia</th>
                    <?php for ($i = 1; $i <= 7; $i++): ?>
                        <th>Nota <?= $i ?></th>
                    <?php endfor; ?>
                    <th>Promedio</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Lenguaje</td>
                    <?php foreach ($notas as $nota): ?>
                        <td><?= $nota ?></td>
                    <?php endforeach; ?>
                    <td><?= number_format($promedio, 2) ?></td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td>Matemática</td>
                    <?php foreach ($notas as $nota): ?>
                        <td><?= $nota ?></td>
                    <?php endforeach; ?>
                    <td><?= number_format($promedio, 2) ?></td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td>Historia</td>
                    <?php foreach ($notas as $nota): ?>
                        <td><?= $nota ?></td>
                    <?php endforeach; ?>
                    <td><?= number_format($promedio, 2) ?></td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td>Ciencias Naturales</td>
                    <?php foreach ($notas as $nota): ?>
                        <td><?= $nota ?></td>
                    <?php endforeach; ?>
                    <td><?= number_format($promedio, 2) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <footer class="fixed-bottom container-fluid bg-gray p-4">
        <div class="row">
            <div class="col-md-4">
                <h5 class="text-uppercase">Contacto</h5>
                <ul class="list-unstyled">
                    <li><i class="fas fa-envelope"></i> <a href="mailto:info@eduadmin.com"
                            class="text-secondary">info@eduadmin.com</a></li>
                    <li><i class="fas fa-phone"></i> <a href="https://wa.me/56974394982" target="_blank"
                            class="text-secondary">+56 9 7439 4982</a></li>
                    <li><i class="fab fa-facebook-f"></i> <a href="https://www.facebook.com/eduadmin" target="_blank"
                            class="text-secondary">Facebook</a></li>
                    <li><i class="fab fa-instagram"></i> <a href="https://instagram.com/eduadmin" target="_blank"
                            class="text-secondary">Instagram</a></li>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
