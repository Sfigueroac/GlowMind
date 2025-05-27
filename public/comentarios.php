<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../config/db.php';

// Verificar que el usuario esté logueado y sea rol 'usuario'
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'usuario') {
    header("Location: /login.php");
    exit;
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener la lista de psicólogos
$sql_psicologos = "SELECT id, nombre FROM usuarios WHERE rol = 'psicologo' ORDER BY nombre";
$result_psicologos = $conn->query($sql_psicologos);

$psicologos = [];
if ($result_psicologos->num_rows > 0) {
    while ($row = $result_psicologos->fetch_assoc()) {
        $psicologos[] = $row;
    }
}

// Procesar formulario al enviar
$comentarioErr = $psicologoErr = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $comentario = trim($_POST['comentario']);
    $psicologo_id = $_POST['psicologo_id'];

    if (empty($comentario)) {
        $comentarioErr = "El comentario no puede estar vacío";
    }
    if (empty($psicologo_id) || !is_numeric($psicologo_id)) {
        $psicologoErr = "Seleccione un psicólogo válido";
    }

    if (!$comentarioErr && !$psicologoErr) {
        $stmt = $conn->prepare("INSERT INTO comentarios (usuario_id, psicologo_id, comentario, fecha) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $_SESSION['usuario_id'], $psicologo_id, $comentario);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Comentario enviado correctamente.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error al enviar el comentario.</div>";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Enviar Comentario - Glowmind</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="p-4">
    <h2>Deja tu comentario para un psicólogo</h2>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="psicologo_id" class="form-label">Selecciona un psicólogo:</label>
            <select name="psicologo_id" id="psicologo_id" class="form-select" required>
                <option value="">-- Seleccione --</option>
                <?php foreach ($psicologos as $psicologo): ?>
                    <option value="<?php echo $psicologo['id']; ?>" <?php if (isset($psicologo_id) && $psicologo_id == $psicologo['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($psicologo['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="text-danger"><?php echo $psicologoErr; ?></div>
        </div>

        <div class="mb-3">
            <label for="comentario" class="form-label">Comentario:</label>
            <textarea name="comentario" id="comentario" rows="4" class="form-control" required><?php echo isset($comentario) ? htmlspecialchars($comentario) : ''; ?></textarea>
            <div class="text-danger"><?php echo $comentarioErr; ?></div>
        </div>

        <button type="submit" class="btn btn-primary">Enviar comentario</button>
    </form>
</body>
</html>
