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
        
        $query = "DELETE FROM alumnos WHERE id = '$id'";
        $result = pg_query($conn, $query);

        if ($result) {
            echo "Alumno eliminado correctamente.";
        } else {
            echo "Error al eliminar el alumno: " . pg_last_error($conn);
        }
    } elseif ($accion == 'permiso') {
        
        $query = "UPDATE alumnos SET permiso = true WHERE id = '$id'";
        $result = pg_query($conn, $query);

        if ($result) {
            echo "Permiso otorgado correctamente.";
        } else {
            echo "Error al otorgar permiso: " . pg_last_error($conn);
        }
    }

    
    header("Location: index_administrador.php");
    pg_close($conn);
}
?>
