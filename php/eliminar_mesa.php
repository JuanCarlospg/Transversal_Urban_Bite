<?php
session_start();
require_once('./conexion.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

$id_mesa = $_GET['id'] ?? null;
$id_sala = $_GET['id_sala'] ?? null;

if (!$id_mesa || !$id_sala) {
    echo json_encode(['success' => false, 'message' => 'ID de mesa o ID de sala no proporcionados']);
    exit();
}

try {
    $query_check = "SELECT COUNT(*) FROM tbl_mesas WHERE id_mesa = ?";
    $stmt_check = $conexion->prepare($query_check);
    $stmt_check->execute([$id_mesa]);
    
    if ($stmt_check->fetchColumn() == 0) {
        echo json_encode(['success' => false, 'message' => 'La mesa no existe']);
        exit();
    }

    $query = "DELETE FROM tbl_mesas WHERE id_mesa = ?";
    $stmt = $conexion->prepare($query);
    
    if ($stmt->execute([$id_mesa])) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Error al eliminar la mesa');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al eliminar la mesa: ' . $e->getMessage()
    ]);
}
?>