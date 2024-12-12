<?php
session_start();
require_once('./conexion.php');

// Obtener el ID de la sala
$id_sala = $_GET['id_sala'] ?? null;

if (!$id_sala) {
    die("ID de sala no proporcionado.");
}

// Consultar la sala para mostrar el nombre
$query = "SELECT nombre_sala FROM tbl_salas WHERE id_sala = ?";
$stmt = $conexion->prepare($query);
$stmt->execute([$id_sala]);
$sala = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$sala) {
    die("Sala no encontrada.");
}

// Consultar mesas existentes en la sala
$query_mesas = "SELECT * FROM tbl_mesas WHERE id_sala = ?";
$stmt_mesas = $conexion->prepare($query_mesas);
$stmt_mesas->execute([$id_sala]);
$result_mesas = $stmt_mesas->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/gestionar_usuarios.css">
    <link rel="stylesheet" href="../css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/sweetalert_eliminar_mesa.js"></script>
    <title>Gestión de Mesas</title>
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
                <a href="../gestionar_salas.php"><img src="../img/atras.png" alt="Logout" class="navbar-icon"></a>
            </div>

            <div class="navbar-right">
                <a href="../salir.php"><img src="../img/logout.png" alt="Logout" class="navbar-icon"></a>
            </div>
        </nav>
    </div>

    <div class="container container-crud">
        <h2 class="mb-4">Gestión de Mesas en la Sala "<?php echo $sala['nombre_sala']; ?>"</h2>
        
        <a href="form_añadir_mesa.php?id_sala=<?php echo $id_sala; ?>" class="btn btn-primary mb-4">Añadir Nueva Mesa</a>

        <h2 class="mb-4">Lista de mesas de "<?php echo $sala['nombre_sala']; ?>"</h2>
        
        <?php if ($result_mesas && count($result_mesas) > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Número de Mesa</th>
                        <th>Número de Sillas</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result_mesas as $mesa): ?>
                        <tr>
                            <td><?php echo $mesa['id_mesa']; ?></td>
                            <td><?php echo $mesa['numero_mesa']; ?></td>
                            <td><?php echo $mesa['numero_sillas']; ?></td>
                            <td><?php echo $mesa['estado']; ?></td>
                            <td>
                                <a href="editar_mesa.php?id=<?php echo $mesa['id_mesa']; ?>" class="btn btn-warning btn-sm">Editar</a>
                                <a href="javascript:void(0);" onclick="eliminarMesa(<?php echo $mesa['id_mesa']; ?>, <?php echo $id_sala; ?>)" class="btn btn-danger btn-sm">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="mensaje">No hay mesas creadas en esta sala.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
