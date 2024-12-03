<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['asistencia'])) {
    // Conexión a la base de datos
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

    $fecha_actual = date("Y-m-d");

    foreach ($_POST['asistencia'] as $alumno_id => $estado) {
        if ($estado !== 'sin_asignar') {
            // Obtener nombre y apellido del alumno
            $query_alumno = "SELECT nombre, apellido FROM alumnos WHERE id = $1";
            $result_alumno = pg_query_params($conn, $query_alumno, array($alumno_id));
            $alumno = pg_fetch_assoc($result_alumno);
            
            if ($alumno) {
                $nombre = $alumno['nombre'];
                $apellido = $alumno['apellido'];

                // Insertar o actualizar registro de asistencia con nombre y apellido
                $query = "INSERT INTO asistencia (alumno_id, fecha, estado, nombre, apellido) 
                          VALUES ($1, $2, $3, $4, $5)
                          ON CONFLICT (alumno_id, fecha) 
                          DO UPDATE SET estado = EXCLUDED.estado, nombre = EXCLUDED.nombre, apellido = EXCLUDED.apellido";
                $result = pg_query_params($conn, $query, array($alumno_id, $fecha_actual, $estado, $nombre, $apellido));
                
                if (!$result) {
                    echo "Error al registrar la asistencia del alumno con ID $alumno_id: " . pg_last_error($conn);
                }
            }
        }
    }

    pg_close($conn);

    echo "<script>alert('Asistencia registrada correctamente.'); window.location.href = 'asistencia_profesor.php';</script>";
} else {
    echo "<script>alert('Datos de asistencia incompletos.'); window.location.href = 'asistencia_profesor.php';</script>";
}
?>
