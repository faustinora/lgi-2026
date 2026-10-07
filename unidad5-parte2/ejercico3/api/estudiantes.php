<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/database.php';

$conn = getConnection();
$metodo = $_SERVER['REQUEST_METHOD'];

// 1. GET: Consultar / Buscar
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

// 2. POST: Crear
if ($metodo === 'POST') {
    $inputRaw = file_get_contents('php://input');
    $datos = json_decode($inputRaw, true) ?? $_POST;

    $nombre   = trim($datos['nombre'] ?? '');
    $apellido = trim($datos['apellido'] ?? '');
    $email    = trim($datos['email'] ?? '');

    if (empty($nombre) || empty($apellido) || empty($email)) {
        http_response_code(400);
        echo json_encode(["error" => "Todos los campos son obligatorios."]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["error" => "El correo no es válido."]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO estudiantes (nombre, apellido, email, activo) VALUES (?, ?, ?, 1)");
    $stmt->bind_param("sss", $nombre, $apellido, $email);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode([
            "mensaje" => "Estudiante creado correctamente.",
            "id" => $stmt->insert_id
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error al guardar los datos."]);
    }

    $stmt->close();
    $conn->close();
    exit;
}

//PUT:Actualizar / Editar
if ($metodo === 'PUT') {
    $inputRaw = file_get_contents('php://input');
    $datos = json_decode($inputRaw, true);

    $id       = (int)($datos['id'] ?? 0);
    $nombre   = trim($datos['nombre'] ?? '');
    $apellido = trim($datos['apellido'] ?? '');
    $email    = trim($datos['email'] ?? '');

    if ($id <= 0 || empty($nombre) || empty($apellido) || empty($email)) {
        http_response_code(400);
        echo json_encode(["error" => "Todos los campos son obligatorios."]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["error" => "El correo no es válido."]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE estudiantes SET nombre = ?, apellido = ?, email = ? WHERE id = ? AND activo = 1");
    $stmt->bind_param("sssi", $nombre, $apellido, $email, $id);

    if ($stmt->execute()) {
        echo json_encode(["mensaje" => "Actualización correcta."]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error al actualizar."]);
    }

    $stmt->close();
    $conn->close();
    exit;
}

// Borrar
if ($metodo === 'DELETE') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($id <= 0) {
        $inputRaw = file_get_contents('php://input');
        $datos = json_decode($inputRaw, true);
        $id = (int)($datos['id'] ?? 0);
    }

    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(["error" => "ID no válido."]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE estudiantes SET activo = 0 WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["mensaje" => "Eliminación correcta."]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error al eliminar."]);
    }

    $stmt->close();
    $conn->close();
    exit;
}

http_response_code(405);
echo json_encode(["error" => "Método no permitido."]);