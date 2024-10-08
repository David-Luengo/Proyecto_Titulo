<?php
session_start();
session_unset(); // Limpiar todas las variables de sesión
session_destroy(); // Destruir la sesión

// Redirigir al usuario a la página de inicio de sesión
header("Location: /Proyecto_titulo/login.html");
exit();
?>
