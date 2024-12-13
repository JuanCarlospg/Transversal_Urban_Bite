<?php
session_start();
require_once('./conexion.php');

$id_mesa = $_GET['id_mesa'] ?? null;

if (!$id_mesa) {
    die("ID de mesa no proporcionado.");
}

$query_franjas = "SELECT id_franja, hora_inicio, hora_fin FROM tbl_franjas_horarias";
$stmt_franjas = $conexion->prepare($query_franjas);
$stmt_franjas->execute();
$franjas = $stmt_franjas->fetchAll(PDO::FETCH_ASSOC);

$reservadas = [];
$fecha_seleccionada = $_POST['fecha'] ?? $_GET['filtro_fecha'] ?? date('Y-m-d');  
if ($fecha_seleccionada) {
    $query_reservadas = "SELECT id_franja FROM tbl_reservas WHERE id_mesa = ? AND fecha = ?";
    $stmt_reservadas = $conexion->prepare($query_reservadas);
    $stmt_reservadas->execute([$id_mesa, $fecha_seleccionada]);
    $reservadas = $stmt_reservadas->fetchAll(PDO::FETCH_COLUMN);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $fecha_seleccionada) {
    $nombre = $_POST['nombre'];
    $id_franja = $_POST['id_franja'];

    $query_franja = "SELECT hora_inicio, hora_fin FROM tbl_franjas_horarias WHERE id_franja = ?";
    $stmt_franja = $conexion->prepare($query_franja);
    $stmt_franja->execute([$id_franja]);
    $franja = $stmt_franja->fetch(PDO::FETCH_ASSOC);

    $query_check = "SELECT COUNT(*) FROM tbl_reservas WHERE id_mesa = ? AND fecha = ? AND id_franja = ?";
    $stmt_check = $conexion->prepare($query_check);
    $stmt_check->execute([$id_mesa, $fecha_seleccionada, $id_franja]);
    $exists = $stmt_check->fetchColumn();

    if ($exists) {
        $error_message = "Ya existe una reserva para esta franja horaria.";
    } else {
        $query_insert = "INSERT INTO tbl_reservas (id_mesa, nombre, fecha, id_franja) VALUES (?, ?, ?, ?)";
        $stmt_insert = $conexion->prepare($query_insert);
        $stmt_insert->execute([$id_mesa, $nombre, $fecha_seleccionada, $id_franja]);

        $query_ocupacion = "INSERT INTO tbl_ocupaciones (id_usuario, nombre_reserva, id_mesa, fecha_inicio, fecha_fin) VALUES (?, ?, ?, ?, ?)";
        $stmt_ocupacion = $conexion->prepare($query_ocupacion);
        $stmt_ocupacion->execute([$_SESSION['id_usuario'], $nombre, $id_mesa, "$fecha_seleccionada {$franja['hora_inicio']}", "$fecha_seleccionada {$franja['hora_fin']}"]);

        $success_message = "Reserva y ocupación realizadas con éxito.";

        header("Location: reservar_mesa.php?id_mesa=$id_mesa");
        exit();
    }
}

$query_reservas = "SELECT r.id_reserva, r.nombre, r.fecha, f.hora_inicio, f.hora_fin 
                   FROM tbl_reservas r
                   JOIN tbl_franjas_horarias f ON r.id_franja = f.id_franja
                   WHERE r.id_mesa = ?";
$params = [$id_mesa];

if (!empty($_GET['filtro_fecha'])) {
    $query_reservas .= " AND r.fecha = ?";
    $params[] = $_GET['filtro_fecha'];
}

if (!empty($_GET['filtro_hora'])) {
    $query_reservas .= " AND f.hora_inicio = ?";
    $params[] = $_GET['filtro_hora'];
}

$stmt_reservas = $conexion->prepare($query_reservas);
$stmt_reservas->execute($params);
$reservas = $stmt_reservas->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/gestionar_usuarios.css">
    <link rel="stylesheet" href="../css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/sweetalert_eliminar_reserva.js"></script>
    <title>Reservas de Mesa</title>
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
                <a href="../menu.php"><img src="../img/atras.png" alt="Logout" class="navbar-icon"></a>
            </div>

            <div class="navbar-right">
                <a href="../salir.php"><img src="../img/logout.png" alt="Logout" class="navbar-icon"></a>
            </div>
        </nav>
    </div>

    <div class="container container-crud">
        <h2>Reservas para la Mesa <?php echo htmlspecialchars($id_mesa); ?></h2>

        <a href="hacer_reserva.php?id_mesa=<?php echo $id_mesa; ?>" class="btn btn-primary mb-4">Nueva Reserva</a>

        <form method="GET" class="d-flex align-items-end mb-4">
            <input type="hidden" name="id_mesa" value="<?php echo htmlspecialchars($id_mesa); ?>">
            <div class="form-group mb-0 me-2">
                <label for="filtro_fecha" class="form-label">Fecha:</label>
                <input type="date" id="filtro_fecha" name="filtro_fecha" class="form-control" value="<?php echo htmlspecialchars($_GET['filtro_fecha'] ?? ''); ?>">
            </div>
            <div class="form-group mb-0 me-2">
                <label for="filtro_hora" class="form-label">Hora:</label>
                <select id="filtro_hora" name="filtro_hora" class="form-control">
                    <option value="">Todas</option>
                    <?php foreach ($franjas as $franja): ?>
                        <option value="<?php echo $franja['hora_inicio']; ?>" <?php echo (isset($_GET['filtro_hora']) && $_GET['filtro_hora'] === $franja['hora_inicio']) ? 'selected' : ''; ?>>
                            <?php echo $franja['hora_inicio']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary me-2">Filtrar</button>
            <a href="reservar_mesa.php?id_mesa=<?php echo $id_mesa; ?>" class="btn btn-secondary">Borrar Filtros</a>
        </form>

        <h3 class="mt-4 texto-blanco">Lista de Reservas Existentes</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre Reserva</th>
                    <th>Fecha</th>
                    <th>Hora de Inicio</th>
                    <th>Hora de Fin</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservas as $reserva): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($reserva['id_reserva']); ?></td>
                        <td><?php echo htmlspecialchars($reserva['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($reserva['fecha']); ?></td>
                        <td><?php echo htmlspecialchars($reserva['hora_inicio']); ?></td>
                        <td><?php echo htmlspecialchars($reserva['hora_fin']); ?></td>
                        <td>
                            <a href="editar_reserva.php?id_reserva=<?php echo htmlspecialchars($reserva['id_reserva']); ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="javascript:void(0);" onclick="eliminarReserva(<?php echo $reserva['id_reserva']; ?>)" class="btn btn-danger btn-sm">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
