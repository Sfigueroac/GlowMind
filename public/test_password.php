<?php
require_once '../config/db.php';
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) die("Error: " . $conn->connect_error);
$email = 'laura.psicologo@glowmind.com';
$password = 'psicologo123';
$sql = "SELECT password FROM usuarios WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
echo "Hash en la base de datos: " . $usuario['password'] . "<br>";
echo "Verificación: " . (password_verify($password, $usuario['password']) ? 'Correcta' : 'Incorrecta');
$stmt->close();
$conn->close();
?>