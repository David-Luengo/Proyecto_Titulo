<?php
session_start();

// Verificar si la sesión está iniciada
if (!isset($_SESSION['usuario'])) {
    // Si no hay sesión iniciada, redirigir al login
    header("Location: /Proyecto_titulo/index.html"); 
    exit();
}

// Conectar a la base de datos
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

// Obtener el correo o identificador del usuario actual desde la sesión
$usuario_actual = $_SESSION['usuario'];

// Consultar el valor de 'permiso' para el alumno
$query = "SELECT permiso FROM alumnos WHERE correo = $1"; 
$result = pg_query_params($conn, $query, array($usuario_actual));

if ($result && pg_num_rows($result) > 0) {
    $row = pg_fetch_assoc($result);
    $permiso = $row['permiso'];

    // Verificar si el permiso es 'false'
    if (!$permiso) {
        // Si no tiene permiso, redirigir a otra página o mostrar un mensaje de error
        echo "<script>alert('No tienes permiso para acceder a esta página.');</script>";
        header("Location: /Proyecto_titulo/sin_permiso.html"); // Redirigir a una página de error o salida
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
    
            <!-- Actualiza el enlace a logout.php -->
            <a class="nav-link nav-item fs-6" href="../logout.php" style="color: white;">Cerrar Sesión</a>
        </div>
    </nav>
    

    <title>Perfil de Usuario</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            height: 80vh;
            background-color: #f4f4f4;
        }

        .profile-container {
            display: flex;
            width: 75%;
            margin: auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .profile-info {
            flex: 2;
            padding: 20px;
        }

        .profile-info img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
        }

        .profile-info h1 {
            margin-top: 10px;
            font-size: 24px;
            color: #333;
        }

        .profile-info p {
            margin: 8px 0;
            color: #666;
        }

        .profile-info a {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 16px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
        }

        .profile-info a:hover {
            background-color: #0056b3;
        }

        .dashboard {
            flex: 1;
            padding: 20px;
            background-color: #f8f9fa;
            border-left: 1px solid #ddd;
        }

        .dashboard h3 {
            margin-bottom: 20px;
            color: #333;
        }

        .dashboard ul {
            list-style: none;
            padding: 0;
        }

        .dashboard ul li {
            margin: 10px 0;
            padding: 10px;
            background-color: #e9ecef;
            border-radius: 4px;
        }
    </style>

    <div class="profile-container">
        <div class="profile-info">
            <img src="../img/alumnos.jpg" alt="Foto de perfil">
            <h1>Juan Pérez</h1>
            <p><strong>Apellido:</strong> Pérez</p>
            <p><strong>Correo:</strong> juan.perez@example.com</p>
            <p><strong>Número Telefónico:</strong> +569 1234 5678</p>
            <p><strong>Dirección:</strong> Calle Falsa 123, Ciudad</p>
            <a href="editar-datos.html">Editar Datos</a>
        </div>

        <div class="dashboard">
            <h3>Dashboard</h3>
            <ul>
                <li>Lenguaje</li>
                <li>Matemática</li>
                <li>Historia</li>
                <li>Ciencias Naturales</li>
                <li>Física</li>
            </ul>
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

</body>

</html>
