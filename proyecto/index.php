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
    $res = $conn->query('SELECT * FROM estudiantes WHERE activo = 1 ORDER BY apellido ASC');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Estudiantes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    
    <style>
        .titulo-principal {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        /* Estilos de la Tabla y Bordes */
        table.tabla-estudiantes {
            border-collapse: collapse;
            width: 100%;
            border: 1px solid #cbd5e1;
        }

        table.tabla-estudiantes th, 
        table.tabla-estudiantes td {
            border: 1px solid #cbd5e1;
            padding: 0.6rem 0.8rem;
        }

        table.tabla-estudiantes thead th {
            background-color: #1e293b;
            color: #ffffff;
        }

        table.tabla-estudiantes tbody tr {
            background-color: #ffffff;
        }

        table.tabla-estudiantes tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        /* Columna de Acciones reducida */
        .col-acciones {
            width: 1%;
            white-space: nowrap;
            text-align: center !important;
        }
    </style>
</head>
<body>
    <main class="container" style="padding-top: 2rem;">
        <!-- ENVOLVEMOS TODO EN UN <article> IGUAL QUE EN EDITAR.PHP -->
        <article>
            <header>
                <h1 class="titulo-principal" style="margin-bottom: 0;">Estudiantes</h1>
            </header>

            <header style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.5rem;">
                <div>
                    <a href="crear.php" role="button" style="margin: 0;">Agregar nuevo</a>
                </div>

                <form action="index.php" method="GET" style="margin: 0; width: 100%; max-width: 400px;">
                    <fieldset role="group" style="margin-bottom: 0;">
                        <input type="search" name="buscar" placeholder="Buscar..." value="<?php echo htmlspecialchars($busqueda); ?>">
                        <button type="submit">Buscar</button>
                        <?php if (!empty($busqueda)): ?>
                            <a href="index.php" role="button" class="secondary outline">Limpiar</a>
                        <?php endif; ?>
                    </fieldset>
                </form>
            </header>

            <!-- TABLA CON BORDES Y COLORES -->
            <div class="overflow-auto">
                <table class="tabla-estudiantes">
                    <thead>
                        <tr>
                            <th>Apellido</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th class="col-acciones">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($res && $res->num_rows > 0): ?>
                        <?php while ($fila = $res->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($fila['apellido']); ?></td>
                                <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($fila['email']); ?></td>
                                <td class="col-acciones">
                                    <a href="editar.php?id=<?php echo $fila['id']; ?>" role="button" style="padding: 0.25rem 0.5rem; font-size: 0.85rem;">Editar</a>
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
        </article>
    </main>
</body>
</html>