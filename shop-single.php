<?php
require_once __DIR__ . "/admin/db.php";
if (session_status() === PHP_SESSION_NONE) session_start();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT p.*, c.nombre AS categoria
                       FROM productos p
                       LEFT JOIN categorias c ON c.id = p.categoria_id
                       WHERE p.id = :id AND p.habilitado = 1");
$stmt->execute(["id" => $id]);
$prod = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prod) {
  // producto no encontrado
  header("Location: shop.php");
  exit;
}

$cart_count = 0;
if (!empty($_SESSION['cart'])) foreach ($_SESSION['cart'] as $q) $cart_count += $q;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title><?= htmlspecialchars($prod['nombre']) ?> - FarmaCuyo</title>
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
        <div class="main-nav d-none d-lg-block">
          <nav class="site-navigation text-right text-md-center" role="navigation">
            <ul class="site-menu js-clone-nav d-none d-lg-block">
              <li><a href="index.php">Home</a></li>
              <li><a href="shop.php">Store</a></li>
            </ul>
          </nav>
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
        <div class="col-md-12 mb-0">
          <a href="index.php">Home</a> <span class="mx-2 mb-0">/</span>
          <a href="shop.php">Store</a> <span class="mx-2 mb-0">/</span>
          <strong class="text-black"><?= htmlspecialchars($prod['nombre']) ?></strong>
        </div>
      </div>
    </div>
  </div>

  <div class="site-section">
    <div class="container">
      <div class="row">
        <div class="col-md-5 mr-auto">
          <div class="border text-center">
            <img src="<?= htmlspecialchars($prod['imagen'] ?: 'images/product_07_large.png') ?>" class="img-fluid p-5" alt="">
          </div>
        </div>
        <div class="col-md-6">
          <h2 class="text-black"><?= htmlspecialchars($prod['nombre']) ?></h2>
          <p><?= nl2br(htmlspecialchars($prod['descripcion'] ?: '')) ?></p>
          <p><strong class="text-primary h4">$<?= number_format($prod['precio'], 2, ',', '.') ?></strong></p>

          <form action="cart.php" method="post" class="d-flex align-items-center mb-3" style="max-width:240px;">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="id" value="<?= $prod['id'] ?>">
            <div class="input-group me-2">
              <input type="number" name="qty" class="form-control text-center" value="1" min="1">
            </div>
            <button class="btn btn-primary">Add To Cart</button>
          </form>
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
