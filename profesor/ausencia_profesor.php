<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: /Proyecto_titulo/index.html");
    exit();
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="./profesor.css/cursos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <title>Notificar Ausencia</title>
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
            <a class="nav-link nav-item fs-6" href="../logout.php" style="color: white;">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="container" style="margin-top: 100px;">
        <h2>Notificar Ausencia a Apoderados</h2>

        <!-- Formulario de Búsqueda de Apoderados -->
        <div class="container" style="margin-top: 30px;">
            <h2>Buscar Apoderados</h2>
            <form action="" method="GET" class="mb-3">
                <div class="input-group" style="padding: 20px 0px 20px 0px;">
                    <input type="text" name="search" class="form-control" placeholder="Ingrese el nombre o correo" required>
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </form>
        </div>

        <?php
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

        if (isset($_GET['search'])) {
            $search = pg_escape_string($conn, $_GET['search']);

            $query_apoderados = "SELECT id, nombre, apellido, correo, correo_personal, numero, region, comuna, direccion 
                                 FROM apoderados 
                                 WHERE nombre ILIKE '%$search%' OR apellido ILIKE '%$search%' OR correo ILIKE '%$search%'";

            $result_apoderados = pg_query($conn, $query_apoderados);

            if ($result_apoderados && pg_num_rows($result_apoderados) > 0) {
                echo '<form action="enviar_correo.php" method="POST">';
                echo '<div class="container"><table class="table">';
                echo '<thead><tr><th>Seleccionar</th><th>Nombre</th><th>Apellido</th><th>Correo</th><th>Correo Personal</th><th>Número</th><th>Dirección</th></tr></thead>';
                echo '<tbody>';

                while ($row = pg_fetch_assoc($result_apoderados)) {
                    echo '<tr>';
                    echo '<td><input type="checkbox" name="apoderados[]" value="' . htmlspecialchars($row['correo_personal']) . '"></td>';
                    echo '<td>' . htmlspecialchars($row['nombre']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['apellido']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['correo']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['correo_personal']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['numero']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['direccion']) . ', ' . htmlspecialchars($row['comuna']) . ', ' . htmlspecialchars($row['region']) . '</td>';
                    echo '</tr>';
                }

                echo '</tbody></table></div>';
            } else {
                echo '<div class="container"><h3>No se encontraron resultados.</h3></div>';
            }
        }

        pg_close($conn);
        ?>

        <!-- Formulario para Enviar Correo -->
        <div class="mb-3" style="margin-top: 20px;">
            <label for="asunto" class="form-label">Asunto</label>
            <input type="text" class="form-control" id="asunto" name="asunto" required>
        </div>
        <div class="mb-3" style="margin-top: 20px;">
            <label for="mensaje" class="form-label">Mensaje</label>
            <textarea class="form-control" id="mensaje" name="mensaje" rows="5" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Enviar Correo</button>
        </form> <!-- Fin del formulario de envío de correo -->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <footer class="sticky-footer container-fluid bg-gray p-4">
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
</body>

</html>