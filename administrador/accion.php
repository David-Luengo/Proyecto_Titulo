<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $accion = $_POST['accion'];

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

    if ($accion == 'eliminar') {
        // Eliminar tanto en la tabla alumnos como en la tabla apoderados
        $query_alumnos = "DELETE FROM alumnos WHERE id = '$id'";
        $result_alumnos = pg_query($conn, $query_alumnos);

        $query_apoderados = "DELETE FROM apoderados WHERE id = '$id'";
        $result_apoderados = pg_query($conn, $query_apoderados);

        if ($result_alumnos || $result_apoderados) {
            echo "Registro eliminado correctamente.";
        } else {
            echo "Error al eliminar el registro: " . pg_last_error($conn);
        }
    } elseif ($accion == 'permiso') {
        // Otorgar permiso tanto en la tabla alumnos como en la tabla apoderados
        $query_alumnos = "UPDATE alumnos SET permiso = true WHERE id = '$id'";
        $result_alumnos = pg_query($conn, $query_alumnos);

        $query_apoderados = "UPDATE apoderados SET permiso = true WHERE id = '$id'";
        $result_apoderados = pg_query($conn, $query_apoderados);

        if ($result_alumnos || $result_apoderados) {
            echo "Permiso otorgado correctamente.";
        } else {
            echo "Error al otorgar permiso: " . pg_last_error($conn);
        }
    }

    // Redirigir de vuelta a la página principal
    header("Location: index_administrador.php");
    pg_close($conn);
}
?>
