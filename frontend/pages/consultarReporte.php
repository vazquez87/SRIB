<?php
session_start();
require_once __DIR__ . '/../../backend/conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$reporte = null;
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $folio = trim($_POST['folio'] ?? '');
    $id_usuario = $_SESSION['id_usuario'];

    if ($folio === '') {
        $mensaje = 'Debes ingresar un folio.';
    } else {
        $stmt = $conexion->prepare("
            SELECT folio, descripcion, categoria, ubicacion, prioridad, estado, fecha_reporte
            FROM incidencias
            WHERE folio = ? AND id_usuario = ?
            LIMIT 1
        ");

        $stmt->bind_param("si", $folio, $id_usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $reporte = $resultado->fetch_assoc();
        } else {
            $mensaje = 'No se encontró ningún reporte con ese folio.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Reporte - SRIB</title>
    <link rel="stylesheet" href="../css/consultarReporte.css">
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

<main class="contenedor">

    <section class="busqueda">
        <form method="POST" action="consultarReporte.php">
            <h2>No. Folio:</h2>

            <input
                type="text"
                name="folio"
                id="folio"
                placeholder="Ejemplo: SRIB-2026-0001"
                required
                autocomplete="off">

            <button type="submit" class="btn-consultar">
                <i class="fa-solid fa-magnifying-glass"></i>
                Consultar reporte
            </button>
        </form>
    </section>

    <?php if ($mensaje): ?>
        <section class="resultado">
            <h2>Seguimiento del reporte</h2>
            <div class="linea"></div>
            <div class="detalle">
                <p><?php echo htmlspecialchars($mensaje); ?></p>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($reporte): ?>
        <section class="resultado">
            <h2>Seguimiento del reporte</h2>
            <div class="linea"></div>

            <div class="detalle">
                <p><strong>Folio:</strong> <?php echo htmlspecialchars($reporte['folio']); ?></p>
                <p><strong>Categoría:</strong> <?php echo htmlspecialchars($reporte['categoria']); ?></p>
                <p><strong>Prioridad:</strong> <?php echo htmlspecialchars($reporte['prioridad']); ?></p>
                <p><strong>Estado:</strong> <?php echo htmlspecialchars($reporte['estado']); ?></p>
                <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($reporte['ubicacion']); ?></p>
                <p><strong>Fecha de reporte:</strong> <?php echo htmlspecialchars($reporte['fecha_reporte']); ?></p>
                <p><strong>Descripción:</strong> <?php echo htmlspecialchars($reporte['descripcion']); ?></p>
            </div>
        </section>
    <?php endif; ?>

</main>

</body>
</html>
