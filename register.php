<?php
// Mostrar errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'db.php';

$errors = [];
$nombre = $email = $password = $fecha_nacimiento = $celular = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
    $celular = trim($_POST['celular'] ?? '');

    // Validaciones
    if (!$nombre || !$email || !$password || !$fecha_nacimiento || !$celular) {
        $errors[] = "Todos los campos son obligatorios.";
    }

    // Validar nombre (solo letras y espacios)
    if ($nombre && !preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/", $nombre)) {
        $errors[] = "Por favor ingresa un nombre válido. Solo letras y espacios.";
    }

    // Validar email
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Correo electrónico inválido. Usa un formato válido (ejemplo: usuario@dominio.com).";
    }

    // Validar contraseña
    if ($password && strlen($password) < 8) {
        $errors[] = "La contraseña debe tener al menos 8 caracteres.";
    }

    // Validar fecha de nacimiento y mayor de 18 años
    if ($fecha_nacimiento) {
        $fecha_ts = strtotime($fecha_nacimiento);
        if (!$fecha_ts) {
            $errors[] = "Fecha de nacimiento inválida.";
        } else {
            $edad = (int)((time() - $fecha_ts) / (365.25*24*60*60));
            if ($edad < 18) {
                $errors[] = "Lo siento, solo mayores de 18 años pueden registrarse.";
            }
        }
    }

    // Validar celular (Bolivia, solo 8 números)
    if ($celular && !preg_match("/^\d{8}$/", $celular)) {
        $errors[] = "Número de celular inválido. Debe tener exactamente 8 dígitos (solo números).";
    }

    // Verificar si email ya existe
    if ($email && empty($errors)) {
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "Este correo ya está siendo utilizado. Intenta con otro.";
        }
    }

    // Registrar usuario si no hay errores
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (nombre, email, password, fecha_nacimiento, celular) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $email, $hashed_password, $fecha_nacimiento, $celular]);

        $_SESSION['user_id'] = $db->lastInsertId();
        $_SESSION['nombre'] = $nombre;

        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registro - Cartridge & Disc</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
:root { --primary-red: #d8403f; --dark-red: #b0302f; --light-red: #e8605f; --bg-dark: #1a1a1a; }
</style>
</head>
<body class="bg-[var(--bg-dark)] text-white font-sans">

<!-- Logo -->
<header class="flex justify-center pt-4">
  <a href="index.php" class="hover:scale-110 transition">
    <img src="img/logo_normal.webp" alt="Cartridge & Disc Logo" class="h-12">
  </a>
</header>

<section class="flex items-center justify-center min-h-screen">
  <div class="bg-[#000] p-8 rounded-2xl shadow-lg w-96 border border-[var(--primary-red)]">
    <h2 class="text-3xl font-bold mb-6 text-center text-[var(--light-red)]">Crear Cuenta</h2>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-700 text-white p-2 rounded mb-4">
            <?php foreach ($errors as $error) echo "<p>$error</p>"; ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <input type="text" name="nombre" placeholder="Nombre completo" value="<?= htmlspecialchars($nombre) ?>" required class="w-full px-4 py-2 rounded bg-[#1a1a1a] border border-gray-600 focus:border-[var(--primary-red)] focus:ring-2 focus:ring-[var(--primary-red)]">
      <input type="email" name="email" placeholder="Correo electrónico" value="<?= htmlspecialchars($email) ?>" required class="w-full px-4 py-2 rounded bg-[#1a1a1a] border border-gray-600 focus:border-[var(--primary-red)] focus:ring-2 focus:ring-[var(--primary-red)]">

      <!-- Contraseña con mostrar/ocultar -->
      <div class="relative">
        <input type="password" id="password" name="password" placeholder="Contraseña" required class="w-full px-4 py-2 rounded bg-[#1a1a1a] border border-gray-600 focus:border-[var(--primary-red)] focus:ring-2 focus:ring-[var(--primary-red)]">
        <button type="button" id="togglePassword" class="absolute right-2 top-2 text-gray-400 hover:text-white">Mostrar</button>
      </div>

      <input type="date" name="fecha_nacimiento" value="<?= htmlspecialchars($fecha_nacimiento) ?>" class="w-full px-4 py-2 rounded bg-[#1a1a1a] border border-gray-600 focus:border-[var(--primary-red)] focus:ring-2 focus:ring-[var(--primary-red)]">
      <input type="text" name="celular" placeholder="Celular" value="<?= htmlspecialchars($celular) ?>" class="w-full px-4 py-2 rounded bg-[#1a1a1a] border border-gray-600 focus:border-[var(--primary-red)] focus:ring-2 focus:ring-[var(--primary-red)]">

      <button type="submit" class="w-full bg-[var(--primary-red)] py-2 rounded font-bold hover:bg-[var(--light-red)] transition">Registrarse</button>
    </form>

    <p class="text-center text-sm text-gray-400 mt-4">
      ¿Ya tienes cuenta? <a href="login.php" class="text-[var(--light-red)] hover:underline">Inicia Sesión</a>
    </p>
  </div>
</section>

<script>
const togglePassword = document.querySelector('#togglePassword');
const password = document.querySelector('#password');

togglePassword.addEventListener('click', () => {
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    togglePassword.textContent = type === 'password' ? 'Mostrar' : 'Ocultar';
});
</script>

</body>
</html>
