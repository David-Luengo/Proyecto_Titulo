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

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <link rel="stylesheet" href="./administrador.css/administrador.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <title>Index</title>
</head>

<body>
  <nav class="navbar navbar-expand-lg p-3 fixed-top " id="menu" style="background-color: black; padding: 10px;">
    <div class="container-fluid">
      <a class="navbar-brand" href="index_administrador.php">
        <img src="../img/icono.png" alt="" width="30" height="30" class="d-inline-block align-top">
        <span class="custom-text fs-5 fw-bold" style="color: white;">EduAdmin</span>
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <a class="nav-link nav-item fs-6" href="../logout.php" style="color: white;">Cerrar Sesión</a>
    </div>
  </nav>

  <div class="container" style="margin-top: 100px;">
    <h2>Buscar Alumnos y Apoderados</h2>
    <form action="" method="GET" class="mb-3">
      <div class="input-group" style="padding: 20px 0px 20px 0px;">
        <input type="text" name="search" class="form-control" placeholder="Ingrese el nombre o correo" required>
        <button type="submit" class="btn btn-primary">Buscar</button>
      </div>
    </form>
  </div>

  <?php
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

  if (isset($_GET['search'])) {
    $search = pg_escape_string($conn, $_GET['search']);

    // Consulta para alumnos
    $query_alumnos = "SELECT id, nombre, apellido, correo, numero, region, comuna, direccion, permiso 
                      FROM alumnos 
                      WHERE id::text ILIKE '%$search%' OR nombre ILIKE '%$search%' OR apellido ILIKE '%$search%' OR correo ILIKE '%$search%'";

    // Consulta para apoderados
    $query_apoderados = "SELECT id, nombre, apellido, correo, numero, region, comuna, direccion, permiso 
                         FROM apoderados 
                         WHERE id::text ILIKE '%$search%' OR nombre ILIKE '%$search%' OR apellido ILIKE '%$search%' OR correo ILIKE '%$search%'";

    // Ejecutar consultas
    $result_alumnos = pg_query($conn, $query_alumnos);
    $result_apoderados = pg_query($conn, $query_apoderados);

    if ($result_alumnos && pg_num_rows($result_alumnos) > 0 || $result_apoderados && pg_num_rows($result_apoderados) > 0) {
      echo '<div class="container" style="padding-buttom: 20px"><table class="table">';
      echo '<thead><tr><th>ID</th><th>Nombre</th><th>Apellido</th><th>Correo</th><th>Teléfono</th><th>Región</th><th>Comuna</th><th>Dirección</th><th>Tipo</th><th>Archivo</th><th>Permiso</th></tr></thead>';
      echo '<tbody>';

      // Mostrar alumnos
      while ($row = pg_fetch_assoc($result_alumnos)) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['nombre']) . '</td>';
        echo '<td>' . htmlspecialchars($row['apellido']) . '</td>';
        echo '<td>' . htmlspecialchars($row['correo']) . '</td>';
        echo '<td>' . htmlspecialchars($row['numero']) . '</td>';
        echo '<td>' . htmlspecialchars($row['region']) . '</td>';
        echo '<td>' . htmlspecialchars($row['comuna']) . '</td>';
        echo '<td>' . htmlspecialchars($row['direccion']) . '</td>';
        echo '<td>Alumno</td>';

        echo '<td> <a href="descargar_archivo.php?id=' . htmlspecialchars($row['id']) . '" class="btn btn-info">
        Ver / Descargar </a> </td>';
  
        if ($row['permiso'] == 'f') {
          echo '<td> <form action="accion.php" method="post" style="display:inline; margin-right: 45px">
                      <input type="hidden" name="id" value="' . htmlspecialchars($row['id']) . '">
                      <button type="submit" name="accion" value="permiso" class="btn btn-success">
                          <i class="fas fa-check"></i>
                      </button>
                    </form>';
        } else {
          echo '<td> <span class="text-success" style="margin-right: 20px">Aceptado</span>';
        }

        echo '<form action="accion.php" method="post" style="display:inline;">
                  <input type="hidden" name="id" value="' . htmlspecialchars($row['id']) . '">
                  <button type="submit" name="accion" value="eliminar" class="btn btn-danger">
                      <i class="fas fa-times"></i>
                  </button>
                </form>';
        echo '</tr>';
      }

      // Mostrar apoderados
      while ($row = pg_fetch_assoc($result_apoderados)) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['nombre']) . '</td>';
        echo '<td>' . htmlspecialchars($row['apellido']) . '</td>';
        echo '<td>' . htmlspecialchars($row['correo']) . '</td>';
        echo '<td>' . htmlspecialchars($row['numero']) . '</td>';
        echo '<td>' . htmlspecialchars($row['region']) . '</td>';
        echo '<td>' . htmlspecialchars($row['comuna']) . '</td>';
        echo '<td>' . htmlspecialchars($row['direccion']) . '</td>';
        echo '<td>Apoderado</td>';

        echo '<td> <a href="descargar_archivo.php?id=' . htmlspecialchars($row['id']) . '" class="btn btn-info">
              Ver / Descargar </a> </td>';
        

        if ($row['permiso'] == 'f') {
          echo '<td> <form action="accion.php" method="post" style="display:inline; margin-right: 45px">
                      <input type="hidden" name="id" value="' . htmlspecialchars($row['id']) . '">
                      <button type="submit" name="accion" value="permiso" class="btn btn-success">
                          <i class="fas fa-check"></i>
                      </button>
                    </form>';
        } else {
          echo '<td> <span class="text-success" style="margin-right: 20px">Aceptado</span>';
        }

        echo '<form action="accion.php" method="post" style="display:inline;">
                  <input type="hidden" name="id" value="' . htmlspecialchars($row['id']) . '">
                  <button type="submit" name="accion" value="eliminar" class="btn btn-danger">
                      <i class="fas fa-times"></i>
                  </button>
                </form>';
        echo '</tr>';
      }

      echo '</tbody></table></div>';
    } else {
      echo '<div class="container"><h3>No se encontraron resultados.</h3></div>';
    }
  }

  pg_close($conn);
  ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  <footer class="sticky-footer container-fluid bg-gray p-4">
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