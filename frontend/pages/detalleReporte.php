<?php
session_start();
require_once __DIR__ . '/../../backend/conexion.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit();
}

$id = intval($_GET['id'] ?? 0);
$mensaje = '';

if ($id <= 0) {
    header("Location: menuAdministrador.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estado = $_POST['estado'] ?? '';
    $prioridad = $_POST['prioridad'] ?? '';

    if ($estado !== '' && $prioridad !== '') {
        $stmt = $conexion->prepare("
            UPDATE incidencias
            SET estado = ?, prioridad = ?
            WHERE id_incidencia = ?
        ");
        $stmt->bind_param("ssi", $estado, $prioridad, $id);
        $stmt->execute();

        $mensaje = "Cambios guardados correctamente.";
    }
}

$stmt = $conexion->prepare("
    SELECT i.*, u.nombre AS nombre_usuario, u.correo
    FROM incidencias i
    INNER JOIN usuarios u ON i.id_usuario = u.id_usuario
    WHERE i.id_incidencia = ?
    LIMIT 1
");

$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    header("Location: menuAdministrador.php");
    exit();
}

$reporte = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Reporte - SRIB</title>
    <link rel="stylesheet" href="../css/detalleReporte.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>

<header class="header">
    <h1>SISTEMA DE REPORTE DE INCIDENCIAS <span>BUAP</span></h1>
</header>

<a href="menuAdministrador.php" class="btn-back">
    <i class="fa-solid fa-arrow-left"></i>
    Regresar al menú
</a>

<main class="contenedor">
    <div class="card">

        <h2>Datos del <span>reporte</span></h2>

        <?php if ($mensaje): ?>
            <div class="alerta-exito">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="detalleReporte.php?id=<?php echo $id; ?>">

            <div class="fila">
                <div class="campo">
                    <label>No. Folio</label>
                    <input type="text" value="<?php echo htmlspecialchars($reporte['folio']); ?>" readonly>
                </div>

                <div class="campo pequeño">
                    <label>Fecha de registro</label>
                    <input type="text" value="<?php echo htmlspecialchars($reporte['fecha_reporte']); ?>" readonly>
                </div>
            </div>

            <div class="fila">
                <div class="campo">
                    <label>Usuario</label>
                    <input type="text" value="<?php echo htmlspecialchars($reporte['nombre_usuario']); ?>" readonly>
                </div>

                <div class="campo pequeño">
                    <label>Correo</label>
                    <input type="text" value="<?php echo htmlspecialchars($reporte['correo']); ?>" readonly>
                </div>
            </div>

            <div class="campo">
                <label>Descripción</label>
                <textarea readonly><?php echo htmlspecialchars($reporte['descripcion']); ?></textarea>
            </div>

            <div class="fila">
                <div class="campo">
                    <label>Categoría</label>
                    <input type="text" value="<?php echo htmlspecialchars($reporte['categoria']); ?>" readonly>
                </div>

                <div class="campo pequeño">
                    <label>Prioridad actual</label>
                    <div class="estado">
                        <?php echo htmlspecialchars($reporte['prioridad']); ?>
                    </div>
                </div>
            </div>

            <div class="fila">
                <div class="campo">
                    <label>Ubicación</label>
                    <input type="text" value="<?php echo htmlspecialchars($reporte['ubicacion']); ?>" readonly>
                </div>

                <div class="campo pequeño">
                    <label>Estatus actual</label>
                    <div class="estado">
                        <?php echo htmlspecialchars($reporte['estado']); ?>
                    </div>
                </div>
            </div>

            <div class="fila-select">
                <div>
                    <label for="estado">Cambiar estatus</label>
                    <select name="estado" id="estado" required>
                        <option value="Pendiente" <?php if ($reporte['estado'] === 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                        <option value="En proceso" <?php if ($reporte['estado'] === 'En proceso') echo 'selected'; ?>>En proceso</option>
                        <option value="Resuelta" <?php if ($reporte['estado'] === 'Resuelta') echo 'selected'; ?>>Resuelta</option>
                    </select>
                </div>

                <div>
                    <label for="prioridad">Cambiar prioridad</label>
                    <select name="prioridad" id="prioridad" required>
                        <option value="Baja" <?php if ($reporte['prioridad'] === 'Baja') echo 'selected'; ?>>Baja</option>
                        <option value="Media" <?php if ($reporte['prioridad'] === 'Media') echo 'selected'; ?>>Media</option>
                        <option value="Alta" <?php if ($reporte['prioridad'] === 'Alta') echo 'selected'; ?>>Alta</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn-finalizar">
                <i class="fa-solid fa-check"></i>
                Guardar cambios
            </button>

        </form>

    </div>
</main>

</body>
</html>
