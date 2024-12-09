<?php
session_start();
require_once('./conexion.php');

$id_usuario = $_GET['id'];
$query = "DELETE FROM tbl_usuarios WHERE id_usuario = ?";
$stmt = mysqli_prepare($conexion, $query);
mysqli_stmt_bind_param($stmt, "i", $id_usuario);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("Location: ../gestionar_usuarios.php");
exit();
?> 