<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $host = "localhost";
    $port = "5432";
    $dbname = "Proyecto_Titulo";
    $user = "postgres";
    $password = "123456";

    $conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password";

    $conn = pg_connect($conn_string);

    $correo = isset($_POST['correo']) ? $_POST['correo'] : null;
    $contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : null;

    if ($correo && $contrasena) {
        if (strpos($correo, '@administrador.com') !== false) {
            $tabla = 'administrador';
            $pagina = 'administrador/index_administrador.html';
        } elseif (strpos($correo, '@profesor.com') !== false) {
            $tabla = 'profesor';
            $pagina = 'profesor/index_profesor.html';
        } elseif (strpos($correo, '@alumnos.com') !== false) {
            $tabla = 'alumnos';
            $pagina = 'alumnos/index_alumnos.html';
        } elseif (strpos($correo, '@apoderados.com') !== false) {
            $tabla = 'apoderadors';
            $pagina = 'apoderados/index_apoderados.html';
        } else {
            echo "Correo no válido.";
            exit();
        }

        
        $query = "SELECT contrasena FROM $tabla WHERE correo = '$correo'";
        $result = pg_query($conn, $query);

        if ($result && pg_num_rows($result) > 0) {
            $row = pg_fetch_assoc($result);
            $hash_contrasena = $row['contrasena'];

            if (password_verify($contrasena, $hash_contrasena)) {
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
