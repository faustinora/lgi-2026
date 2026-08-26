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
            $mensaje = 'Estudiante agregado correctamente.';
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Estudiante</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
</head>
<body>
    <main class="container">
        <article>
            <header>
                <h2>Agregar Nuevo Estudiante</h2>
            </header>

            <?php if ($mensaje): ?>
                <ins><strong><?= htmlspecialchars($mensaje) ?></strong></ins>
                <br><br>
            <?php endif; ?>

            <form action="crear.php" method="POST">
                <label for="nombre">Nombre:
                    <input type="text" id="nombre" name="nombre" placeholder="Nombre" required>
                </label>

                <label for="apellido">Apellido:
                    <input type="text" id="apellido" name="apellido" placeholder="Apellido" required>
                </label>

                <label for="email">Correo Electrónico:
                    <input type="email" id="email" name="email" placeholder="Email" required>
                </label>

                <button type="submit">Guardar Estudiante</button>
            </form>

            <footer>
                <a href="index.php" role="button" class="secondary outline">Volver a estudiantes</a>
            </footer>
        </article>
    </main>
</body>
</html>