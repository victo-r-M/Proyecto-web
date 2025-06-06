<?php
include 'conexion.php';

$term = $_GET['term'] ?? '';

$sql = "SELECT IdProducto, Nombre, PrecioVenta, Imagen FROM Productos WHERE Nombre LIKE ? AND Stock > 0";
$params = ["%$term%"];

$stmt = sqlsrv_query($conn, $sql, $params);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$productos = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $productos[] = [
        'id' => $row['IdProducto'],
        'nombre' => $row['Nombre'],
        'precio' => floatval($row['PrecioVenta']),
        'imagen' => $row['Imagen']
    ];
}

header('Content-Type: application/json');
echo json_encode($productos);
?>
