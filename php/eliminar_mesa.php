<?php
session_start();
require_once('./conexion.php');

// Obtener el ID de la mesa y el ID de la sala
$id_mesa = $_GET['id'] ?? null;
$id_sala = $_GET['id_sala'] ?? null;

if (!$id_mesa || !$id_sala) {
    die("ID de mesa o ID de sala no proporcionados.");
}

// Eliminar la mesa de la base de datos
$query = "DELETE FROM tbl_mesas WHERE id_mesa = ?";
$stmt = mysqli_prepare($conexion, $query);
mysqli_stmt_bind_param($stmt, "i", $id_mesa);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// Redirigir a la página anterior con un mensaje de éxito
header("Location: añadir_mesa.php?id_sala=" . $id_sala . "&mensaje=mesa_eliminada");
exit();
?> 