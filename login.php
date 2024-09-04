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
    $correo = isset($_POST['correo']) ? $_POST['correo'] : null;
    $contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : null;

    // Verificar si los datos fueron recibidos correctamente
    if ($correo && $contrasena) {
        // Determinar la tabla y la carpeta según el dominio del correo
        if (strpos($correo, '@administrador.com') !== false) {
            $tabla = 'administrador';
            $pagina = 'administrador/pagina_administrador.html';
        } elseif (strpos($correo, '@profesor.com') !== false) {
            $tabla = 'profesor';
            $pagina = 'profesor/pagina_profesor.html';
        } elseif (strpos($correo, '@alumnos.com') !== false) {
            $tabla = 'alumnos';
            $pagina = 'alumnos/pagina_alumnos.html';
        } elseif (strpos($correo, '@apoderados.com') !== false) {
            $tabla = 'apoderadors';
            $pagina = 'apoderados/pagina_apoderados.html';
        } else {
            echo "Correo no válido.";
            exit();
        }

        // Consulta SQL para obtener el usuario por correo electrónico
        $query = "SELECT contrasena FROM $tabla WHERE correo = '$correo'";
        $result = pg_query($conn, $query);

        if ($result && pg_num_rows($result) > 0) {
            $row = pg_fetch_assoc($result);
            $hash_contrasena = $row['contrasena'];

            // Verificar la contraseña
            if (password_verify($contrasena, $hash_contrasena)) {
                // Redirigir a la página correspondiente en la carpeta adecuada
                header("Location: /Proyecto_titulo/$pagina");
                exit();
            } else {
                echo "Correo o contraseña incorrectos.";
            }
        } else {
            echo "Usuario no encontrado.";
        }
    } else {
        echo "Por favor, ingrese ambos campos.";
    }

    // Cerrar la conexión
    pg_close($conn);
}
?>
