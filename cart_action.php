<?php
session_start();
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';
if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if($action === 'add'){
    $item = [
        'title' => $_POST['title'],
        'price' => floatval($_POST['price']),
        'thumbnail' => $_POST['thumbnail'],
        'quantity' => intval($_POST['quantity'])
    ];

    $found = false;
    foreach($_SESSION['cart'] as &$cartItem){
        if($cartItem['title'] === $item['title']){
            $cartItem['quantity'] += $item['quantity'];
            $found = true;
            break;
        }
    }
    if(!$found) $_SESSION['cart'][] = $item;
    echo json_encode(['status'=>'ok','cart'=>$_SESSION['cart']]);
    exit;
}

if($action === 'remove'){
    $title = $_POST['title'];
    $_SESSION['cart'] = array_filter($_SESSION['cart'], fn($i)=>$i['title']!==$title);
    echo json_encode(['status'=>'ok','cart'=>$_SESSION['cart']]);
    exit;
}

if($action === 'get'){
    echo json_encode($_SESSION['cart']);
    exit;
}
