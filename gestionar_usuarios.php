<?php
session_start();
require_once('./php/conexion.php');

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'Administrador') {
    header("Location: index.php?error=acceso_denegado");
    exit();
}

$query_usuarios = "SELECT u.id_usuario, u.nombre_user, r.nombre_rol FROM tbl_usuarios u JOIN tbl_roles r ON u.id_rol = r.id_rol";
$stmt_usuarios = $conexion->query($query_usuarios);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/gestionar_usuarios.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/sweetalert_eliminar_usuario.js"></script>
    <title>Gestión de Usuarios</title>
    
</head>
<body>
    <div class="container">
        <nav class="navegacion">
            <div class="navbar-left">
                <a href="./menu.php"><img src="./img/logo.png" alt="Logo de la Marca" class="logo" style="width: 100%;"></a>
                <a href="./registro.php"><img src="./img/lbook.png" alt="Ícono adicional" class="navbar-icon"></a>
            </div>
            
            <div class="navbar-title">
                <h3>Bienvenido <?php if (isset($_SESSION['usuario'])) {
                                    echo $_SESSION['usuario'];
                                } ?></h3>
            </div>
            <div class="navbar-right" style="margin-right: 18px;">
                <a href="./menu.php"><img src="./img/atras.png" alt="Logout" class="navbar-icon"></a>
            </div>

            <div class="navbar-right">
                <a href="./salir.php"><img src="./img/logout.png" alt="Logout" class="navbar-icon"></a>
            </div>
            
        </nav>
    </div>

    <div class="container container-crud">
        <h1 class="mb-4">Gestión de Usuarios</h1>
        <a href="./php/crear_usuario.php" class="btn btn-primary mb-3">Crear Nuevo Usuario</a>
        <h2 class="mb-4">Listado de Usuarios</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de Usuario</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($usuario = $stmt_usuarios->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo $usuario['id_usuario']; ?></td>
                        <td><?php echo $usuario['nombre_user']; ?></td>
                        <td><?php echo $usuario['nombre_rol']; ?></td>
                        <td>
                            <a href="./php/editar_usuario.php?id=<?php echo $usuario['id_usuario']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="javascript:void(0);" onclick="eliminarUsuario(<?php echo $usuario['id_usuario']; ?>)" class="btn btn-danger btn-sm">Eliminar</a>                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+3paNdF+Ll9gL0L4cU5I5t5L5t5L5" crossorigin="anonymous"></script>
</body>
</html>