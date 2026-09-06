<?php
session_start();
require_once __DIR__ . '/../../backend/conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$nombre = $_SESSION['nombre'];
$idUsuario = $_SESSION['id_usuario'];

$stmt = $conexion->prepare("
    SELECT folio, categoria, estado, fecha_reporte
    FROM incidencias
    WHERE id_usuario = ?
    ORDER BY fecha_reporte DESC
    LIMIT 5
");

$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$reportes = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Usuario - SRIB</title>
    <link rel="stylesheet" href="../css/menuUsuario.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>

<header class="header">
    <h1>
        SISTEMA DE REPORTE DE INCIDENCIAS
        <span>BUAP</span>
    </h1>

    <a href="../../backend/logout.php" class="btn-logout">
        <i class="fa-solid fa-right-from-bracket"></i>
        Cerrar sesión
    </a>
</header>

<main class="dashboard">

    <section class="profile">
        <img src="../img/perfil-default.png" alt="Foto de perfil" class="profile-img">

        <h2>
            Bienvenido,
            <?php echo htmlspecialchars($nombre); ?>
        </h2>

        <p>
            Selecciona una opción para continuar.
        </p>
    </section>

    <section class="menu-panel">

        <h2>¿Qué deseas realizar hoy?</h2>

        <div class="menu-buttons">

            <a href="nuevoReporte.php" class="btn-option">
                <i class="fa-solid fa-file-circle-plus"></i>
                Nuevo reporte
            </a>

            <a href="consultarReporte.php" class="btn-option">
                <i class="fa-solid fa-magnifying-glass"></i>
                Consultar reportes
            </a>

        </div>

        <div class="mis-reportes">

            <h3>Mis reportes recientes</h3>

            <?php if ($reportes->num_rows > 0): ?>

                <table>
                    <thead>
                        <tr>
                            <th>Folio</th>
                            <th>Categoría</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acción</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php while ($fila = $reportes->fetch_assoc()): ?>

                            <tr>

                                <td>
                                    <?php echo htmlspecialchars($fila['folio']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($fila['categoria']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($fila['estado']); ?>
                                </td>

                                <td>
                                    <?php echo date('d/m/Y', strtotime($fila['fecha_reporte'])); ?>
                                </td>

                                <td>
                                    <a class="btn-consultar-mini"
                                       href="consultarReporte.php?folio=<?php echo urlencode($fila['folio']); ?>">
                                        Ver
                                    </a>
                                </td>

                            </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>

            <?php else: ?>

                <p class="sin-reportes">
                    Aún no has registrado incidencias.
                </p>

            <?php endif; ?>

        </div>

    </section>

</main>

</body>
</html>
