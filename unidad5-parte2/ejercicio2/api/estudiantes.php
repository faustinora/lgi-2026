<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/../config/database.php';

$conn = getConnection();
$metodo = $_SERVER['REQUEST_METHOD'];


if ($metodo === 'GET') {
    $busqueda = trim($_GET['q'] ?? '');

    if (!empty($busqueda)) {
        $stmt = $conn->prepare("SELECT id, nombre, apellido, email FROM estudiantes WHERE activo = 1 AND (nombre LIKE ? OR apellido LIKE ? OR email LIKE ?) ORDER BY apellido ASC");
        $term = "%" . $busqueda . "%";
        $stmt->bind_param("sss", $term, $term, $term);
        $stmt->execute();
        $res = $stmt->get_result();
    } else {
        $res = $conn->query("SELECT id, nombre, apellido, email FROM estudiantes WHERE activo = 1 ORDER BY apellido ASC");
    }

    $estudiantes = [];
    if ($res) {
        while ($fila = $res->fetch_assoc()) {
            $estudiantes[] = $fila;
        }
    }

    echo json_encode($estudiantes, JSON_UNESCAPED_UNICODE);
    $conn->close();
    exit;
}


if ($metodo === 'POST') {
    $inputRaw = file_get_contents('php://input');
    $datos = json_decode($inputRaw, true);

    if (!$datos) {
        $datos = $_POST;
    }

    $nombre   = trim($datos['nombre'] ?? '');
    $apellido = trim($datos['apellido'] ?? '');
    $email    = trim($datos['email'] ?? '');


    if (empty($nombre) || empty($apellido) || empty($email)) {
        http_response_code(400);
        echo json_encode(["error" => "Todos los campos (Nombre, Apellido, Email) son obligatorios."]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["error" => "El correo electrónico no es válido."]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO estudiantes (nombre, apellido, email, activo) VALUES (?, ?, ?, 1)");
    $stmt->bind_param("sss", $nombre, $apellido, $email);

    if ($stmt->execute()) {
        $nuevoId = $stmt->insert_id;
        http_response_code(201);
        echo json_encode([
            "mensaje" => "Estudiante agregado correctamente.",
            "estudiante" => [
                "id" => $nuevoId,
                "nombre" => $nombre,
                "apellido" => $apellido,
                "email" => $email
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error al guardar en la base de datos: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit;
}


http_response_code(405);
echo json_encode(["error" => "Método no permitido."]);