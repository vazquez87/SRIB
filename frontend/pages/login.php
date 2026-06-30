<?php
session_start();
require_once __DIR__ . '/../../backend/conexion.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim(strtolower($_POST['correo'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    if ($correo === '' || $password === '') {
        $mensaje = 'Debes completar todos los campos.';
    } else {
        $stmt = $conexion->prepare("SELECT id_usuario, nombre, correo, password, rol FROM usuarios WHERE correo = ? LIMIT 1");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($usuario = $resultado->fetch_assoc()) {
            if (password_verify($password, $usuario['password'])) {
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['correo'] = $usuario['correo'];
                $_SESSION['rol'] = $usuario['rol'];

                if ($usuario['rol'] === 'administrador') {
                    header("Location: menuAdministrador.php");
                } else {
                    header("Location: menuUsuario.php");
                }
                exit();
            }
        }

        $mensaje = 'Correo o contraseña incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - SRIB</title>
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>

<header class="header">
    <h1>SISTEMA DE REPORTE DE INCIDENCIAS <span>BUAP</span></h1>
</header>

<a href="index.php" class="btn-back">
    <i class="fa-solid fa-arrow-left"></i>
    Regresar al menú
</a>

<main class="login-container">
    <section class="left-panel">
        <img src="../img/logo-srib.png" alt="Logo SRIB" class="logo-srib">

        <h2>Tu participación es<br>muy importante</h2>

        <p class="descripcion">
            Inicia sesión para consultar el estado de tus reportes
            y dar seguimiento a las incidencias registradas.
        </p>

        <div class="linea"></div>
    </section>

    <section class="right-panel">
        <div class="form-card">
            <h2>Iniciar <span>sesión</span></h2>

            <?php if ($mensaje): ?>
                <div class="alerta alerta-error">
                    <?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>

            <form id="formLogin" method="POST" action="login.php">
                <div class="campo">
                    <label id="lblCorreo" for="correo">
                        <i class="fa-solid fa-envelope"></i>
                        Correo institucional
                    </label>

                    <input
                        type="email"
                        name="correo"
                        id="correo"
                        placeholder="ejemplo@alumno.buap.mx"
                        required
                        autocomplete="username">

                    <small class="correo-ayuda">
                        Utiliza tu correo institucional BUAP.
                    </small>
                </div>

                <div class="campo">
                    <label id="lblPassword" for="password">
                        <i class="fa-solid fa-lock"></i>
                        Contraseña
                    </label>

                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Ingresa tu contraseña"
                        required
                        autocomplete="current-password">
                </div>

                <button type="submit" class="btn-login">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    INICIAR SESIÓN
                </button>
            </form>

            <p class="register-link">
                ¿No tienes una cuenta?
                <a href="registro.php">Regístrate</a>
            </p>
        </div>
    </section>
</main>

</body>
</html>
