<?php
$host = 'localhost';
$usuario = 'root';
$contrasena = ''; // en XAMPP normalmente se deja vacío en Mac
$base_de_datos = 'glowmind';

$conn = new mysqli($host, $usuario, $contrasena, $base_de_datos);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
