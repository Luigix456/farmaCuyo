<?php
require __DIR__ . "/_bootstrap.php";

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = []; // [id => qty]

$action = $_GET['action'] ?? $_POST['action'] ?? 'get';

switch ($action) {
  case 'get': {
    $cart = $_SESSION['cart'];
    if (!$cart) json_ok(["items" => [], "count" => 0, "subtotal" => 0]);

    // Traemos info de productos
    $ids = array_keys($cart);
    $in  = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT id, nombre, imagen, precio FROM productos WHERE id IN ($in)");
    $stmt->execute($ids);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $items = [];
    $subtotal = 0; $count = 0;
    foreach ($rows as $p) {
      $qty = (int)$cart[$p['id']];
      $line = $qty * (float)$p['precio'];
      $subtotal += $line; $count += $qty;
      $items[] = [
        "id" => (int)$p['id'],
        "nombre" => $p['nombre'],
        "imagen" => $p['imagen'],
        "precio" => (float)$p['precio'],
        "qty" => $qty,
        "total" => $line
      ];
    }
    json_ok(["items" => $items, "count" => $count, "subtotal" => $subtotal]);
  }

  case 'add': {
    $id = (int)($_POST['id'] ?? 0);
    $qty = max(1, (int)($_POST['qty'] ?? 1));
    if ($id <= 0) json_error("ID inválido");
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + $qty;
    json_ok(["count" => array_sum($_SESSION['cart'])]);
  }

  case 'update': {
    $id = (int)($_POST['id'] ?? 0);
    $qty = (int)($_POST['qty'] ?? 1);
    if ($id <= 0) json_error("ID inválido");
    if ($qty <= 0) unset($_SESSION['cart'][$id]);
    else $_SESSION['cart'][$id] = $qty;
    json_ok(["count" => array_sum($_SESSION['cart'])]);
  }

  case 'remove': {
    $id = (int)($_POST['id'] ?? 0);
    unset($_SESSION['cart'][$id]);
    json_ok(["count" => array_sum($_SESSION['cart'])]);
  }

  case 'clear': {
    $_SESSION['cart'] = [];
    json_ok(["count" => 0]);
  }
}
