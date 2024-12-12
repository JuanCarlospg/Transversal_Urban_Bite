<?php
session_start();
require_once('./conexion.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

$tipo = $_GET['tipo'] ?? 'salas';
$recursos = [
    'salas' => 'tbl_salas',
    'mesas' => 'tbl_mesas',
    'sillas' => 'tbl_sillas'
];

if (!array_key_exists($tipo, $recursos)) {
    echo json_encode(['success' => false, 'message' => 'Recurso no válido']);
    exit();
}

$tabla = $recursos[$tipo];
$id_campo = 'id_' . substr($tipo, 0, -1);
$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
    exit();
}

try {
    $query_check = "SELECT COUNT(*) FROM $tabla WHERE $id_campo = ?";
    $stmt_check = $conexion->prepare($query_check);
    $stmt_check->execute([$id]);
    
    if ($stmt_check->fetchColumn() == 0) {
        echo json_encode(['success' => false, 'message' => 'Elemento no encontrado']);
        exit();
    }

    $query = "DELETE FROM $tabla WHERE $id_campo = ?";
    $stmt = $conexion->prepare($query);
    $stmt->execute([$id]);

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error al eliminar: ' . $e->getMessage()
    ]);
}
?>