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

// Manejo de la subida de archivos
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['archivo']) && isset($_POST['unidad'])) {
    $nombreArchivo = $_FILES['archivo']['name'];
    $rutaTemporal = $_FILES['archivo']['tmp_name'];
    $unidad = $_POST['unidad'];
    $descripcion = $_POST['descripcion'] ?? '';

    $directorioSubida = "uploads/";
    if (!file_exists($directorioSubida)) {
        mkdir($directorioSubida, 0777, true);
    }

    $rutaArchivo = $directorioSubida . basename($nombreArchivo);

    if (move_uploaded_file($rutaTemporal, $rutaArchivo)) {
        $fechaSubida = date("Y-m-d H:i:s");
        $query = "INSERT INTO primero_a (nombre_archivo, ruta_archivo, fecha_subida, descripcion, unidad) VALUES ($1, $2, $3, $4, $5)";
        $result = pg_query_params($conn, $query, array($nombreArchivo, $rutaArchivo, $fechaSubida, $descripcion, $unidad));

        if ($result) {
            echo "<script>alert('Archivo subido y guardado en la base de datos correctamente.');</script>";
        } else {
            echo "<p>Error al guardar en la base de datos: " . pg_last_error($conn) . "</p>";
        }
    } else {
        echo "<p>Error al subir el archivo.</p>";
    }
}

// Manejo de la eliminación de archivos
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Obtener la ruta del archivo a eliminar
    $query = "SELECT ruta_archivo FROM primero_a WHERE id = $1";
    $result = pg_query_params($conn, $query, array($id));
    if ($result) {
        $file = pg_fetch_assoc($result);
        $rutaArchivo = $file['ruta_archivo'];

        // Eliminar el archivo físico
        if (file_exists($rutaArchivo)) {
            unlink($rutaArchivo);
        }

        // Eliminar el registro de la base de datos
        $query = "DELETE FROM primero_a WHERE id = $1";
        $result = pg_query_params($conn, $query, array($id));
        
        if ($result) {
            echo "<script>alert('Archivo eliminado correctamente.');</script>";
        } else {
            echo "<p>Error al eliminar el archivo de la base de datos: " . pg_last_error($conn) . "</p>";
        }
    }
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
    <title>Subida y Gestión de Archivos - Profesor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg p-3 fixed-top " id="menu" style="background-color: black; padding: 10px;">
    <div class="container-fluid">
      <a class="navbar-brand" href="index_profesor.php">
        <img src="../img/icono.png" alt="" width="30" height="30" class="d-inline-block align-top">
        <span class="custom-text fs-5 fw-bold" style="color:  white;">EduAdmin</span>
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <ul class="navbar-nav mr-auto">
        <li class="nav-item">
          <a class="nav-link" href="cursos_profesor.php" style="color:  white;">Cursos</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="reuniones_profesor.php" style="color:  white;">Reuniones</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="asistencia_profesor.php" style="color:  white;">Asistencia</a>
      </ul>

      <a class="nav-link nav-item fs-6" href="./ausencia_profesor.php" style="color: white;">En caso de ausencia click aquí</a>
      <a class="nav-link nav-item fs-6" href="../logout.php" style="color: white;">Cerrar Sesión</a>
    </div>
  </nav>
    <div class="container " style="margin-top: 100px ;">
        <h2>Subida y Gestión de Archivos para el Curso - Primero A</h2>

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
                            <!-- Formulario para subir archivos -->
                            <form action="primero_a.php" method="post" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="archivo" class="form-label">Subir Archivo</label>
                                    <input type="file" class="form-control" name="archivo" required>
                                    <input type="hidden" name="unidad" value="<?= $unidad ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="descripcion" class="form-label">Descripción (opcional)</label>
                                    <textarea class="form-control" name="descripcion" rows="3"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Subir</button>
                            </form>

                            <!-- Lista de archivos subidos -->
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
                                                <a href="<?= htmlspecialchars($archivo['ruta_archivo']) ?>" class="btn btn-sm btn-success" download>Descargar</a>
                                                <a href="primero_a.php?delete=<?= $archivo['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este archivo?');">Eliminar</a>
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
    
</body>
</html>
