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
    $valores = [];
    foreach ($campos as $campo) {
        $valores[] = $_POST[$campo];
    }
    
    // Manejo de la imagen
    if (isset($_FILES['imagen_sala']) && $_FILES['imagen_sala']['error'] == UPLOAD_ERR_OK) {
        $imagenSala = 'img/' . basename($_FILES['imagen_sala']['name']);
        move_uploaded_file($_FILES['imagen_sala']['tmp_name'], '../' . $imagenSala);
        $valores[] = $imagenSala; // Agregar la nueva imagen a los valores
    } else {
        $valores[] = $recurso['imagen_sala']; // Mantener la imagen existente
    }
    
    // Asegúrate de que el número de campos y valores coincidan
    $set = implode(', ', array_map(fn($campo) => "$campo = ?", $campos));
    $query = "UPDATE $tabla SET $set, imagen_sala = ? WHERE $id_campo = ?";
    $valores[] = $id; // Agregar el ID al final de los valores
    $stmt = $conexion->prepare($query);
    $stmt->execute($valores);
    
    header("Location: ../gestionar_salas.php?tipo=$tipo");
    exit();
}

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
        <form method="POST" class="form-crear-sala border p-4 bg-light" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nombre_sala">Nombre de la Sala:</label>
                <input type="text" id="nombre_sala" name="nombre_sala" value="<?php echo htmlspecialchars($recurso['nombre_sala']); ?>" required class="form-control">
            </div>

            <div class="form-group">
                <label for="capacidad">Capacidad:</label>
                <input type="number" id="capacidad" name="capacidad" value="<?php echo htmlspecialchars($recurso['capacidad']); ?>" required class="form-control">
            </div>

            <div class="form-group">
                <label for="tipo_sala">Tipo de Sala:</label>
                <select id="tipo_sala" name="tipo_sala" required class="form-control">
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