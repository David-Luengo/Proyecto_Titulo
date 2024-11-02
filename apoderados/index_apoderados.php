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

$query = "SELECT permiso, nombre, apellido, correo, numero, direccion, correo_personal FROM apoderados WHERE correo = $1";
$result = pg_query_params($conn, $query, array($usuario_actual));

if ($result && pg_num_rows($result) > 0) {
    $row = pg_fetch_assoc($result);
    $permiso = $row['permiso'];
    $nombre = $row['nombre'];
    $apellido = $row['apellido'];
    $correo = $row['correo'];
    $numero = $row['numero'];
    $direccion = $row['direccion'];
    $correo_personal = $row['correo_personal'];

    if (!$permiso) {
        echo "<script>alert('No tienes permiso para acceder a esta página.');</script>";
        header("Location: /Proyecto_titulo/sin_permiso.html");
        exit();
    }
} else {
    echo "Error al verificar el permiso.";
    exit();
}

// Procesar cambios al editar datos
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar'])) {
    $nuevo_nombre = $_POST['nombre'];
    $nuevo_apellido = $_POST['apellido'];
    $nuevo_correo = $_POST['correo'];
    $nuevo_numero = $_POST['numero'];
    $nueva_direccion = $_POST['direccion'];
    $nuevo_correo_personal = $_POST['correo_personal'];

    $query_update = "UPDATE apoderados SET nombre = $1, apellido = $2, correo = $3, numero = $4, direccion = $5, correo_personal = $6 WHERE correo = $7";
    $result_update = pg_query_params($conn, $query_update, array($nuevo_nombre, $nuevo_apellido, $nuevo_correo, $nuevo_numero, $nueva_direccion, $nuevo_correo_personal, $usuario_actual));

    if ($result_update) {
        echo "<script>alert('Datos actualizados correctamente.');</script>";
        // Actualizar variables con los nuevos valores
        $nombre = $nuevo_nombre;
        $apellido = $nuevo_apellido;
        $correo = $nuevo_correo;
        $numero = $nuevo_numero;
        $direccion = $nueva_direccion;
        $correo_personal = $nuevo_correo_personal;
    } else {
        echo "Error al actualizar los datos: " . pg_last_error($conn);
    }
}

pg_close($conn);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="./alumnos.css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />


    <title>Index</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg p-3 fixed-top" id="menu" style="background-color: black; padding: 10px;">
        <div class="container-fluid">
            <a class="navbar-brand" href="index_alumnos.php">
                <img src="../img/icono.png" alt="" width="30" height="30" class="d-inline-block align-top">
                <span class="custom-text fs-5 fw-bold" style="color: white;">EduAdmin</span>
            </a>
            <ul class="navbar-nav mr-auto">
                <li class="nav-item"><a class="nav-link" href="asistencia_apoderados.php" style="color:  white;">Asistencia alumno</a></li>
                <li class="nav-item"><a class="nav-link" href="notas_apoderados.php" style="color:  white;">Notas alumno</a></li>
                <li class="nav-item"><a class="nav-link" href="reuniones_apoderados.php" style="color:  white;">Reuniones</a></li>
                <li class="nav-item"><a class="nav-link" href="pago_matricula_apoderados.php" style="color:  white;">Pago de matriculas</a></li>
            </ul>
            <a class="nav-link nav-item fs-6" href="../logout.php" style="color: white;">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="container profile-container" style="display: flex; width: 75%; margin-top: 100px; padding: 20px; background-color: #fff; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); border-radius: 8px;">
        <div class="profile-info" style="flex: 2; padding: 20px;">
            <div class="row">
                <div class="col-4">
                    <img src="../img/alumno_ico.png" alt=""  width="350" height="350">
                </div>
                <div class="col-4">
                    <h1>Perfil de Usuario</h1>
                    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($nombre); ?></p>
                    <p><strong>Apellido:</strong> <?php echo htmlspecialchars($apellido); ?></p>
                    <p><strong>Correo:</strong> <?php echo htmlspecialchars($correo); ?></p>
                    <p><strong>Número:</strong> <?php echo htmlspecialchars($numero); ?></p>
                    <p><strong>Dirección:</strong> <?php echo htmlspecialchars($direccion); ?></p>
                    <p><strong>Correo Personal:</strong> <?php echo htmlspecialchars($correo_personal); ?></p>
                    <!-- Botón para abrir el modal de edición -->
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editarModal">Editar Datos</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Edición -->
    <div class="modal fade" id="editarModal" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarModalLabel">Editar Datos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="index_apoderados.php">
                    <div class="modal-body">
                        <label for="nombre"><strong>Nombre:</strong></label>
                        <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($nombre); ?>" class="form-control" required>

                        <label for="apellido"><strong>Apellido:</strong></label>
                        <input type="text" name="apellido" id="apellido" value="<?php echo htmlspecialchars($apellido); ?>" class="form-control" required>

                        <label for="correo"><strong>Correo:</strong></label>
                        <input type="email" name="correo" id="correo" value="<?php echo htmlspecialchars($correo); ?>" class="form-control" required>

                        <label for="numero"><strong>Número:</strong></label>
                        <input type="text" name="numero" id="numero" value="<?php echo htmlspecialchars($numero); ?>" class="form-control" required>

                        <label for="direccion"><strong>Dirección:</strong></label>
                        <input type="text" name="direccion" id="direccion" value="<?php echo htmlspecialchars($direccion); ?>" class="form-control" required>

                        <label for="correo_personal"><strong>Correo Personal:</strong></label>
                        <input type="email" name="correo_personal" id="correo_personal" value="<?php echo htmlspecialchars($correo_personal); ?>" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="guardar" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>

</html>