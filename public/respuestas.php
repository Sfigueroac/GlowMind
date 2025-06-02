<?php
session_start();
require_once '../config/db.php';

// Verificar que el usuario esté logueado y sea rol 'usuario'
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'usuario') {
    header("Location: /login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener solo comentarios del usuario actual que tengan respuesta
$sql = "SELECT c.comentario, c.respuesta, c.created_at, c.responded_at, u.nombre AS psicologo_nombre
        FROM comentarios c
        JOIN usuarios u ON c.psicologo_id = u.id
        WHERE c.usuario_id = ? AND c.respuesta IS NOT NULL
        ORDER BY c.responded_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!-- Aquí iría tu HTML personalizado -->

<?php if ($result->num_rows === 0): ?>
    <p>No has recibido respuestas aún.</p>
<?php else: ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="respuesta">
            <p><strong>Psicólogo:</strong> <?= htmlspecialchars($row['psicologo_nombre']) ?></p>
            <p><strong>Tu comentario:</strong><br><?= nl2br(htmlspecialchars($row['comentario'])) ?></p>
            <p><strong>Respuesta:</strong><br><?= nl2br(htmlspecialchars($row['respuesta'])) ?></p>
            <p><em>Respondido el <?= $row['responded_at'] ?></em></p>
        </div>
    <?php endwhile; ?>
<?php endif; ?>

<?php
$stmt->close();
$conn->close();
?>
