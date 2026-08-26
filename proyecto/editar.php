<?php
require_once 'config/database.php';
$mensaje = '';
$conn = getConnection();

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $email    = trim($_POST['email'] ?? '');

    if (empty($nombre) || empty($apellido) || empty($email)) {
        $mensaje = 'Todos los campos son obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = 'El correo electrónico no es válido.';
    } else {
        $stmt = $conn->prepare("UPDATE estudiantes SET nombre = ?, apellido = ?, email = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nombre, $apellido, $email, $id);

        if ($stmt->execute()) {
            $mensaje = 'Estudiante actualizado correctamente.';
        } else {
            $mensaje = 'Error al actualizar estudiante: ' . $stmt->error;
        }
        $stmt->close();
    }
}

$stmt = $conn->prepare("SELECT * FROM estudiantes WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$estudiante = $resultado->fetch_assoc();
$stmt->close();

if (!$estudiante) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Estudiante</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
</head>
<body>
    <main class="container" style="max-width: 600px; padding-top: 2rem;">
        <article>
            <header>
                <h3 style="margin-bottom: 0;">Editar Estudiante</h3>
            </header>

            <?php if ($mensaje): ?>
                <mark style="display: block; margin-bottom: 1rem; padding: 0.5rem 1rem;"><?php echo htmlspecialchars($mensaje); ?></mark>
            <?php endif; ?>

            <form action="editar.php?id=<?php echo $id; ?>" method="POST">
                <div class="grid">
                    <label for="nombre">Nombre
                        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($estudiante['nombre']); ?>" required>
                    </label>

                    <label for="apellido">Apellido
                        <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($estudiante['apellido']); ?>" required>
                    </label>
                </div>

                <label for="email">Correo Electrónico
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($estudiante['email']); ?>" required>
                </label>

                <button type="submit">Guardar Cambios</button>
            </form>

            <footer>
                <a href="index.php" role="button" class="secondary outline" style="width: 100%; text-align: center;">Volver a la lista</a>
            </footer>
        </article>
    </main>
</body>
</html>
