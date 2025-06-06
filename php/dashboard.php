<?php
include 'conexion.php';

// Total de productos
$sql = "SELECT COUNT(*) AS total_productos FROM Productos";
$stmt = sqlsrv_query($conn, $sql);
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
$totalProductos = $row['total_productos'];

// Ventas del mes actual
$sql = "SELECT ISNULL(SUM(Total), 0) AS ventas_mes 
        FROM Ventas 
        WHERE MONTH(FechaVenta) = MONTH(GETDATE()) 
        AND YEAR(FechaVenta) = YEAR(GETDATE())";
$stmt = sqlsrv_query($conn, $sql);
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
$ventasMes = $row['ventas_mes'];

// Productos bajos en stock
$sql = "SELECT COUNT(*) AS productos_bajo_stock 
        FROM Productos 
        WHERE Stock <= StockMinimo";
$stmt = sqlsrv_query($conn, $sql);
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
$productosBajoStock = $row['productos_bajo_stock'];

// Productos en stock (Stock > 0)
$sql = "SELECT SUM(Stock) AS productos_en_stock 
        FROM Productos ";
$stmt = sqlsrv_query($conn, $sql);
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
$productosEnStock = $row['productos_en_stock'];

// Productos sin stock
$sql = "SELECT COUNT(*) AS productos_sin_stock 
        FROM Productos 
        WHERE Stock = 0";
$stmt = sqlsrv_query($conn, $sql);
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
$productosSinStock = $row['productos_sin_stock'];

// Producto más vendido
$sql = "SELECT TOP 1 P.Nombre, SUM(DV.Cantidad) AS total_vendida
        FROM DetalleVenta DV
        JOIN Productos P ON DV.IdProducto = P.IdProducto
        GROUP BY P.Nombre
        ORDER BY total_vendida DESC";
$stmt = sqlsrv_query($conn, $sql);
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
$productoMasVendido = $row ? $row['Nombre'] . " ({$row['total_vendida']} uds)" : "Sin ventas";

// Producto menos vendido (pero que tenga al menos una venta)
$sql = "SELECT TOP 1 P.Nombre, SUM(DV.Cantidad) AS total_vendida
        FROM DetalleVenta DV
        JOIN Productos P ON DV.IdProducto = P.IdProducto
        GROUP BY P.Nombre
        HAVING SUM(DV.Cantidad) > 0
        ORDER BY total_vendida ASC";
$stmt = sqlsrv_query($conn, $sql);
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
$productoMenosVendido = $row ? $row['Nombre'] . " ({$row['total_vendida']} uds)" : "Sin ventas";


// Total de ventas realizadas
$sql = "SELECT COUNT(*) AS total_ventas FROM Ventas";
$stmt = sqlsrv_query($conn, $sql);
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
$totalVentas = $row['total_ventas'];
?>
