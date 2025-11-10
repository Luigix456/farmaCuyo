<?php
require_once __DIR__ . "/admin/db.php";
if (session_status() === PHP_SESSION_NONE) session_start();

// 1. comprobar login de cliente
if (!isset($_SESSION['cliente_id'])) {
  // lo mandamos a login y que vuelva
  header("Location: login.php?redirect=checkout.php");
  exit;
}

// 2. comprobar carrito
if (empty($_SESSION['cart'])) {
  header("Location: cart.php");
  exit;
}

// 3. obtener productos del carrito
$ids = array_keys($_SESSION['cart']);
$ids = array_map('intval', $ids);
$ids_lista = implode(',', $ids);

// por seguridad, si por alguna razón quedara vacío:
if ($ids_lista === '') {
  header("Location: cart.php");
  exit;
}

$stmt = $pdo->query("SELECT id, nombre, precio FROM productos WHERE id IN ($ids_lista)");
$productos = $stmt->fetchAll(PDO::FETCH_UNIQUE);

// 4. cuando confirma
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // calcular total
  $total = 0;
  foreach ($_SESSION['cart'] as $pid => $qty) {
    if (!isset($productos[$pid])) continue;
    $precio = (float)$productos[$pid]['precio'];
    $total += $precio * (int)$qty;
  }

  // insertar en pedidos
  $ins = $pdo->prepare("INSERT INTO pedidos (cliente_id, total, estado)
                        VALUES (:cid, :total, 'pendiente')");
  $ins->execute([
    'cid'   => $_SESSION['cliente_id'],
    'total' => $total
  ]);
  $pedido_id = (int)$pdo->lastInsertId();

  // insertar en pedido_items
  $insItem = $pdo->prepare("INSERT INTO pedido_items
      (pedido_id, producto_id, nombre, precio, qty, subtotal)
      VALUES (:pedido_id, :producto_id, :nombre, :precio, :qty, :subtotal)");

  foreach ($_SESSION['cart'] as $pid => $qty) {
    if (!isset($productos[$pid])) continue;
    $nombre = $productos[$pid]['nombre'];
    $precio = (float)$productos[$pid]['precio'];
    $subtotal = $precio * (int)$qty;

    $insItem->execute([
      'pedido_id'  => $pedido_id,
      'producto_id'=> $pid,
      'nombre'     => $nombre,
      'precio'     => $precio,
      'qty'        => $qty,
      'subtotal'   => $subtotal
    ]);
  }

  // vaciar carrito
  $_SESSION['cart'] = [];

  header("Location: thankyou.php?id=" . $pedido_id);
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Checkout</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
  <h3 class="mb-4">Confirmar pedido</h3>

  <div class="mb-3">
    <p>Estos son los productos que se van a incluir en tu pedido:</p>
    <ul>
      <?php foreach ($_SESSION['cart'] as $pid => $qty):
        if (!isset($productos[$pid])) continue;
      ?>
        <li>
          <?= htmlspecialchars($productos[$pid]['nombre']) ?> x <?= (int)$qty ?> -
          $<?= number_format($productos[$pid]['precio'] * $qty, 2, ',', '.') ?>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>

  <form method="post">
    <button class="btn btn-primary">Finalizar pedido</button>
    <a href="cart.php" class="btn btn-secondary">Volver al carrito</a>
  </form>
</div>
</body>
</html>
