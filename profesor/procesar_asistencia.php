<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: /Proyecto_titulo/index.html");
    exit();
}

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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['alumnos'])) {
    $alumnos = $_POST['alumnos'];
    $fecha = date("Y-m-d"); // Obtener la fecha actual del sistema

    // Preparar la consulta para insertar la asistencia con nombre y apellido
    $query = "INSERT INTO asistencia (nombre, apellido, fecha) VALUES ($1, $2, $3)";
    
    foreach ($alumnos as $alumno_id) {
        // Obtener nombre y apellido del alumno
        $query_alumno = "SELECT nombre, apellido FROM alumnos WHERE id = $1";
        $result_alumno = pg_query_params($conn, $query_alumno, array($alumno_id));

        if ($result_alumno && pg_num_rows($result_alumno) > 0) {
            $alumno = pg_fetch_assoc($result_alumno);
            $nombre = $alumno['nombre'];
            $apellido = $alumno['apellido'];

            // Insertar asistencia con nombre, apellido y fecha actual
            $result = pg_query_params($conn, $query, array($nombre, $apellido, $fecha));
            if (!$result) {
                echo "<p>Error al registrar la asistencia para $nombre $apellido: " . pg_last_error($conn) . "</p>";
            }
        }
    }

    echo "<script>alert('Asistencia registrada correctamente.'); window.location.href = 'asistencia_profesor.php';</script>";
} else {
    echo "<script>alert('Por favor, selecciona al menos un alumno.'); window.location.href = 'asistencia_profesor.php';</script>";
}

pg_close($conn);
?>
