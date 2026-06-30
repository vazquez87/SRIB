<?php
session_start();
require_once __DIR__ . '/../../backend/conexion.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit();
}

$nombreAdmin = $_SESSION['nombre'];
$folioBuscado = trim($_GET['folio'] ?? '');

if ($folioBuscado !== '') {
    $stmt = $conexion->prepare("
        SELECT i.id_incidencia, i.folio, i.categoria, i.ubicacion, i.prioridad, i.estado, i.fecha_reporte,
               u.nombre AS nombre_usuario
        FROM incidencias i
        INNER JOIN usuarios u ON i.id_usuario = u.id_usuario
        WHERE i.folio = ?
        ORDER BY i.fecha_reporte DESC
    ");
    $stmt->bind_param("s", $folioBuscado);
} else {
    $stmt = $conexion->prepare("
        SELECT i.id_incidencia, i.folio, i.categoria, i.ubicacion, i.prioridad, i.estado, i.fecha_reporte,
               u.nombre AS nombre_usuario
        FROM incidencias i
        INNER JOIN usuarios u ON i.id_usuario = u.id_usuario
        ORDER BY i.fecha_reporte DESC
    ");
}

$stmt->execute();
$reportes = $stmt->get_result();
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

    <section class="panel-izquierdo">

        <div class="consulta">
            <label for="folio">
                <i class="fa-solid fa-hashtag"></i>
                No. de folio
            </label>

            <form class="busqueda" method="GET" action="menuAdministrador.php">
                <input
                    type="text"
                    name="folio"
                    id="folio"
                    placeholder="Ejemplo: SRIB-2026-0001"
                    value="<?php echo htmlspecialchars($folioBuscado); ?>"
                    autocomplete="off">

                <button type="submit" id="btnConsultar">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    Consultar reporte
                </button>
            </form>
        </div>

        <div class="historial">
            <h2>Historial de reportes</h2>

            <div class="tabla-reportes">
                <?php if ($reportes->num_rows > 0): ?>

                    <table>
                        <thead>
                            <tr>
                                <th>Folio</th>
                                <th>Usuario</th>
                                <th>Categoría</th>
                                <th>Prioridad</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Acción</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php while ($reporte = $reportes->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($reporte['folio']); ?></td>
                                    <td><?php echo htmlspecialchars($reporte['nombre_usuario']); ?></td>
                                    <td><?php echo htmlspecialchars($reporte['categoria']); ?></td>
                                    <td><?php echo htmlspecialchars($reporte['prioridad']); ?></td>
                                    <td><?php echo htmlspecialchars($reporte['estado']); ?></td>
                                    <td><?php echo htmlspecialchars($reporte['fecha_reporte']); ?></td>
                                    <td>
                                        <a class="btn-ver" href="detalleReporte.php?id=<?php echo $reporte['id_incidencia']; ?>">
                                            Ver
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                <?php else: ?>

                    <p>No se encontraron reportes.</p>

                <?php endif; ?>
            </div>
        </div>

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
