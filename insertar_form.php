<?php
require_once 'funcion_database.php';
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $email = $_POST['email'] ?? '';

    // Validación de los datos
    if (empty($nombre) || empty($apellido) || empty($email)) {
        $mensaje = 'Todos los campos son obligatorios.';
    }else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = 'El correo electrónico no es válido.';
    } else {
        $conn = getConnection();

        $nombre = $conn->real_escape_string($nombre);
        $apellido = $conn->real_escape_string($apellido);
        $email = $conn->real_escape_string($email);
        
        $sql= ("INSERT INTO estudiantes (nombre, apellido, email) VALUES ('$nombre', '$apellido', '$email')");

        if($conn->query($sql)){
            $mensaje = 'Estudiante agregado correctamente. ID:'.$conn->insert_id;
        } else {
            $mensaje = 'Error al agregar estudiante: ' . $conn->error;
        }
    }
}
?>
<html>
<html lang="es">
<body>
    <? php if ($mensaje): ?>
        <p> <strong><?= htmlspecialchars($mensaje)  ?></strong></p>
    <?php endif;?>

</body>
</html>