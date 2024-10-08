<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Datos de conexión
    $host = "localhost";
    $port = "5432";
    $dbname = "Proyecto_Titulo";
    $user = "postgres";
    $password = "123456";

    // Crear la cadena de conexión
    $conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password";

    // Establecer la conexión
    $conn = pg_connect($conn_string);

    // Recibir los datos del formulario
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : null;
    $apellido = isset($_POST['apellido']) ? $_POST['apellido'] : null;
    $correo = isset($_POST['correo']) ? $_POST['correo'] : null;
    $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : null;
    $direccion = isset($_POST['direccion']) ? $_POST['direccion'] : null;
    $contrasena = isset($_POST['contrasena']) ? password_hash($_POST['contrasena'], PASSWORD_DEFAULT) : null;

    // Procesar la imagen subida, si se envió una
    $imagenCodificada = null; // Inicializar la variable de imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $imagen = $_FILES['imagen']['tmp_name'];

        // Leer el archivo de imagen
        $imagenData = file_get_contents($imagen); // leer el archivo
        if ($imagenData !== false) {
            $imagenCodificada = pg_escape_bytea($conn, $imagenData); // Preparar la imagen para almacenar en PostgreSQL
        } else {
            echo "Error al leer la imagen.";
            exit();
        }
    }

    if ($nombre && $apellido && $correo && $telefono && $direccion && $contrasena) {
        
        if (strpos($correo, '@administrador.cl') !== false) {
            $tabla = 'administrador';
        } elseif (strpos($correo, '@profesor.cl') !== false) {
            $tabla = 'profesor';
        } elseif (strpos($correo, '@apoderados.cl') !== false) {
            $tabla = 'apoderados';
        } else {
            $tabla = 'alumnos';
        }

        $query = "INSERT INTO $tabla (nombre, apellido, correo, numero, direccion, contrasena, imagen) 
                  VALUES ('$nombre', '$apellido', '$correo', '$telefono', '$direccion', '$contrasena', '$imagenCodificada')";

        $result = pg_query($conn, $query);

        if ($result) {
            echo "<div style='position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: white; padding: 20px; border-radius: 5px; text-align: center; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);'>
                    <p>Usuario registrado correctamente.</p>
                    <button onclick=\"window.location.href = 'login.html';\">Continuar</button>
                  </div>";
            exit(); 
        } else {
            echo "Error al registrar al usuario: " . pg_last_error($conn) . "\n";
        }
    } else {
        echo "Error: No se recibieron todos los datos del formulario.";
    }
    pg_close($conn);
}
?>
