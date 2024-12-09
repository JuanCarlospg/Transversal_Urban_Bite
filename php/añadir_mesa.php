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

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero_mesa = $_POST['numero_mesa'];
    $numero_sillas = $_POST['numero_sillas'];
    $estado = $_POST['estado'];

    // Insertar nueva mesa
    $query = "INSERT INTO tbl_mesas (numero_mesa, id_sala, numero_sillas, estado) VALUES (?, ?, ?, ?)";
    $stmt = $conexion->prepare($query);
    $stmt->execute([$numero_mesa, $id_sala, $numero_sillas, $estado]);

    header("Location: añadir_mesa.php?id_sala=$id_sala");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/gestionar_usuarios.css">
    <link rel="stylesheet" href="../css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Gestión de Mesas</title>
</head>
<body>
    <div class="container">
        <nav class="navegacion">
            <div class="navbar-left">
                <a href="./menu.php"><img src="../img/logo.png" alt="Logo de la Marca" class="logo" style="width: 100%;"></a>
                <a href="./registro.php"><img src="../img/lbook.png" alt="Ícono adicional" class="navbar-icon"></a>
            </div>
            
            <div class="navbar-title">
                <h3>Bienvenido <?php if (isset($_SESSION['usuario'])) {
                                    echo $_SESSION['usuario'];
                                } ?></h3>
            </div>
            <div class="navbar-right" style="margin-right: 18px;">
                <a href="../menu.php"><img src="../img/atras.png" alt="Logout" class="navbar-icon"></a>
            </div>

            <div class="navbar-right">
                <a href="../salir.php"><img src="../img/logout.png" alt="Logout" class="navbar-icon"></a>
            </div>
        </nav>
    </div>

    <div class="container container-crud">
        <h2 class="mb-4">Gestión de Mesas en la Sala "<?php echo $sala['nombre_sala']; ?>"</h2>
        
        <form method="POST" class="mb-4 p-4 border rounded bg-light">
            <div class="mb-3">
                <label for="numero_mesa" class="form-label">Número de Mesa:</label>
                <input type="number" name="numero_mesa" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="numero_sillas" class="form-label">Número de Sillas:</label>
                <input type="number" name="numero_sillas" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="estado" class="form-label">Estado:</label>
                <select name="estado" class="form-select">
                    <option value="libre">Libre</option>
                    <option value="ocupada">Ocupada</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Añadir Mesa</button>
        </form>

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
                                <a href="eliminar_mesa.php?id=<?php echo $mesa['id_mesa']; ?>&id_sala=<?php echo $id_sala; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar esta mesa?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <pc class="mensaje">No hay mesas creadas en esta sala.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+3paNdF+Ll9gL0L4cU5I5t5L5t5L5" crossorigin="anonymous"></script>
</body>
</html>
