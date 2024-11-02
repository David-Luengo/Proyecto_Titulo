<?php
session_start(); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $host = "localhost";
    $port = "5432";
    $dbname = "Proyecto_Titulo";
    $user = "postgres";
    $password = "123456";

    $conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password";
    $conn = pg_connect($conn_string);

    $correo = isset($_POST['correo']) ? pg_escape_string($conn, $_POST['correo']) : null;
    $contrasena = isset($_POST['contrasena']) ? pg_escape_string($conn, $_POST['contrasena']) : null;

    if ($correo && $contrasena) {
        if (strpos($correo, '@administrador.com') !== false) {
            $tabla = 'administrador';
            $pagina = 'administrador/index_administrador.php';
        } elseif (strpos($correo, '@profesor.cl') !== false) {
            $tabla = 'profesor';
            $pagina = 'profesor/index_profesor.php';
        } elseif (strpos($correo, '@alumnos.cl') !== false) {
            $tabla = 'alumnos';
            $pagina = 'alumnos/index_alumnos.php';
            $verificar_permiso = true; 
        } elseif (strpos($correo, '@apoderados.cl') !== false) {
            $tabla = 'apoderados';
            $pagina = 'apoderados/index_apoderados.php';
            $verificar_permiso = true; 
        } else {
            echo "Correo no válido.";
            exit();
        }

        $query = "SELECT contrasena" . ($verificar_permiso ? ", permiso" : "") . " FROM $tabla WHERE correo = '$correo'";
        $result = pg_query($conn, $query);

        if ($result && pg_num_rows($result) > 0) {
            $row = pg_fetch_assoc($result);
            $hash_contrasena = $row['contrasena'];

            if (password_verify($contrasena, $hash_contrasena)) {
                if (isset($verificar_permiso) && $verificar_permiso) { 
                    $permiso = $row['permiso'];

                    if ($permiso === 't') { 
                        $_SESSION['usuario'] = $correo;
                        header("Location: /Proyecto_titulo/$pagina");
                        exit();
                    } else {
                        echo "No tienes permiso para acceder a tu cuenta.";
                    }
                } else { 
                    $_SESSION['usuario'] = $correo;
                    header("Location: /Proyecto_titulo/$pagina");
                    exit();
                }
            } else {
                echo "Correo o contraseña incorrectos.";
            }
        } else {
            echo "Usuario no encontrado.";
        }
    } else {
        echo "Por favor, ingrese ambos campos.";
    }

    pg_close($conn);
}
