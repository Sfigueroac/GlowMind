<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once '../config/db.php';

// Definir constantes para la conexión
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('DB_NAME')) define('DB_NAME', 'glowmind');

// Inicializar variables de error
$emailErr = $passErr = $loginErr = '';
$email = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validar email
    if (empty($email)) {
        $emailErr = "El correo es requerido";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Formato de correo inválido";
    }

    // Validar contraseña
    if (empty($password)) {
        $passErr = "La contraseña es requerida";
    }

    // Si no hay errores de validación, verificar credenciales
    if (empty($emailErr) && empty($passErr)) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            $loginErr = "Error de conexión: " . $conn->connect_error;
        } else {
            // Consulta insensible a mayúsculas/minúsculas
            $sql = "SELECT id, nombre, email, pass_word, rol FROM usuarios WHERE email = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $usuario = $result->fetch_assoc();
                var_dump(password_verify('psicologo123', '$2y$10$J8z7eX9Y5vZ3Q2W1K6M8.uX9fR4bT2N0P7Q5L3M9K2J1H6G4F8I2'));

                if (password_verify($password, $usuario['pass_word'])) {
                    // Sesión segura
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['usuario_nombre'] = $usuario['nombre'];
                    $_SESSION['usuario_rol'] = $usuario['rol'];
                    $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
                    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

                    // Redirección por rol
                    if ($usuario['rol'] == 'psicologo') {
                        header("Location: ../psicologo/dashboard.php");
                    } else {
                        header("Location: ../usuario/dashboard_usuario.php");
                    }
                    exit;
                } else {
                    $passErr = "Contraseña incorrecta";
                }
            } else {
                $emailErr = "Correo no registrado";
            }

            $stmt->close();
            $conn->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión - Glowmind</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles.css">
</head>
<body class="d-flex justify-content-center align-items-center vh-100" style="background: linear-gradient(135deg, #6a11cb, #2575fc);">
    <div class="container bg-light bg-opacity-10 p-5 rounded-4 shadow" style="max-width: 420px; backdrop-filter: blur(10px);">
        <h2 class="text-center text-white mb-4">Iniciar Sesión</h2>
        <?php if ($loginErr): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($loginErr); ?></div>
        <?php endif; ?>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="mb-3">
                <input type="email" id="email" name="email" class="form-control bg-transparent text-white border-0 border-bottom border-white" placeholder="Correo" value="<?php echo htmlspecialchars($email); ?>" required>
                <span class="text-danger"><?php echo $emailErr; ?></span>
            </div>
            <div class="mb-3">
                <input type="password" id="password" name="password" class="form-control bg-transparent text-white border-0 border-bottom border-white" placeholder="Contraseña" required>
                <span class="text-danger"><?php echo $passErr; ?></span>
            </div>
            <button type="submit" class="btn w-100" style="background-color: #ffd700; color: #333;">Iniciar Sesión</button>
        </form>
        <p class="text-center text-white mt-3">¿No tienes cuenta? <a href="registro.php" class="text-warning">Regístrate</a></p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>