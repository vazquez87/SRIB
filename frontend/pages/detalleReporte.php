<?php
session_start();
require_once __DIR__ . '/../../backend/conexion.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit();
}

$id = intval($_GET['id'] ?? 0);

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

        if ($stmt->execute()) {
            $_SESSION['mensaje_exito'] = "La actualización del reporte se guardó correctamente.";
            header("Location: detalleReporte.php?id=" . $id);
            exit();
        }
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

function claseEstado($estado) {
    return strtolower(str_replace(' ', '-', $estado));
}

function clasePrioridad($prioridad) {
    return strtolower(str_replace(' ', '-', $prioridad));
}
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

<?php if (isset($_SESSION['mensaje_exito'])): ?>
<div class="modal-exito" id="modalExito">
    <div class="modal-card">
        <i class="fa-solid fa-circle-check icono-ok"></i>
        <h2>Cambios guardados</h2>
        <p><?php echo htmlspecialchars($_SESSION['mensaje_exito']); ?></p>
        <button type="button" onclick="cerrarModal()">Aceptar</button>
    </div>
</div>
<?php unset($_SESSION['mensaje_exito']); ?>
<?php endif; ?>

<a href="menuAdministrador.php" class="btn-back">
    <i class="fa-solid fa-arrow-left"></i>
    Regresar al panel
</a>

<main class="contenedor">

    <section class="card">

        <div class="encabezado-reporte">
            <div>
                <span class="label-folio">Folio del reporte</span>
                <h2><?php echo htmlspecialchars($reporte['folio']); ?></h2>
            </div>

            <div class="badges-superiores">
                <div class="info-badge estado-<?php echo claseEstado($reporte['estado']); ?>">
    <small>Estatus actual</small>
    <strong><?php echo htmlspecialchars($reporte['estado']); ?></strong>
</div>

<div class="info-badge prioridad-<?php echo clasePrioridad($reporte['prioridad']); ?>">
    <small>Prioridad actual</small>
    <strong><?php echo htmlspecialchars($reporte['prioridad']); ?></strong>
</div>
            </div>
        </div>

        <form method="POST" action="detalleReporte.php?id=<?php echo $id; ?>">

            <div class="seccion">
                <h3>
                    <i class="fa-solid fa-circle-info"></i>
                    Información general
                </h3>

                <div class="fila">
                    <div class="campo">
                        <label>Usuario</label>
                        <input type="text" value="<?php echo htmlspecialchars($reporte['nombre_usuario']); ?>" readonly>
                    </div>

                    <div class="campo">
                        <label>Correo</label>
                        <input type="text" value="<?php echo htmlspecialchars($reporte['correo']); ?>" readonly>
                    </div>
                </div>

                <div class="fila">
                    <div class="campo">
                        <label>Categoría</label>
                        <input type="text" value="<?php echo htmlspecialchars($reporte['categoria']); ?>" readonly>
                    </div>

                    <div class="campo">
                        <label>Fecha de registro</label>
                        <input type="text" value="<?php echo date('d/m/Y', strtotime($reporte['fecha_reporte'])); ?>" readonly>
                    </div>
                </div>

                <div class="campo">
                    <label>Ubicación</label>
                    <input type="text" value="<?php echo htmlspecialchars($reporte['ubicacion']); ?>" readonly>
                </div>

                <div class="campo">
                    <label>Descripción</label>
                    <textarea readonly><?php echo htmlspecialchars($reporte['descripcion']); ?></textarea>
                </div>
            </div>

            <div class="seccion actualizar">
                <h3>
                    <i class="fa-solid fa-pen-to-square"></i>
                    Actualizar seguimiento
                </h3>

                <p class="texto-ayuda">
                    Modifica el estatus o la prioridad del reporte según el avance de atención.
                </p>

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
                    Guardar actualización
                </button>
            </div>

        </form>

    </section>

</main>

<script>
function cerrarModal(){
    document.getElementById("modalExito").style.display = "none";
}
</script>

</body>
</html>
