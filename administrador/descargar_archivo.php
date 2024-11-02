<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Información de conexión a la base de datos
    $host = "localhost";
    $port = "5432";
    $dbname = "Proyecto_Titulo";
    $user = "postgres";
    $password = "123456";

    // Conexión a la base de datos
    $conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password";
    $conn = pg_connect($conn_string);

    if (!$conn) {
        die("Error de conexión: " . pg_last_error());
    }

    // Consulta para obtener el archivo desde la tabla 'alumnos'
    $query_alumno = "SELECT archivo, nombre_archivo FROM alumnos WHERE id = $1";
    $result_alumno = pg_query_params($conn, $query_alumno, array($id));

    // Consulta para obtener el archivo desde la tabla 'apoderados'
    $query_apoderado = "SELECT archivo, nombre_archivo FROM apoderados WHERE id = $1";
    $result_apoderado = pg_query_params($conn, $query_apoderado, array($id));

    if ($result_alumno && pg_num_rows($result_alumno) > 0) {
        // Si se encuentra el archivo en 'alumnos', se muestra
        $row = pg_fetch_assoc($result_alumno);
        $archivo = $row['archivo'];
        $nombre_archivo = $row['nombre_archivo'];

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $nombre_archivo . '"');
        echo pg_unescape_bytea($archivo);
    } elseif ($result_apoderado && pg_num_rows($result_apoderado) > 0) {
        // Si se encuentra el archivo en 'apoderados', se muestra
        $row = pg_fetch_assoc($result_apoderado);
        $archivo = $row['archivo'];
        $nombre_archivo = $row['nombre_archivo'];

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $nombre_archivo . '"');
        echo pg_unescape_bytea($archivo);
    } else {
        // Si no se encuentra el archivo en ninguna de las tablas
        echo "Archivo no encontrado.";
    }

    // Cerrar la conexión
    pg_close($conn);
} else {
    echo "ID no proporcionado.";
}
?>
