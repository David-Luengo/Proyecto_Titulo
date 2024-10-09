<?php
// Verificar si se ha proporcionado el ID del archivo
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitiza el ID recibido por GET

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

    // Consulta para obtener el archivo almacenado en la base de datos
    $query = "SELECT archivo, mime_type FROM alumnos WHERE id = $id";
    $result = pg_query($conn, $query);

    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        $archivo = pg_unescape_bytea($row['archivo']);  // Descodificar el archivo almacenado en bytea
        $mime_type = $row['mime_type'];  // Recuperar el tipo MIME almacenado

        // Establecer las cabeceras para la descarga o visualización del archivo
        header("Content-Type: $mime_type");
        header('Content-Disposition: inline; filename="archivo_usuario"');
        header('Content-Length: ' . strlen($archivo));

        // Enviar el contenido del archivo
        echo $archivo;
    } else {
        echo "Archivo no encontrado.";
    }

    // Cerrar la conexión
    pg_close($conn);
} else {
    echo "ID no proporcionado.";
}
?>
