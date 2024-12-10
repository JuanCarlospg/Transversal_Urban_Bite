<?php
session_start();
require_once('./conexion.php');

// Obtener tipo de recurso y verificar su validez
$tipo = $_GET['tipo'] ?? 'salas';
$recursos = [
    'salas' => 'tbl_salas',
    'mesas' => 'tbl_mesas',
    'sillas' => 'tbl_sillas'
];

if (!array_key_exists($tipo, $recursos)) {
    die("Recurso no válido.");
}

$tabla = $recursos[$tipo];
$id_campo = 'id_' . substr($tipo, 0, -1);
$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID no proporcionado.");
}

// Eliminar recurso
$query = "DELETE FROM $tabla WHERE $id_campo = ?";
$stmt = $conexion->prepare($query);
$stmt->execute([$id]);

header("Location: ../gestionar_salas.php?tipo=$tipo");
exit();
?>