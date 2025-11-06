<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../db.php";
require_once __DIR__ . "/../includes/header.php";

$stmt = $pdo->query("SELECT p.*, c.nombre AS categoria FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id ORDER BY p.id DESC");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="/farmaCuyo/admin/index.php">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Products</li>
      </ol>
    </nav>
    <h1 class="h4 mb-0">All products</h1>
  </div>
  <div class="d-flex gap-2">
    <a href="create.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> Add product</a>
    <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-download me-1"></i> Export</button>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="mb-3 d-flex align-items-center gap-2">
      <input type="text" class="form-control" placeholder="Search for products">
      <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-funnel"></i></button>
      <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-three-dots-vertical"></i></button>
    </div>

    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr class="text-muted small">
            <th><input class="form-check-input" type="checkbox"></th>
            <th>IMAGE</th>
            <th>NAME</th>
            <th>CATEGORY</th>
            <th>PRICE</th>
            <th>STOCK</th>
            <th class="text-end">ACTIONS</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($productos as $p): ?>
          <tr>
            <td><input class="form-check-input" type="checkbox"></td>
            <td>
              <?php if (!empty($p['imagen'])): ?>
                <img src="<?= htmlspecialchars($p['imagen']) ?>" alt="" style="width:48px;height:48px;object-fit:cover;border-radius:6px;">
              <?php else: ?>
                <span class="text-muted small">No img</span>
              <?php endif; ?>
            </td>
            <td>
              <div class="fw-semibold"><?= htmlspecialchars($p['nombre']) ?></div>
              <div class="text-muted small"><?= $p['habilitado'] ? 'Visible' : 'Oculto' ?></div>
            </td>
            <td><?= htmlspecialchars($p['categoria'] ?? 'Sin categoría') ?></td>
            <td>$<?= number_format($p['precio'], 2, ',', '.') ?></td>
            <td><?= (int)$p['stock'] ?></td>
            <td class="text-end">
              <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-info btn-sm text-white"><i class="bi bi-pencil-square me-1"></i> Edit</a>
              <a href="delete.php?id=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar producto?')">
                <i class="bi bi-trash me-1"></i> Delete
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($productos)): ?>
          <tr><td colspan="7" class="text-center text-muted py-4">No hay productos.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
