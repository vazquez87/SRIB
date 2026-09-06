<?php
session_start();
require_once __DIR__ . '/../../backend/conexion.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit();
}

$nombreAdmin = $_SESSION['nombre'];
$folioBuscado = trim($_GET['folio'] ?? '');

$queryBase = "
    SELECT i.id_incidencia, i.folio, i.categoria, i.ubicacion, i.prioridad, i.estado, i.fecha_reporte,
           u.nombre AS nombre_usuario
    FROM incidencias i
    INNER JOIN usuarios u ON i.id_usuario = u.id_usuario
";

if ($folioBuscado !== '') {
    $stmt = $conexion->prepare($queryBase . "
        WHERE i.folio = ?
        ORDER BY 
            FIELD(i.estado,'Pendiente','En proceso','Resuelta'),
            FIELD(i.prioridad,'Alta','Media','Baja'),
            i.fecha_reporte DESC
    ");
    $stmt->bind_param("s", $folioBuscado);
} else {
    $stmt = $conexion->prepare($queryBase . "
        ORDER BY 
            FIELD(i.estado,'Pendiente','En proceso','Resuelta'),
            FIELD(i.prioridad,'Alta','Media','Baja'),
            i.fecha_reporte DESC
    ");
}

$stmt->execute();
$resultado = $stmt->get_result();

$reportesPorEstado = [
    'Pendiente' => [],
    'En proceso' => [],
    'Resuelta' => []
];

while ($reporte = $resultado->fetch_assoc()) {
    $reportesPorEstado[$reporte['estado']][] = $reporte;
}

$totalPendientes = count($reportesPorEstado['Pendiente']);
$totalProceso = count($reportesPorEstado['En proceso']);
$totalResueltas = count($reportesPorEstado['Resuelta']);
$totalReportes = $totalPendientes + $totalProceso + $totalResueltas;

function clasePrioridad($prioridad) {
    return strtolower(str_replace(' ', '-', $prioridad));
}

function claseEstado($estado) {
    return strtolower(str_replace(' ', '-', $estado));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador | SRIB</title>
    <link rel="stylesheet" href="../css/menuAdministrador.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>

<header class="header">
    <h1>SISTEMA DE REPORTE DE INCIDENCIAS <span>BUAP</span></h1>
</header>

<main class="contenedor">

    <section class="panel-principal">

        <section class="resumen">
            <div class="card-resumen pendiente-card">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div>
                    <h3><?php echo $totalPendientes; ?></h3>
                    <p>Pendientes</p>
                </div>
            </div>

            <div class="card-resumen proceso-card">
                <i class="fa-solid fa-spinner"></i>
                <div>
                    <h3><?php echo $totalProceso; ?></h3>
                    <p>En proceso</p>
                </div>
            </div>

            <div class="card-resumen resuelta-card">
                <i class="fa-solid fa-circle-check"></i>
                <div>
                    <h3><?php echo $totalResueltas; ?></h3>
                    <p>Resueltas</p>
                </div>
            </div>
        </section>

        <section class="consulta">
            <div>
                <h2><i class="fa-solid fa-magnifying-glass"></i> Buscar reporte</h2>
                <p>Consulta una incidencia mediante su número de folio.</p>
            </div>

            <form class="busqueda" method="GET" action="menuAdministrador.php">
                <input
                    type="text"
                    name="folio"
                    id="folio"
                    placeholder="Ejemplo: SRIB-2026-0001"
                    value="<?php echo htmlspecialchars($folioBuscado); ?>"
                    autocomplete="off">

                <button type="submit">
                    Buscar
                </button>

                <a href="menuAdministrador.php" class="btn-limpiar">
                    Limpiar
                </a>
            </form>
        </section>

        <section class="historial">
            <h2>Panel de incidencias</h2>

            <?php foreach ($reportesPorEstado as $estado => $reportes): ?>
                <div class="grupo-reportes">
                    <div class="grupo-header <?php echo claseEstado($estado); ?>">
                        <h3>
                            <?php if ($estado === 'Pendiente'): ?>
                                <i class="fa-solid fa-circle-exclamation"></i>
                            <?php elseif ($estado === 'En proceso'): ?>
                                <i class="fa-solid fa-spinner"></i>
                            <?php else: ?>
                                <i class="fa-solid fa-circle-check"></i>
                            <?php endif; ?>

                            <?php echo htmlspecialchars($estado); ?>
                            
                        </h3>
                    </div>

                    <?php if (count($reportes) > 0): ?>
                        <div class="tabla-reportes">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Folio</th>
                                        <th>Usuario</th>
                                        <th>Categoría</th>
                                        <th>Prioridad</th>
                                        <th>Fecha</th>
                                        
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php foreach ($reportes as $reporte): ?>
                                        <tr>
                                            <td class="folio"><?php echo htmlspecialchars($reporte['folio']); ?></td>
                                            <td><?php echo htmlspecialchars($reporte['nombre_usuario']); ?></td>
                                            <td><?php echo htmlspecialchars($reporte['categoria']); ?></td>
                                            <td>
                                                <span class="badge prioridad-<?php echo clasePrioridad($reporte['prioridad']); ?>">
                                                    <?php echo htmlspecialchars($reporte['prioridad']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y', strtotime($reporte['fecha_reporte'])); ?></td>
                                            <td>
                                                <a class="btn-ver" href="detalleReporte.php?id=<?php echo $reporte['id_incidencia']; ?>">
                                                    <i class="fa-solid fa-eye"></i>
                                                    Ver detalle
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="sin-reportes">No hay reportes en esta sección.</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </section>

    </section>

    <aside class="panel-derecho">
        <img src="../img/perfil-default.png" class="foto-perfil" alt="Perfil">

        <h2>Administrador</h2>
        <p><?php echo htmlspecialchars($nombreAdmin); ?></p>



        <a href="../../backend/logout.php" class="btn-salir">
            <i class="fa-solid fa-right-from-bracket"></i>
            Cerrar sesión
        </a>
    </aside>

</main>

</body>
</html>
