<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$nombre = $_SESSION['nombre'];
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Panel de Usuario - SRIB</title>

    <link rel="stylesheet" href="../css/menuUsuario.css">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

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

        <img src="../img/perfil-default.png"
             alt="Foto de perfil"
             class="profile-img">

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

    </section>

</main>

</body>

</html>
