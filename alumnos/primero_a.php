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
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <title>Index</title>
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
    

    <style>
        .unit-content {
            display: none;
            padding: 10px;
            background-color: #f3f3f3;
            margin-top: 5px;
        }

        .unit-header {
            cursor: pointer;
            margin-bottom: 10px;
        }

        .chevron-down {
            transform: rotate(90deg);
            transition: transform 0.3s;
        }

        .chevron-right {
            transition: transform 0.3s;
        }
    </style>

    <div class="container" style="margin-top: 120px;">

        <div>
            <!-- Unidad 2 -->
            <a class="bg-gray-200 p-4 flex justify-between items-center unit-header"
                style="color: rgb(0, 0, 0); text-decoration: none;">
                <span>UNIDAD 1</span>
                <i class="fas fa-chevron-right chevron-icon"></i>
            </a>
            <div class="unit-content">
                <p>Este es el contenido de la Unidad 1.</p>
            </div>
        </div>

        <div>
            <!-- Unidad 2 -->
            <a class="bg-gray-200 p-4 flex justify-between items-center unit-header"
                style="color: rgb(0, 0, 0); text-decoration: none;">
                <span>UNIDAD 2</span>
                <i class="fas fa-chevron-right chevron-icon"></i>
            </a>
            <div class="unit-content">
                <p>Este es el contenido de la Unidad 2.</p>
            </div>
        </div>

        <div>
            <!-- Unidad 3 -->
            <a class="bg-gray-200 p-4 flex justify-between items-center unit-header"
                style="color: rgb(0, 0, 0); text-decoration: none;">
                <span>UNIDAD 3</span>
                <i class="fas fa-chevron-right chevron-icon"></i>
            </a>
            <div class="unit-content">
                <p>Este es el contenido de la Unidad 3.</p>
            </div>
        </div>

        <div>
            <!-- Unidad 4 -->
            <a class="bg-gray-200 p-4 flex justify-between items-center unit-header"
                style="color: rgb(0, 0, 0); text-decoration: none;">
                <span>UNIDAD 4</span>
                <i class="fas fa-chevron-right chevron-icon"></i>
            </a>
            <div class="unit-content">
                <p>Este es el contenido de la Unidad 4.</p>
            </div>
        </div>

        <div>
            <!-- Unidad 5 -->
            <a class="bg-gray-200 p-4 flex justify-between items-center unit-header"
                style="color: rgb(0, 0, 0); text-decoration: none;">
                <span>UNIDAD 5</span>
                <i class="fas fa-chevron-right chevron-icon"></i>
            </a>
            <div class="unit-content">
                <p>Este es el contenido de la Unidad 5.</p>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <script>
        // Obtener todos los encabezados de las unidades
        const unitHeaders = document.querySelectorAll('.unit-header');

        unitHeaders.forEach(header => {
            header.addEventListener('click', function () {
                const content = this.nextElementSibling;
                const chevronIcon = this.querySelector('.chevron-icon');

                // Alternar visibilidad del contenido
                if (content.style.display === "none" || content.style.display === "") {
                    content.style.display = "block";
                    chevronIcon.classList.add('chevron-down');
                } else {
                    content.style.display = "none";
                    chevronIcon.classList.remove('chevron-down');
                }
            });
        });
    </script>
</body>

</html>