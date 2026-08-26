<?php
require_once 'config/database.php';
$mensaje = '';
$conn = getConnection();

// 1. OBTENER Y VALIDAR EL ID DESDE LA URL
$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    header('Location: index.php');
    exit;
}

// 2. PROCESAR LA ACTUALIZACIÓN (Cuando se envía el formulario vía POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $email    = trim($_POST['email'] ?? '');

    if (empty($nombre) || empty($apellido) || empty($email)) {
        $mensaje = 'Todos los campos son obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = 'El correo electrónico no es válido.';
    } else {
        // Actualizar datos en MySQL mediante Sentencia Preparada
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

// 3. CONSULTAR LOS DATOS ACTUALES DEL ESTUDIANTE (Para mostrarlos en los inputs)
$stmt = $conn->prepare("SELECT * FROM estudiantes WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$estudiante = $resultado->fetch_assoc();
$stmt->close();

// Si el ID no existe en la base de datos, redirigir
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

    <link rel="stylesheet"href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">

</head>
<body>

<main class="container">
        <article>
            <header>
                <h2>Editar Estudiante</h2>
            </header>

            <?php if ($mensaje): ?>
                <ins><strong><?= htmlspecialchars($mensaje) ?></strong></ins>
                <br><br>
            <?php endif; ?>

            <!-- Formulario adaptado a la sintaxis limpia de PicoCSS -->
            <form action="editar.php?id=<?= $id ?>" method="POST">
                <label for="nombre">Nombre:
                    <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($estudiante['nombre']) ?>" required>
                </label>

                <label for="apellido">Apellido:
                    <input type="text" id="apellido" name="apellido" value="<?= htmlspecialchars($estudiante['apellido']) ?>" required>
                </label>

                <label for="email">Email:
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($estudiante['email']) ?>" required>
                </label>

                <button type="submit">Guardar Cambios</button>
            </form>

            <footer>
                <a href="index.php" role="button" class="secondary outline">Volver a la lista de estudiantes</a>
            </footer>
        </article>
    </main>
</body>
</html>