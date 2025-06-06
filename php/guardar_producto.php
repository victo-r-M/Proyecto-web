<?php
include "conexion.php";

// Recibir datos del formulario
$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$precioCompra = $_POST['precioCompra'];
$precioVenta = $_POST['precioVenta'];
$stock = $_POST['stock'];
$stockMinimo = $_POST['stockMinimo'];

// Manejo de imagen
$imagenNombre = null;
$uploadDir = "../img/productos/";

if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['imagen']['tmp_name'];
    $fileName = $_FILES['imagen']['name'];
    $fileSize = $_FILES['imagen']['size'];
    $fileType = $_FILES['imagen']['type'];

    // Limpiar el nombre del archivo (quitar espacios, acentos, etc.)
    $fileName = preg_replace("/[^a-zA-Z0-9\._-]/", "", basename($fileName));
    
    // Convertir a minúsculas (opcional)
    $fileName = strtolower($fileName);

    $destPath = $uploadDir . $fileName;

    // Validar extensión
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (in_array($fileExtension, $allowedExtensions)) {
        // Si ya existe, puedes renombrar (opcional)
        if (file_exists($destPath)) {
            $fileName = pathinfo($fileName, PATHINFO_FILENAME) . '_' . time() . '.' . $fileExtension;
            $destPath = $uploadDir . $fileName;
        }

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $imagenNombre = $fileName;
        } else {
            echo "<script>alert('Error al mover la imagen.'); window.history.back();</script>";
            exit();
        }
    } else {
        echo "<script>alert('Formato de imagen no permitido.'); window.history.back();</script>";
        exit();
    }
} else {
    if (!isset($_POST['id'])) {
        echo "<script>alert('Debe subir una imagen válida.'); window.history.back();</script>";
        exit();
    }
    $imagenNombre = null;
}

// Consulta con parámetros

if (isset($_POST['id'])) {
    // EDICIÓN
    $id = intval($_POST['id']);

    // Si se sube nueva imagen, actualiza también la imagen
    if ($imagenNombre) {
        $sql = "UPDATE Productos SET Nombre=?, Descripcion=?, PrecioCompra=?, PrecioVenta=?, Stock=?, StockMinimo=?, Imagen=? WHERE IdProducto=?";
        $params = [$nombre, $descripcion, $precioCompra, $precioVenta, $stock, $stockMinimo, $imagenNombre, $id];
    } else {
        $sql = "UPDATE Productos SET Nombre=?, Descripcion=?, PrecioCompra=?, PrecioVenta=?, Stock=?, StockMinimo=? WHERE IdProducto=?";
        $params = [$nombre, $descripcion, $precioCompra, $precioVenta, $stock, $stockMinimo, $id];
    }
        header("Location: ../html/lista_producto.html");

} else {
    // INSERCIÓN
    $sql = "INSERT INTO Productos (Nombre, Descripcion, PrecioCompra, PrecioVenta, Stock, StockMinimo, Imagen) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $params = [$nombre, $descripcion, $precioCompra, $precioVenta, $stock, $stockMinimo, $imagenNombre];
}


$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    echo "Error al insertar el producto:<br>";
    die(print_r(sqlsrv_errors(), true));
} else { 
    if (isset($_POST['id'])) {
        //  si fue una edición
        header("Location: ../html/lista_producto.php");
    }else{
    header("Location: ../html/producto_nuevo.php");
    exit();
    }
}
?>
