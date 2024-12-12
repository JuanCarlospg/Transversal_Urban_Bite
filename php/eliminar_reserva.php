<?php
session_start();
require_once('./conexion.php');

// Obtener el ID de la reserva
$id_reserva = $_GET['id_reserva'] ?? null;

if (!$id_reserva) {
    die("ID de reserva no proporcionado.");
}

// Primero obtener el id_mesa antes de eliminar la reserva
$query_mesa = "SELECT id_mesa FROM tbl_reservas WHERE id_reserva = ?";
$stmt_mesa = $conexion->prepare($query_mesa);
$stmt_mesa->execute([$id_reserva]);
$id_mesa = $stmt_mesa->fetchColumn();

if (!$id_mesa) {
    die("No se encontró la mesa asociada a esta reserva.");
}

// Obtener la información de la reserva para eliminar la ocupación
$query_reserva = "SELECT fecha, id_franja FROM tbl_reservas WHERE id_reserva = ?";
$stmt_reserva = $conexion->prepare($query_reserva);
$stmt_reserva->execute([$id_reserva]);
$reserva = $stmt_reserva->fetch(PDO::FETCH_ASSOC);

// Eliminar la ocupación asociada si existe
$query_delete_ocupacion = "DELETE FROM tbl_ocupaciones WHERE id_mesa = ? AND fecha_inicio LIKE ?";
$stmt_delete_ocupacion = $conexion->prepare($query_delete_ocupacion);
$stmt_delete_ocupacion->execute([$id_mesa, $reserva['fecha'] . '%']);

// Eliminar la reserva de la base de datos
$query_delete = "DELETE FROM tbl_reservas WHERE id_reserva = ?";
$stmt_delete = $conexion->prepare($query_delete);
$stmt_delete->execute([$id_reserva]);

// Redirigir de vuelta a la página de reservas con el id_mesa correcto
header("Location: reservar_mesa.php?id_mesa=$id_mesa&deleted=1");
exit();
?> 