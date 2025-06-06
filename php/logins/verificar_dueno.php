<?php
include "../conexion.php";

$query = "SELECT COUNT(*) AS Total FROM Usuarios U
          INNER JOIN Roles R ON U.IdRol = R.IdRol
          WHERE R.NombreRol = 'Dueño'";

$stmt = sqlsrv_query($conn, $query);

if ($stmt && $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    echo json_encode(["existeDueno" => $row["Total"] > 0]);
} else {
    echo json_encode(["error" => "Error al consultar la base de datos"]);
}
?>
