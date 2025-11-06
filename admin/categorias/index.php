<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../db.php";
require_once __DIR__ . "/../includes/header.php";

$stmt = $pdo->query("SELECT * FROM categorias ORDER BY id DESC");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb mb-1">
    <li class="breadcrumb-item"><a href="/farmaCuyo/admin/index.php">Home</a></li>
    <li class="breadcrumb-item active" aria-current="page">Categorías</li>
  </ol>
</nav>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Categorías</h1>
  <a href="create.php" class="btn btn-primary btn-sm">
    <i class="bi bi-plus-lg"></i> Nueva categoría
  </a>
</div>

<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr class="text-muted small">
            <th style="width:60px;">ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th style="width:180px;" class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($categorias as $c): ?>
          <tr>
            <td><?= $c['id'] ?></td>
            <td class="fw-semibold"><?= htmlspecialchars($c['nombre']) ?></td>
            <td><?= htmlspecialchars($c['descripcion']) ?></td>
            <td class="text-end">
              <a href="edit.php?id=<?= $c['id'] ?>" class="btn btn-info btn-sm text-white">
                <i class="bi bi-pencil-square me-1"></i> Editar
              </a>
              <a href="delete.php?id=<?= $c['id'] ?>" class="btn btn-danger btn-sm"
                 onclick="return confirm('¿Eliminar categoría?')">
                <i class="bi bi-trash me-1"></i> Eliminar
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($categorias)): ?>
          <tr><td colspan="4" class="text-center text-muted py-4">No hay categorías cargadas.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
