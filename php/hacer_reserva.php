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

$fecha_seleccionada = $_POST['fecha'] ?? $_GET['filtro_fecha'] ?? date('Y-m-d');
$query_reservadas = "SELECT id_franja FROM tbl_reservas WHERE id_mesa = ? AND fecha = ?";
$stmt_reservadas = $conexion->prepare($query_reservadas);
$stmt_reservadas->execute([$id_mesa, $fecha_seleccionada]);
$reservadas = $stmt_reservadas->fetchAll(PDO::FETCH_COLUMN);

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    
    header('Content-Type: application/json');
    
    $nombre = trim($_POST['nombre'] ?? '');
    $id_franja = $_POST['id_franja'] ?? '';

    if (empty($nombre)) {
        $errores['nombre'] = 'El nombre es obligatorio';
    } elseif (strlen($nombre) < 3) {
        $errores['nombre'] = 'El nombre debe tener al menos 3 caracteres';
    } elseif (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $nombre)) {
        $errores['nombre'] = 'El nombre solo puede contener letras';
    }

    if (empty($id_franja)) {
        $errores['franja'] = 'Debe seleccionar una franja horaria';
    }

    if (empty($errores)) {
        $query_check = "SELECT COUNT(*) FROM tbl_reservas WHERE id_mesa = ? AND fecha = ? AND id_franja = ?";
        $stmt_check = $conexion->prepare($query_check);
        $stmt_check->execute([$id_mesa, $fecha_seleccionada, $id_franja]);
        $exists = $stmt_check->fetchColumn();

        if ($exists) {
            $errores['franja'] = "Ya existe una reserva para esta franja horaria.";
        }
    }

    if (empty($errores)) {
        try {
            $query_franja = "SELECT hora_inicio, hora_fin FROM tbl_franjas_horarias WHERE id_franja = ?";
            $stmt_franja = $conexion->prepare($query_franja);
            $stmt_franja->execute([$id_franja]);
            $franja = $stmt_franja->fetch(PDO::FETCH_ASSOC);

            $query_insert = "INSERT INTO tbl_reservas (id_mesa, nombre, fecha, id_franja) VALUES (?, ?, ?, ?)";
            $stmt_insert = $conexion->prepare($query_insert);
            $stmt_insert->execute([$id_mesa, $nombre, $fecha_seleccionada, $id_franja]);

            $query_ocupacion = "INSERT INTO tbl_ocupaciones (id_usuario, nombre_reserva, id_mesa, fecha_inicio, fecha_fin) VALUES (?, ?, ?, ?, ?)";
            $stmt_ocupacion = $conexion->prepare($query_ocupacion);
            $stmt_ocupacion->execute([
                $_SESSION['id_usuario'],
                $nombre,
                $id_mesa,
                "$fecha_seleccionada {$franja['hora_inicio']}", 
                "$fecha_seleccionada {$franja['hora_fin']}"
            ]);

            echo json_encode(['success' => true]);
            exit();
        } catch (Exception $e) {
            echo json_encode([
                'success' => false, 
                'message' => 'Error al realizar la reserva: ' . $e->getMessage()
            ]);
            exit();
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => implode('<br>', $errores)
        ]);
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
    <title>Nueva Reserva</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/validacion_Hacerreserva.js"></script>
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
                <a href="reservar_mesa.php?id_mesa=<?php echo $id_mesa; ?>">
                    <img src="../img/atras.png" alt="Atrás" class="navbar-icon">
                </a>
            </div>
            <div class="navbar-right">
                <a href="../salir.php"><img src="../img/logout.png" alt="Logout" class="navbar-icon"></a>
            </div>
        </nav>
    </div>

    <div class="container container-crud">
        <h2>Nueva Reserva para la Mesa <?php echo htmlspecialchars($id_mesa); ?></h2>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" id="formReserva" class="form-reserva mb-4 p-4 border rounded bg-light">
            <div class="form-group mb-3">
                <label for="nombre" class="form-label1">Nombre:</label>
                <input type="text" id="nombre" name="nombre" class="form-control" 
                       value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>">
                <div id="nombre-error" class="text-danger">
                    <?php echo $errores['nombre'] ?? ''; ?>
                </div>
            </div>
            <div class="form-group mb-3">
                <label for="fecha" class="form-label1">Fecha:</label>
                <input type="date" id="fecha" name="fecha" class="form-control" 
                       value="<?php echo htmlspecialchars($fecha_seleccionada); ?>" 
                       min="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="form-group mb-3">
                <label for="id_franja" class="form-label1">Franja Horaria:</label>
                <select id="id_franja" name="id_franja" class="form-control">
                    <option value="" disabled selected>Seleccione una franja horaria</option>
                    <?php foreach ($franjas as $franja): ?>
                        <option value="<?php echo $franja['id_franja']; ?>" 
                                <?php echo in_array($franja['id_franja'], $reservadas) ? 'disabled' : ''; ?>
                                <?php echo (isset($_POST['id_franja']) && $_POST['id_franja'] == $franja['id_franja']) ? 'selected' : ''; ?>>
                            <?php echo $franja['hora_inicio'] . ' - ' . $franja['hora_fin']; ?>
                            <?php echo in_array($franja['id_franja'], $reservadas) ? ' (Reservada)' : ''; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div id="franja-error" class="text-danger">
                    <?php echo $errores['franja'] ?? ''; ?>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Reservar</button>
        </form>
    </div>

    <script src="../js/sweetalert_reserva.js"></script>
</body>
</html> 