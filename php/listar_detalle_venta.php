<?php
header('Content-Type: application/json');
include 'conexion.php';

if (!isset($_GET['idVenta'])) {
    echo json_encode(['error' => 'Falta el parámetro idVenta']);
    exit;
}

$idVenta = intval($_GET['idVenta']);

$sql = "SELECT p.Nombre, dv.Cantidad, dv.PrecioUnitario, dv.Subtotal
        FROM DetalleVenta dv
        INNER JOIN Productos p ON dv.IdProducto = p.IdProducto
        WHERE dv.IdVenta = ?";

$params = [$idVenta];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    echo json_encode(['error' => 'Error en la consulta']);
    exit;
}

$detalle = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $detalle[] = [
        'Nombre' => $row['Nombre'],
        'Cantidad' => $row['Cantidad'],
        'PrecioUnitario' => $row['PrecioUnitario'],
        'Subtotal' => $row['Subtotal']
    ];
}

echo json_encode($detalle);
