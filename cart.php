<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/admin/db.php";

// inicializar carrito
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
  $qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;

  // SI NO ESTÁ LOGUEADO Y QUIERE AGREGAR, LO MANDAMOS A LOGIN
  if ($action === 'add' && !isset($_SESSION['cliente_id'])) {
    $_SESSION['pending_add'] = [
      'id' => $id,
      'qty' => $qty > 0 ? $qty : 1
    ];
    header("Location: login.php?redirect=cart.php");
    exit;
  }

  if ($action === 'add' && $id > 0) {
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + max(1, $qty);
    header("Location: cart.php");
    exit;
  }

  if ($action === 'update' && $id > 0) {
    if ($qty <= 0) unset($_SESSION['cart'][$id]);
    else $_SESSION['cart'][$id] = $qty;
    header("Location: cart.php");
    exit;
  }

  if ($action === 'remove' && $id > 0) {
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit;
  }
}

// construir vista
$cart = $_SESSION['cart'];
$items = [];
$subtotal = 0;

if ($cart) {
  $ids = implode(',', array_map('intval', array_keys($cart)));
  $stmt = $pdo->query("SELECT id, nombre, imagen, precio FROM productos WHERE id IN ($ids)");
  $prods = $stmt->fetchAll(PDO::FETCH_ASSOC);
  foreach ($prods as $p) {
    $q = $cart[$p['id']];
    $line = $q * $p['precio'];
    $subtotal += $line;
    $items[] = [
      'id' => $p['id'],
      'nombre' => $p['nombre'],
      'imagen' => $p['imagen'],
      'precio' => $p['precio'],
      'qty' => $q,
      'total' => $line,
    ];
  }
}

$cart_count = 0;
foreach ($cart as $q) $cart_count += $q;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Carrito - FarmaCuyo</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="site-wrap">
  <div class="site-navbar py-2">
    <div class="container">
      <div class="d-flex align-items-center justify-content-between">
        <div class="logo">
          <a href="index.php" class="js-logo-clone"><strong class="text-primary">Farma</strong>Cuyo</a>
        </div>
        <div class="icons">
          <a href="cart.php" class="icons-btn d-inline-block bag">
            <span class="icon-shopping-bag"></span>
            <span class="number"><?= $cart_count ?></span>
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="bg-light py-3">
    <div class="container">
      <div class="row">
        <div class="col-md-12 mb-0"><a href="index.php">Home</a> <span class="mx-2 mb-0">/</span> <strong class="text-black">Cart</strong></div>
      </div>
    </div>
  </div>

  <div class="site-section">
    <div class="container">
      <div class="row mb-5">
        <div class="col-md-12">
          <div class="site-blocks-table">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Image</th>
                  <th>Product</th>
                  <th>Price</th>
                  <th>Quantity</th>
                  <th>Total</th>
                  <th>Remove</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($items)): ?>
                  <tr><td colspan="6" class="text-center py-4">Tu carrito está vacío.</td></tr>
                <?php else: ?>
                  <?php foreach ($items as $it): ?>
                    <tr>
                      <td><img src="<?= htmlspecialchars($it['imagen'] ?: 'images/product_01.png') ?>" style="width:80px;"></td>
                      <td><?= htmlspecialchars($it['nombre']) ?></td>
                      <td>$<?= number_format($it['precio'], 2, ',', '.') ?></td>
                      <td>
                        <form action="cart.php" method="post" class="d-flex" style="max-width:140px;">
                          <input type="hidden" name="action" value="update">
                          <input type="hidden" name="id" value="<?= $it['id'] ?>">
                          <input type="number" name="qty" value="<?= $it['qty'] ?>" min="0" class="form-control text-center me-2">
                          <button class="btn btn-sm btn-outline-primary">OK</button>
                        </form>
                      </td>
                      <td>$<?= number_format($it['total'], 2, ',', '.') ?></td>
                      <td>
                        <form action="cart.php" method="post">
                          <input type="hidden" name="action" value="remove">
                          <input type="hidden" name="id" value="<?= $it['id'] ?>">
                          <button class="btn btn-sm btn-danger">X</button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="row justify-content-end">
        <div class="col-md-4">
          <div class="border p-3 rounded">
            <h3 class="text-black h4">Cart Totals</h3>
            <p class="d-flex">
              <span>Subtotal</span>
              <span class="ms-auto">$<?= number_format($subtotal, 2, ',', '.') ?></span>
            </p>
            <p class="d-flex">
              <span>Total</span>
              <span class="ms-auto">$<?= number_format($subtotal, 2, ',', '.') ?></span>
            </p>
            <p><a href="checkout.php" class="btn btn-primary btn-block">Proceed to checkout</a></p>
          </div>
        </div>
      </div>

    </div>
  </div>

  <footer class="site-footer bg-light">
    <div class="container text-center py-4">
      <p class="mb-0">&copy; <?= date('Y') ?> FarmaCuyo</p>
    </div>
  </footer>
</div>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
