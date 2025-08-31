<?php
session_start();
require 'db.php';

$selectedConsole = $_GET['console'] ?? 'Todo';
$search = $_GET['search'] ?? '';

if ($selectedConsole === 'Todo') {
    $stmt = $db->prepare("SELECT * FROM games WHERE title LIKE ?");
    $stmt->execute(["%$search%"]);
} else {
    $stmt = $db->prepare("SELECT * FROM games WHERE console = ? AND title LIKE ?");
    $stmt->execute([$selectedConsole, "%$search%"]);
}
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);

$consoles = ['Todo','GBA','GC','N64','NES','PS1','PS2','PS3','PS4','PSP','SNES','Wii'];

function getImagePath($game){
    $console = $game['console'];
    $filename = $game['image'];
    $thumbPath = "img/juegos/thumbnails/$console/".pathinfo($filename, PATHINFO_FILENAME)."_thumbnail.webp";
    if(file_exists($thumbPath)) return $thumbPath;
    $fullPath = "img/juegos/full/$console/$filename";
    if(file_exists($fullPath)) return $fullPath;
    return "img/logo_normal.webp";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Catálogo - Cartridge & Disc</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
:root{
    --primary-red:#d8403f;
    --light-red:#e8605f;
    --bg-dark:#1a1a1a;
}
.sidebar{
    width:280px; background:#111; height:100vh; padding:20px; position:fixed; top:0; left:0;
    overflow-y:auto; font-size:1.1rem;
}
.console-list{ display:flex; flex-direction:column; gap:10px; margin-top:20px; }
.game-card img{ width:100%; height:200px; object-fit:contain; background:#1a1a1a; }
.modal-bg{ position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); display:none; align-items:center; justify-content:center; z-index:1000; }
.modal{ background:#000; display:flex; max-width:900px; width:90%; border-radius:12px; overflow:hidden; position:relative; }
.modal img{ width:50%; height:auto; max-height:80vh; object-fit:contain; background:#1a1a1a; }
.modal-content{ flex:1; padding:20px; color:white; display:flex; flex-direction:column; gap:10px; }
.modal-content h3{ font-size:1.8rem; color:var(--light-red); }
.modal-content p{ font-size:1rem; }
.modal-content input[type="number"]{ width:80px; padding:5px; border-radius:4px; border:1px solid #ccc; color:black; background:white; }
.modal-content button{ background:var(--primary-red); color:white; padding:10px; border-radius:6px; font-weight:bold; cursor:pointer; }
.modal-content button:hover{ background:var(--light-red); }
.main-content{ margin-left:280px; padding:20px; flex:1; overflow-y:auto; height:100vh; }
.user-cart{ display:flex; gap:20px; justify-content:center; margin-top:10px; margin-bottom:20px; }
.user-cart img{ height:40px; cursor:pointer; transition: transform 0.3s; }
.user-cart img:hover{ transform: scale(1.1); }
.user-menu{ position:absolute; top:120px; left:20px; width:240px; background:#1a1a1a; border-radius:8px; box-shadow:0 4px 6px rgba(0,0,0,0.3); z-index:500; display:none; flex-direction:column; }
.user-menu a{ display:block; padding:10px 15px; color:white; text-decoration:none; }
.user-menu a:hover{ background:var(--primary-red); }
.user-menu.show{ display:flex; }
#cartSide{ position:fixed; bottom:0; right:0; width:350px; height:400px; background:#111; transform:translateY(100%); transition:transform 0.3s; z-index:1000; padding:20px; display:flex; flex-direction:column; }
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
<body class="bg-[var(--bg-dark)] text-white font-sans flex">

<aside class="sidebar">
    <a href="index.php" class="mb-6 flex justify-center hover:scale-110 transition">
        <img src="img/logo_normal.webp" alt="Logo" class="h-12 w-auto">
    </a>
    <div class="user-cart">
        <button id="userButton"><img src="img/user.webp" alt="Usuario" class="rounded-full"></button>
        <button id="cartButton"><img src="img/carrito.webp" alt="Carrito"></button>
    </div>
    <div id="userMenu" class="user-menu">
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="perfil.php">Perfil</a>
            <a href="logout.php">Cerrar Sesión</a>
        <?php else: ?>
            <a href="login.php">Iniciar Sesión</a>
            <a href="register.php">Registrarse</a>
        <?php endif; ?>
    </div>
    <form method="GET" class="mb-4">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Buscar juegos..." class="w-full px-3 py-2 rounded bg-[#1a1a1a] border border-gray-600 focus:border-[var(--primary-red)] focus:ring-2 focus:ring-[var(--primary-red)]">
        <button type="submit" class="hidden">Buscar</button>
    </form>
    <h2 class="text-lg font-bold text-[var(--light-red)]">Consolas</h2>
    <div class="console-list">
        <?php foreach($consoles as $console): ?>
            <a href="?console=<?= $console ?>&search=<?= urlencode($search) ?>" class="px-4 py-2 rounded hover:bg-[var(--primary-red)] <?= ($selectedConsole === $console)?'bg-[var(--primary-red)]':'' ?>"><?= $console ?></a>
        <?php endforeach; ?>
    </div>
</aside>

<main class="main-content">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach($games as $game): ?>
        <div class="game-card bg-[#000] rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition border-2 border-transparent hover:border-[var(--primary-red)]">
            <img src="<?= getImagePath($game) ?>" alt="<?= htmlspecialchars($game['title']) ?>">
            <div class="p-4 text-center">
                <h3 class="text-xl font-bold text-[var(--light-red)]"><?= htmlspecialchars($game['title']) ?></h3>
                <p class="text-gray-300 font-semibold">Estado: <?= htmlspecialchars($game['condition']) ?></p>
                <p class="text-gray-300 font-semibold">Precio: <?= number_format($game['price'],2) ?> Bs</p>
                <button class="mt-4 bg-[var(--primary-red)] text-white px-4 py-2 rounded hover:bg-[var(--light-red)] view-details"
                    data-title="<?= htmlspecialchars($game['title']) ?>"
                    data-image="<?= getImagePath($game) ?>"
                    data-price="<?= number_format($game['price'],2) ?>"
                    data-condition="<?= htmlspecialchars($game['condition']) ?>"
                    data-stock="<?= $game['stock'] ?>"
                    data-description="<?= htmlspecialchars($game['description']) ?>"
                    data-developer="<?= htmlspecialchars($game['developer']) ?>">Ver detalles</button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</main>

<div class="modal-bg" id="modal">
    <div class="modal">
        <img id="modal-image" src="">
        <div class="modal-content">
            <h3 id="modal-title"></h3>
            <p>Estado: <span id="modal-condition"></span></p>
            <p>Stock: <span id="modal-stock"></span></p>
            <p>Precio: <span id="modal-price"></span> Bs</p>
            <p>Descripción: <span id="modal-description"></span></p>
            <p>Desarrolladora: <span id="modal-developer"></span></p>
            <p>Cantidad: <input type="number" id="modal-quantity" value="1" min="1" max="10"></p>
            <button id="add-to-cart">Agregar al carrito</button>
        </div>
        <button id="modal-close" class="absolute top-2 right-2 text-[var(--primary-red)] font-bold text-xl">X</button>
    </div>
</div>

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
const addToCartModal = document.getElementById('add-to-cart');
const modal = document.getElementById('modal');

let cart = JSON.parse(localStorage.getItem('cart')) || [];

function saveCart() { localStorage.setItem('cart', JSON.stringify(cart)); }

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
                Cantidad: ${item.quantity}<br>
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

document.querySelectorAll('.view-details').forEach(btn=>{
    btn.addEventListener('click', ()=>{
        modal.style.display='flex';
        document.getElementById('modal-image').src = btn.dataset.image;
        document.getElementById('modal-title').textContent = btn.dataset.title;
        document.getElementById('modal-price').textContent = btn.dataset.price;
        document.getElementById('modal-condition').textContent = btn.dataset.condition;
        document.getElementById('modal-description').textContent = btn.dataset.description;
        document.getElementById('modal-developer').textContent = btn.dataset.developer;
        document.getElementById('modal-stock').textContent = btn.dataset.stock;
        document.getElementById('modal-quantity').value = 1;
        document.getElementById('modal-quantity').max = btn.dataset.stock;
    });
});

document.getElementById('modal-close').addEventListener('click', ()=>{ modal.style.display='none'; });
modal.addEventListener('click', e=>{ if(e.target===modal) modal.style.display='none'; });

addToCartModal.addEventListener('click', ()=>{
    <?php if(isset($_SESSION['user_id'])): ?>
    const title = document.getElementById('modal-title').textContent;
    const price = parseFloat(document.getElementById('modal-price').textContent);
    const thumbnail = document.getElementById('modal-image').src;
    const quantity = parseInt(document.getElementById('modal-quantity').value);
    const existing = cart.find(i=>i.title===title);
    if(existing) existing.quantity+=quantity;
    else cart.push({title,price,thumbnail,quantity});
    updateCart();
    cartSide.style.transform='translateY(0)';
    <?php else: ?>
    alert("Por favor, inicie sesión para agregar al carrito.");
    <?php endif; ?>
});

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

cartButton.addEventListener('click', ()=>cartSide.style.transform='translateY(0)');
closeCart.addEventListener('click', ()=>cartSide.style.transform='translateY(100%)');

userButton.addEventListener('click', (e)=>{ e.stopPropagation(); userMenu.classList.toggle('show'); userMenu.classList.toggle('hidden'); });
document.addEventListener('click', (e)=>{ if(!userButton.contains(e.target) && !userMenu.contains(e.target)){ userMenu.classList.remove('show'); userMenu.classList.add('hidden'); }});

updateCart();
</script>
</body>
</html>
