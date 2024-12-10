<?php
session_start();
require_once('./conexion.php');

// Verificar permisos
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'Administrador') {
    header("Location: index.php?error=acceso_denegado");
    exit();
}

// Obtener los tipos de sala existentes en la base de datos
$queryTipos = "SELECT DISTINCT tipo_sala FROM tbl_salas";
$stmtTipos = $conexion->query($queryTipos);
$tiposSala = $stmtTipos->fetchAll(PDO::FETCH_COLUMN);

// Procesar creación de sala
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreSala = $_POST['nombre_sala'];
    $capacidad = $_POST['capacidad'];
    $tipoSala = $_POST['tipo_sala'];
    
    // Manejo de la imagen
    $imagenSala = null;
    if (isset($_FILES['imagen_sala']) && $_FILES['imagen_sala']['error'] == UPLOAD_ERR_OK) {
        $imagenSala = 'img/' . basename($_FILES['imagen_sala']['name']);
        move_uploaded_file($_FILES['imagen_sala']['tmp_name'], '../' . $imagenSala);
    }

    $query = "INSERT INTO tbl_salas (nombre_sala, capacidad, tipo_sala, imagen_sala) VALUES (?, ?, ?, ?)";
    $stmt = $conexion->prepare($query);
    
    if ($stmt->execute([$nombreSala, $capacidad, $tipoSala, $imagenSala])) {
        header("Location: ../gestionar_salas.php?tipo=salas&mensaje=creado");
        exit();
    } else {
        $error = "Error al crear la sala.";
    }
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
    <title>Crear Nueva Sala</title>
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
                <a href="../gestionar_usuarios.php"><img src="../img/atras.png" alt="Atras" class="navbar-icon"></a>
            </div>

            <div class="navbar-right">
                <a href="../salir.php"><img src="../img/logout.png" alt="Logout" class="navbar-icon"></a>
            </div>
        </nav>
    </div>

    <div class="container container-crud">
        <h2 class="mb-4">Crear Nueva Sala</h2>
        <form method="POST" class="form-crear-sala border p-4 bg-light" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nombre_sala">Nombre de la Sala:</label>
                <input type="text" id="nombre_sala" name="nombre_sala" required class="form-control">
            </div>

            <div class="form-group">
                <label for="capacidad">Capacidad:</label>
                <input type="number" id="capacidad" name="capacidad" required class="form-control">
            </div>

            <div class="form-group">
                <label for="tipo_sala">Tipo de Sala:</label>
                <select id="tipo_sala" name="tipo_sala" required class="form-control">
                    <option value="" disabled selected>Seleccione un tipo</option>
                    <?php foreach ($tiposSala as $tipo): ?>
                        <option value="<?php echo htmlspecialchars($tipo); ?>"><?php echo ucfirst(htmlspecialchars($tipo)); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="imagen_sala">Imagen de la Sala:</label>
                <input type="file" id="imagen_sala" name="imagen_sala" accept="image/*" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Crear Sala</button>
        </form>
    </div>
</body>
</html>