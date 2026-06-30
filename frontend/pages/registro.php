<?php
session_start();
require_once __DIR__ . '/../../backend/conexion.php';

$mensaje = '';
$tipo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim(strtolower($_POST['correo'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    if ($nombre == '' || $correo == '' || $password == '') {

        $mensaje = "Todos los campos son obligatorios.";
        $tipo = "error";

    } else {

        $consulta = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE correo = ?");
        $consulta->bind_param("s",$correo);
        $consulta->execute();
        $resultado = $consulta->get_result();

        if($resultado->num_rows > 0){

            $mensaje = "Ese correo ya está registrado.";
            $tipo = "error";

        }else{

            $passwordHash = password_hash($password,PASSWORD_DEFAULT);

            $stmt = $conexion->prepare("INSERT INTO usuarios(nombre,correo,password,rol) VALUES(?,?,?,'usuario')");
            $stmt->bind_param("sss",$nombre,$correo,$passwordHash);

            if($stmt->execute()){

                $mensaje = "Registro exitoso. Ya puedes iniciar sesión.";
                $tipo = "exito";

            }else{

                $mensaje = "Ocurrió un error al registrar.";
                $tipo = "error";

            }

        }

    }

}
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Registro - SRIB</title>

<link rel="stylesheet" href="../css/registro.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body>

<header class="header">

<h1>SISTEMA DE REPORTE DE INCIDENCIAS <span>BUAP</span></h1>

</header>

<a href="index.php" class="btn-back">

<i class="fa-solid fa-arrow-left"></i>

Regresar al menú

</a>

<main class="registro-container">

<section class="left-panel">

<img src="../img/logo-srib.png" alt="Logo SRIB" class="logo-srib">

<h2>Tu participación es muy importante</h2>

<p class="descripcion">

Regístrate para reportar incidencias y dar seguimiento
a tus reportes.

</p>

</section>

<section class="right-panel">

<div class="form-card">

<h2>Crear una <span>cuenta</span></h2>

<p class="subtitle">

Completa los datos para registrarte en SRIB.

</p>

<?php if($mensaje!=""): ?>

<div class="alerta alerta-<?php echo $tipo; ?>">

<?php echo htmlspecialchars($mensaje); ?>

</div>

<?php endif; ?>

<form method="POST" action="registro.php">

<div class="campo">

<label>

<i class="fa-solid fa-user"></i>

Nombre completo

</label>

<input
type="text"
name="nombre"
id="nombre"
placeholder="Ingresa tu nombre completo"
required>

</div>

<div class="campo">

<label>

<i class="fa-solid fa-envelope"></i>

Correo institucional

</label>

<input
type="email"
name="correo"
id="correo"
placeholder="ejemplo@alumno.buap.mx"
required>

<small class="correo-ayuda">

Utiliza un correo institucional BUAP.

</small>

</div>

<div class="campo">

<label>

<i class="fa-solid fa-lock"></i>

Contraseña

</label>

<input
type="password"
name="password"
id="password"
placeholder="Mínimo 8 caracteres"
minlength="8"
required>

</div>

<button class="btn-register" type="submit">

<i class="fa-solid fa-user-plus"></i>

REGISTRARSE

</button>

</form>

<p class="login-link">

¿Ya tienes una cuenta?

<a href="login.php">

Inicia sesión

</a>

</p>

</div>

</section>

</main>

<script src="../js/validaciones.js"></script>

</body>

</html>
