<?php
require_once 'config/database.php';
$conn = getConnection();

// Obtener el término de búsqueda si existe
$busqueda = trim($_GET['buscar'] ?? '');

if (!empty($busqueda)) {
    // Consulta filtrada usando Sentencias Preparadas (Seguridad)
    $stmt = $conn->prepare("SELECT * FROM estudiantes WHERE activo = 1 AND (nombre LIKE ? OR apellido LIKE ? OR email LIKE ?) ORDER BY apellido");
    $term = "%" . $busqueda . "%";
    $stmt->bind_param("sss", $term, $term, $term);
    $stmt->execute();
    $res = $stmt->get_result();
} else {
    // Consulta general sin filtro
    $res = $conn->query('SELECT * FROM estudiantes WHERE activo = 1 ORDER BY id ASC');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Estudiantes</title>

    <link rel="stylesheet"href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">


</head>
<body>
    <h1>Estudiantes</h1>

    <p><a href="crear.php">Agregar nuevo estudiante</a></p>

    <!-- FORMULARIO DE BÚSQUEDA -->
    <form action="index.php" method="GET">
        <input type="text" name="buscar" placeholder="Buscar por nombre, apellido o email..." value="<?= htmlspecialchars($busqueda) ?>">
        <button type="submit">Buscar</button>
        <?php if (!empty($busqueda)): ?>
            <a href="index.php">Limpiar búsqueda</a>
        <?php endif; ?>
    </form>
    <br>

    <table border="1">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Email</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($res->num_rows > 0): ?>
            <?php while ($fila = $res->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($fila['nombre']) ?></td>
                    <td><?= htmlspecialchars($fila['apellido']) ?></td>
                    <td><?= htmlspecialchars($fila['email']) ?></td>
                    <td>
                        <a href="editar.php?id=<?= $fila['id'] ?>">Editar</a> |
                        <!-- Enlace para eliminar pasando el ID en la URL -->
                        <a href="eliminar.php?id=<?= $fila['id'] ?>" onclick="return confirm('¿Seguro que deseas eliminar este estudiante?');">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">No se encontraron estudiantes.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
    <p>Total: <?= $res->num_rows ?> estudiantes</p>
</body>
</html>