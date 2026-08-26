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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Estudiantes</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
</head>
<body>
    <main class="container">
        <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h1>Estudiantes</h1>
            <p><a href="crear.php">Agregar nuevo estudiante</a></p>
        </header>

        <!-- FORMULARIO DE BÚSQUEDA -->
        <form action="index.php" method="GET">
            <fieldset role="group">
                <input type="text" name="buscar" placeholder="Buscar por nombre, apellido o email..." value="<?php echo htmlspecialchars($busqueda); ?>">
                <button type="submit">Buscar</button>
                <?php if (!empty($busqueda)): ?>
                    <a href="index.php">Limpiar búsqueda</a>
                <?php endif; ?>
            </fieldset>
        </form>

        <!-- TABLA CON ESTILO PICOCSS -->
        <div class="overflow-auto">
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Email</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($res && $res->num_rows > 0): ?>
                    <?php while ($fila = $res->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($fila['apellido']); ?></td>
                            <td><?php echo htmlspecialchars($fila['email']); ?></td>
                            <td>
                                <a href="editar.php?id=<?php echo $fila['id']; ?>">Editar</a> |
                                <a href="eliminar.php?id=<?php echo $fila['id']; ?>" onclick="return confirm('¿Seguro que deseas eliminar este estudiante?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align:center;">No se encontraron estudiantes.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <footer>
            <small><strong>Total:</strong> <?php echo $res ? $res->num_rows : 0; ?> estudiantes</small>
        </footer>
    </main>
</body>
</html>
