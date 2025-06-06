<?php
header('Content-Type: application/json');
include 'conexion.php'; 

$sql = "SELECT v.IdVenta, u.NombreCompleto AS Vendedor, v.FechaVenta, v.Total
        FROM Ventas v
        INNER JOIN Usuarios u ON v.IdUsuario = u.IdUsuario
        ORDER BY v.FechaVenta DESC";

$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    echo json_encode(['error' => 'Error en la consulta']);
    exit;
}

$ventas = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    // Formatear FechaVenta a string
    $fechaStr = $row['FechaVenta'] ? $row['FechaVenta']->format('Y-m-d H:i:s') : null;
    $ventas[] = [
        'IdVenta' => $row['IdVenta'],
        'Vendedor' => $row['Vendedor'],
        'FechaVenta' => $fechaStr,
        'Total' => $row['Total']
    ];
}

echo json_encode($ventas);
