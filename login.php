<?php
session_start();
require 'db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!$email || !$password) {
        $errors[] = "Todos los campos son obligatorios.";
    } else {
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nombre'] = $user['nombre'];
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Correo o contraseña incorrectos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Iniciar Sesión - Cartridge & Disc</title>
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

<section class="flex items-center justify-center min-h-screen pt-8">
  <div class="bg-[#000] p-8 rounded-2xl shadow-lg w-96 border border-[var(--primary-red)]">
    <h2 class="text-3xl font-bold mb-6 text-center text-[var(--light-red)]">Iniciar Sesión</h2>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-700 text-white p-2 rounded mb-4">
            <?php foreach ($errors as $error) echo "<p>$error</p>"; ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <input type="email" name="email" placeholder="Correo electrónico" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required class="w-full px-4 py-2 rounded bg-[#1a1a1a] border border-gray-600 focus:border-[var(--primary-red)] focus:ring-2 focus:ring-[var(--primary-red)]">

      <!-- Contraseña con mostrar/ocultar -->
      <div class="relative">
        <input type="password" id="password" name="password" placeholder="Contraseña" required class="w-full px-4 py-2 rounded bg-[#1a1a1a] border border-gray-600 focus:border-[var(--primary-red)] focus:ring-2 focus:ring-[var(--primary-red)]">
        <button type="button" id="togglePassword" class="absolute right-2 top-2 text-gray-400 hover:text-white">Mostrar</button>
      </div>

      <button type="submit" class="w-full bg-[var(--primary-red)] py-2 rounded font-bold hover:bg-[var(--light-red)] transition">Entrar</button>
    </form>

    <p class="text-center text-sm text-gray-400 mt-4">
      ¿No tienes cuenta? <a href="register.php" class="text-[var(--light-red)] hover:underline">Regístrate</a>
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
