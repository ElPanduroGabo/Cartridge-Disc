<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contacto - Cartridge & Disc</title>
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

    <h2 class="text-3xl font-bold mb-6 text-center text-[var(--light-red)]">Contáctanos</h2>

    <!-- Encuéntranos -->
    <div>
      <h3 class="font-semibold mb-2 text-[var(--light-red)]">Encuéntranos</h3>
      <div class="border border-gray-600 rounded overflow-hidden">
        <iframe src="https://www.google.com/maps/d/embed?mid=1qz534tXV59WvBLM2ZMJX9d-ws17jKks&ehbc=2E312F&noprof=1" width="100%" height="400"></iframe>
      </div>
    </div>

    <!-- Recoge tus pedidos -->
    <div>
      <h3 class="font-semibold mb-2 text-[var(--light-red)]">Recoge tus pedidos</h3>
      <div class="bg-[#111] p-4 rounded-xl border border-[var(--primary-red)] shadow-md space-y-2">
        <p><strong>Email:</strong> <a href="mailto:gaboalanoca@gmail.com" class="text-[var(--primary-red)] hover:underline">gaboalanoca@gmail.com</a></p>
        <p><strong>WhatsApp:</strong> <a href="https://wa.me/63115698" target="_blank" class="text-[var(--primary-red)] hover:underline">63115698</a></p>
      </div>
    </div>

  </div>
</section>

</body>
</html>
