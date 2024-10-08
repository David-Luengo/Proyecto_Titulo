<?php
session_start();

// Verificar si la sesión está iniciada
if (!isset($_SESSION['usuario'])) {
  // Si no hay sesión iniciada, redirigir al login
  header("Location: /Proyecto_titulo/index.html"); // o la página de login que utilices
  exit();
}
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
  <link rel="stylesheet" href="./administrador.css/administrador.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <title>Index</title>
</head>

<body>

  <nav class="navbar navbar-expand-lg p-3 fixed-top " id="menu" style="background-color: black; padding: 10px;">
    <div class="container-fluid">
      <a class="navbar-brand" href="index_administrador.php">
        <img src="../img/icono.png" alt="" width="30" height="30" class="d-inline-block align-top">
        <span class="custom-text fs-5 fw-bold" style="color:  white;">EduAdmin</span>
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>


      <!-- Actualiza el enlace a logout.php -->
      <a class="nav-link nav-item fs-6" href="../logout.php" style="color: white;">Cerrar Sesión</a>
    </div>
  </nav>

  <div class="container" style="margin-top: 100px;">
    <h2>Buscar Alumnos</h2>
    <form action="" method="GET" class="mb-3">
      <div class="input-group">
        <input type="text" name="search" class="form-control" placeholder="Ingrese el nombre o correo del alumno" required>
        <button type="submit" class="btn btn-primary">Buscar</button>
      </div>
    </form>
  </div>

  <?php
  session_start();

  // Verificar si la sesión está iniciada
  if (!isset($_SESSION['usuario'])) {
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

  // Manejar la búsqueda
  if (isset($_GET['search'])) {
    $search = pg_escape_string($conn, $_GET['search']);

    $query = "SELECT * FROM alumnos WHERE nombre ILIKE '%$search%' OR correo ILIKE '%$search%'";
    $result = pg_query($conn, $query);

    if ($result) {
      if (pg_num_rows($result) > 0) {
        echo '<div class="container"><h3>Resultados de la búsqueda:</h3><table class="table">';
        echo '<thead><tr><th>Nombre</th><th>Apellido</th><th>Correo</th><th>Teléfono</th><th>Dirección</th></tr></thead>';
        echo '<tbody>';

        while ($row = pg_fetch_assoc($result)) {
          echo '<tr>';
          echo '<td>' . htmlspecialchars($row['nombre']) . '</td>';
          echo '<td>' . htmlspecialchars($row['apellido']) . '</td>';
          echo '<td>' . htmlspecialchars($row['correo']) . '</td>';
          echo '<td>' . htmlspecialchars($row['numero']) . '</td>';
          echo '<td>' . htmlspecialchars($row['direccion']) . '</td>';
          echo '</tr>';
        }
        echo '</tbody></table></div>';
      } else {
        echo '<div class="container"><h3>No se encontraron resultados.</h3></div>';
      }
    } else {
      echo "Error en la consulta: " . pg_last_error($conn);
    }
  }


  // Cerrar la conexión
  pg_close($conn);
  ?>



  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
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
</body>

</html>