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

// Filtrar alumnos si se ha realizado una búsqueda
$search = $_GET['search'] ?? '';
$search_query = "SELECT id, nombre, apellido FROM public.alumnos";
if ($search) {
    $search_query .= " WHERE nombre ILIKE '%' || $1 || '%' OR apellido ILIKE '%' || $1 || '%'";
    $result = pg_query_params($conn, $search_query, array($search));
} else {
    $result = pg_query($conn, $search_query);
}

if (!$result) {
    die("Error al realizar la consulta.");
}

// Guardar o actualizar cada nota individual
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['notas'])) {
    foreach ($_POST['notas'] as $alumno_id => $notas) {
        $total = 0;
        $count = 0;

        for ($i = 1; $i <= 7; $i++) {
            $nota = isset($notas["nota_$i"]) ? (float)$notas["nota_$i"] : 0;
            $nota_column = "nota_$i";

            $query_nota = "UPDATE notas SET $nota_column = $1 WHERE alumno_id = $2";
            pg_query_params($conn, $query_nota, array($nota, $alumno_id));

            if ($nota > 0) {
                $total += $nota;
                $count++;
            }
        }

        $promedio = $count > 0 ? $total / $count : 0;
        $query_promedio = "UPDATE notas SET promedio = $1 WHERE alumno_id = $2";
        pg_query_params($conn, $query_promedio, array($promedio, $alumno_id));
    }
    echo "<script>alert('Notas registradas correctamente.'); window.location.href = 'notas_profesor.php';</script>";
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro de Notas</title>
    <link rel="stylesheet" href="./profesor.css/cursos.css">
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
    <h1 class="text-center mb-4">Registro de Notas</h1>

    <!-- Formulario de búsqueda -->
    <form method="GET" action="notas_profesor.php" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Buscar por nombre o apellido" value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-primary">Buscar</button>
        </div>
    </form>

    <h2 class="text-center mt-5">Lista de Alumnos</h2>
    <form method="POST" action="notas_profesor.php">
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <?php for ($i = 1; $i <= 7; $i++): ?>
                        <th>Nota <?= $i ?></th>
                    <?php endfor; ?>
                    <th>Promedio</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = pg_fetch_assoc($result)) {
                    $alumno_id = $row['id'];
                    $nombre = htmlspecialchars($row['nombre']);
                    $apellido = htmlspecialchars($row['apellido']);

                    $query_notas = "SELECT nota_1, nota_2, nota_3, nota_4, nota_5, nota_6, nota_7, promedio FROM notas WHERE alumno_id = $1";
                    $result_notas = pg_query_params($conn, $query_notas, array($alumno_id));
                    $notas_actuales = pg_fetch_assoc($result_notas);

                    echo "<tr>";
                    echo "<td>$nombre</td>";
                    echo "<td>$apellido</td>";
                    
                    for ($i = 1; $i <= 7; $i++) {
                        $nota_valor = $notas_actuales["nota_$i"] ?? '';
                        echo "<td><input type='number' step='0.01' name='notas[$alumno_id][nota_$i]' class='form-control' value='$nota_valor'></td>";
                    }

                    $promedio = $notas_actuales['promedio'] ?? 0;
                    echo "<td><input type='text' class='form-control' value='" . number_format($promedio, 2) . "' readonly></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <div class="text-center">
            <button type="submit" class="btn btn-primary mt-4">Registrar o Actualizar Notas</button>
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

<?php
pg_close($conn);
?>
