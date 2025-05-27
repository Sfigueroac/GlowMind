<?php
session_start();

// Validar que el usuario esté autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.php");
    exit;
}

// Opcional: validar IP y User Agent para mayor seguridad
if ($_SESSION['ip'] !== $_SERVER['REMOTE_ADDR'] || $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
    // Se puede cerrar sesión por seguridad
    session_destroy();
    header("Location: ../login/login.php");
    exit;
}

$usuario_nombre = $_SESSION['usuario_nombre'];
$usuario_rol = $_SESSION['usuario_rol'];

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard Usuario - Glowmind</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body style="background: linear-gradient(135deg, #6a11cb, #2575fc); color: white;">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Glowmind</a>
            <div class="d-flex">
                <span class="navbar-text me-3">Hola, <?php echo htmlspecialchars($usuario_nombre); ?></span>
                <a href="../public/login.php" class="btn btn-outline-warning">Cerrar sesión</a>
            </div>
        </div>
    </nav>

    <main class="container mt-5">
        <h1>Bienvenido al Dashboard de Usuario</h1>
        <p>Tu rol es: <strong><?php echo htmlspecialchars($usuario_rol); ?></strong></p>

        <!-- Aquí puedes agregar más contenido del dashboard, por ejemplo -->
        <div class="card mt-4 bg-light bg-opacity-25 text-dark p-3 rounded">
            <h2>Información de Usuario</h2>
            <ul>
                <li>Nombre: <?php echo htmlspecialchars($usuario_nombre); ?></li>
                <li>Rol: <?php echo htmlspecialchars($usuario_rol); ?></li>
                <li>ID: <?php echo htmlspecialchars($_SESSION['usuario_id']); ?></li>
            </ul>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
