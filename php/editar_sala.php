<?php
session_start();
require_once('./conexion.php');

// Obtener tipo de recurso y verificar su validez
$tipo = $_GET['tipo'] ?? 'salas';
$recursos = [
    'salas' => [
        'tabla' => 'tbl_salas',
        'campos' => ['nombre_sala', 'capacidad', 'tipo_sala']
    ],
    'mesas' => [
        'tabla' => 'tbl_mesas',
        'campos' => ['numero_mesa', 'id_sala', 'numero_sillas', 'estado']
    ],
    'sillas' => [
        'tabla' => 'tbl_sillas',
        'campos' => ['numero_silla', 'id_mesa', 'material']
    ]
];

if (!array_key_exists($tipo, $recursos)) {
    die("Recurso no válido.");
}

$config = $recursos[$tipo];
$tabla = $config['tabla'];
$campos = $config['campos'];

// Obtener ID del recurso
$id_campo = 'id_' . substr($tipo, 0, -1);
$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID no proporcionado.");
}

// Obtener datos del recurso
$query = "SELECT * FROM $tabla WHERE $id_campo = ?";
$stmt = $conexion->prepare($query);
$stmt->execute([$id]);
$recurso = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$recurso) {
    die("Recurso no encontrado.");
}

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreSala = trim($_POST['nombre_sala']);
    $capacidad = $_POST['capacidad'];
    $tipoSala = $_POST['tipo_sala'];

    $errores = [];

    if (empty($nombreSala)) {
        $errores[] = "El nombre de la sala es obligatorio.";
    } else {
        // Verificar si el nombre de la sala ya existe en otra sala
        $query_check_nombre = "SELECT COUNT(*) FROM tbl_salas WHERE nombre_sala = ? AND $id_campo != ?";
        $stmt_check_nombre = $conexion->prepare($query_check_nombre);
        $stmt_check_nombre->execute([$nombreSala, $id]);
        $nombre_existe = $stmt_check_nombre->fetchColumn();

        if ($nombre_existe > 0) {
            $errores[] = "El nombre de la sala ya está en uso. Por favor, elige otro.";
        }
    }

    if (empty($capacidad)) {
        $errores[] = "La capacidad es obligatoria.";
    } else if (!is_numeric($capacidad) || $capacidad <= 0) {
        $errores[] = "La capacidad debe ser un número positivo.";
    }

    if (empty($tipoSala)) {
        $errores[] = "El tipo de sala es obligatorio.";
    }

    if (count($errores) > 0) {
        $_SESSION['errores'] = $errores;
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $id);
        exit();
    }

    // Manejo de la imagen
    if (isset($_FILES['imagen_sala']) && $_FILES['imagen_sala']['error'] == UPLOAD_ERR_OK) {
        $imagenSala = 'img/' . basename($_FILES['imagen_sala']['name']);
        move_uploaded_file($_FILES['imagen_sala']['tmp_name'], '../' . $imagenSala);
    } else {
        $imagenSala = $recurso['imagen_sala'];
    }

    $query = "UPDATE $tabla SET nombre_sala = ?, capacidad = ?, tipo_sala = ?, imagen_sala = ? WHERE $id_campo = ?";
    $stmt = $conexion->prepare($query);
    $stmt->execute([$nombreSala, $capacidad, $tipoSala, $imagenSala, $id]);

    header("Location: ../gestionar_salas.php?tipo=$tipo");
    exit();
}

// Obtener errores de la sesión
$errores = isset($_SESSION['errores']) ? $_SESSION['errores'] : [];
unset($_SESSION['errores']); // Limpiar errores después de mostrarlos

// Obtener los tipos de sala existentes en la base de datos
$queryTipos = "SELECT DISTINCT tipo_sala FROM tbl_salas";
$stmtTipos = $conexion->query($queryTipos);
$tiposSala = $stmtTipos->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Editar <?php echo ucfirst(substr($tipo, 0, -1)); ?></title>
    <script src="../js/validacion_EditarSala.js"></script>
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
                <a href="../gestionar_salas.php?tipo=<?php echo $tipo; ?>" class="navbar-icon"><img src="../img/atras.png" alt="Atras"></a>
            </div>

            <div class="navbar-right">
                <a href="../salir.php"><img src="../img/logout.png" alt="Logout" class="navbar-icon"></a>
            </div>
        </nav>
    </div>

    <div class="container container-crud">
        <h2 class="mb-4">Editar <?php echo ucfirst(substr($tipo, 0, -1)); ?></h2>
        <form method="POST" class="form-editar-sala border p-4 bg-light" enctype="multipart/form-data">
            <?php if (!empty($errores)): ?>
                <div class="alert alert-danger mb-3">
                    <?php foreach ($errores as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="form-group">
                <label for="nombre_sala">Nombre de la Sala:</label>
                <input type="text" id="nombre_sala" name="nombre_sala" value="<?php echo htmlspecialchars($recurso['nombre_sala']); ?>" class="form-control">
            </div>

            <div class="form-group">
                <label for="capacidad">Capacidad:</label>
                <input type="number" id="capacidad" name="capacidad" value="<?php echo htmlspecialchars($recurso['capacidad']); ?>" class="form-control">
            </div>

            <div class="form-group">
                <label for="tipo_sala">Tipo de Sala:</label>
                <select id="tipo_sala" name="tipo_sala" class="form-control">
                    <option value="" disabled>Seleccione un tipo</option>
                    <?php foreach ($tiposSala as $tipoSala): ?>
                        <option value="<?php echo htmlspecialchars($tipoSala); ?>" <?php echo ($tipoSala === $recurso['tipo_sala']) ? 'selected' : ''; ?>><?php echo ucfirst(htmlspecialchars($tipoSala)); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="imagen_sala">Imagen de la Sala:</label>
                <input type="file" id="imagen_sala" name="imagen_sala" accept="image/*" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Actualizar Sala</button>
        </form>
    </div>
</body>
</html>