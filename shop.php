<?php
require_once __DIR__ . "/admin/db.php";

// productos habilitados
$stmt = $pdo->query("SELECT p.*, c.nombre AS categoria
                     FROM productos p
                     LEFT JOIN categorias c ON c.id = p.categoria_id
                     WHERE p.habilitado = 1
                     ORDER BY p.id DESC");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// sesión para contar carrito
if (session_status() === PHP_SESSION_NONE) session_start();
$cart_count = 0;
if (!empty($_SESSION['cart'])) {
  foreach ($_SESSION['cart'] as $q) $cart_count += (int)$q;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Tienda - FarmaCuyo</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="fonts/icomoon/style.css">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="fonts/flaticon/font/flaticon.css">
  <link rel="stylesheet" href="css/style.css">

  <style>
    /* --- Imagen responsiva con recorte sin deformar --- */
    .product-thumb {
      position: relative;
      width: 100%;
      aspect-ratio: 1 / 1;            /* Navegadores modernos */
      overflow: hidden;
      border-radius: 10px;
      background: #f6f7fb;            /* Fondo suave si no hay imagen */
      border: 1px solid #e9ecf2;
    }
    /* Fallback para navegadores sin aspect-ratio */
    .product-thumb::before {
      content: "";
      display: block;
      padding-top: 100%;               /* Mantiene 1:1 */
    }
    .product-thumb > img {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;               /* Recorte centrado sin deformar */
      object-position: center;
      display: block;
    }
    .item-v2 h3 {
      margin-top: .75rem;
      min-height: 2.2em;               /* Evita saltos por títulos de 1–2 líneas */
    }
    .price {
      margin-bottom: .5rem;
    }
  </style>
</head>
<body>
<div class="site-wrap">

  <!-- Navbar -->
  <div class="site-navbar py-2">
    <div class="container">
      <div class="d-flex align-items-center justify-content-between">
        <div class="logo">
          <a href="index.php" class="js-logo-clone"><strong class="text-primary">Farma</strong>Cuyo</a>
        </div>
        <div class="main-nav d-none d-lg-block">
          <nav class="site-navigation text-right text-md-center" role="navigation">
            <ul class="site-menu js-clone-nav d-none d-lg-block">
              <li><a href="index.php">Inicio</a></li>
              <li class="active"><a href="shop.php">Productos</a></li>
              <li><a href="contact.php">Contacto</a></li>
            </ul>
          </nav>
        </div>
        <div class="icons">
          <a href="cart.php" class="icons-btn d-inline-block bag">
            <span class="icon-shopping-bag"></span>
            <span class="number"><?= $cart_count ?></span>
          </a>
          <a href="#" class="site-menu-toggle js-menu-toggle ml-3 d-inline-block d-lg-none">
            <span class="icon-menu"></span>
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Breadcrumb -->
  <div class="bg-light py-3">
    <div class="container">
      <div class="row">
        <div class="col-md-12 mb-0">
          <a href="index.php">Home</a> <span class="mx-2 mb-0">/</span>
          <strong class="text-black">Store</strong>
        </div>
      </div>
    </div>
  </div>

  <!-- Grid de productos -->
  <div class="site-section bg-light">
    <div class="container">
      <div class="row">
        <?php if (empty($productos)): ?>
          <div class="col-12 text-center py-5">No hay productos</div>
        <?php else: ?>
          <?php foreach ($productos as $p): ?>
            <?php
              $img = trim((string)$p['imagen']);
              if ($img === '') $img = 'images/product_01.png'; // placeholder existente del template
            ?>
            <div class="col-sm-6 col-lg-4 text-center item mb-4 item-v2">
              <a href="shop-single.php?id=<?= (int)$p['id'] ?>">
                <div class="product-thumb">
                  <img
                    src="<?= htmlspecialchars($img) ?>"
                    alt="<?= htmlspecialchars($p['nombre']) ?>"
                    loading="lazy"
                    width="600" height="600"  <!-- pistas de tamaño para evitar CLS -->
                  >
                </div>
              </a>

              <h3 class="text-dark mt-3">
                <a href="shop-single.php?id=<?= (int)$p['id'] ?>">
                  <?= htmlspecialchars($p['nombre']) ?>
                </a>
              </h3>

              <p class="price mb-2">$<?= number_format((float)$p['precio'], 2, ',', '.') ?></p>

              <form action="cart.php" method="post" class="d-inline-block">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                <button class="btn btn-sm btn-primary">Agregar</button>
              </form>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="site-footer bg-light">
    <div class="container text-center py-4">
      <p class="mb-0">&copy; <?= date('Y') ?> FarmaCuyo</p>
    </div>
  </footer>
</div>

<script src="js/bootstrap.min.js"></script>
</body>
</html>
