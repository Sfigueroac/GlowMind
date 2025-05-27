<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'psicologo') {

    header('Location: ../public/login.php');
    exit;
}
require_once '../config/db.php';
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
// Obtener estadísticas
$sql = "SELECT COUNT(id) as total_pacientes FROM usuarios WHERE id = 2";
$pacientes = $conn->query($sql)->fetch_assoc();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Psicólogo</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="dashboard-container">
        <h1>Bienvenid@, <?= htmlspecialchars($_SESSION['usuario_nombre']) ?></h1>
        
        <!-- Cards de Estadísticas -->
        <div class="card-grid">
            <div class="card">
                <h3>Pacientes Activos</h3>
                <p><?= $pacientes['total_pacientes'] ?></p>
                <a href="sesiones.php">Ver todos</a>
            </div>
            <div class="card">
                <h3>Próximas Sesiones</h3>
                <p>5</p>
                <a href="sesiones.php">Gestionar</a>
            </div>
        </div>

        <!-- Sección rápida de acciones -->
        <div class="quick-actions">
            <a href="sesiones.php?action=create" class="btn-primary">+ Nueva Sesión</a>
            <a href="../public/info.php" class="btn-secondary">Ver Recursos</a>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>