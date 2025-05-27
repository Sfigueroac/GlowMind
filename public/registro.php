<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once '../config/db.php';

// Definir constantes para la conexión si no están definidas
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('DB_NAME')) define('DB_NAME', 'glowmind');

$emailErr = $passErr = $nameErr = $registerErr = '';
$email = $password = $nombre = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $rol = 'usuario'; // o 'psicologo' si quieres permitir rol al registrar

    // Validar nombre
    if (empty($nombre)) {
        $nameErr = "El nombre es requerido";
    }

    // Validar email
    if (empty($email)) {
        $emailErr = "El correo es requerido";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Formato de correo inválido";
    }

    // Validar contraseña
    if (empty($password)) {
        $passErr = "La contraseña es requerida";
    } elseif (strlen($password) < 6) {
        $passErr = "La contraseña debe tener al menos 6 caracteres";
    }

    // Si no hay errores, insertar usuario
    if (empty($emailErr) && empty($passErr) && empty($nameErr)) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            $registerErr = "Error de conexión: " . $conn->connect_error;
        } else {
            // Verificar si email ya existe
            $sql_check = "SELECT id FROM usuarios WHERE LOWER(email) = LOWER(?)";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("s", $email);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();

            if ($result_check->num_rows > 0) {
                $emailErr = "El correo ya está registrado";
            } else {
                // Insertar nuevo usuario
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $sql_insert = "INSERT INTO usuarios (nombre, email, pass_word, rol) VALUES (?, ?, ?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bind_param("ssss", $nombre, $email, $hash, $rol);

                if ($stmt_insert->execute()) {
                    // Registro exitoso, redirigir o mostrar mensaje
                    header("Location: login.php?registro=exito");
                    exit;
                } else {
                    $registerErr = "Error al registrar usuario: " . $stmt_insert->error;
                }

                $stmt_insert->close();
            }

            $stmt_check->close();
            $conn->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Registro - Glowmind</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="d-flex justify-content-center align-items-center vh-100" style="background: linear-gradient(135deg, #6a11cb, #2575fc);">
    <div class="container bg-light bg-opacity-10 p-5 rounded-4 shadow" style="max-width: 420px; backdrop-filter: blur(10px);">
        <h2 class="text-center text-white mb-4">Registro de Usuario</h2>
        <?php if ($registerErr): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($registerErr); ?></div>
        <?php endif; ?>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="mb-3">
                <input type="text" name="nombre" class="form-control bg-transparent text-white border-0 border-bottom border-white" placeholder="Nombre completo" value="<?php echo htmlspecialchars($nombre); ?>" required />
                <span class="text-danger"><?php echo $nameErr; ?></span>
            </div>
            <div class="mb-3">
                <input type="email" name="email" class="form-control bg-transparent text-white border-0 border-bottom border-white" placeholder="Correo" value="<?php echo htmlspecialchars($email); ?>" required />
                <span class="text-danger"><?php echo $emailErr; ?></span>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control bg-transparent text-white border-0 border-bottom border-white" placeholder="Contraseña" required />
                <span class="text-danger"><?php echo $passErr; ?></span>
            </div>
            <button type="submit" class="btn w-100" style="background-color: #ffd700; color: #333;">Registrarse</button>
        </form>
        <p class="text-center text-white mt-3">¿Ya tienes cuenta? <a href="login.php" class="text-warning">Inicia sesión</a></p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
