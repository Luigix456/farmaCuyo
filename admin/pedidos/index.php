<?php
// admin/pedidos/index.php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db.php';

// Traer pedidos con datos básicos del cliente
$sql = "SELECT p.id,
               p.cliente_id,
               p.fecha,
               p.total,
               p.estado,
               c.nombre AS cliente_nombre,
               c.email  AS cliente_email
        FROM pedidos p
        LEFT JOIN clientes c ON c.id = p.cliente_id
        ORDER BY p.id DESC";
$stmt = $pdo->query($sql);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../includes/header.php';
?>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Pedidos</h4>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover table-striped mb-0">
          <thead>
            <tr>
              <th style="width:70px;">#</th>
              <th>Cliente</th>
              <th>Fecha</th>
              <th>Total</th>
              <th>Estado</th>
              <th style="width:140px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!$pedidos): ?>
              <tr><td colspan="6" class="text-center py-4">No hay pedidos.</td></tr>
            <?php else: foreach ($pedidos as $p): ?>
              <tr>
                <td><?= (int)$p['id'] ?></td>
                <td>
                  <?= htmlspecialchars($p['cliente_nombre'] ?? ('Cliente #'.$p['cliente_id'])) ?><br>
                  <small class="text-muted"><?= htmlspecialchars($p['cliente_email'] ?? '') ?></small>
                </td>
                <td><?= htmlspecialchars($p['fecha']) ?></td>
                <td>$<?= number_format((float)$p['total'], 2, ',', '.') ?></td>
                <td>
                  <span class="badge bg-secondary"><?= htmlspecialchars($p['estado']) ?></span>
                </td>
                <td>
                  <a href="ver.php?id=<?= (int)$p['id'] ?>" class="btn btn-sm btn-outline-primary">Ver</a>
                  <a href="estado.php?id=<?= (int)$p['id'] ?>" class="btn btn-sm btn-outline-secondary">Estado</a>
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
