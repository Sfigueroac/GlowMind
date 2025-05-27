<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'psicologo') {
    header("Location: /login.php");
    exit;
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['respuesta'], $_POST['comentario_id'])) {
    $respuesta = trim($_POST['respuesta']);
    $comentario_id = intval($_POST['comentario_id']);
    if ($respuesta !== '') {
        $stmt = $conn->prepare("UPDATE comentarios SET respuesta = ?, responded_at = NOW() WHERE id = ?");
        $stmt->bind_param("si", $respuesta, $comentario_id);
        $stmt->execute();
        $stmt->close();
    }
}

$sql = "SELECT c.id, c.comentario, c.respuesta, c.created_at, c.responded_at, u.nombre AS usuario_nombre
        FROM comentarios c
        JOIN usuarios u ON c.usuario_id = u.id
        ORDER BY c.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comentarios para responder</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
    <div class="container" style="max-width:800px;">
        <h2>Comentarios de usuarios</h2>

        <?php if ($result->num_rows == 0): ?>
            <p>No hay comentarios todavía.</p>
        <?php else: ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($row['usuario_nombre']) ?> <small class="text-muted"><?= $row['created_at'] ?></small></h5>
                        <p class="card-text"><?= nl2br(htmlspecialchars($row['comentario'])) ?></p>

                        <?php if ($row['respuesta']): ?>
                            <div class="alert alert-success">
                                <strong>Respuesta:</strong><br>
                                <?= nl2br(htmlspecialchars($row['respuesta'])) ?>
                                <br><small class="text-muted">Respondido el <?= $row['responded_at'] ?></small>
                            </div>
                        <?php else: ?>
                            <form method="POST" action="">
                                <input type="hidden" name="comentario_id" value="<?= $row['id'] ?>">
                                <div class="mb-3">
                                    <textarea name="respuesta" class="form-control" rows="3" placeholder="Escribe tu respuesta..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-success">Responder</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>

    </div>
</body>
</html>

<?php
$conn->close();
?>
