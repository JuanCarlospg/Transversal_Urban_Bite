<?php
session_start();
require_once('./conexion.php');

// Obtener el ID de la mesa
$id_mesa = $_GET['id_mesa'] ?? null;

if (!$id_mesa) {
    die("ID de mesa no proporcionado.");
}

// Procesar el formulario de reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $fecha = $_POST['fecha'];
    $hora_inicio = $_POST['hora_inicio'];

    // Verificar si ya existe una reserva para la misma mesa, fecha y hora
    $query_check = "SELECT COUNT(*) FROM tbl_reservas WHERE id_mesa = ? AND fecha = ? AND hora_inicio = ?";
    $stmt_check = $conexion->prepare($query_check);
    $stmt_check->execute([$id_mesa, $fecha, $hora_inicio]);
    $exists = $stmt_check->fetchColumn();

    if ($exists) {
        $error_message = "Ya existe una reserva para esta hora.";
    } else {
        // Calcular la hora de finalización (una hora después de la hora de inicio)
        $hora_fin = date('H:i', strtotime($hora_inicio) + 3600);

        // Insertar la nueva reserva en la base de datos
        $query_insert = "INSERT INTO tbl_reservas (id_mesa, nombre, fecha, hora_inicio, hora_fin) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert = $conexion->prepare($query_insert);
        $stmt_insert->execute([$id_mesa, $nombre, $fecha, $hora_inicio, $hora_fin]);

        // Registrar la ocupación
        $query_ocupacion = "INSERT INTO tbl_ocupaciones (id_usuario, nombre_reserva, id_mesa, fecha_inicio, fecha_fin) VALUES (?, ?, ?, ?, ?)";
        $stmt_ocupacion = $conexion->prepare($query_ocupacion);
        $stmt_ocupacion->execute([$_SESSION['id_usuario'], $nombre, $id_mesa, "$fecha $hora_inicio", "$fecha $hora_fin"]);

        $success_message = "Reserva y ocupación realizadas con éxito.";

        // Redirigir para evitar reenvío del formulario
        header("Location: reservar_mesa.php?id_mesa=$id_mesa");
        exit();
    }
}

// Consultar las reservas existentes para la mesa con filtros
$query_reservas = "SELECT id_reserva, nombre, fecha, hora_inicio, hora_fin FROM tbl_reservas WHERE id_mesa = ?";
$params = [$id_mesa];

if (!empty($_GET['filtro_fecha'])) {
    $query_reservas .= " AND fecha = ?";
    $params[] = $_GET['filtro_fecha'];
}

if (!empty($_GET['filtro_hora'])) {
    $query_reservas .= " AND hora_inicio = ?";
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
    <title>Reservas de Mesa</title>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reservas = <?php echo json_encode($reservas); ?>;
            console.log('Reservas:', reservas);

            const fechaInput = document.getElementById('fecha');
            const horaSelect = document.getElementById('hora_inicio');

            fechaInput.addEventListener('change', function() {
                const selectedDate = this.value;
                console.log('Fecha seleccionada:', selectedDate);

                const reservedTimes = [...new Set(reservas
                    .filter(reserva => reserva.fecha === selectedDate)
                    .map(reserva => reserva.hora_inicio.substring(0, 5)))];

                console.log('Horas reservadas:', reservedTimes);

                // Limpiar opciones de hora
                horaSelect.innerHTML = '';

                // Añadir opción predeterminada
                const defaultOption = document.createElement('option');
                defaultOption.textContent = 'Seleccione una hora';
                defaultOption.disabled = true;
                defaultOption.selected = true;
                horaSelect.appendChild(defaultOption);

                // Generar opciones de hora desde las 10:00 hasta las 20:00
                for (let hour = 10; hour <= 20; hour++) {
                    const time = `${hour.toString().padStart(2, '0')}:00`;
                    const option = document.createElement('option');
                    option.value = time;
                    option.textContent = time;
                    if (reservedTimes.includes(time)) {
                        option.disabled = true;
                    }
                    horaSelect.appendChild(option);
                }
            });

            // Disparar el evento de cambio al cargar la página para establecer las horas iniciales
            fechaInput.dispatchEvent(new Event('change'));
        });
    </script>
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

        <!-- Formulario para realizar una nueva reserva -->
        <form method="POST" class="form-reserva mb-4 p-4 border rounded bg-light">
            <div class="form-group mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label for="fecha" class="form-label">Fecha:</label>
                <input type="date" id="fecha" name="fecha" class="form-control" value="<?php echo htmlspecialchars($_POST['fecha'] ?? date('Y-m-d')); ?>" required>
            </div>
            <div class="form-group mb-3">
                <label for="hora_inicio" class="form-label">Hora de Inicio:</label>
                <select id="hora_inicio" name="hora_inicio" class="form-control" required>
                    <!-- Las opciones se generarán dinámicamente -->
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Reservar</button>
        </form>
        
        <!-- Mostrar mensajes de error o éxito -->
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php elseif (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

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
                    <?php for ($hour = 10; $hour <= 20; $hour++): ?>
                        <?php $time = sprintf('%02d:00', $hour); ?>
                        <option value="<?php echo $time; ?>" <?php echo (isset($_GET['filtro_hora']) && $_GET['filtro_hora'] === $time) ? 'selected' : ''; ?>>
                            <?php echo $time; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary me-2">Filtrar</button>
            <a href="reservar_mesa.php?id_mesa=<?php echo $id_mesa; ?>" class="btn btn-secondary">Borrar Filtros</a>
        </form>

        <h3 class="mt-4 texto-blanco">Lista de Reservas Existentes</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID Reserva</th>
                    <th>Nombre</th>
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
                            <a href="eliminar_reserva.php?id_reserva=<?php echo $reserva['id_reserva']; ?>&id_mesa=<?php echo $id_mesa; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
