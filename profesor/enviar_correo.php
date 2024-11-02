<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: /Proyecto_titulo/index.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['apoderados']) && isset($_POST['asunto']) && isset($_POST['mensaje'])) {
    
    $apoderados = $_POST['apoderados'];
    $asunto = $_POST['asunto'];
    $mensaje = $_POST['mensaje'];
    
    // Encabezados del correo
    $headers = "From: eluengofa@gmail.com\r\n";
    $headers .= "Reply-To: eluengofa@gmail.com\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    foreach ($apoderados as $correo_personal) {
        $correo_personal = filter_var($correo_personal, FILTER_VALIDATE_EMAIL);
        
        if ($correo_personal) {
            $enviado = mail($correo_personal, $asunto, nl2br($mensaje), $headers);
            
            if (!$enviado) {
                echo "<p>No se pudo enviar el correo a $correo_personal.</p>";
            }
        } else {
            echo "<p>Correo no válido: $correo_personal</p>";
        }
    }
    
    echo "<script>alert('Correos enviados correctamente.'); window.location.href = 'ausencia_profesor.php';</script>";
} else {
    echo "<script>alert('Por favor, selecciona al menos un apoderado y completa el asunto y el mensaje.'); window.location.href = 'ausencia_profesor.php';</script>";
}
?>
