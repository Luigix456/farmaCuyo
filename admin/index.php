<?php
require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/includes/header.php";
?>
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb mb-1">
    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
  </ol>
</nav>
<h1 class="h4 mb-4">Bienvenido al panel</h1>

<div class="row g-3">
  <div class="col-md-6 col-lg-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <h5 class="card-title">Usuarios</h5>
        <p class="card-text text-muted">Gestiona los usuarios del sistema.</p>
        <a href="/farmaCuyo/admin/usuarios/index.php" class="btn btn-sm btn-primary">Ir a usuarios</a>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-lg-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <h5 class="card-title">Productos</h5>
        <p class="card-text text-muted">Gestiona los productos de la farmacia.</p>
        <a href="/farmaCuyo/admin/productos/index.php" class="btn btn-sm btn-primary">Ir a productos</a>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . "/includes/footer.php"; ?>
