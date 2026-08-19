<?php
require_once 'config/database.php';

// Validar que se haya recibido un ID válido por la URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id'];
    $conn = getConnection();

    // Borrado lógico (se marca como inactivo)
    $stmt = $conn->prepare("UPDATE estudiantes SET activo = 0 WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    // Si prefieres borrarlo definitivamente de la base de datos, usa:
    // $stmt = $conn->prepare("DELETE FROM estudiantes WHERE id = ?");

    $stmt->execute();
    $stmt->close();
    $conn->close();
}

// Redirigir de vuelta a la lista principal
header('Location: index.php');
exit;
?>