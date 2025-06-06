<?php
header('Content-Type: application/json');

include 'conexion.php';

$sql = "SELECT IdProducto, Nombre, Descripcion, PrecioCompra, PrecioVenta, Stock, StockMinimo, FechaRegistro, Imagen FROM Productos";
$stmt = sqlsrv_query($conn, $sql);

$productos = [];

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $productos[] = [
    "id" => $row["IdProducto"],
    "nombre" => $row["Nombre"],
    "descripcion" => $row["Descripcion"],
    "precioCompra" => floatval($row["PrecioCompra"]),
    "precioVenta" => floatval($row["PrecioVenta"]),
    "stock" => $row["Stock"],
    "stockMinimo" => $row["StockMinimo"],
    "fecha" => $row["FechaRegistro"]->format('Y-m-d H:i:s'),
     "imagen" => $row["Imagen"] ? $row["Imagen"] : "sin_imagen.png"
    
];

}

echo json_encode($productos);
