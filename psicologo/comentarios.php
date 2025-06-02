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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Comentarios - GlowMind</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Dosis:400,500|Poppins:400,700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2d89ef;
            --secondary-color: #00d4aa;
            --accent-color: #17a2b8;
            --text-primary: #333;
            --text-secondary: #666;
            --sidebar-width: 280px;
            --header-height: 70px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, rgba(168,232,247,255) 0%, rgba(168,232,247,255) 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: float 30s ease-in-out infinite;
            z-index: -1;
            pointer-events: none;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(90deg); }
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(25px);
            border-right: 1px solid rgba(255, 255, 255, 0.3);
            z-index: 1000;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.08);
            transform: translateX(0);
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar.collapsed .nav-text,
        .sidebar.collapsed .sidebar-title {
            opacity: 0;
            visibility: hidden;
        }

        .sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 1rem;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: var(--sidebar-width);
                box-shadow: none;
                transition: none;
                visibility: hidden;
                pointer-events: none;
            }

            .sidebar.show {
                transform: translateX(0);
                box-shadow: 0 0 50px rgba(0, 0, 0, 0.3);
                visibility: visible;
                pointer-events: auto;
                transition: none;
            }

            .main-content {
                margin-left: 0 !important;
                transition: none;
            }

            .main-content.sidebar-open {
                margin-left: 0 !important;
            }
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 1rem;
            min-height: var(--header-height);
        }

        .sidebar-logo {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            animation: pulse 4s ease-in-out infinite;
        }

        .sidebar-logo i {
            font-size: 1.1rem;
            color: white;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }

        .sidebar-title {
            font-family: 'Dosis', sans-serif;
            font-size: 1.8rem;
            font-weight: 600;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            white-space: nowrap;
            opacity: 1;
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed .sidebar-title {
            opacity: 0;
            visibility: hidden;
        }

        .sidebar-toggle {
            position: absolute;
            top: 20px;
            right: -15px;
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 50%;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(45, 137, 239, 0.3);
        }

        .sidebar-toggle:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(45, 137, 239, 0.4);
        }

        @media (max-width: 768px) {
            .sidebar-toggle {
                display: none;
            }
        }

        .sidebar-nav {
            padding: 1rem 0;
            height: calc(100vh - var(--header-height));
            overflow-y: auto;
        }

        .nav-item {
            margin: 0.5rem 1rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            color: var(--text-primary);
            text-decoration: none;
            border-radius: 15px;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            transition: left 0.3s ease;
            z-index: -1;
        }

        .nav-link:hover::before,
        .nav-link.active::before {
            left: 0;
        }

        .nav-link:hover,
        .nav-link.active {
            color: white;
            transform: translateX(3px);
            box-shadow: 0 3px 10px rgba(45, 137, 239, 0.2);
        }

        .nav-icon {
            font-size: 1.2rem;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .nav-text {
            font-weight: 500;
            white-space: nowrap;
            opacity: 1;
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed .nav-text {
            opacity: 0;
            visibility: hidden;
        }

        .sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 1rem;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .main-content.expanded {
            margin-left: 80px;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.05);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 999;
            width: 100%;
        }

        .navbar-brand {
            font-family: 'Dosis', sans-serif;
            font-size: 1.8rem;
            font-weight: 600;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .navbar-text {
            color: var(--text-primary) !important;
            font-weight: 500;
        }

        .btn-outline-warning {
            background: #e74c3c;
            border: none;
            border-radius: 12px;
            padding: 0.55rem 1.2rem;
            color: #fff !important;
            font-weight: 600;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(231, 76, 60, 0.10);
            transition: none;
            position: relative;
            overflow: hidden;
        }

        .btn-outline-warning:hover, .btn-outline-warning:focus {
            background: #c0392b;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.16);
            transform: none;
            border: none;
        }

        .container {
            padding: 2rem;
        }

        .container h1 {
            font-size: 2.5rem;
            font-weight: 600;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }

        .container h2 {
            font-size: 1.8rem;
            color: var(--text-primary);
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .container h2 i {
            color: var(--primary-color);
            font-size: 1.5rem;
        }

        .card {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(20px);
            border-radius: 25px !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06) !important;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
            padding: 2rem !important;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 25px 25px 0 0;
        }

        .card::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(45, 137, 239, 0.03) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.4s ease;
            pointer-events: none;
        }

        .card:hover {
            transform: translateY(-5px) scale(1.01);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1) !important;
        }

        .card:hover::after {
            opacity: 1;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }

        .form-select, .form-control {
            border: 2px solid rgba(45, 137, 239, 0.1);
            border-radius: 15px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-select:focus, .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(45, 137, 239, 0.15);
            background: rgba(255, 255, 255, 1);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 15px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(45, 137, 239, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(45, 137, 239, 0.3);
            background: linear-gradient(135deg, #1a6bb8, #00b894);
        }

        .text-danger {
            color: #e74c3c !important;
            font-size: 0.9rem;
            margin-top: 0.25rem;
            font-weight: 500;
        }

        .alert {
            border: none;
            border-radius: 15px;
            padding: 1rem 1.5rem;
            font-weight: 500;
            margin-bottom: 0.1rem;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(0, 212, 170, 0.1), rgba(0, 212, 170, 0.05));
            color: #00a085;
            border-left: 4px solid var(--secondary-color);
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(231, 76, 60, 0.1), rgba(231, 76, 60, 0.05));
            color: #c0392b;
            border-left: 4px solid #e74c3c;
        }

        .mobile-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(45, 137, 239, 0.3);
            transition: all 0.3s ease;
        }

        .mobile-toggle:hover {
            transform: scale(1.05);
        }

        .tooltip-custom {
            position: relative;
        }

        .tooltip-custom::after {
            content: attr(data-tooltip);
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            margin-left: 10px;
            padding: 0.5rem 1rem;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            border-radius: 8px;
            white-space: nowrap;
            font-size: 0.8rem;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
            z-index: 1000;
        }

        .sidebar.collapsed .tooltip-custom:hover::after {
            opacity: 1;
        }

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        @media (max-width: 768px) {
            .mobile-toggle {
                display: block;
            }

            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .main-content.expanded {
                margin-left: 0;
            }

            .container {
                padding: 1rem;
            }

            .navbar {
                padding: 1rem;
                margin-left: 0 !important;
            }
        }

        @media (min-width: 769px) {
            .mobile-toggle {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Overlay para cerrar sidebar en mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <!-- Mobile toggle button -->
    <button class="mobile-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <button class="sidebar-toggle" onclick="toggleSidebar()">
            <i class="fas fa-chevron-left" id="toggleIcon"></i>
        </button>
        
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <i class="fas fa-brain"></i>
            </div>
            <h2 class="sidebar-title">GlowMind</h2>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-item">
                <a href="/glowmind/public/index.php" class="nav-link tooltip-custom" data-tooltip="Inicio">
                    <i class="nav-icon fas fa-home"></i>
                    <span class="nav-text">Inicioㅤ</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="#" class="nav-link tooltip-custom" data-tooltip="Perfil">
                    <i class="nav-icon fas fa-user"></i>
                    <span class="nav-text">Perfilㅤ</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="dashboard.php" class="nav-link tooltip-custom" data-tooltip="Pacientes">
                    <i class="nav-icon fas fa-users"></i>
                    <span class="nav-text">Pacientesㅤ</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="#" class="nav-link active tooltip-custom" data-tooltip="Comentarios">
                    <i class="nav-icon fas fa-comments"></i>
                    <span class="nav-text">Comentariosㅤ</span>
                </a>
            </div>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">GlowMind</a>
                <div class="d-flex">
                    <span class="navbar-text me-3">Hola, <?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Psicólogo'); ?></span>
                    <a href="../public/login.php" class="btn btn-outline-warning">Cerrar sesión</a>
                </div>
            </div>
        </nav>

        <!-- Contenido -->
        <main class="container mt-5">
            <h1>Comentarios de usuarios</h1>

            <?php if ($result->num_rows == 0): ?>
                <div class="alert alert-info shadow-sm" style="font-size:1.15rem; background:rgba(45,137,239,0.08); border-left:5px solid var(--primary-color); color:var(--primary-color); border-radius:18px; margin-bottom:0.1rem !important;">
                    <i class="fas fa-info-circle me-2"></i>
                    No hay comentarios todavía.
                </div>
            <?php else: ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="card mb-4">
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
                                    <button type="submit" class="btn btn-primary">Responder</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </main>
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
               
    <script>
        let sidebarCollapsed = false;

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleIcon = document.getElementById('toggleIcon');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (window.innerWidth <= 768) {
                const isOpen = sidebar.classList.toggle('show');
                if (sidebar.classList.contains('show')) {
                    overlay.classList.add('show');
                    document.body.style.overflow = 'hidden';
                    sidebar.style.visibility = 'visible';
                    sidebar.style.pointerEvents = 'auto';
                    mainContent.classList.add('sidebar-open');
                } else {
                    overlay.classList.remove('show');
                    document.body.style.overflow = '';
                    sidebar.style.visibility = 'hidden';
                    sidebar.style.pointerEvents = 'none';
                    mainContent.classList.remove('sidebar-open');
                }
            } else {
                sidebarCollapsed = !sidebarCollapsed;
                if (sidebarCollapsed) {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('expanded');
                    toggleIcon.className = 'fas fa-chevron-right';
                } else {
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.remove('expanded');
                    toggleIcon.className = 'fas fa-chevron-left';
                }
            }
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const mainContent = document.getElementById('mainContent');
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
            sidebar.style.visibility = 'hidden';
            sidebar.style.pointerEvents = 'none';
            mainContent.classList.remove('sidebar-open');
        }

        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const overlay = document.getElementById('sidebarOverlay');
            if (window.innerWidth > 768) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
                if (sidebarCollapsed) {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('expanded');
                }
            } else {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('expanded');
                sidebarCollapsed = false;
                document.getElementById('toggleIcon').className = 'fas fa-chevron-left';
            }
        });

        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 768) {
                const sidebar = document.getElementById('sidebar');
                const mobileToggle = document.querySelector('.mobile-toggle');
                const overlay = document.getElementById('sidebarOverlay');
                if (
                    overlay.classList.contains('show') &&
                    !sidebar.contains(event.target) &&
                    !mobileToggle.contains(event.target)
                ) {
                    closeSidebar();
                }
            }
        });

        window.addEventListener('load', function() {
            document.body.style.opacity = '0';
            document.body.style.transition = 'opacity 0.5s ease';
            
            setTimeout(() => {
                document.body.style.opacity = '1';
            }, 100);
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>