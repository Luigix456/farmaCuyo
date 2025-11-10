<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/header.php';

// opcional: contar registros para mostrar números
$totProductos = $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn();
$totUsuarios  = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$totCategorias= $pdo->query("SELECT COUNT(*) FROM categorias")->fetchColumn();
$totClientes  = $pdo->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
?>
<div class="container py-4">
  <h3 class="mb-4">Panel de administración</h3>

  <div class="row g-3">
    <!-- Productos -->
    <div class="col-md-3">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body">
          <h6 class="text-muted mb-1">Productos</h6>
          <h3 class="mb-3"><?= (int)$totProductos ?></h3>
          <a href="productos/index.php" class="btn btn-sm btn-primary">Ver productos</a>
        </div>
      </div>
    </div>

    <!-- Usuarios -->
    <div class="col-md-3">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body">
          <h6 class="text-muted mb-1">Usuarios</h6>
          <h3 class="mb-3"><?= (int)$totUsuarios ?></h3>
          <a href="usuarios/index.php" class="btn btn-sm btn-primary">Ver usuarios</a>
        </div>
      </div>
    </div>

    <!-- Categorías -->
    <div class="col-md-3">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body">
          <h6 class="text-muted mb-1">Categorías</h6>
          <h3 class="mb-3"><?= (int)$totCategorias ?></h3>
          <a href="categorias/index.php" class="btn btn-sm btn-primary">Ver categorías</a>
        </div>
      </div>
    </div>

    <!-- Clientes -->
    <div class="col-md-3">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body">
          <h6 class="text-muted mb-1">Clientes</h6>
          <h3 class="mb-3"><?= (int)$totClientes ?></h3>
          <a href="clientes/index.php" class="btn btn-sm btn-primary">Ver clientes</a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
