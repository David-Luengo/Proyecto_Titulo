<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];

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

    // Obtener el archivo de la base de datos
    $query = "SELECT archivo, nombre_archivo FROM alumnos WHERE id = $1";
    $result = pg_query_params($conn, $query, array($id));

    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        $archivo = $row['archivo'];
        $nombre_archivo = $row['nombre_archivo'];

        // Configurar los encabezados para mostrar el archivo
        header('Content-Type: application/pdf'); // Cambiar el tipo según el archivo
        header('Content-Disposition: inline; filename="' . $nombre_archivo . '"');
        echo pg_unescape_bytea($archivo);
    } else {
        echo "Archivo no encontrado.";
    }

    // Cerrar la conexión
    pg_close($conn);
} else {
    echo "ID no proporcionado.";
}
?>
