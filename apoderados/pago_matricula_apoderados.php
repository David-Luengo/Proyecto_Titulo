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

$query = "SELECT permiso FROM apoderados WHERE correo = $1";
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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pago de Matrículas</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Flatpickr CSS for the calendar -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Flatpickr Plugin for Month Selection -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">

    <link rel="stylesheet" href="./alumnos.css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>

    <nav class="navbar navbar-expand-lg p-3 fixed-top " id="menu" style="background-color: black; padding: 10px;">
        <div class="container-fluid">
            <a class="navbar-brand" href="index_apoderados.php">
                <img src="../img/icono.png" alt="" width="30" height="30" class="d-inline-block align-top">
                <span class="custom-text fs-5 fw-bold" style="color:  white;">EduAdmin</span>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="asistencia_apoderados.php" style="color:  white;">Asistencia alumno</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="notas_apoderados.php" style="color:  white;">Notas alumno</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="reuniones_apoderados.php" style="color:  white;">Reuniones</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pago_matricula_apoderados.php" style="color:  white;">Pago de matrículas</a>
                </li>
            </ul>
    
            <a class="nav-link nav-item fs-6" href="../logout.php" style="color: white;">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="container " style="margin-top: 100px;">
        <h2>Pago de Matrícula</h2>
        <p>Seleccione el mes para realizar el pago de la mensualidad:</p>
        
        <!-- Calendar for selecting the payment month -->
        <label for="paymentDate">Mes de Pago:</label>
        <input type="text" id="paymentDate" class="form-control mb-3" placeholder="Seleccionar mes">

        <!-- Button to proceed with Webpay -->
        <button id="payButton" class="btn btn-primary">Pagar Mensualidad</button>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Flatpickr JS for the calendar -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- Flatpickr Plugin for Month Selection -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>

    <script>
        // Initialize the calendar to allow month selection only
        flatpickr("#paymentDate", {
            altInput: true,
            altFormat: "F Y", // Format to display only month and year
            dateFormat: "Y-m", // Format to store as year-month
            defaultDate: new Date(),
            plugins: [new monthSelectPlugin({
                shorthand: true,
                dateFormat: "Y-m", // Format to store as year-month
                altFormat: "F Y"   // Format for display
            })]
        });

        // Handle the payment button click
        document.getElementById('payButton').addEventListener('click', function() {
            const selectedDate = document.getElementById('paymentDate').value;
            if (!selectedDate) {
                alert('Por favor, seleccione un mes para pagar.');
            } else {
                // Here you can trigger Webpay or any payment process
                window.location.href = `procesar_pago.php?fecha_pago=${selectedDate}`;
            }
        });
    </script>

</body>
</html>
