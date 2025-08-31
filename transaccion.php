<?php
session_start();
require 'db.php';

if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
    exit;
}

$cart = [];
if(isset($_POST['cart'])){
    $cart = json_decode($_POST['cart'], true);
}

$total = 0;
foreach($cart as $item){
    $total += $item['price'] * $item['quantity'];
}

if(isset($_POST['confirm'])){
    foreach($cart as $item){
        $stmt = $db->prepare("UPDATE games SET stock = stock - ? WHERE title = ?");
        $stmt->execute([$item['quantity'], $item['title']]);
    }
    $stmt = $db->prepare("INSERT INTO orders (user_id, items, total, payment_method, fecha_hora) VALUES (?, ?, ?, ?, datetime('now'))");
    $stmt->execute([$_SESSION['user_id'], json_encode($cart), $total, 'Pago en tienda']);
    $cart = [];
    echo "<script>alert('Transacción completada');</script>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Transacción - Cartridge & Disc</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
:root{
    --primary-red:#d8403f;
    --light-red:#e8605f;
    --bg-dark:#1a1a1a;
}
body{background:var(--bg-dark);color:white;font-family:sans-serif;}
header{padding:20px;text-align:center;}
header img{height:80px;transition:transform 0.3s;}
header img:hover{transform:scale(1.05);}
.main-content{padding:20px;}
.grid-games{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;}
.card{background:#000;border-radius:12px;overflow:hidden;transition:transform 0.3s;}
.card:hover{transform:scale(1.03);}
.card img{width:100%;height:200px;object-fit:contain;background:var(--bg-dark);}
.card-content{padding:15px;text-align:center;}
.card-content h3{color:var(--light-red);font-size:1.2rem;font-weight:bold;margin-bottom:5px;}
.card-content p{color:#ccc;margin:2px 0;}
#checkout{background:var(--primary-red);color:white;padding:12px 20px;border-radius:8px;font-weight:bold;cursor:pointer;margin-top:20px;display:block;margin-left:auto;margin-right:auto;transition:all 0.3s;}
#checkout:hover{background:var(--light-red);}
</style>
</head>
<body>

<header>
    <a href="index.php"><img src="img/logo_normal.webp" alt="Logo"></a>
</header>

<main class="main-content">
    <h2 class="text-3xl text-[var(--light-red)] mb-6 text-center">Carrito de compras</h2>

    <?php if(empty($cart)): ?>
        <p class="text-center">El carrito está vacío</p>
    <?php else: ?>
        <div class="grid-games">
            <?php foreach($cart as $item): ?>
                <div class="card">
                    <img src="<?= htmlspecialchars($item['thumbnail']) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
                    <div class="card-content">
                        <h3><?= htmlspecialchars($item['title']) ?></h3>
                        <p>Cantidad: <?= $item['quantity'] ?></p>
                        <p>Precio unitario: <?= number_format($item['price'],2) ?> Bs</p>
                        <p>Subtotal: <?= number_format($item['price']*$item['quantity'],2) ?> Bs</p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <p class="text-xl font-bold mt-6 text-center">Total: <?= number_format($total,2) ?> Bs</p>

        <form method="POST" onsubmit="return confirm('¿Desea confirmar el pago en tienda?');">
            <input type="hidden" name="cart" value='<?= json_encode($cart) ?>'>
            <button type="submit" name="confirm" id="checkout">Pago en tienda</button>
        </form>
    <?php endif; ?>
</main>

</body>
</html>
