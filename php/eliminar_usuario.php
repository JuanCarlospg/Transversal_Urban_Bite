<?php
session_start();
require_once('./conexion.php');

$id_usuario = $_GET['id'];
$query = "DELETE FROM tbl_usuarios WHERE id_usuario = ?";
$stmt = $conexion->prepare($query);
$stmt->execute([$id_usuario]);

header("Location: ../gestionar_usuarios.php");
exit();
?>