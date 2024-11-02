<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: /Proyecto_titulo/index.html");
    exit();
}

$fecha_pago = $_GET['fecha_pago'] ?? null;

if (!$fecha_pago) {
    echo "Error: No se ha seleccionado una fecha para el pago.";
    exit();
}

// Aquí debes iniciar el proceso de pago utilizando el SDK de Webpay
require_once 'webpay_sdk.php'; // Reemplazar con el path correcto del SDK de Webpay

// Configuración de Webpay
$commerce_code = "123456789";  // Código de comercio proporcionado por Webpay
$api_key = "your-api-key";     // Llave API proporcionada por Webpay
$amount = 50000;               // Monto de la matrícula, por ejemplo 50,000 pesos



// Datos para la transacción
$buy_order = rand(100000, 999999); // Número aleatorio para la orden de compra
$session_id = session_id();
$return_url = "https://tusitio.com/confirmacion_pago.php"; // URL para confirmar el pago
$final_url = "https://tusitio.com/final_pago.php";         // URL donde será redirigido tras el pago

// Crear la transacción en Webpay
$response = $webpay->createTransaction($buy_order, $session_id, $amount, $return_url, $final_url);

if ($response->status == "ok") {
    header("Location: " . $response->url); // Redirige al usuario a Webpay para completar el pago
} else {
    echo "Error al procesar el pago: " . $response->message;
}
?>
