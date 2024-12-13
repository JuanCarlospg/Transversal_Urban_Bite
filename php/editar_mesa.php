<?php
session_start();
require_once('./conexion.php');

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID de mesa no proporcionado']);
    exit();
}

$query = "SELECT m.*, s.nombre_sala FROM tbl_mesas m 
          JOIN tbl_salas s ON m.id_sala = s.id_sala 
          WHERE m.id_mesa = ?";
$stmt = $conexion->prepare($query);
$stmt->execute([$id]);
$mesa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mesa) {
    echo json_encode(['success' => false, 'message' => 'Mesa no encontrada']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero_mesa = $_POST['numero_mesa'] ?? '';
    $numero_sillas = $_POST['numero_sillas'] ?? '';
    $estado = $_POST['estado'] ?? $mesa['estado']; 
    
    $errores = [];

    if (empty($numero_mesa)) {
        $errores[] = "El número de mesa es obligatorio.";
    } elseif (!is_numeric($numero_mesa) || $numero_mesa <= 0) {
        $errores[] = "El número de mesa debe ser un número positivo.";
    } else {
        $query_check_mesa = "SELECT COUNT(*) FROM tbl_mesas WHERE numero_mesa = ? AND id_sala = ? AND id_mesa != ?";
        $stmt_check_mesa = $conexion->prepare($query_check_mesa);
        $stmt_check_mesa->execute([$numero_mesa, $mesa['id_sala'], $id]);
        $mesa_existe = $stmt_check_mesa->fetchColumn();

        if ($mesa_existe > 0) {
            $errores[] = "El número de mesa ya está en uso en esta sala.";
        }
    }

    if (empty($numero_sillas)) {
        $errores[] = "El número de sillas es obligatorio.";
    } elseif (!is_numeric($numero_sillas) || $numero_sillas <= 0) {
        $errores[] = "El número de sillas debe ser un número positivo.";
    }

    if (count($errores) > 0) {
        echo json_encode(['success' => false, 'message' => implode('<br>', $errores)]);
        exit();
    }

    try {
        $query = "UPDATE tbl_mesas SET numero_mesa = ?, numero_sillas = ?, estado = ? WHERE id_mesa = ?";
        $stmt = $conexion->prepare($query);
        
        if ($stmt->execute([$numero_mesa, $numero_sillas, $estado, $id])) {
            echo json_encode([
                'success' => true,
                'id_sala' => $mesa['id_sala']
            ]);
        } else {
            throw new Exception('Error al actualizar la mesa');
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al actualizar la mesa: ' . $e->getMessage()
        ]);
    }
    exit();
}

$errores = isset($_SESSION['errores']) ? $_SESSION['errores'] : [];
unset($_SESSION['errores']);

$querySalas = "SELECT id_sala, nombre_sala FROM tbl_salas";
$stmtSalas = $conexion->query($querySalas);
$salas = $stmtSalas->fetchAll(PDO::FETCH_ASSOC);
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
    <script src="../js/validacion_EditarMesa.js"></script>
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
                <a href="./añadir_mesa.php?id_sala=<?php echo $mesa['id_sala']; ?>" class="navbar-icon">
                    <img src="../img/atras.png" alt="Atras">
                </a>
            </div>

            <div class="navbar-right">
                <a href="../salir.php"><img src="../img/logout.png" alt="Logout" class="navbar-icon"></a>
            </div>
        </nav>
    </div>

    <div class="container container-crud">
        <h2 class="mb-4">Editar Mesa</h2>
        
        <form method="POST" id="formEditarMesa" class="mb-4 p-4 border rounded bg-light">
            <?php if (!empty($errores)): ?>
                <div class="alert alert-danger mb-3">
                    <?php foreach ($errores as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="form-group">
                <label for="numero_mesa">Número de la Mesa:</label>
                <input type="text" id="numero_mesa" name="numero_mesa" value="<?php echo htmlspecialchars($mesa['numero_mesa']); ?>" class="form-control">
            </div>

            <div class="form-group">
                <label for="numero_sillas">Número de Sillas:</label>
                <input type="number" id="numero_sillas" name="numero_sillas" value="<?php echo htmlspecialchars($mesa['numero_sillas']); ?>" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Actualizar Mesa</button>
        </form>
    </div>
    
    <script src="../js/sweetalert_editar_mesa.js"></script>
</body>
</html>