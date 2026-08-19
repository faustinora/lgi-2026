<?php
require_once 'config/database.php';
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $email    = trim($_POST['email'] ?? '');

    if (empty($nombre) || empty($apellido) || empty($email)) {
        $mensaje = 'Todos los campos son obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = 'El correo electrónico no es válido.';
    } else {
        $conn = getConnection();
        // Consulta preparada para mayor seguridad
        $stmt = $conn->prepare("INSERT INTO estudiantes (nombre, apellido, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombre, $apellido, $email);

        if ($stmt->execute()) {
            $mensaje = 'Estudiante agregado correctamente. ID: ' . $stmt->insert_id;
        } else {
            $mensaje = 'Error al agregar estudiante: ' . $stmt->error;
        }
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Estudiante</title>
</head>
<body>
    <?php if ($mensaje): ?>
        <p><strong><?= htmlspecialchars($mensaje) ?></strong></p>
    <?php endif; ?>

    <form action="crear.php" method="POST">
        <input type="text" name="nombre" placeholder="Nombre">
        <input type="text" name="apellido" placeholder="Apellido">
        <input type="email" name="email" placeholder="Email">
        <button type="submit">Guardar</button>
    </form>
</body>
</html>