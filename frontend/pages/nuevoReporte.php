<?php
session_start();
require_once __DIR__ . '/../../backend/conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$mensaje = '';
$tipo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_SESSION['id_usuario'];
    $descripcion = trim($_POST['descripcion'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $edificio = trim($_POST['edificio'] ?? '');
    $detalleUbicacion = trim($_POST['detalleUbicacion'] ?? '');
    $fecha = trim($_POST['fecha'] ?? '');

    $ubicacion = $edificio . ' - ' . $detalleUbicacion;

    if ($descripcion === '' || $categoria === '' || $edificio === '' || $detalleUbicacion === '' || $fecha === '') {
        $mensaje = 'Debes completar todos los campos.';
        $tipo = 'error';
    } else {
        $stmt = $conexion->prepare("
            INSERT INTO incidencias 
            (id_usuario, descripcion, categoria, ubicacion, estado, fecha_reporte)
            VALUES (?, ?, ?, ?, 'Pendiente', ?)
        ");

        $stmt->bind_param("issss", $id_usuario, $descripcion, $categoria, $ubicacion, $fecha);

        if ($stmt->execute()) {
            $mensaje = 'Reporte enviado correctamente.';
            $tipo = 'exito';
        } else {
            $mensaje = 'Error al enviar el reporte.';
            $tipo = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Reporte - SRIB</title>
    <link rel="stylesheet" href="../css/nuevoReporte.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>

<header class="header">
    <h1>SISTEMA DE REPORTE DE INCIDENCIAS <span>BUAP</span></h1>
</header>

<a href="menuUsuario.php" class="btn-back">
    <i class="fa-solid fa-arrow-left"></i>
    Regresar al menú
</a>

<main class="reporte-container">
    <div class="form-card">
        <h2>Nuevo <span>reporte</span></h2>

        <?php if ($mensaje): ?>
            <div class="alerta alerta-<?php echo $tipo; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <form id="formReporte" method="POST" action="nuevoReporte.php">

            <div class="campo">
                <label>
                    <i class="fa-solid fa-file-lines"></i>
                    Descripción
                </label>

                <textarea
                    name="descripcion"
                    id="descripcion"
                    placeholder="Describe la incidencia encontrada..."
                    required></textarea>
            </div>

            <div class="campo">
                <label>
                    <i class="fa-solid fa-tags"></i>
                    Categoría
                </label>

                <select name="categoria" id="categoria" required>
                    <option value="">Selecciona una categoría</option>
                    <option>Equipos de cómputo</option>
                    <option>Internet</option>
                    <option>Proyectores</option>
                    <option>Mobiliario</option>
                    <option>Infraestructura</option>
                    <option>Otro</option>
                </select>
            </div>

            <div class="campo">
                <label>
                    <i class="fa-solid fa-location-dot"></i>
                    Ubicación
                </label>

                <div class="ubicacion-container">
                    <select name="edificio" id="edificio" required>
                        <option value="">Selecciona edificio</option>
                        <option>CC01</option>
                        <option>CC02</option>
                        <option>CC03</option>
                        <option>CC04</option>
                        <option>CC05</option>
                    </select>

                    <input
                        type="text"
                        name="detalleUbicacion"
                        id="detalleUbicacion"
                        placeholder="No. de salón, laboratorio, cubículo, etc."
                        required>
                </div>
            </div>

            <div class="campo">
                <label>
                    <i class="fa-solid fa-calendar"></i>
                    Fecha de registro
                </label>

                <input
                    type="date"
                    name="fecha"
                    id="fecha"
                    required>

                <small class="fecha-info">
                    Solo se permiten incidencias ocurridas en los últimos 15 días.
                </small>
            </div>

            <button type="submit" class="btn-enviar">
                <i class="fa-solid fa-paper-plane"></i>
                Enviar reporte
            </button>

        </form>
    </div>
</main>

<script src="../js/validaciones.js"></script>

</body>
</html>
