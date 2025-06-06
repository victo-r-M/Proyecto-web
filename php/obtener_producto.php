<?php
include "conexion.php";

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(["error" => "ID no proporcionado"]);
    exit();
}

$id = intval($_GET['id']);

$sql = "SELECT * FROM Productos WHERE IdProducto = ?";
$params = [$id];
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(["error" => "Error al consultar la base de datos"]);
    exit();
}

$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if ($row) {
    echo json_encode([
        "id" => $row["IdProducto"],
        "nombre" => $row["Nombre"],
        "descripcion" => $row["Descripcion"],
        "precioCompra" => $row["PrecioCompra"],
        "precioVenta" => $row["PrecioVenta"],
        "stock" => $row["Stock"],
        "stockMinimo" => $row["StockMinimo"],
        "imagen" => $row["Imagen"]
    ]);
} else {
    echo json_encode([]);
}
?>
