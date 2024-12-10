<?php
session_start();
require_once('./php/conexion.php');

// Verificar permisos
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'Administrador') {
    header("Location: index.php?error=acceso_denegado");
    exit();
}

// Determinar el tipo de recurso
$tipo = $_GET['tipo'] ?? 'salas'; // Por defecto, gestionar salas

// Configuración para cada recurso
$recursos = [
    'salas' => [
        'tabla' => 'tbl_salas',
        'campos' => ['id_sala', 'nombre_sala', 'capacidad', 'tipo_sala'],
        'etiquetas' => ['ID', 'Nombre', 'Capacidad', 'Tipo de Sala']
    ],
    'mesas' => [
        'tabla' => 'tbl_mesas',
        'campos' => ['id_mesa', 'numero_mesa', 'id_sala', 'numero_sillas', 'estado'],
        'etiquetas' => ['ID', 'Número', 'Sala', 'Sillas', 'Estado']
    ],
    'sillas' => [
        'tabla' => 'tbl_sillas', // Crea esta tabla si no existe
        'campos' => ['id_silla', 'numero_silla', 'id_mesa', 'material'],
        'etiquetas' => ['ID', 'Número', 'Mesa', 'Material']
    ],
];

// Verificar recurso válido
if (!array_key_exists($tipo, $recursos)) {
    die("Recurso no válido.");
}

// Obtener configuración del recurso
$config = $recursos[$tipo];
$tabla = $config['tabla'];
$campos = $config['campos'];
$etiquetas = $config['etiquetas'];

// Procesar creación de recurso
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear'])) {
    $valores = array_map(fn($campo) => $_POST[$campo], array_slice($campos, 1)); // Excluir ID al insertar
    $placeholders = implode(', ', array_fill(0, count($valores), '?'));
    $query = "INSERT INTO $tabla (" . implode(', ', array_slice($campos, 1)) . ") VALUES ($placeholders)";
    $stmt = $conexion->prepare($query);
    $stmt->execute($valores);
}

// Leer datos
$query = "SELECT * FROM $tabla";
$result = $conexion->query($query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/menu.css">
    <link rel="stylesheet" href="./css/gestionar_usuarios.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Gestión de <?php echo ucfirst($tipo); ?></title>
</head>
<body>
    <div class="container">
        <nav class="navegacion">
            <div class="navbar-left">
                <a href="./menu.php"><img src="./img/logo.png" alt="Logo de la Marca" class="logo" style="width: 100%;"></a>
                <a href="./registro.php"><img src="./img/lbook.png" alt="Ícono adicional" class="navbar-icon"></a>
            </div>
            
            <div class="navbar-title">
                <h3>Bienvenido <?php if (isset($_SESSION['usuario'])) {
                                    echo $_SESSION['usuario'];
                                } ?></h3>
            </div>
            <div class="navbar-right" style="margin-right: 18px;">
                <a href="./menu.php"><img src="./img/atras.png" alt="Logout" class="navbar-icon"></a>
            </div>

            <div class="navbar-right">
                <a href="./salir.php"><img src="./img/logout.png" alt="Logout" class="navbar-icon"></a>
            </div>
        </nav>
        <br>
        <h1 class="titulo">Gestión de Salas</h1>

        <a href="./php/crear_sala.php" class="btn btn-primary mb-3">Crear Nueva Sala</a>

        <h2 class="subtitulo">Lista de Salas</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <?php foreach ($etiquetas as $etiqueta): ?>
                        <th><?php echo $etiqueta; ?></th>
                    <?php endforeach; ?>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <?php foreach ($campos as $campo): ?>
                            <td><?php echo $row[$campo]; ?></td>
                        <?php endforeach; ?>
                        <td>
                            <a href="./php/editar_sala.php?tipo=<?php echo $tipo; ?>&id=<?php echo $row['id_' . substr($tipo, 0, -1)]; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="./php/eliminar_sala.php?tipo=<?php echo $tipo; ?>&id=<?php echo $row['id_' . substr($tipo, 0, -1)]; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                            <?php if ($tipo === 'salas'): ?>
                                <a href="./php/añadir_mesa.php?id_sala=<?php echo $row['id_sala']; ?>" class="btn btn-success btn-sm">Añadir Mesas</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+3paNdF+Ll9gL0L4cU5I5t5L5t5L5" crossorigin="anonymous"></script>
</body>
</html>