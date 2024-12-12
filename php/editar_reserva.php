<?php
session_start();
require_once('./conexion.php');

$id_reserva = $_GET['id_reserva'] ?? null;

if (!$id_reserva) {
    die("ID de reserva no proporcionado.");
}

$query_reserva = "SELECT r.*, m.id_mesa 
                 FROM tbl_reservas r 
                 JOIN tbl_mesas m ON r.id_mesa = m.id_mesa 
                 WHERE r.id_reserva = ?";
$stmt_reserva = $conexion->prepare($query_reserva);
$stmt_reserva->execute([$id_reserva]);
$reserva = $stmt_reserva->fetch(PDO::FETCH_ASSOC);

if (!$reserva) {
    die("Reserva no encontrada.");
}

$query_franjas = "SELECT id_franja, hora_inicio, hora_fin FROM tbl_franjas_horarias";
$stmt_franjas = $conexion->prepare($query_franjas);
$stmt_franjas->execute();
$franjas = $stmt_franjas->fetchAll(PDO::FETCH_ASSOC);

$fecha_seleccionada = $_POST['fecha'] ?? $_GET['filtro_fecha'] ?? $reserva['fecha'];
$query_reservadas = "SELECT id_franja FROM tbl_reservas 
                    WHERE id_mesa = ? AND fecha = ? AND id_reserva != ?";
$stmt_reservadas = $conexion->prepare($query_reservadas);
$stmt_reservadas->execute([$reserva['id_mesa'], $fecha_seleccionada, $id_reserva]);
$reservadas = $stmt_reservadas->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $fecha = $_POST['fecha'] ?? '';
    $id_franja = $_POST['id_franja'] ?? '';
    $errores = [];

    if (empty($nombre)) {
        $errores['nombre'] = 'El nombre es obligatorio';
    } elseif (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{3,}$/', $nombre)) {
        $errores['nombre'] = 'El nombre debe contener solo letras y tener al menos 3 caracteres';
    }

    if (empty($fecha)) {
        $errores['fecha'] = 'La fecha es obligatoria';
    } elseif ($fecha < date('Y-m-d')) {
        $errores['fecha'] = 'La fecha no puede ser anterior a hoy';
    }

    if (empty($id_franja)) {
        $errores['franja'] = 'Debe seleccionar una franja horaria';
    }

    if (empty($errores) && ($fecha != $reserva['fecha'] || $id_franja != $reserva['id_franja'])) {
        $query_check = "SELECT COUNT(*) FROM tbl_reservas 
                       WHERE id_mesa = ? AND fecha = ? AND id_franja = ? AND id_reserva != ?";
        $stmt_check = $conexion->prepare($query_check);
        $stmt_check->execute([$reserva['id_mesa'], $fecha, $id_franja, $id_reserva]);
        $exists = $stmt_check->fetchColumn();

        if ($exists) {
            $errores['franja'] = "Esta franja horaria ya está reservada";
        }
    }

    if (empty($errores)) {
        $query_update = "UPDATE tbl_reservas 
                        SET nombre = ?, fecha = ?, id_franja = ? 
                        WHERE id_reserva = ?";
        $stmt_update = $conexion->prepare($query_update);
        $stmt_update->execute([$nombre, $fecha, $id_franja, $id_reserva]);

        $query_franja = "SELECT hora_inicio, hora_fin FROM tbl_franjas_horarias WHERE id_franja = ?";
        $stmt_franja = $conexion->prepare($query_franja);
        $stmt_franja->execute([$id_franja]);
        $franja = $stmt_franja->fetch(PDO::FETCH_ASSOC);

        $query_update_ocupacion = "UPDATE tbl_ocupaciones 
                                 SET nombre_reserva = ?, 
                                     fecha_inicio = ?, 
                                     fecha_fin = ? 
                                 WHERE id_mesa = ? AND fecha_inicio LIKE ?";
        $stmt_update_ocupacion = $conexion->prepare($query_update_ocupacion);
        $stmt_update_ocupacion->execute([
            $nombre,
            "$fecha {$franja['hora_inicio']}", 
            "$fecha {$franja['hora_fin']}", 
            $reserva['id_mesa'],
            $reserva['fecha'] . '%'
        ]);

        if ($stmt_update && $stmt_update_ocupacion) {
            echo json_encode([
                'success' => true,
                'id_mesa' => $reserva['id_mesa']
            ]);
            exit();
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Error al actualizar la reserva'
            ]);
            exit();
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Hay errores en el formulario']);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/gestionar_usuarios.css">
    <link rel="stylesheet" href="../css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Editar Reserva</title>
    <script src="../js/validacion_Editarreserva.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <a href="reservar_mesa.php?id_mesa=<?php echo $reserva['id_mesa']; ?>">
                    <img src="../img/atras.png" alt="Atrás" class="navbar-icon">
                </a>
            </div>
            <div class="navbar-right">
                <a href="../salir.php"><img src="../img/logout.png" alt="Logout" class="navbar-icon"></a>
            </div>
        </nav>
    </div>

    <div class="container container-crud">
        <h2>Editar Reserva</h2>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" id="formReserva" class="form-reserva mb-4 p-4 border rounded bg-light">
            <div class="form-group mb-3">
                <label for="nombre" class="form-label1">Nombre:</label>
                <input type="text" id="nombre" name="nombre" class="form-control" 
                       value="<?php echo htmlspecialchars($reserva['nombre']); ?>">
                <?php if (isset($errores['nombre'])): ?>
                    <span class="error-message" style="color: red;"><?php echo $errores['nombre']; ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group mb-3">
                <label for="fecha" class="form-label1">Fecha:</label>
                <input type="date" id="fecha" name="fecha" class="form-control" 
                       value="<?php echo htmlspecialchars($fecha_seleccionada); ?>" 
                       min="<?php echo date('Y-m-d'); ?>">
                <?php if (isset($errores['fecha'])): ?>
                    <span class="error-message" style="color: red;"><?php echo $errores['fecha']; ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group mb-3">
                <label for="id_franja" class="form-label1">Franja Horaria:</label>
                <select id="id_franja" name="id_franja" class="form-control">
                    <?php foreach ($franjas as $franja): 
                        $es_franja_actual = $franja['id_franja'] == $reserva['id_franja'];
                    ?>
                        <option value="<?php echo $franja['id_franja']; ?>" 
                                <?php echo $es_franja_actual ? 'selected' : ''; ?>
                                <?php echo (!$es_franja_actual && in_array($franja['id_franja'], $reservadas)) ? 'disabled' : ''; ?>>
                            <?php echo $franja['hora_inicio'] . ' - ' . $franja['hora_fin']; ?>
                            <?php echo in_array($franja['id_franja'], $reservadas) ? ' (Reservada)' : ''; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errores['franja'])): ?>
                    <span class="error-message" style="color: red;"><?php echo $errores['franja']; ?></span>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="reservar_mesa.php?id_mesa=<?php echo $reserva['id_mesa']; ?>" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <script src="../js/sweetalert_editar_reserva.js"></script>
</body>
</html> 