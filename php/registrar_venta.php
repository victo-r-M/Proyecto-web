<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['IdUsuario'])) {
    http_response_code(401);
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

$idUsuario = $_SESSION['IdUsuario'];
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !is_array($data)) {
    http_response_code(400);
    echo json_encode(["error" => "Datos inválidos o vacíos."]);
    exit;
}

// Validar que todos los productos tengan id, cantidad y precio válidos
foreach ($data as $producto) {
    if (
        !isset($producto['id'], $producto['cantidad'], $producto['precio']) ||
        !is_numeric($producto['id']) ||
        !is_numeric($producto['cantidad']) ||
        !is_numeric($producto['precio'])
    ) {
        http_response_code(400);
        echo json_encode(["error" => "Producto con datos incompletos o inválidos."]);
        exit;
    }
}

// Iniciar transacción
if (!sqlsrv_begin_transaction($conn)) {
    http_response_code(500);
    echo json_encode(["error" => "Error al iniciar transacción."]);
    exit;
}

// Calcular total
$total = 0;
foreach ($data as $p) {
    $total += $p['precio'] * $p['cantidad'];
}

// Insertar venta
$sqlVenta = "INSERT INTO Ventas (IdUsuario, Total, FechaVenta) OUTPUT INSERTED.IdVenta VALUES (?, ?, GETDATE())";
$paramsVenta = [$idUsuario, $total];
$stmtVenta = sqlsrv_query($conn, $sqlVenta, $paramsVenta);

if ($stmtVenta === false) {
    sqlsrv_rollback($conn);
    http_response_code(500);
    echo json_encode(["error" => "Error al registrar venta", "detalle" => sqlsrv_errors()]);
    exit;
}

$row = sqlsrv_fetch_array($stmtVenta, SQLSRV_FETCH_ASSOC);
$idVenta = $row['IdVenta'];

// Insertar detalles de venta y actualizar stock
foreach ($data as $producto) {
    $sqlDetalle = "INSERT INTO DetalleVenta (IdVenta, IdProducto, Cantidad, PrecioUnitario)
                   VALUES (?, ?, ?, ?)";
    $paramsDetalle = [$idVenta, $producto['id'], $producto['cantidad'], $producto['precio']];
    $stmtDetalle = sqlsrv_query($conn, $sqlDetalle, $paramsDetalle);

    if ($stmtDetalle === false) {
        sqlsrv_rollback($conn);
        http_response_code(500);
        echo json_encode(["error" => "Error al registrar detalle", "detalle" => sqlsrv_errors()]);
        exit;
    }

    $sqlStock = "UPDATE Productos SET Stock = Stock - ? WHERE IdProducto = ?";
    $paramsStock = [$producto['cantidad'], $producto['id']];
    $stmtStock = sqlsrv_query($conn, $sqlStock, $paramsStock);

    if ($stmtStock === false) {
        sqlsrv_rollback($conn);
        http_response_code(500);
        echo json_encode(["error" => "Error al actualizar stock", "detalle" => sqlsrv_errors()]);
        exit;
    }
}

// Confirmar transacción
if (!sqlsrv_commit($conn)) {
    http_response_code(500);
    echo json_encode(["error" => "Error al confirmar la transacción"]);
    exit;
}

echo json_encode(["mensaje" => "Venta registrada correctamente"]);
?>
