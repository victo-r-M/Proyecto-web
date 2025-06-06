<?php
$serverName = "ELITEBOOK\SQLEXPRESS"; // o nombre del servidor SQL
$connectionOptions = array(
    "Database" => "InventarioDB", // Nombre de tu BD
    "Uid" => "sa",                // Usuario de SQL Server
    "PWD" => "12345",    // Contraseña de SQL Server
    "CharacterSet" => "UTF-8"
);

// Establecer conexión
$conn = sqlsrv_connect($serverName, $connectionOptions);

// Verificar conexión
if (!$conn) {
    echo json_encode(["error" => "Conexión fallida"]);
    exit;
}
?>

