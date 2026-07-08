<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $email = $_POST['email'] ?? '';

    // Validate input
    if (empty($nombre) || empty($apellido) || empty($email)) {
        $error = 'Datos invalidos.';
    } else {
        $conn = getConnection();
        $nombre = $conn->real_escape_string($nombre);
        $apellido = $conn->real_escape_string($apellido);
        $email = $conn->real_escape_string($email);
        $conn->query("INSERT INTO estudiantes (nombre, apellido, email) VALUES ('$nombre', '$apellido', '$email')");
        header('Location: leerymostrar.php');
        exit;
    }
}
?>