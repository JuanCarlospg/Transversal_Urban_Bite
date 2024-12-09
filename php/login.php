<?php
require_once('conexion.php');
session_start();
if (isset($_POST['btn_iniciar_sesion']) && !empty($_POST['Usuario']) && !empty($_POST['Contra'])) {
    $contra = htmlspecialchars($_POST['Contra']);
    $usuario = htmlspecialchars($_POST['Usuario']);
    $_SESSION['usuario'] = $usuario;
    try {
        $conexion->beginTransaction();

        $sql = "SELECT u.nombre_user, u.contrasena, r.nombre_rol FROM tbl_usuarios u JOIN tbl_roles r ON u.id_rol = r.id_rol WHERE u.nombre_user = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$usuario]);

        if ($usuario_db = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (password_verify($contra, $usuario_db['contrasena'])) {
                $_SESSION['Usuario'] = $usuario;
                $_SESSION['rol'] = $usuario_db['nombre_rol'];
                header("Location: ../menu.php");
                exit();
            } else {
                header('Location:../index.php?error=contrasena_incorrecta');
            }
        } else {
            header('Location:../index.php?error=usuario_no_encontrado');
        }

        $conexion->commit();
    } catch (Exception $e) {
        $conexion->rollBack();
        echo "Se produjo un error: " . htmlspecialchars($e->getMessage());
    }
} else {
    header('Location: ../index.php?error=campos_vacios');
}