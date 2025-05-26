<?php
session_start();

// Conexión a la base de datos
$host = 'localhost';
$db = 'glowmind';  // Nombre de la base de datos
$user = 'root';  // Usuario de la base de datos
$pass = '';  // Contraseña del usuario

$conn = new mysqli($host, $user, $pass, $db);

// Comprobar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Procesar formulario de inicio de sesión
$emailErr = $passErr = $loginErr = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Comprobar si el correo existe
    $stmt = $conn->prepare("SELECT id, nombre, password FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $nombre, $hashed_password);
        $stmt->fetch();

        // Verificar contraseña
        if (password_verify($password, $hashed_password)) {
            // Iniciar sesión correctamente
            $_SESSION['usuario_id'] = $id;
            $_SESSION['usuario_nombre'] = $nombre;
            echo "<div id='notification' class='show'>¡Inicio de sesión exitoso! Redirigiendo...</div>";
            echo "<script>
                    setTimeout(function() {
                        document.getElementById('notification').style.opacity = '0';
                    }, 3000); 
                    setTimeout(function() {
                        window.location.href = 'dashboard.php';
                    }, 4000);
                  </script>";
            exit;
        } else {
            $passErr = "Contraseña incorrecta.";
        }
    } else {
        $emailErr = "El correo no está registrado.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            background-size: cover;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
            width: 420px;
            text-align: center;
            backdrop-filter: blur(10px);
        }
        h2 {
            color: white;
            font-size: 28px;
            margin-bottom: 20px;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 20px 0;
            background: transparent;
            border: none;
            border-bottom: 2px solid #fff;
            color: white;
            font-size: 18px;
        }
        input[type="email"]:focus, input[type="password"]:focus {
            outline: none;
            border-bottom: 2px solid #ffd700;
        }
        input[type="submit"] {
            width: 100%;
            padding: 15px;
            background-color: #ffd700;
            color: #333;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #ffcc00;
        }
        .error {
            color: #ffcccc;
            font-size: 14px;
        }
        #notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            opacity: 1;
            transition: opacity 1s ease;
        }
        .show {
            opacity: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Iniciar Sesión</h2>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="email" id="email" name="email" placeholder="Correo" required>
            <span class="error"><?php echo $emailErr; ?></span>
            <input type="password" id="password" name="password" placeholder="Contraseña" required>
            <span class="error"><?php echo $passErr; ?></span>
            <input type="submit" value="Iniciar Sesión">
            <span class="error"><?php echo $loginErr; ?></span>
        </form>
    </div>
</body>
</html>
