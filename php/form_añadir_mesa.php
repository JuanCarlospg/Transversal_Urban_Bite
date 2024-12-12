<?php
session_start();
require_once('./conexion.php');

$id_sala = $_GET['id_sala'] ?? null;

if (!$id_sala) {
    die("ID de sala no proporcionado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero_mesa = $_POST['numero_mesa'];
    $numero_sillas = $_POST['numero_sillas'];
    $errores = [];

    if (empty($numero_mesa)) {
        $errores[] = "El número de mesa es obligatorio.";
    } elseif (!is_numeric($numero_mesa) || $numero_mesa <= 0) {
        $errores[] = "El número de mesa debe ser un número positivo.";
    } else {
        $query_check_mesa = "SELECT COUNT(*) FROM tbl_mesas WHERE numero_mesa = ? AND id_sala = ?";
        $stmt_check_mesa = $conexion->prepare($query_check_mesa);
        $stmt_check_mesa->execute([$numero_mesa, $id_sala]);
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
        $query = "INSERT INTO tbl_mesas (numero_mesa, id_sala, numero_sillas, estado) VALUES (?, ?, ?, 'libre')";
        $stmt = $conexion->prepare($query);
        $stmt->execute([$numero_mesa, $id_sala, $numero_sillas]);
        
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al crear la mesa: ' . $e->getMessage()]);
    }
    exit();
}

$query = "SELECT nombre_sala FROM tbl_salas WHERE id_sala = ?";
$stmt = $conexion->prepare($query);
$stmt->execute([$id_sala]);
$sala = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$sala) {
    die("Sala no encontrada.");
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/validacion_AñadirMesa.js"></script>
    <title>Añadir Mesa</title>
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
                <a href="añadir_mesa.php?id_sala=<?php echo $id_sala; ?>"><img src="../img/atras.png" alt="Atras" class="navbar-icon"></a>
            </div>

            <div class="navbar-right">
                <a href="../salir.php"><img src="../img/logout.png" alt="Logout" class="navbar-icon"></a>
            </div>
        </nav>
    </div>

    <div class="container container-crud">
        <h2 class="mb-4">Añadir Mesa a la Sala "<?php echo $sala['nombre_sala']; ?>"</h2>
        
        <form method="POST" id="formMesa" class="mb-4 p-4 border rounded bg-light">
            <div class="mb-3">
                <label for="numero_mesa" class="form-label1">Número de Mesa:</label>
                <input type="number" name="numero_mesa" class="form-control">
            </div>
            <div class="mb-3">
                <label for="numero_sillas" class="form-label1">Número de Sillas:</label>
                <input type="number" name="numero_sillas" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Añadir Mesa</button>
        </form>
    </div>

    <script src="../js/sweetalert_añadir_mesa.js"></script>
</body>
</html> 