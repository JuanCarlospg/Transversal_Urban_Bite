<?php
session_start();
require_once('./conexion.php');

$id_usuario = $_GET['id'];
$query_usuario = "SELECT * FROM tbl_usuarios WHERE id_usuario = ?";
$stmt_usuario = $conexion->prepare($query_usuario);
$stmt_usuario->execute([$id_usuario]);
$usuario = $stmt_usuario->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_user = $_POST['nombre_user'];
    $id_rol = $_POST['id_rol'];
    $contrasena = $_POST['contrasena'];

    if (!empty($contrasena)) {
        $contrasena_hash = password_hash($contrasena, PASSWORD_BCRYPT);
        $query = "UPDATE tbl_usuarios SET nombre_user = ?, id_rol = ?, contrasena = ? WHERE id_usuario = ?";
        $stmt = $conexion->prepare($query);
        $stmt->execute([$nombre_user, $id_rol, $contrasena_hash, $id_usuario]);
    } else {
        $query = "UPDATE tbl_usuarios SET nombre_user = ?, id_rol = ? WHERE id_usuario = ?";
        $stmt = $conexion->prepare($query);
        $stmt->execute([$nombre_user, $id_rol, $id_usuario]);
    }

    if ($stmt->rowCount()) {
        header("Location: ../gestionar_usuarios.php");
        exit();
    } else {
        echo "Error al actualizar el usuario.";
    }
}

// Obtener roles para el formulario
$query_roles = "SELECT * FROM tbl_roles";
$stmt_roles = $conexion->query($query_roles);
$roles = $stmt_roles->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Editar Usuario</title>
</head>
<body>
    <div class="container">
        <nav class="navegacion">
            <div class="navbar-left">
                <a href="../menu.php"><img src="../img/logo.png" alt="Logo de la Marca" class="logo" style="width: 100%;"></a>
                <a href="../registro.php"><img src="../img/lbook.png" alt="Ícono adicional" class="navbar-icon"></a>
            </div>
            
            <div class="navbar-title">
                <h3>Bienvenido <?php if (isset($_SESSION['usuario'])) {
                                    echo $_SESSION['usuario'];
                                } ?></h3>
            </div>
            <div class="navbar-right" style="margin-right: 18px;">
                <a href="../gestionar_usuarios.php"><img src="../img/atras.png" alt="Atras" class="navbar-icon"></a>
            </div>

            <div class="navbar-right">
                <a href="../salir.php"><img src="../img/logout.png" alt="Logout" class="navbar-icon"></a>
            </div>
        </nav>
    </div>

    <div class="container container-crud">
        <h2 class="mb-4">Editar Usuario</h2>
        <form method="POST" class="form-crear-usuario border p-4">
            <div class="form-group">
                <label for="nombre_user">Nombre de Usuario:</label>
                <input type="text" name="nombre_user" value="<?php echo htmlspecialchars($usuario['nombre_user']); ?>" required class="form-control">
            </div>
            <div class="form-group">
                <label for="contrasena">Nueva Contraseña (opcional):</label>
                <input type="password" name="contrasena" class="form-control" placeholder="Dejar en blanco si no desea cambiarla">
            </div>
            <div class="form-group">
                <label for="id_rol">Rol:</label>
                <select name="id_rol" class="form-control">
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?php echo htmlspecialchars($rol['id_rol']); ?>" <?php echo $rol['id_rol'] == $usuario['id_rol'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($rol['nombre_rol']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar</button>
        </form>
    </div>
</body>
</html>