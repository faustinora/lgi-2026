<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config/basedato.php';

$conn = getConnection();
$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    $busqueda = trim($_GET['q'] ??'');

    if(!empty($busqueda)){
        $stmt = $conn->prepare("SELECT * FROM productos WHERE nombre LIKE ?");
        $term = "%$busqueda%";
        $stmt->bind_param("s", $term);
        $stmt->execute();
        $res = $stmt->get_result();
    } else{
        $res = $conn->query("SELECT * FROM productos ORDER BY nombre ASC");
    }

$productos = [];
if ($res){
    while ($fila = $res->fetch_assoc()) {
        $productos[] = $fila;
    }
}

echo json_encode($productos, JSON_UNESCAPED_UNICODE);
$conn->close();
exit;
}

if ($metodo === 'POST') {
    $inputRaw = file_get_contents('php://input');
    $datos = json_decode($inputRaw, true) ?? $_POST;

    $codigo = trim($datos['codigo'] ?? '');
    $nombre = trim($datos['nombre'] ?? '');
    $precio = trim($datos['precio'] ?? '');
    $stock = trim($datos['stock'] ?? '');

    if (empty($codigo) || empty($nombre) || empty($precio) || empty($stock)) {
        http_response_code(400);
        echo json_encode(['error' => 'Todos los campos son obligatorios.']);
        exit;
    }

    if (!is_numeric($precio) || $precio < 0 || !is_numeric($stock) || $stock < 0){
        http_response_code(400);
        echo json_encode(["error" => "Precio y Stock deben ser números positivos"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO productos (codigo, nombre, precio, stock) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssdi", $codigo, $nombre, $precio, $stock);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(['message' => 'Producto agregado exitosamente.']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error al cargar el producto.']);
    }

    $stmt->close();
    $conn->close();
    exit;
}

if ($metodo === 'PUT') {
    $inputRaw = file_get_contents('php://input');
    $datos = json_decode($inputRaw, true);

    $id = (int)($datos['id'] ?? '');
    $codigo = trim($datos['codigo'] ?? '');
    $nombre = trim($datos['nombre'] ?? '');
    $precio = trim($datos['precio'] ?? '');
    $stock = trim($datos['stock'] ?? '');

    if (empty($id) || empty($codigo) || empty($nombre) || empty($precio) || empty($stock)) {
        http_response_code(400);
        echo json_encode(['error' => 'Todos los campos son obligatorios.']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE productos SET codigo = ?, nombre = ?, precio = ?, stock = ? WHERE id = ?");
    $stmt->bind_param("ssdii", $codigo, $nombre, $precio, $stock, $id);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(['message' => 'Producto actualizado.']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error al actualizar.']);
    }

    $stmt->close();
    $conn->close();
    exit;
}

if ($metodo === 'DELETE') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($id <= 0){
        $inputRaw = file_get_contents('php://input');
        $datos = json_decode($inputRaw, true);
        $id = (int)($datos['id'] ?? 0);
    }

    if ($id<=0) {
        http_response_code(400);
        echo json_encode(['error' => 'ID no válido.']);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(['message' => 'Producto eliminado.']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error al eliminar.']);
    }

    $stmt->close();
    $conn->close();
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Método no permitido.']);