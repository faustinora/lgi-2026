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

$accion = $_GET['accion'] ?? '';

if ($metodo === 'GET' && $accion === 'listar_ventas') {
    $res = $conn->query("SELECT * FROM ventas ORDER BY id DESC");
    $ventas = [];
    if ($res) {
        while ($fila = $res->fetch_assoc()) {
            $ventas[] = $fila;
        }
    }
    echo json_encode($ventas, JSON_UNESCAPED_UNICODE);
    $conn->close();
    exit;
}

if ($metodo === 'POST' && $accion === 'vender') {
    $inputRaw = file_get_contents('php://input');
    $datos = json_decode($inputRaw, true);

    $id_producto = (int)($datos['id_producto'] ?? 0);
    $cantidad = (int)($datos['cantidad'] ?? 0);

    if ($id_producto <= 0 || $cantidad <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Datos de venta no válidos.']);
        exit;
    }

    $stmtCheck = $conn->prepare("SELECT nombre, precio, stock FROM productos_prueba WHERE id = ? AND activo = 1");
    $stmtCheck->bind_param("i", $id_producto);
    $stmtCheck->execute();
    $resultado = $stmtCheck->get_result();
    $prod = $resultado->fetch_assoc();
    $stmtCheck->close();

    if (!$prod) {
        http_response_code(404);
        echo json_encode(['error' => 'Producto no encontrado.']);
        exit;
    }

    if ((int)$prod['stock'] < $cantidad) {
        http_response_code(400);
        echo json_encode(['error' => 'Stock insuficiente para realizar la venta.']);
        exit;
    }

    $nuevoStock = (int)$prod['stock'] - $cantidad;
    $totalCobrado = (float)$prod['precio'] * $cantidad;
    $nombreProducto = $prod['nombre'];

   
    $stmtUpdate = $conn->prepare("UPDATE productos_prueba SET stock = ? WHERE id = ?");
    $stmtUpdate->bind_param("ii", $nuevoStock, $id_producto);
    
    if ($stmtUpdate->execute()) {
        $stmtUpdate->close();

   
        $stmtInsert = $conn->prepare("INSERT INTO ventas (id_producto, nombre_producto, cantidad, total_cobrado) VALUES (?, ?, ?, ?)");
        $stmtInsert->bind_param("isid", $id_producto, $nombreProducto, $cantidad, $totalCobrado);
        
        if ($stmtInsert->execute()) {
            $stmtInsert->close();
            http_response_code(200);
            echo json_encode(['message' => '¡Venta registrada!']);
            $conn->close();
            exit;
        }
    }

    http_response_code(500);
    echo json_encode(['error' => 'Error al procesar la venta en la base de datos.']);
    $conn->close();
    exit;
}

if ($metodo === 'GET') {
    $busqueda = trim($_GET['q'] ?? '');

    if (!empty($busqueda)) {
        $stmt = $conn->prepare("SELECT * FROM productos_prueba WHERE activo = 1 AND (nombre LIKE ? OR codigo LIKE ?) ORDER BY nombre ASC");
        $term = "%$busqueda%";
        $stmt->bind_param("ss", $term, $term);
        $stmt->execute();
        $res = $stmt->get_result();
    } else {
        $res = $conn->query("SELECT * FROM productos_prueba WHERE activo = 1 ORDER BY nombre ASC");
    }

    $productos = [];
    if ($res) {
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

    if (empty($codigo) || empty($nombre) || $precio === '' || $stock === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Todos los campos son obligatorios.']);
        exit;
    }

    if (!is_numeric($precio) || $precio < 0 || !is_numeric($stock) || $stock < 0) {
        http_response_code(400);
        echo json_encode(["error" => "Precio y Stock deben ser números positivos."]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO productos_prueba (codigo, nombre, precio, stock, activo) VALUES (?, ?, ?, ?, 1)");
    $stmt->bind_param("ssdi", $codigo, $nombre, $precio, $stock);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(['message' => 'Producto agregado.']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error al cargar.']);
    }

    $stmt->close();
    $conn->close();
    exit;
}

if ($metodo === 'PUT') {
    $inputRaw = file_get_contents('php://input');
    $datos = json_decode($inputRaw, true);

    $id = (int)($datos['id'] ?? 0);
    $codigo = trim($datos['codigo'] ?? '');
    $nombre = trim($datos['nombre'] ?? '');
    $precio = trim($datos['precio'] ?? '');
    $stock = trim($datos['stock'] ?? '');

    if ($id <= 0 || empty($codigo) || empty($nombre) || $precio === '' || $stock === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Todos los campos son obligatorios.']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE productos_prueba SET codigo = ?, nombre = ?, precio = ?, stock = ? WHERE id = ? AND activo = 1");
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

    if ($id <= 0) {
        $inputRaw = file_get_contents('php://input');
        $datos = json_decode($inputRaw, true);
        $id = (int)($datos['id'] ?? 0);
    }

    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'ID no válido.']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE productos_prueba SET activo = 0 WHERE id = ?");
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