<?php
session_start();

if (!isset($_SESSION['IdUsuario'])) {
    header("Location: ../../index.php?error=Debes iniciar sesión");
    exit();
}

$nombreUsuario = $_SESSION['Nombre'];
$rol = $_SESSION['IdRol'];
?>