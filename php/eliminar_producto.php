<?php
include("conexion.php");
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "DELETE" && isset($_GET["id"])) {
    $id = $_GET["id"];
    $sql = "DELETE FROM Productos WHERE IdProducto = ?";
    $stmt = sqlsrv_prepare($conn, $sql, array(&$id));

    if (sqlsrv_execute($stmt)) {
        http_response_code(200);
        echo json_encode(["mensaje" => "Producto eliminado correctamente"]);
    } else {
        http_response_code(500);
        echo json_encode(["mensaje" => "Error al eliminar el producto"]);
    }
} else {
    http_response_code(400);
    echo json_encode(["mensaje" => "Solicitud no válida"]);
}
?>
