<?php
require_once 'config/database.php';
$conn = getConnection();

// Obtener el término de búsqueda si existe
$busqueda = trim($_GET['buscar'] ?? '');

if (!empty($busqueda)) {
    $stmt = $conn->prepare("SELECT * FROM estudiantes WHERE activo = 1 AND (nombre LIKE ? OR apellido LIKE ? OR email LIKE ?) ORDER BY apellido");
    $term = "%" . $busqueda . "%";
    $stmt->bind_param("sss", $term, $term, $term);
    $stmt->execute();
    $res = $stmt->get_result();
} else {
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
        <!-- ENCABEZADO CON BOTÓN Y BUSCADOR COMPACTO -->
        <header style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.5rem;">
            <div>
                <h1 style="margin-bottom: 0;">Estudiantes</h1>
                <a href="crear.php" role="button" class="contrast" style="margin-top: 0.5rem;">+ Agregar nuevo</a>
            </div>

            <!-- Buscador compacto acotado a 400px -->
            <form action="index.php" method="GET" style="margin-bottom: 0; width: 100%; max-width: 400px;">
                <fieldset role="group" style="margin-bottom: 0;">
                    <input type="search" name="buscar" placeholder="Buscar..." value="<?php echo htmlspecialchars($busqueda); ?>">
                    <button type="submit">Buscar</button>
                    <?php if (!empty($busqueda)): ?>
                        <a href="index.php" role="button" class="secondary outline">Limpiar</a>
                    <?php endif; ?>
                </fieldset>
            </form>
        </header>

        <!-- TABLA -->
        <div class="overflow-auto">
            <table class="striped">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Email</th>
                        <th style="text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($res && $res->num_rows > 0): ?>
                    <?php while ($fila = $res->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($fila['apellido']); ?></td>
                            <td><?php echo htmlspecialchars($fila['email']); ?></td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="editar.php?id=<?php echo $fila['id']; ?>" role="button" class="outline secondary" style="padding: 0.25rem 0.5rem; font-size: 0.85rem;">Editar</a>
                                <a href="eliminar.php?id=<?php echo $fila['id']; ?>" onclick="return confirm('¿Seguro que deseas eliminar este estudiante?');" role="button" class="outline contrast" style="padding: 0.25rem 0.5rem; font-size: 0.85rem;">Eliminar</a>
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
