<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db.php';

$sql = "SELECT p.id, p.nombre, p.precio, p.stock,
               c.nombre AS categoria,
               pa.nombre AS pais
        FROM productos p
        LEFT JOIN categorias c ON c.id = p.categoria_id
        LEFT JOIN paises pa ON pa.id = p.pais_id
        ORDER BY p.id DESC";
$st = $pdo->query($sql);
$rows = $st->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../includes/header.php';
?>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Productos</h4>
    <a href="create.php" class="btn btn-primary btn-sm">Nuevo producto</a>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th style="width:80px;">#</th>
              <th>Nombre</th>
              <th style="width:120px;">Precio</th>
              <th style="width:100px;">Stock</th>
              <th style="width:160px;">Categoría</th>
              <th style="width:160px;">País</th>
              <th style="width:150px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!$rows): ?>
              <tr><td colspan="7" class="text-center py-4">No hay productos.</td></tr>
            <?php else: foreach ($rows as $r): ?>
              <tr>
                <td><?= (int)$r['id'] ?></td>
                <td><?= htmlspecialchars($r['nombre']) ?></td>
                <td>$<?= number_format((float)$r['precio'], 2, ',', '.') ?></td>
                <td><?= (int)$r['stock'] ?></td>
                <td><?= htmlspecialchars($r['categoria'] ?? '—') ?></td>
                <td><?= htmlspecialchars($r['pais'] ?? '—') ?></td>
                <td>
                  <a class="btn btn-sm btn-outline-primary" href="edit.php?id=<?= (int)$r['id'] ?>">Editar</a>
                  <a class="btn btn-sm btn-outline-danger" href="delete.php?id=<?= (int)$r['id'] ?>"
                     onclick="return confirm('¿Eliminar producto?')">Borrar</a>
                </td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
