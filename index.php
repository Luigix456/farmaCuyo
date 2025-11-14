<?php
require_once __DIR__ . "/admin/db.php";
if (session_status() === PHP_SESSION_NONE) session_start();

// Traemos hasta 30 productos habilitados para el carrusel
$stmt = $pdo->query("SELECT id, nombre, precio, imagen 
                     FROM productos 
                     WHERE habilitado = 1 
                     ORDER BY id DESC 
                     LIMIT 30");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contar carrito
$cart_count = 0;
if (!empty($_SESSION['cart'])) {
  foreach ($_SESSION['cart'] as $q) {
    $cart_count += (int)$q;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>FarmaCuyo</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- los mismos CSS del template -->
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="fonts/icomoon/style.css">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="fonts/flaticon/font/flaticon.css">
  <link rel="stylesheet" href="css/owl.carousel.min.css">
  <link rel="stylesheet" href="css/owl.theme.default.min.css">
  <link rel="stylesheet" href="css/style.css">

  <style>
    /* Contenedor cuadrado para las imágenes del carrusel */
    .product-thumb {
      position: relative;
      width: 100%;
      aspect-ratio: 1/1; /* moderno */
      background: #f6f7fb;
      border-radius: 10px;
      overflow: hidden;
    }
    /* fallback */
    .product-thumb::before {
      content: "";
      display: block;
      padding-top: 100%;
    }
    .product-thumb img {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      object-position: center;
    }
    .card.h-100 {
      box-shadow: none;
    }
  </style>
</head>
<body>
  <div class="site-wrap">

    <!-- NAVBAR -->
    <div class="site-navbar py-2">
      <div class="container">
        <div class="d-flex align-items-center justify-content-between">
          <div class="logo">
            <a href="index.php" class="js-logo-clone">
              <strong class="text-primary">Farma</strong>Cuyo
            </a>
          </div>
          <div class="main-nav d-none d-lg-block">
            <nav class="site-navigation text-right text-md-center" role="navigation">
              <ul class="site-menu js-clone-nav d-none d-lg-block">
                <li class="active"><a href="index.php">Inicio</a></li>
                <li><a href="shop.php">Productos</a></li>
                <li><a href="about.php">Nosotros</a></li>
                <li><a href="contact.php">Contacto</a></li>
              </ul>
            </nav>
          </div>
          <div class="icons">
            <a href="cart.php" class="icons-btn d-inline-block bag">
              <span class="icon-shopping-bag"></span>
              <span class="number"><?= $cart_count ?></span>
            </a>
            <?php if (isset($_SESSION['cliente_id'])): ?>
              <span class="ms-2">Hola, <?= htmlspecialchars($_SESSION['cliente_nombre']) ?></span>
              <a href="logout.php" class="btn btn-sm btn-outline-secondary ms-2">Salir</a>
            <?php else: ?>
              <a href="login.php" class="btn btn-sm btn-outline-primary ms-2">Ingresar</a>
              <a href="register.php" class="btn btn-sm btn-primary ms-2">Registrarme</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- HERO -->
    <div class="site-blocks-cover" style="background-image: url('images/hero_1.jpg');" data-aos="fade">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-7 text-center text-lg-left">
            <span class="sub-text">Bienvenido a FarmaCuyo</span>
            <h1>Tu farmacia online</h1>
            <p>
              <a href="shop.php" class="btn btn-primary px-5 py-3">Ver productos</a>
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- CARRUSEL BOOTSTRAP CON PRODUCTOS -->
    <div class="site-section">
      <div class="container">
        <div class="row mb-4">
          <div class="col-12 text-center">
            <h2 class="section-title mb-3">Productos destacados</h2>
          </div>
        </div>

        <div id="carouselProductos" class="carousel slide" data-ride="carousel">
          <div class="carousel-inner">

            <?php
            // dividir en grupos de 6
            $chunks = array_chunk($productos, 6);
            if (empty($chunks)): ?>
              <div class="carousel-item active">
                <div class="container py-4">
                  <p class="text-center mb-0">No hay productos.</p>
                </div>
              </div>
            <?php else: ?>
              <?php foreach ($chunks as $index => $grupo): ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                  <div class="container py-4">
                    <div class="row">
                      <?php foreach ($grupo as $p): 
                        $img = trim((string)$p['imagen']);
                        if ($img === '') $img = 'images/product_01.png';
                      ?>
                        <div class="col-6 col-md-4 col-lg-2 mb-4">
                          <div class="card h-100 text-center border-0">
                            <div class="product-thumb mb-2">
                              <img
                                src="<?= htmlspecialchars($img) ?>"
                                alt="<?= htmlspecialchars($p['nombre']) ?>"
                                class="img-fluid"
                                loading="lazy"
                              >
                            </div>
                            <h6 class="mb-1" style="min-height:2.5em;">
                              <a href="shop-single.php?id=<?= (int)$p['id'] ?>">
                                <?= htmlspecialchars($p['nombre']) ?>
                              </a>
                            </h6>
                            <p class="text-primary mb-2">
                              $<?= number_format((float)$p['precio'], 2, ',', '.') ?>
                            </p>
                            <a href="shop-single.php?id=<?= (int)$p['id'] ?>" class="btn btn-sm btn-outline-primary">
                              Ver
                            </a>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>

          </div>

          <a class="carousel-control-prev" href="#carouselProductos" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
          </a>
          <a class="carousel-control-next" href="#carouselProductos" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
          </a>
        </div>

      </div>
    </div>

    <!-- FOOTER -->
    <footer class="site-footer bg-light">
      <div class="container text-center py-4">
        <p class="mb-0">&copy; <?= date('Y') ?> FarmaCuyo</p>
      </div>
    </footer>

  </div>

  <!-- scripts necesarios -->
  <script src="js/jquery-3.3.1.min.js"></script>
  <script src="js/popper.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/main.js"></script>
</body>
</html>
