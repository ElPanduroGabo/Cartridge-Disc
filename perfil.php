<?php
session_start();
require 'db.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success = "";

// Obtener información actual del usuario
$stmt = $db->prepare("SELECT nombre, email, fecha_nacimiento, celular, password FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Actualizar nombre y celular
if (isset($_POST['update_info'])) {
    $new_name = trim($_POST['nombre']);
    $new_celular = trim($_POST['celular']);

    if (!$new_name || !preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/", $new_name)) {
        $errors[] = "El nombre debe ser válido.";
    }

    if (!$new_celular || !preg_match("/^\d{8}$/", $new_celular)) {
        $errors[] = "El celular debe tener 8 dígitos.";
    }

    if (empty($errors)) {
        $stmt = $db->prepare("UPDATE users SET nombre = ?, celular = ? WHERE id = ?");
        $stmt->execute([$new_name, $new_celular, $user_id]);
        $success = "Datos actualizados correctamente.";
        $_SESSION['nombre'] = $new_name;
        $user['nombre'] = $new_name;
        $user['celular'] = $new_celular;
    }
}

// Cambiar contraseña
if (isset($_POST['update_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];

    if (!password_verify($current, $user['password'])) {
        $errors[] = "Contraseña actual incorrecta.";
    } elseif (strlen($new) < 8) {
        $errors[] = "La nueva contraseña debe tener al menos 8 caracteres.";
    } else {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed, $user_id]);
        $success = "Contraseña actualizada correctamente.";
        $user['password'] = $hashed;
    }
}

// Eliminar cuenta
if (isset($_POST['delete_account'])) {
    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    session_destroy();
    header("Location: index.php");
    exit;
}

// Obtener transacciones de compras
$stmt = $db->prepare("SELECT id, total, payment_method, fecha_hora FROM orders WHERE user_id = ? ORDER BY fecha_hora DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener transacciones de ventas
$stmt = $db->prepare("SELECT id, juego, consola, estado, precio_sugerido, fecha_hora FROM ventas WHERE user_id = ? ORDER BY fecha_hora DESC");
$stmt->execute([$user_id]);
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Perfil - Cartridge & Disc</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
:root {
    --primary-red: #d8403f;
    --bg-dark: #1a1a1a;
    --light-red: #e8605f;
}
</style>
</head>
<body class="bg-[var(--bg-dark)] text-white font-sans">

<header class="flex justify-center pt-4">
  <a href="index.php" class="hover:scale-110 transition">
    <img src="img/logo_normal.webp" alt="Cartridge & Disc Logo" class="h-12">
  </a>
</header>

<section class="flex items-center justify-center py-8">
  <div class="bg-[#000] p-8 rounded-2xl shadow-lg w-full max-w-4xl border border-[var(--primary-red)] space-y-6">

    <h2 class="text-3xl font-bold mb-6 text-center text-[var(--light-red)]">Perfil</h2>

    <?php if(!empty($errors)): ?>
      <div class="bg-red-700 text-white p-2 rounded">
        <?php foreach($errors as $error) echo "<p>$error</p>"; ?>
      </div>
    <?php endif; ?>

    <?php if($success): ?>
      <div class="bg-green-700 text-white p-2 rounded">
        <p><?= $success ?></p>
      </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Información del usuario -->
      <div>
        <h3 class="font-semibold mb-2">Información del usuario</h3>
        <p><strong>Nombre:</strong> <?= htmlspecialchars($user['nombre']) ?></p>
        <p><strong>Correo:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Fecha de nacimiento:</strong> <?= htmlspecialchars($user['fecha_nacimiento']) ?></p>
        <p><strong>Celular:</strong> <?= htmlspecialchars($user['celular']) ?></p>
      </div>

      <!-- Actualizar datos -->
      <div>
        <form method="POST" class="space-y-4">
          <h3 class="font-semibold">Actualizar datos</h3>
          <input type="text" name="nombre" value="<?= htmlspecialchars($user['nombre']) ?>" placeholder="Nombre completo" class="w-full px-4 py-2 rounded bg-[#1a1a1a] border border-gray-600 focus:border-[var(--primary-red)] focus:ring-2 focus:ring-[var(--primary-red)]" required>
          <input type="text" name="celular" value="<?= htmlspecialchars($user['celular']) ?>" placeholder="Celular" class="w-full px-4 py-2 rounded bg-[#1a1a1a] border border-gray-600 focus:border-[var(--primary-red)] focus:ring-2 focus:ring-[var(--primary-red)]" required>
          <button type="submit" name="update_info" class="w-full bg-[var(--primary-red)] py-2 rounded font-bold hover:bg-[var(--light-red)] transition">Actualizar Datos</button>
        </form>
      </div>
    </div>

    <!-- Cambiar contraseña -->
    <form method="POST" class="space-y-4 mt-4">
      <h3 class="font-semibold">Cambiar contraseña</h3>
      <div class="relative">
        <input type="password" id="current_password" name="current_password" placeholder="Contraseña actual" class="w-full px-4 py-2 rounded bg-[#1a1a1a] border border-gray-600 focus:border-[var(--primary-red)] focus:ring-2 focus:ring-[var(--primary-red)]" required>
        <button type="button" id="toggleCurrent" class="absolute right-2 top-2 text-gray-400 hover:text-white">Mostrar</button>
      </div>
      <div class="relative">
        <input type="password" id="new_password" name="new_password" placeholder="Nueva contraseña" class="w-full px-4 py-2 rounded bg-[#1a1a1a] border border-gray-600 focus:border-[var(--primary-red)] focus:ring-2 focus:ring-[var(--primary-red)]" required>
        <button type="button" id="toggleNew" class="absolute right-2 top-2 text-gray-400 hover:text-white">Mostrar</button>
      </div>
      <button type="submit" name="update_password" class="w-full bg-[var(--primary-red)] py-2 rounded font-bold hover:bg-[var(--light-red)] transition">Cambiar Contraseña</button>
    </form>

    <!-- Eliminar cuenta -->
    <form method="POST" class="mt-4">
      <button type="submit" name="delete_account" onclick="return confirm('¿Estás seguro de eliminar tu cuenta?');" class="w-full bg-red-700 py-2 rounded font-bold hover:bg-red-600 transition">Eliminar Cuenta</button>
    </form>

    <!-- Compras realizadas -->
    <h3 class="font-semibold mt-6 mb-2 text-[var(--light-red)]">Compras realizadas</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-h-64 overflow-y-auto">
      <?php if($orders): ?>
        <?php foreach($orders as $o): ?>
          <div class="bg-[#111] p-4 rounded-xl border border-[var(--primary-red)] shadow-md">
            <p><strong>Fecha:</strong> <?= htmlspecialchars($o['fecha_hora']) ?></p>
            <p><strong>Total:</strong> <?= number_format($o['total'],2) ?> Bs</p>
            <p><strong>Método de pago:</strong> <?= htmlspecialchars($o['payment_method']) ?></p>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-gray-400">No hay compras registradas.</p>
      <?php endif; ?>
    </div>

    <!-- Ventas realizadas -->
    <h3 class="font-semibold mt-6 mb-2 text-[var(--light-red)]">Ventas realizadas</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-h-64 overflow-y-auto">
      <?php if($sales): ?>
        <?php foreach($sales as $s): ?>
          <div class="bg-[#111] p-4 rounded-xl border border-[var(--primary-red)] shadow-md">
            <p><strong>Fecha:</strong> <?= htmlspecialchars($s['fecha_hora']) ?></p>
            <p><strong>Juego:</strong> <?= htmlspecialchars($s['juego']) ?></p>
            <p><strong>Consola:</strong> <?= htmlspecialchars($s['consola']) ?></p>
            <p><strong>Estado:</strong> <?= htmlspecialchars($s['estado']) ?></p>
            <p><strong>Precio:</strong> <?= number_format($s['precio_sugerido'],2) ?> Bs</p>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-gray-400">No hay ventas registradas.</p>
      <?php endif; ?>
    </div>

  </div>
</section>

<script>
const toggleCurrent = document.querySelector('#toggleCurrent');
const currentPassword = document.querySelector('#current_password');
toggleCurrent.addEventListener('click', () => {
    const type = currentPassword.getAttribute('type') === 'password' ? 'text' : 'password';
    currentPassword.setAttribute('type', type);
    toggleCurrent.textContent = type === 'password' ? 'Mostrar' : 'Ocultar';
});

const toggleNew = document.querySelector('#toggleNew');
const newPassword = document.querySelector('#new_password');
toggleNew.addEventListener('click', () => {
    const type = newPassword.getAttribute('type') === 'password' ? 'text' : 'password';
    newPassword.setAttribute('type', type);
    toggleNew.textContent = type === 'password' ? 'Mostrar' : 'Ocultar';
});
</script>

</body>
</html>
