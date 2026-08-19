<?php
require_once 'config/database.php';
$conn = getConnection();
$res = $conn->query('SELECT * FROM estudiantes WHERE activo = 1 ORDER BY apellido');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Estudiantes</title>
</head>
<body>
    <h1>Estudiantes</h1>
    <table border="1">
        <thead>
            <tr><th>Nombre</th><th>Apellido</th><th>Email</th></tr>
        </thead>
        <tbody>
        <?php while ($fila = $res->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($fila['nombre']) ?></td>
                <td><?= htmlspecialchars($fila['apellido']) ?></td>
                <td><?= htmlspecialchars($fila['email']) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <p>Total: <?= $res->num_rows ?> estudiantes</p>
</body>
</html>