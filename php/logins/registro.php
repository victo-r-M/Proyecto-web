<?php
header('Content-Type: application/json');
require_once '../conexion.php'; // Ajusta ruta si es necesario

// Recibir datos POST
$nombre = $_POST['nombre'] ?? '';
$correo = $_POST['correo'] ?? '';
$contrasena = $_POST['contrasena'] ?? '';

if (!$nombre || !$correo || !$contrasena) {
    echo json_encode(['success' => false, 'error' => 'Faltan datos obligatorios']);
    exit;
}

// Validar si ya existe correo
$sqlCheck = "SELECT * FROM Usuarios WHERE Correo = ?";
$paramsCheck = [$correo];
$stmtCheck = sqlsrv_query($conn, $sqlCheck, $paramsCheck);

if ($stmtCheck === false) {
    echo json_encode(['success' => false, 'error' => 'Error en consulta para verificar correo: '.print_r(sqlsrv_errors(), true)]);
    exit;
}

if (sqlsrv_has_rows($stmtCheck)) {
    echo json_encode(['success' => false, 'error' => 'El correo ya está registrado']);
    exit;
}

// Verificar si existe un dueño ya registrado
$sqlDueno = "SELECT u.IdUsuario FROM Usuarios u INNER JOIN Roles r ON u.IdRol = r.IdRol WHERE r.NombreRol = 'Dueño'";
$stmtDueno = sqlsrv_query($conn, $sqlDueno);

if ($stmtDueno === false) {
    echo json_encode(['success' => false, 'error' => 'Error en consulta para verificar dueño: '.print_r(sqlsrv_errors(), true)]);
    exit;
}

$existeDueno = sqlsrv_has_rows($stmtDueno);

if ($existeDueno) {
    // Ya hay dueño, entonces nuevo usuario es empleado
    $idRol = 2; // Asumiendo que 2 = Empleado
} else {
    // No hay dueño, este usuario será dueño
    $idRol = 1; // Asumiendo que 1 = Dueño
}

// Encriptar contraseña (usa password_hash)
$contrasenaHash = password_hash($contrasena, PASSWORD_DEFAULT);

// Insertar nuevo usuario
$sqlInsert = "INSERT INTO Usuarios (NombreCompleto, Correo, Contraseña, IdRol) VALUES (?, ?, ?, ?)";
$paramsInsert = [$nombre, $correo, $contrasenaHash, $idRol];

$stmtInsert = sqlsrv_query($conn, $sqlInsert, $paramsInsert);

if ($stmtInsert === false) {
    echo json_encode(['success' => false, 'error' => 'Error al insertar usuario: '.print_r(sqlsrv_errors(), true)]);
    exit;
}

echo json_encode(['success' => true]);
?>
