<?php
session_start();
require_once __DIR__ . '/../../backend/conexion.php';

date_default_timezone_set('America/Mexico_City');

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$mensaje = '';
$tipo = '';
$folioGenerado = '';

$hoy = date('Y-m-d');
$fechaMinima = date('Y-m-d', strtotime('-15 days'));

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
    } elseif ($fecha < $fechaMinima || $fecha > $hoy) {
        $mensaje = 'La fecha debe estar dentro de los últimos 15 días y no puede ser posterior a hoy.';
        $tipo = 'error';
    } else {
        $stmt = $conexion->prepare("
            INSERT INTO incidencias 
            (id_usuario, descripcion, categoria, ubicacion, estado, fecha_reporte)
            VALUES (?, ?, ?, ?, 'Pendiente', ?)
        ");

        $stmt->bind_param("issss", $id_usuario, $descripcion, $categoria, $ubicacion, $fecha);

        if ($stmt->execute()) {
            $id_incidencia = $conexion->insert_id;
            $anio = date("Y");
            $folio = "SRIB-" . $anio . "-" . str_pad($id_incidencia, 4, "0", STR_PAD_LEFT);

            $stmtFolio = $conexion->prepare("
                UPDATE incidencias
                SET folio = ?
                WHERE id_incidencia = ?
            ");

            $stmtFolio->bind_param("si", $folio, $id_incidencia);
            $stmtFolio->execute();

            $mensaje = "Reporte enviado correctamente.";
            $tipo = "exito";
            $folioGenerado = $folio;
        } else {
            $mensaje = "Error al enviar el reporte.";
            $tipo = "error";
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

        <?php if ($mensaje && $tipo === 'error'): ?>
            <div class="alerta alerta-error">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <form id="formReporte" method="POST" action="nuevoReporte.php">

            <div class="campo">
                <label><i class="fa-solid fa-file-lines"></i> Descripción</label>
                <textarea name="descripcion" id="descripcion" placeholder="Describe la incidencia encontrada..." required></textarea>
            </div>

            <div class="campo">
                <label><i class="fa-solid fa-tags"></i> Categoría</label>
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
                <label><i class="fa-solid fa-location-dot"></i> Ubicación</label>

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
                <label><i class="fa-solid fa-calendar"></i> Fecha de registro</label>

                <input
                    type="date"
                    name="fecha"
                    id="fecha"
                    min="<?php echo $fechaMinima; ?>"
                    max="<?php echo $hoy; ?>"
                    required>

                <small class="fecha-info">
                    Solo se permiten incidencias ocurridas entre el
                    <?php echo date('d/m/Y', strtotime($fechaMinima)); ?>
                    y el
                    <?php echo date('d/m/Y', strtotime($hoy)); ?>.
                </small>
            </div>

            <button type="submit" class="btn-enviar">
                <i class="fa-solid fa-paper-plane"></i>
                Enviar reporte
            </button>

        </form>
    </div>
</main>

<?php if ($tipo === 'exito' && $folioGenerado !== ''): ?>
<div class="modal-folio" id="modalFolio">
    <div class="modal-contenido">
        <i class="fa-solid fa-circle-check icono-exito"></i>

        <h2>Reporte enviado correctamente</h2>

        <p>Tu número de folio es:</p>

        <div class="folio-box" id="folioTexto">
            <?php echo htmlspecialchars($folioGenerado); ?>
        </div>

        <button type="button" onclick="copiarFolio()" class="btn-copiar">
            <i class="fa-solid fa-copy"></i>
            Copiar folio
        </button>

        <a href="consultarReporte.php" class="btn-consultar-modal">
            Consultar reporte
        </a>

        <button type="button" onclick="cerrarModal()" class="btn-cerrar-modal">
            Cerrar
        </button>
    </div>
</div>

<script>
function copiarFolio() {
    const folio = document.getElementById("folioTexto").innerText.trim();

    navigator.clipboard.writeText(folio).then(() => {
        const boton = document.querySelector(".btn-copiar");
        boton.innerHTML = '<i class="fa-solid fa-check"></i> Folio copiado';
    });
}

function cerrarModal() {
    document.getElementById("modalFolio").style.display = "none";
}
</script>
<?php endif; ?>

<script src="../js/validaciones.js"></script>

</body>
</html>
