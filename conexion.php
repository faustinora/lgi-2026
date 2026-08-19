<?php
require_once 'config.php';
//conectar
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
//verifica si funciona la conexion
if ($conn->connect_error) {
    die("Error de conexión:" . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>