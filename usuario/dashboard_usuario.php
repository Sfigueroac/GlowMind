<?php
session_start();

// Validar que el usuario esté autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.php");
    exit;
}

// Opcional: validar IP y User Agent para mayor seguridad
if ($_SESSION['ip'] !== $_SERVER['REMOTE_ADDR'] || $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
    // Se puede cerrar sesión por seguridad
    session_destroy();
    header("Location: ../login/login.php");
    exit;
}

$usuario_nombre = $_SESSION['usuario_nombre'];
$usuario_rol = $_SESSION['usuario_rol'];

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard Usuario - GlowMind</title>
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
            --sidebar-width: 280px; /* Más ancho para mejor alineación con el logo */
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

        /* Animated background - más sutil */
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

        /* Sidebar mejorada */
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

        /* Mobile sidebar fix */
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

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .main-content.expanded {
            margin-left: 80px;
        }

        /* Fixed Navbar Styles */
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

        /* Container and content styling */
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

        .container p {
            color: var(--text-primary);
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        .container p strong {
            color: var(--primary-color);
            font-weight: 600;
        }

        /* Card styling mejorado - animaciones más suaves */
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

        .card h2 {
            color: var(--text-primary);
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
            padding-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card h2 i {
            color: var(--primary-color);
            font-size: 1.5rem;
        }

        .info-grid {
            display: grid;
            gap: 1.5rem;
            margin-top: 1.5rem;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .info-item {
            background: rgba(255, 255, 255, 0.55);
            backdrop-filter: blur(16px) saturate(180%);
            border-radius: 24px;
            padding: 1.6rem 1.3rem 1.3rem 1.3rem;
            box-shadow: 0 8px 32px rgba(45,137,239,0.10), 0 2px 8px rgba(0,0,0,0.05);
            border: 1.5px solid rgba(45,137,239,0.13);
            display: flex;
            align-items: flex-start;
            gap: 1.2rem;
            min-width: 0;
        }

        .info-item:hover {
            background: rgba(255,255,255,0.90);
            box-shadow: 0 14px 40px rgba(45,137,239,0.16), 0 3px 12px rgba(0,0,0,0.08);
        }

        .info-icon {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.7rem;
            background: linear-gradient(135deg, #2d89ef, #6dd5ed);
            color: #fff;
            box-shadow: 0 4px 16px rgba(45,137,239,0.13);
            flex-shrink: 0;
            border: 2.5px solid rgba(255,255,255,0.7);
            margin-bottom: 0.1rem;
            transition: transform 0.12s;
        }
        .info-icon-nombre {
            background: linear-gradient(135deg, #2d89ef, #6dd5ed);
        }
        .info-icon-rol {
            background: linear-gradient(135deg, #f7971e, #ffd200);
        }
        .info-icon-id {
            background: linear-gradient(135deg, #43cea2, #185a9d);
        }
        .info-item:hover .info-icon {
            transform: scale(1.08) rotate(-2deg);
        }
        .info-label {
            font-weight: 700;
            color: #2d89ef;
            font-size: 1.01rem;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            margin-bottom: 0.22rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .info-value {
            font-size: 1.22rem;
            color: #222b45;
            font-weight: 500;
            word-break: break-word;
        }

        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
                gap: 1.1rem;
            }
            .info-item {
                padding: 1.1rem 1rem 1rem 1rem;
            }
            .info-icon {
                width: 42px;
                height: 42px;
                font-size: 1.18rem;
            }
        }

        /* Estilos para la página de inicio */
        .welcome-card {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(20px);
            border-radius: 25px !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06) !important;
            padding: 3rem !important;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .welcome-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 25px 25px 0 0;
        }

        .welcome-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            animation: gentlePulse 6s ease-in-out infinite;
        }

        .welcome-icon i {
            font-size: 2.5rem;
            color: white;
        }

        @keyframes gentlePulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 3rem;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.7);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .feature-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            background: rgba(255, 255, 255, 0.85);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .feature-icon i {
            font-size: 1.5rem;
            color: white;
        }

        .feature-card h3 {
            color: var(--text-primary);
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .feature-card p {
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.5;
        }

        /* Mobile toggle button */
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

        /* Tooltip styles */
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

        /* Responsive */
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

            .features-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }

        @media (min-width: 769px) {
            .mobile-toggle {
                display: none;
            }
        }

        /* Page content transitions */
        .page-content {
            opacity: 1;
            transform: translateY(0);
            transition: all 0.3s ease;
        }

        .page-content.hidden {
            opacity: 0;
            transform: translateY(20px);
            pointer-events: none;
            position: absolute;
            width: 100%;
        }

        /* Overlay para cerrar sidebar en mobile */
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
                <a href="/glowmind/public/index.php" class="nav-link tooltip-custom" data-tooltip="Inicio" onclick="showPage('inicio')">
                    <i class="nav-icon fas fa-home"></i>
                    <span class="nav-text">Inicioㅤ</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="dashboard_usuario.php" class="nav-link active tooltip-custom" data-tooltip="Perfil" onclick="showPage('perfil')">
                    <i class="nav-icon fas fa-user"></i>
                    <span class="nav-text">Perfilㅤ</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="#" class="nav-link tooltip-custom" data-tooltip="Cuestionarios" onclick="showPage('cuestionarios')">
                    <i class="nav-icon fas fa-clipboard-list"></i>
                    <span class="nav-text">Cuestionariosㅤ</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="../public/comentarios.php" class="nav-link tooltip-custom" data-tooltip="Comentarios" onclick="showPage('comentarios')">
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
                    <span class="navbar-text me-3">Hola, <?php echo htmlspecialchars($usuario_nombre); ?></span>
                    <a href="../public/login.php" class="btn btn-outline-warning">Cerrar sesión</a>
                </div>
            </div>
        </nav>

        <!-- Contenido -->
        <main class="container mt-5">
            <div id="page-perfil" class="page-content">
                <h1>Bienvenido a tú perfil</h1>
                <p>Tu rol es: <strong><?php echo htmlspecialchars($usuario_rol); ?></strong></p>

                <!-- Información de Usuario -->
                <div class="card mt-4">
                    <h2><i class="fas fa-user"></i>Información de Usuario</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-icon info-icon-nombre"><i class="fas fa-user"></i></div>
                            <div>
                                <div class="info-label">Nombre</div>
                                <div class="info-value"><?php echo htmlspecialchars($usuario_nombre); ?></div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon info-icon-rol"><i class="fas fa-user-tag"></i></div>
                            <div>
                                <div class="info-label">Rol</div>
                                <div class="info-value"><?php echo htmlspecialchars($usuario_rol); ?></div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon info-icon-id"><i class="fas fa-id-badge"></i></div>
                            <div>
                                <div class="info-label">ID</div>
                                <div class="info-value"><?php echo htmlspecialchars($usuario_id); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
               
    <script>
        let sidebarCollapsed = false;
        let currentPage = 'perfil';

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

        function showPage(pageName) {
            // Hide all pages
            const pages = document.querySelectorAll('.page-content');
            pages.forEach(page => {
                page.classList.add('hidden');
            });

            // Show selected page after a brief delay for smooth transition
            setTimeout(() => {
                const targetPage = document.getElementById(`page-${pageName}`);
                if (targetPage) {
                    targetPage.classList.remove('hidden');
                }
            }, 150);

            // Update active nav link
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.classList.remove('active');
            });
            event.target.closest('.nav-link').classList.add('active');

            // Close sidebar on mobile after selection
            if (window.innerWidth <= 768) {
                closeSidebar();
            }

            currentPage = pageName;
        }

        // Handle window resize
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
                // Cerrar solo si el overlay está visible y el click es fuera de la barra, el toggle y el overlay
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

        document.addEventListener('DOMContentLoaded', function() {
            showPage(currentPage);
        });
    </script>
</body>
</html>