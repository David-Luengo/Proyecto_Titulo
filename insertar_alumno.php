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

    // Verificar si los datos fueron recibidos correctamente antes de proceder
    if ($nombre && $apellido && $correo && $telefono && $direccion && $contrasena) {
        // Consulta SQL para insertar los datos
        $query = "INSERT INTO alumnos (nombre, apellido, correo, n_telefono, direccion, contrasena) 
                  VALUES ('$nombre', '$apellido', '$correo', '$telefono', '$direccion', '$contrasena')";

        // Ejecutar la consulta
        $result = pg_query($conn, $query);

        if ($result) {
            echo "Alumno registrado correctamente.";
        } else {
            echo "Error al registrar al alumno: " . pg_last_error($conn) . "\n";
        }
    } else {
        echo "Error: No se recibieron todos los datos del formulario.";
    }

    // Cerrar la conexión
    pg_close($conn);
}
?>
