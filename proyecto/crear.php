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
    <main class="container" style="max-width: 600px; padding-top: 2rem;">
        <article>
            <header>
                <h3 style="margin-bottom: 0;">Agregar Nuevo Estudiante</h3>
            </header>

            <?php if ($mensaje): ?>
                <mark style="display: block; margin-bottom: 1rem; padding: 0.5rem 1rem;"><?php echo htmlspecialchars($mensaje); ?></mark>
            <?php endif; ?>

            <form action="crear.php" method="POST">
                <!-- Nombre y Apellido en la misma fila usando grid -->
                <div class="grid">
                    <label for="nombre">Nombre
                        <input type="text" id="nombre" name="nombre" placeholder="Ej. Juan" required>
                    </label>

                    <label for="apellido">Apellido
                        <input type="text" id="apellido" name="apellido" placeholder="Ej. Pérez" required>
                    </label>
                </div>

                <label for="email">Correo Electrónico
                    <input type="email" id="email" name="email" placeholder="juan@ejemplo.com" required>
                </label>

                <button type="submit" class="contrast">Guardar Estudiante</button>
            </form>

            <footer>
                <a href="index.php" role="button" class="secondary outline" style="width: 100%; text-align: center;">Volver a la lista</a>
            </footer>
        </article>
    </main>
</body>
</html>
