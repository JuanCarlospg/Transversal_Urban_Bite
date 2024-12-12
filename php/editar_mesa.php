<?php
session_start();
require_once('./conexion.php');

// Verificar permisos
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'Administrador') {
    header("Location: index.php?error=acceso_denegado");
    exit();
}

// Obtener ID de la mesa
$id_mesa = $_GET['id'] ?? null;
if (!$id_mesa) {
    die("Error: ID de mesa no proporcionado.");
}

// Obtener datos de la mesa
$query = "SELECT * FROM tbl_mesas WHERE id_mesa = ?";
$stmt = $conexion->prepare($query);
$stmt->execute([$id_mesa]);
$mesa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mesa) {
    die("Error: Mesa no encontrada.");
}

// Definir $idSala
$idSala = $mesa['id_sala'];

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numeroMesa = trim($_POST['numero_mesa']);
    $idSala = $_POST['id_sala'];
    $numeroSillas = $_POST['numero_sillas'];

    $errores = [];

    if (empty($numeroMesa)) {
        $errores[] = "El número de la mesa es obligatorio.";
    } else if (!is_numeric($numeroMesa) || $numeroMesa <= 0) {
        $errores[] = "El número de la mesa debe ser un número positivo.";
    } else {
        // Verificar si el número de la mesa ya existe en otra mesa
        $query_check_numero = "SELECT COUNT(*) FROM tbl_mesas WHERE numero_mesa = ? AND id_mesa != ?";
        $stmt_check_numero = $conexion->prepare($query_check_numero);
        $stmt_check_numero->execute([$numeroMesa, $id_mesa]);
        $numero_existe = $stmt_check_numero->fetchColumn();

        if ($numero_existe > 0) {
            $errores[] = "El número de la mesa ya está en uso. Por favor, elige otro.";
        }
    }

    if (empty($idSala)) {
        $errores[] = "La sala es obligatoria.";
    }

    if (empty($numeroSillas)) {
        $errores[] = "El número de sillas es obligatorio.";
    } else if (!is_numeric($numeroSillas) || $numeroSillas <= 0) {
        $errores[] = "El número de sillas debe ser un número positivo.";
    }

    if (count($errores) > 0) {
        $_SESSION['errores'] = $errores;
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $id_mesa);
        exit();
    }

    $query = "UPDATE tbl_mesas SET numero_mesa = ?, id_sala = ?, numero_sillas = ? WHERE id_mesa = ?";
    $stmt = $conexion->prepare($query);
    $stmt->execute([$numeroMesa, $idSala, $numeroSillas, $id_mesa]);

    // Redireccionar a añadir_mesa.php con el ID de sala
    header("Location: ./añadir_mesa.php?id_sala=" . $idSala);
    exit();
}

// Obtener errores de la sesión
$errores = isset($_SESSION['errores']) ? $_SESSION['errores'] : [];
unset($_SESSION['errores']); // Limpiar errores después de mostrarlos

// Obtener las salas existentes en la base de datos
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
                <a href="./añadir_mesa.php?id_sala=<?php echo $idSala; ?>" class="navbar-icon">
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
        <form method="POST" class="form-editar-mesa border p-4 bg-light">
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
                <label for="id_sala">Sala:</label>
                <select id="id_sala" name="id_sala" class="form-control">
                    <option value="" disabled>Seleccione una sala</option>
                    <?php foreach ($salas as $sala): ?>
                        <option value="<?php echo htmlspecialchars($sala['id_sala']); ?>" <?php echo ($sala['id_sala'] == $mesa['id_sala']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($sala['nombre_sala']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="numero_sillas">Número de Sillas:</label>
                <input type="number" id="numero_sillas" name="numero_sillas" value="<?php echo htmlspecialchars($mesa['numero_sillas']); ?>" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Actualizar Mesa</button>
        </form>
    </div>
</body>
</html>