<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Información del usuario logeado
$stmt = $db->prepare("SELECT nombre, email, celular FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$errors = [];
$success = "";

if (isset($_POST['send_sale'])) {
    $juego = trim($_POST['juego']);
    $consola = trim($_POST['consola']);
    $estado = trim($_POST['estado']);
    $precio = trim($_POST['precio']);

    if (!$juego || !$consola || !$estado || !$precio) {
        $errors[] = "Todos los campos son obligatorios.";
    }

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $file_tmp = $_FILES['imagen']['tmp_name'];
        $file_name = $_FILES['imagen']['name'];
        $file_type = $_FILES['imagen']['type'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png'];
        if (!in_array($file_ext, $allowed)) {
            $errors[] = "Solo se permiten imágenes JPG, JPEG o PNG.";
        }
    } else {
        $errors[] = "Debe subir una imagen del juego.";
    }

    if (empty($errors)) {
        $to = "gaboalanoca@gmail.com";
        $subject = "Venta de juego";

        $boundary = md5(time());
        $headers = "From: Cartridge & Disc <mail@cartridgeanddisc.bo>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";

        $message = "--{$boundary}\r\n";
        $message .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message .= "Hola Cartridge & Disc,\n\n";
        $message .= "Asunto: Venta de juego\n";
        $message .= "Usuario: {$user['nombre']}\n";
        $message .= "Email: {$user['email']}\n";
        $message .= "Celular: {$user['celular']}\n";
        $message .= "Juego: {$juego}\n";
        $message .= "Consola: {$consola}\n";
        $message .= "Estado: {$estado}\n";
        $message .= "Precio: Bs {$precio}\n\n";

        $img_data = file_get_contents($file_tmp);
        $img_data = chunk_split(base64_encode($img_data));

        $message .= "--{$boundary}\r\n";
        $message .= "Content-Type: {$file_type}; name=\"{$file_name}\"\r\n";
        $message .= "Content-Disposition: attachment; filename=\"{$file_name}\"\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $message .= $img_data . "\r\n";
        $message .= "--{$boundary}--";

        if (mail($to, $subject, $message, $headers)) {
            $success = "Mensaje enviado. Revise su correo registrado.";
        } else {
            $errors[] = "Error al enviar el correo.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Vender Juego - Cartridge & Disc</title>
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

    <h2 class="text-3xl font-bold mb-6 text-center text-[var(--light-red)]">Vender Juego</h2>

    <?php if(!empty($errors)): ?>
      <div class="bg-red-700 text-white p-2 rounded">
        <?php foreach($errors as $error) echo "<p>$error</p>"; ?>
      </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="space-y-4">
      <input type="text" name="juego" placeholder="Nombre del juego" class="w-full px-4 py-2 rounded bg-[#1a1a1a] border border-gray-600 focus:border-[var(--primary-red)] focus:ring-2 focus:ring-[var(--primary-red)]" required>
      <input type="text" name="consola" placeholder="Consola" class="w-full px-4 py-2 rounded bg-[#1a1a1a] border border-gray-600 focus:border-[var(--primary-red)] focus:ring-2 focus:ring-[var(--primary-red)]" required>
      <select name="estado" class="w-full px-4 py-2 rounded bg-[#1a1a1a] border border-gray-600 focus:border-[var(--primary-red)] focus:ring-2 focus:ring-[var(--primary-red)]" required>
        <option value="">Selecciona Estado</option>
        <option value="Nuevo">Nuevo</option>
        <option value="Usado">Usado</option>
      </select>
      <input type="number" name="precio" placeholder="Precio sugerido" step="0.01" class="w-full px-4 py-2 rounded bg-[#1a1a1a] border border-gray-600 focus:border-[var(--primary-red)] focus:ring-2 focus:ring-[var(--primary-red)]" required>
      <input type="file" name="imagen" accept=".jpg,.jpeg,.png" class="w-full text-white" required>
      <button type="submit" name="send_sale" class="w-full bg-[var(--primary-red)] py-2 rounded font-bold hover:bg-[var(--light-red)] transition">Enviar</button>
    </form>

  </div>
</section>

<?php if($success): ?>
<script>
alert("<?php echo $success; ?>");
</script>
<?php endif; ?>

</body>
</html>
