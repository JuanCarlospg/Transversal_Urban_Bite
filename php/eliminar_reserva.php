<?php
session_start();
require_once('./conexion.php');

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    header("HTTP/1.1 405 Method Not Allowed");
    exit();
}

$id_reserva = $_GET['id_reserva'] ?? null;

if (!$id_reserva) {
    echo json_encode(['success' => false, 'message' => 'ID de reserva no proporcionado']);
    exit();
}

try {
    $query_mesa = "SELECT id_mesa FROM tbl_reservas WHERE id_reserva = ?";
    $stmt_mesa = $conexion->prepare($query_mesa);
    $stmt_mesa->execute([$id_reserva]);
    $id_mesa = $stmt_mesa->fetchColumn();

    if (!$id_mesa) {
        echo json_encode(['success' => false, 'message' => 'No se encontró la mesa asociada a esta reserva']);
        exit();
    }

    $query_reserva = "SELECT fecha, id_franja FROM tbl_reservas WHERE id_reserva = ?";
    $stmt_reserva = $conexion->prepare($query_reserva);
    $stmt_reserva->execute([$id_reserva]);
    $reserva = $stmt_reserva->fetch(PDO::FETCH_ASSOC);

    $query_delete_ocupacion = "DELETE FROM tbl_ocupaciones WHERE id_mesa = ? AND fecha_inicio LIKE ?";
    $stmt_delete_ocupacion = $conexion->prepare($query_delete_ocupacion);
    $stmt_delete_ocupacion->execute([$id_mesa, $reserva['fecha'] . '%']);

    $query_delete = "DELETE FROM tbl_reservas WHERE id_reserva = ?";
    $stmt_delete = $conexion->prepare($query_delete);
    $stmt_delete->execute([$id_reserva]);

    echo json_encode([
        'success' => true,
        'id_mesa' => $id_mesa
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al eliminar la reserva: ' . $e->getMessage()
    ]);
}
?> 