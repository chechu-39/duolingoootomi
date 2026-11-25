<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
   header('Location: login.html');
   exit();
}


require_once __DIR__ . '/conexion.php';


$email = trim($_POST['email'] ?? '');
$pass = $_POST['contraseña'] ?? '';


if ($email === '' || $pass === '') {
   header('Location: login.html?error=credenciales');
   exit();
}


$stmt = $conn->prepare('SELECT id_usuario, nombre, email FROM usuario WHERE email = ? AND contraseña = ?');
$stmt->bind_param('ss', $email, $pass);
$stmt->execute();
$result = $stmt->get_result();


if ($usuario = $result->fetch_assoc()) {
   $recordarme = isset($_POST['recordarme']) && $_POST['recordarme'] === '1';
   $duracionSesion = 30 * 24 * 60 * 60;


   ini_set('session.gc_maxlifetime', (string) $duracionSesion);
   session_set_cookie_params([
      'lifetime' => $recordarme ? $duracionSesion : 0,
      'path' => '/',
      'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
      'httponly' => true,
      'samesite' => 'Lax',
   ]);
   session_start();
   session_regenerate_id(true);
   $_SESSION['usuario'] = $usuario['email'];
   $_SESSION['id_usuario'] = $usuario['id_usuario'];
   $_SESSION['nombre'] = $usuario['nombre'];


   $stmt->close();
   $conn->close();
   header('Location: leccion1.html');
   exit();
}


$stmt->close();
$conn->close();
header('Location: login.html?error=credenciales');
exit();



