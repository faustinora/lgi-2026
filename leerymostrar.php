<?php
require_once 'database.php';
$conn = getConnection();
$res = $conn->query('SELECT * FROM estudiantes WHERE activo = 1 ORDER BY apellido');
<table>
    <thead>
        <tr><th>Nombre</th><th>Apellido</th><th>Email</th></tr>
    </thead>
    <tbody>
        <?php while ($f = $res->fetch_assoc()):?>
            <tr>
                <td><?= htmlspecialchars($f['nombre'].' '.$f['apellido']) ?></td>
                <td><?= htmlspecialchars($f['email']) ?></td>
            </tr>
        <?php endwhile;?>
    </tbody>
</table>
<p>Total: <?= $res->num_rows ?> estudiantes</p>