<?php
session_start();
require_once('./conexion.php');

header('Content-Type: application/json');

try {
    $id_usuario = $_GET['id'] ?? null;

    if (!$id_usuario) {
        throw new Exception('ID de usuario no proporcionado');
    }

    $query_check = "SELECT COUNT(*) FROM tbl_usuarios WHERE id_usuario = ?";
    $stmt_check = $conexion->prepare($query_check);
    $stmt_check->execute([$id_usuario]);
    
    if ($stmt_check->fetchColumn() == 0) {
        throw new Exception('Usuario no encontrado');
    }

    $query = "DELETE FROM tbl_usuarios WHERE id_usuario = ?";
    $stmt = $conexion->prepare($query);
    $stmt->execute([$id_usuario]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}
?>