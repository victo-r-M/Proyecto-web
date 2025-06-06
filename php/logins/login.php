<?php
include '../conexion.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = $_POST['correo'];
    $contraseña = $_POST['contraseña'];

    $sql = "SELECT IdUsuario, Contraseña, IdRol, NombreCompleto FROM Usuarios WHERE Correo = ? AND Activo = 1";
    $params = array($correo);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        // Error en la consulta
        header("Location: ../../index.php?error=Error en la consulta");
        exit();
    }

    $usuario = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    if ($usuario) {
        if (password_verify($contraseña, $usuario['Contraseña'])) {
            $_SESSION['IdUsuario'] = $usuario['IdUsuario'];
            $_SESSION['Nombre'] = $usuario['NombreCompleto'];
            $_SESSION['IdRol'] = $usuario['IdRol'];

            header("Location: ../../main.php"); 
            exit();
        } else {
            header("Location: ../../index.php?error=Contraseña incorrecta");
            exit();
        }
    } else {
        header("Location: ../../index.php?error=Correo no registrado");
        exit();
    }
} else {
    header("Location: ../../index.php?error=Acceso no permitido");
    exit();
}
?>
