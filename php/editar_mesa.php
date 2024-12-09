<?php
session_start();
require_once('./conexion.php');

// Obtener el ID de la mesa
$id_mesa = $_GET['id'] ?? null;

if (!$id_mesa) {
    die("ID de mesa no proporcionado.");
}

// Consultar la mesa para mostrar los datos actuales
$query = "SELECT * FROM tbl_mesas WHERE id_mesa = ?";
$stmt = mysqli_prepare($conexion, $query);
mysqli_stmt_bind_param($stmt, "i", $id_mesa);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$mesa = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$mesa) {
    die("Mesa no encontrada.");
}

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero_mesa = $_POST['numero_mesa'];
    $numero_sillas = $_POST['numero_sillas'];
    $estado = $_POST['estado'];

    // Actualizar la mesa en la base de datos
    $query = "UPDATE tbl_mesas SET numero_mesa = ?, numero_sillas = ?, estado = ? WHERE id_mesa = ?";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($stmt, "iiis", $numero_mesa, $numero_sillas, $estado, $id_mesa);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: añadir_mesa.php?tipo=mesas&id_sala=" . $mesa['id_sala'] . "&mensaje=mesa_actualizada");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Editar Mesa</title>
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
                <a href="../gestionar_salas.php?tipo=mesas" class="navbar-icon"><img src="../img/atras.png" alt="Atras"></a>
            </div>

            <div class="navbar-right">
                <a href="../salir.php"><img src="../img/logout.png" alt="Logout" class="navbar-icon"></a>
            </div>
        </nav>
    </div>
    <div class="container container-crud">
        <h2 class="mb-4">Editar Mesa</h2>
        <form method="POST" class="form-crear-sala border p-4 bg-light">
            <div class="form-group">
                <label>Número de Mesa:</label>
                <input type="number" name="numero_mesa" value="<?php echo $mesa['numero_mesa']; ?>" required class="form-control">
            </div>
            <div class="form-group">
                <label>Número de Sillas:</label>
                <input type="number" name="numero_sillas" value="<?php echo $mesa['numero_sillas']; ?>" required class="form-control">
            </div>
            <div class="form-group">
                <label>Estado:</label>
                <select name="estado" class="form-control">
                    <option value="libre" <?php echo $mesa['estado'] === 'libre' ? 'selected' : ''; ?>>Libre</option>
                    <option value="ocupada" <?php echo $mesa['estado'] === 'ocupada' ? 'selected' : ''; ?>>Ocupada</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar Mesa</button>
        </form>
    </div>
</body>
</html>
