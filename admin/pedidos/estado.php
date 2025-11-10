<?php
// admin/pedidos/estado.php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$st = $pdo->prepare("SELECT id, estado FROM pedidos WHERE id = :id");
$st->execute(['id' => $id]);
$pedido = $st->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
  header("Location: index.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $estado = $_POST['estado'] ?? 'pendiente';
  $up = $pdo->prepare("UPDATE pedidos SET estado = :e WHERE id = :id");
  $up->execute([
    'e' => $estado,
    'id'=> $id
  ]);
  header("Location: ver.php?id=" . $id);
  exit;
}

require_once __DIR__ . '/../includes/header.php';
?>
<div class="container py-4">
  <h4 class="mb-3">Cambiar estado del pedido #<?= (int)$pedido['id'] ?></h4>
  <form method="post">
    <div class="mb-3">
      <label class="form-label">Estado</label>
      <select name="estado" class="form-select">
        <?php
        $estados = ['pendiente','pagado','enviado','cancelado'];
        foreach ($estados as $e): ?>
          <option value="<?= $e ?>" <?= $pedido['estado']===$e?'selected':'' ?>>
            <?= ucfirst($e) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <button class="btn btn-primary">Guardar</button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
