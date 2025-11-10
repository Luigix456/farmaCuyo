<?php
// admin/pedidos/ver.php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// pedido + cliente
$st = $pdo->prepare("SELECT p.*, c.nombre AS cliente_nombre, c.email AS cliente_email
                     FROM pedidos p
                     LEFT JOIN clientes c ON c.id = p.cliente_id
                     WHERE p.id = :id");
$st->execute(['id' => $id]);
$pedido = $st->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
  header("Location: index.php");
  exit;
}

// 1) intentamos con pedido_items
$stItems = $pdo->prepare("SELECT * FROM pedido_items WHERE pedido_id = :id");
$stItems->execute(['id' => $id]);
$items = $stItems->fetchAll(PDO::FETCH_ASSOC);

// 2) si no hay, probamos pedido_detalle
$items_detalle = [];
if (!$items) {
  $stDet = $pdo->prepare("SELECT * FROM pedido_detalle WHERE pedido_id = :id");
  $stDet->execute(['id' => $id]);
  $items_detalle = $stDet->fetchAll(PDO::FETCH_ASSOC);
}

require_once __DIR__ . '/../includes/header.php';
?>
<div class="container py-4">
  <a href="index.php" class="btn btn-link">&larr; Volver</a>
  <h4 class="mb-3">Pedido #<?= (int)$pedido['id'] ?></h4>

  <div class="row mb-4">
    <div class="col-md-6">
      <div class="card mb-3">
        <div class="card-header">Cliente</div>
        <div class="card-body">
          <p class="mb-1"><strong><?= htmlspecialchars($pedido['cliente_nombre'] ?? 'Cliente #'.$pedido['cliente_id']) ?></strong></p>
          <p class="mb-0"><?= htmlspecialchars($pedido['cliente_email'] ?? '') ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card mb-3">
        <div class="card-header">Pedido</div>
        <div class="card-body">
          <p class="mb-1"><strong>Fecha:</strong> <?= htmlspecialchars($pedido['fecha']) ?></p>
          <p class="mb-1"><strong>Estado:</strong> <span class="badge bg-secondary"><?= htmlspecialchars($pedido['estado']) ?></span></p>
          <p class="mb-0"><strong>Total:</strong> $<?= number_format((float)$pedido['total'], 2, ',', '.') ?></p>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">Productos del pedido</div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table mb-0">
          <thead>
            <tr>
              <th>Producto</th>
              <th style="width:100px;">Cant.</th>
              <th style="width:120px;">Precio</th>
              <th style="width:120px;">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $totalCalc = 0;

            // caso 1: pedido_items
            if ($items) {
              foreach ($items as $it) {
                $nombre   = $it['nombre'] ?? ('Producto #'.$it['producto_id']);
                $cant     = (int)($it['qty'] ?? 0);
                $precio   = (float)($it['precio'] ?? 0);
                $subtotal = (float)($it['subtotal'] ?? ($precio * $cant));
                $totalCalc += $subtotal;
                ?>
                <tr>
                  <td><?= htmlspecialchars($nombre) ?></td>
                  <td><?= $cant ?></td>
                  <td>$<?= number_format($precio, 2, ',', '.') ?></td>
                  <td>$<?= number_format($subtotal, 2, ',', '.') ?></td>
                </tr>
                <?php
              }
            }
            // caso 2: pedido_detalle
            elseif ($items_detalle) {
              foreach ($items_detalle as $it) {
                $cant     = (int)$it['cantidad'];
                $precio   = (float)$it['precio_unitario'];
                $subtotal = (float)($it['subtotal'] ?? ($precio * $cant));
                $totalCalc += $subtotal;

                // traemos nombre del producto
                $nombre = 'Producto #'.$it['producto_id'];
                $pStmt = $pdo->prepare("SELECT nombre FROM productos WHERE id = :id");
                $pStmt->execute(['id' => $it['producto_id']]);
                if ($pRow = $pStmt->fetch(PDO::FETCH_ASSOC)) {
                  $nombre = $pRow['nombre'];
                }
                ?>
                <tr>
                  <td><?= htmlspecialchars($nombre) ?></td>
                  <td><?= $cant ?></td>
                  <td>$<?= number_format($precio, 2, ',', '.') ?></td>
                  <td>$<?= number_format($subtotal, 2, ',', '.') ?></td>
                </tr>
                <?php
              }
            } else { ?>
              <tr><td colspan="4" class="text-center py-3">Sin items.</td></tr>
            <?php } ?>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="3" class="text-end">Total calculado</th>
              <th>$<?= number_format($totalCalc, 2, ',', '.') ?></th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
