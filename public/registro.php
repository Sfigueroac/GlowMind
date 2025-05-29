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
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="keywords" content="psicología, salud mental, igualdad género, GlowMind, registro">
    <meta name="description" content="Únete a GlowMind - Tu espacio seguro de apoyo psicológico inclusivo">
    <meta name="author" content="GlowMind">

    <title>Registro - GlowMind</title>

    <!-- Bootstrap core css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Fonts style -->
    <link href="https://fonts.googleapis.com/css?family=Dosis:400,500|Poppins:400,700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #2d89ef;
            --secondary-color: #00d4aa;
            --accent-color: #17a2b8;
            --text-primary: #333;
            --text-secondary: #666;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg,rgba(168,232,247,255) 0%,rgba(168,232,247,255) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background elements */
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: float 20s ease-in-out infinite;
            z-index: 1;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .register-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 450px;
            margin: 20px;
        }

        .register-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            padding: 3rem 2.5rem;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .register-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 35px 70px rgba(0, 0, 0, 0.2);
        }

        .register-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
            border-radius: 25px 25px 0 0;
        }

        .brand-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            animation: pulse 2s ease-in-out infinite;
        }

        .brand-logo i {
            font-size: 2rem;
            color: white;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .brand-title {
            font-family: 'Dosis', sans-serif;
            font-size: 2.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-subtitle {
            color: var(--text-secondary);
            font-size: 0.95rem;
            margin-bottom: 0;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .input-group {
            position: relative;
            margin-bottom: 0.5rem;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            z-index: 3;
            transition: color 0.3s ease;
        }

        .form-control {
            background: rgba(248, 249, 250, 0.8);
            border: 2px solid rgba(0, 0, 0, 0.08);
            border-radius: 15px;
            padding: 15px 15px 15px 45px;
            font-size: 1rem;
            transition: all 0.3s ease;
            color: var(--text-primary);
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.95);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(45, 137, 239, 0.1);
            outline: none;
        }

        .form-control:focus + .input-icon {
            color: var(--primary-color);
        }

        .form-control::placeholder {
            color: var(--text-secondary);
            opacity: 0.7;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .error-message i {
            font-size: 0.8rem;
        }

        .btn-register {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 15px;
            padding: 15px;
            font-weight: 600;
            font-size: 1.1rem;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-register::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-register:hover::before {
            left: 100%;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(45, 137, 239, 0.3);
        }

        .btn-register:active {
            transform: translateY(0);
        }

        .divider {
            text-align: center;
            margin: 2rem 0;
            position: relative;
            color: var(--text-secondary);
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: rgba(0, 0, 0, 0.1);
        }

        .divider span {
            background: rgba(255, 255, 255, 0.95);
            padding: 0 1rem;
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
        }

        .login-link p {
            color: var(--text-secondary);
            margin-bottom: 0;
        }

        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }

        .alert {
            border-radius: 15px;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            color: #155724;
            border-left: 4px solid #28a745;
        }

        /* Loading animation */
        .btn-register.loading {
            pointer-events: none;
        }

        .btn-register.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
        }

        @keyframes spin {
            0% { transform: translateY(-50%) rotate(0deg); }
            100% { transform: translateY(-50%) rotate(360deg); }
        }

        /* Password strength indicator */
        .password-strength {
            height: 4px;
            margin-top: 0.5rem;
            border-radius: 2px;
            background: #e9ecef;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .strength-weak { width: 33%; background: #dc3545; }
        .strength-medium { width: 66%; background: #ffc107; }
        .strength-strong { width: 100%; background: #28a745; }

        /* Responsive design */
        @media (max-width: 576px) {
            .register-card {
                margin: 1rem;
                padding: 2rem 1.5rem;
            }
            
            .brand-title {
                font-size: 2rem;
            }
            
            .brand-logo {
                width: 60px;
                height: 60px;
            }
            
            .brand-logo i {
                font-size: 1.5rem;
            }
        }

        /* Back to home link */
        .back-home {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 10;
        }

        .back-home a {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: white;
            text-decoration: none;
            background: rgba(255, 255, 255, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .back-home a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(-5px);
        }

        /* Welcome message animation */
        .welcome-text {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 600;
            text-align: center;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="back-home">
        <a href="index.php">
            <i class="fas fa-arrow-left"></i>
            <span>Volver al Inicio</span>
        </a>
    </div>

    <div class="register-container">
        <div class="register-card">
            <div class="brand-header">
                <div class="brand-logo">
                    <i class="fas fa-brain"></i>
                </div>
                <h1 class="brand-title">GlowMind</h1>
                <p class="brand-subtitle">Únete a nuestro espacio seguro</p>
            </div>
            <p class="welcome-text">Crea tu cuenta!</p>

            <?php if ($registerErr): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php echo htmlspecialchars($registerErr); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="registerForm">
                <div class="form-group">
                    <div class="input-group">
                        <input type="text" 
                               id="nombre" 
                               name="nombre" 
                               class="form-control" 
                               placeholder="Nombre completo" 
                               value="<?php echo htmlspecialchars($nombre); ?>" 
                               required>
                        <i class="input-icon fas fa-user"></i>
                    </div>
                    <?php if ($nameErr): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo $nameErr; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="form-control" 
                               placeholder="Correo electrónico" 
                               value="<?php echo htmlspecialchars($email); ?>" 
                               required>
                        <i class="input-icon fas fa-envelope"></i>
                    </div>
                    <?php if ($emailErr): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo $emailErr; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-control" 
                               placeholder="Contraseña (mínimo 6 caracteres)" 
                               required>
                        <i class="input-icon fas fa-lock"></i>
                    </div>
                    <div class="password-strength">
                        <div class="password-strength-bar" id="strengthBar"></div>
                    </div>
                    <?php if ($passErr): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo $passErr; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn-register" id="submitBtn">
                    <span>Crear Cuenta</span>
                </button>
            </form>

            <div class="login-link">
                <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add loading animation on form submit
        document.getElementById('registerForm').addEventListener('submit', function() {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.classList.add('loading');
            submitBtn.querySelector('span').textContent = 'Creando cuenta...';
        });

        // Add focus animations
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

        // Password strength indicator
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('strengthBar');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;

            // Check password strength
            if (password.length >= 6) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;

            // Update strength bar
            strengthBar.className = 'password-strength-bar';
            if (strength === 1) {
                strengthBar.classList.add('strength-weak');
            } else if (strength === 2 || strength === 3) {
                strengthBar.classList.add('strength-medium');
            } else if (strength >= 4) {
                strengthBar.classList.add('strength-strong');
            }
        });

        // Form validation enhancements
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const nombre = document.getElementById('nombre').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;

            if (nombre.length < 2) {
                e.preventDefault();
                alert('El nombre debe tener al menos 2 caracteres');
                return false;
            }

            if (password.length < 6) {
                e.preventDefault();
                alert('La contraseña debe tener al menos 6 caracteres');
                return false;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Por favor ingresa un correo electrónico válido');
                return false;
            }
        });

        // Auto-focus first input
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('nombre').focus();
        });
    </script>
</body>
</html>