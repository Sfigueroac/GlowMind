<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

redirigir_si_no_logueado('usuario');

$id_usuario = $_SESSION['usuario_id'];

$sql = "SELECT * FROM sesiones WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Sesiones</title>
</head>
<body>
    <h1>Mis sesiones</h1>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Fecha de inicio</th>
            <th>Estado</th>
        </tr>

        <?php while ($fila = $resultado->fetch_assoc()) { ?>
            <tr>
                <td><?= $fila['id_sesion'] ?></td>
                <td><?= $fila['fecha_inicio'] ?></td>
                <td><?= $fila['estado'] ?></td>
            </tr>
        <?php } ?>
    </table>

    <br>
    <a href="/public/logout.php">Cerrar sesión</a>
</body>
</html>
