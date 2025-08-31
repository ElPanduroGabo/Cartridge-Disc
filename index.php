<?php
session_start();
// Regenerate session ID for security
session_regenerate_id(true);

// Debug session (uncomment to check session contents)
// echo "<pre>Session: ";
// print_r($_SESSION);
// echo "</pre>";
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cartridge & Disc</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
:root {
  --primary-red: #d8403f;
  --dark-red: #b0302f;
  --light-red: #e8605f;
  --bg-dark: #1a1a1a;
}
.menu-transition {
  transition: opacity 0.3s ease, transform 0.3s ease;
}
.menu-hidden {
  opacity: 0;
  transform: translateY(-10px) scale(0.95);
  pointer-events: none;
}
.menu-visible {
  opacity: 1;
  transform: translateY(0) scale(1);
  pointer-events: auto;
}
#cartSide{
  position:fixed; top:0; right:0; width:350px; height:100%; background:#111; transform:translateX(100%);
  transition:transform 0.3s; z-index:1000; padding:20px; display:flex; flex-direction:column;
}
#cartSide h2{ font-size:1.5rem; color:var(--light-red); margin-bottom:10px; }
#cartItems{ flex:1; overflow-y:auto; margin-bottom:10px; }
#cartTotal{ font-weight:bold; margin-bottom:10px; }
#checkout{ background:var(--primary-red); color:white; padding:10px; border-radius:6px; font-weight:bold; cursor:pointer; }
#checkout:hover{ background:var(--light-red); }
#closeCart{ margin-top:auto; text-align:center; color:var(--primary-red); font-weight:bold; cursor:pointer; padding:10px 0; }
::-webkit-scrollbar{ width:8px; }
::-webkit-scrollbar-thumb{ background-color:#d8403f; border-radius:4px; }
</style>
</head>
<body class="bg-[var(--bg-dark)] text-white font-sans">

<header class="fixed top-0 left-0 w-full z-50 bg-[var(--bg-dark)] shadow-lg">
<div class="container mx-auto flex items-center justify-between py-4 relative">
  <div class="flex items-center">
    <a href="index.php" class="cursor-pointer transform transition-transform duration-300 hover:scale-110">
      <img src="img/logo_normal.webp" alt="Cartridge & Disc Logo" class="h-12 w-auto">
    </a>
  </div>
  <nav class="absolute left-1/2 transform -translate-x-1/2 flex space-x-4">
    <a href="index.php" class="text-[var(--primary-red)] font-semibold px-4 py-2 rounded transition transform duration-300 hover:bg-[var(--primary-red)] hover:text-white hover:scale-105">Inicio</a>
    <a href="games.php" class="text-[var(--primary-red)] font-semibold px-4 py-2 rounded transition transform duration-300 hover:bg-[var(--primary-red)] hover:text-white hover:scale-105">Comprar</a>
    <a href="venta.php" class="text-[var(--primary-red)] font-semibold px-4 py-2 rounded transition transform duration-300 hover:bg-[var(--primary-red)] hover:text-white hover:scale-105">Vender</a>
    <a href="contacto.php" class="text-[var(--primary-red)] font-semibold px-4 py-2 rounded transition transform duration-300 hover:bg-[var(--primary-red)] hover:text-white hover:scale-105">Contacto</a>
  </nav>
  <div class="relative flex items-center space-x-4">
    <button id="userButton" class="h-8 w-8 cursor-pointer focus:outline-none relative z-20">
      <img src="img/user.webp" alt="User" class="h-8 w-8 rounded-full hover:scale-110 transition-transform duration-300">
    </button>
    <button id="cartButton" class="h-8 w-8 cursor-pointer focus:outline-none relative z-20">
      <img src="img/carrito.webp" alt="Carrito" class="h-8 w-8 hover:scale-110 transition-transform duration-300">
    </button>
    <div id="userMenu" class="absolute top-full mt-1 left-1/2 transform -translate-x-1/2 w-40 bg-[#1a1a1a] rounded shadow-lg menu-transition menu-hidden">
      <?php if(isset($_SESSION['user_id'])): ?>
        <a href="perfil.php" class="block px-4 py-2 text-white hover:bg-[var(--primary-red)] hover:scale-105 transition-transform rounded-t-md">Perfil</a>
        <a href="logout.php" class="block px-4 py-2 text-white hover:bg-[var(--primary-red)] hover:scale-105 transition-transform rounded-b-md">Cerrar Sesión</a>
      <?php else: ?>
        <a href="login.php" class="block px-4 py-2 text-white hover:bg-[var(--primary-red)] hover:scale-105 transition-transform rounded-t-md">Iniciar Sesión</a>
        <a href="register.php" class="block px-4 py-2 text-white hover:bg-[var(--primary-red)] hover:scale-105 transition-transform rounded-b-md">Registrarse</a>
      <?php endif; ?>
    </div>
  </div>
</div>
</header>

<section class="relative py-20 text-center mt-16" id="inicio">
<img src="img/background.webp" alt="Fondo Hero" class="absolute inset-0 w-full h-full object-cover">
<div class="absolute inset-0 bg-gradient-to-r from-[var(--dark-red)] to-[var(--bg-dark)] opacity-80"></div>
<div class="relative z-10 container mx-auto">
<h2 class="text-5xl font-extrabold mb-4 text-[var(--light-red)]">¡Cartridge & Disc!</h2>
<p class="text-xl mb-6 text-gray-300">Los mejores videojuegos en formato físico: discos y cartuchos para todas tus consolas.</p>
<button onclick="window.location.href='games.php'" class="bg-[var(--primary-red)] text-white px-6 py-3 rounded-full font-bold transition-transform transition-colors duration-300 transform hover:scale-105 hover:bg-[var(--light-red)]">
  Juegos
</button>
</div>
</section>

<section id="juegos" class="py-8">
<div class="container mx-auto">
<h2 class="text-4xl font-extrabold text-center mb-8 text-[var(--light-red)]">Éxitos</h2>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">

<div class="bg-[#000000] rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform border-2 border-transparent hover:border-[#d8403f]">
  <img src="img/Tekken 3.webp" alt="Tekken 3" class="w-full h-60 object-contain bg-[#1a1a1a]">
  <div class="p-4 text-center">
    <h3 class="text-xl font-bold text-[var(--light-red)]">Tekken 3</h3>
    <button onclick="window.open('https://www.youtube.com/results?search_query=Tekken+3+gameplay','_blank')" class="mt-4 bg-[var(--primary-red)] text-white px-4 py-2 rounded hover:bg-[var(--light-red)]">Gameplay</button>
  </div>
</div>

<div class="bg-[#000000] rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform border-2 border-transparent hover:border-[#d8403f]">
  <img src="img/Crash Bandicoot.webp" alt="Crash Bandicoot" class="w-full h-60 object-contain bg-[#1a1a1a]">
  <div class="p-4 text-center">
    <h3 class="text-xl font-bold text-[var(--light-red)]">Crash Bandicoot</h3>
    <button onclick="window.open('https://www.youtube.com/results?search_query=Crash+Bandicoot+gameplay','_blank')" class="mt-4 bg-[var(--primary-red)] text-white px-4 py-2 rounded hover:bg-[var(--light-red)]">Gameplay</button>
  </div>
</div>

<div class="bg-[#000000] rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform border-2 border-transparent hover:border-[#d8403f]">
  <img src="img/Drink - Pepsiman.webp" alt="Drink - Pepsiman" class="w-full h-60 object-contain bg-[#1a1a1a]">
  <div class="p-4 text-center">
    <h3 class="text-xl font-bold text-[var(--light-red)]">Drink - Pepsiman</h3>
    <button onclick="window.open('https://www.youtube.com/results?search_query=Pepsiman+gameplay','_blank')" class="mt-4 bg-[var(--primary-red)] text-white px-4 py-2 rounded hover:bg-[var(--light-red)]">Gameplay</button>
  </div>
</div>

</div>
</div>
</section>

<footer class="bg-[var(--dark-red)] py-1">
<div class="container mx-auto text-center">
<p>&copy; 2025 Cartridge & Disc. Todos los derechos reservados.</p>
</div>
</footer>

<div id="cartSide">
<h2>Carrito</h2>
<div id="cartItems"></div>
<div>Total: <span id="cartTotal">0.00</span> Bs</div>
<button id="checkout">Realizar Transacción</button>
<div id="closeCart">Cerrar</div>
</div>

<script>
const userButton = document.getElementById('userButton');
const userMenu = document.getElementById('userMenu');
const cartButton = document.getElementById('cartButton');
const cartSide = document.getElementById('cartSide');
const closeCart = document.getElementById('closeCart');
const cartItemsContainer = document.getElementById('cartItems');
const cartTotalElem = document.getElementById('cartTotal');
const checkout = document.getElementById('checkout');

let cart = JSON.parse(localStorage.getItem('cart')) || [];

function saveCart(){
    localStorage.setItem('cart', JSON.stringify(cart));
}

function updateCart(){
    cartItemsContainer.innerHTML='';
    let total=0;
    cart.forEach((item,index)=>{
        total += item.price*item.quantity;
        const div = document.createElement('div');
        div.classList.add('flex','items-center','justify-between','mb-2');
        div.innerHTML = `
            <img src="${item.thumbnail}" class="w-16 h-16 object-contain rounded">
            <div class="flex-1 px-2">
                ${item.title}<br>
                Cantidad: <span class="item-qty">${item.quantity}</span><br>
                Precio: ${(item.price*item.quantity).toFixed(2)} Bs
            </div>
            <button onclick="removeItem(${index})" class="text-[var(--primary-red)] font-bold">✕</button>
        `;
        cartItemsContainer.appendChild(div);
    });
    cartTotalElem.textContent = total.toFixed(2);
    saveCart();
}

function removeItem(index){
    cart.splice(index,1);
    updateCart();
}

checkout.addEventListener('click', ()=>{
    if(cart.length===0){
        alert("El carrito está vacío");
        return;
    }

    <?php if(isset($_SESSION['user_id'])): ?>
    // Crear un formulario dinámico para enviar el carrito por POST
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'transaccion.php';

    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'cart';
    input.value = JSON.stringify(cart);

    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
    <?php else: ?>
    alert("Por favor, inicie sesión para realizar la transacción.");
    <?php endif; ?>
});

cartButton.addEventListener('click', ()=>{
    cartSide.style.transform='translateX(0)';
});

closeCart.addEventListener('click', ()=>{
    cartSide.style.transform='translateX(100%)';
});

userButton.addEventListener('click', (e) => {
    e.stopPropagation();
    console.log('User button clicked'); // Debug
    if (userMenu.classList.contains('menu-hidden')) {
        userMenu.classList.remove('menu-hidden');
        userMenu.classList.add('menu-visible');
        console.log('Menu set to visible'); // Debug
    } else {
        userMenu.classList.remove('menu-visible');
        userMenu.classList.add('menu-hidden');
        console.log('Menu set to hidden'); // Debug
    }
});

document.addEventListener('click', (e) => {
    if (!userButton.contains(e.target) && !userMenu.contains(e.target)) {
        userMenu.classList.remove('menu-visible');
        userMenu.classList.add('menu-hidden');
        console.log('Menu hidden due to outside click'); // Debug
    }
});

updateCart();
</script>

</body>
</html>
