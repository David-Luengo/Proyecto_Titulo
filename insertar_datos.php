<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

    $nombre = $_POST['nombre'] ?? null;
    $apellido = $_POST['apellido'] ?? null;
    $correo = $_POST['correo'] ?? null;
    $telefono = $_POST['telefono'] ?? null;
    $direccion = $_POST['direccion'] ?? null;
    $contrasena = isset($_POST['contrasena']) ? password_hash($_POST['contrasena'], PASSWORD_DEFAULT) : null;
    $region = $_POST['region'] ?? null;
    $comuna = $_POST['comuna'] ?? null;

    $archivoContenidoCodificado = null; 
    $nombreArchivo = null; 
    $tipoArchivo = null; 

    if (isset($_FILES['archivo'])) {
        if ($_FILES['archivo']['error'] == UPLOAD_ERR_OK) {
            
            $archivoContenido = file_get_contents($_FILES['archivo']['tmp_name']);
            $nombreArchivo = pg_escape_string($_FILES['archivo']['name']);
            $tipoArchivo = pg_escape_string($_FILES['archivo']['type']);
            $archivoContenidoCodificado = pg_escape_bytea($conn, $archivoContenido);
        } else {
            echo "Error al subir el archivo: " . $_FILES['archivo']['error'];
        }
    } else {
        echo "No se ha subido ningún archivo.";
    }

    if ($nombre && $apellido && $correo && $telefono && $direccion && $contrasena && $region && $comuna && $archivoContenidoCodificado) {
        if (strpos($correo, '@administrador.cl') !== false) {
            $tabla = 'administrador';
        } elseif (strpos($correo, '@profesor.cl') !== false) {
            $tabla = 'profesor';
        } elseif (strpos($correo, '@apoderados.cl') !== false) {
            $tabla = 'apoderados';
        } elseif (strpos($correo, '@alumnos.cl') !== false) {
            $tabla = 'alumnos';
        }

        $query = "INSERT INTO $tabla (nombre, apellido, correo, numero, direccion, contrasena, archivo, nombre_archivo, tipo_archivo, region, comuna) 
                  VALUES ('$nombre', '$apellido', '$correo', '$telefono', '$direccion', '$contrasena', '$archivoContenidoCodificado', '$nombreArchivo', '$tipoArchivo', '$region', '$comuna')";
        $result = pg_query($conn, $query);



        if ($result) {
            echo "<div style='position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: white; padding: 20px; border-radius: 5px; text-align: center; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);'>
                    <p>Usuario registrado correctamente.</p>
                    <button onclick=\"window.location.href = 'login.html';\">Continuar</button>
                  </div>";
            exit();
        } else {
            echo "Error al registrar al usuario: " . pg_last_error($conn);
        }
    } else {
        echo "Error: No se recibieron todos los datos del formulario o el archivo es nulo.";
    }

    pg_close($conn);
}
?>
