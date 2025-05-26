<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

switch ($_SESSION['rol']) {
    case 'usuario':
        header("Location: ../usuario/dashboard_usuario.php");
        break;
    case 'admin':
        header("Location: ../psicologo/dashboard.php");
        break;
    default:
        echo "Rol no reconocido";
}
