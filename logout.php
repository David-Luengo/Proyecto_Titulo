<?php
session_start();
session_unset(); 
session_destroy();

header("Location: /Proyecto_titulo/login.html");
exit();
?>
