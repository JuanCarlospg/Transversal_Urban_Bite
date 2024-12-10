<?php
session_start();
require_once('./conexion.php');

// Obtener el ID de la reserva
$id_reserva = $_GET['id_reserva'] ?? null;
$id_mesa = $_GET['id_mesa'] ?? null;

if (!$id_reserva || !$id_mesa) {
    die("ID de reserva o mesa no proporcionado.");
}

// Eliminar la reserva de la base de datos
$query_delete = "DELETE FROM tbl_reservas WHERE id_reserva = ?";
$stmt_delete = $conexion->prepare($query_delete);
$stmt_delete->execute([$id_reserva]);

// Redirigir de vuelta a la página de reservas
header("Location: reservar_mesa.php?id_mesa=$id_mesa");
exit();
?> 