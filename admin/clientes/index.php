<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db.php';

$q = trim($_GET['q'] ?? '');
$params = [];
$where = '';
if ($q !== '') {
  $where = "WHERE nombre LIKE :q OR email LIKE :q OR telefono LIKE :q";
  $params[':q'] = "%{$q}%";
}

$stmt = $pdo->prepare("SELECT id, nombre, email, telefono, direccion
                       FROM clientes
                       $where
                       ORDER BY id DESC");
$stmt->execute($params);
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../includes/header.php';
?>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Clientes</h4>
    <a href="create.php" class="btn btn-sm btn-primary">Nuevo cliente</a>
  </div>

  <form class="mb-3" method="get">
    <div class="input-group">
      <input type="text" name="q" class="form-control" placeholder="Buscar por nombre, email o teléfono" value="<?= htmlspecialchars($q) ?>">
      <button class="btn btn-outline-secondary">Buscar</button>
    </div>
  </form>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover table-striped mb-0">
          <thead>
            <tr>
              <th style="width:70px;">ID</th>
              <th>Nombre</th>
              <th>Email</th>
              <th>Teléfono</th>
              <th>Dirección</th>
              <th style="width:140px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!$clientes): ?>
              <tr><td colspan="6" class="text-center py-4">No hay clientes</td></tr>
            <?php else: foreach ($clientes as $c): ?>
              <tr>
                <td>#<?= (int)$c['id'] ?></td>
                <td><?= htmlspecialchars($c['nombre']) ?></td>
                <td><a href="mailto:<?= htmlspecialchars($c['email']) ?>"><?= htmlspecialchars($c['email']) ?></a></td>
                <td><?= htmlspecialchars($c['telefono'] ?? '') ?></td>
                <td><?= htmlspecialchars($c['direccion'] ?? '') ?></td>
                <td>
                  <a href="edit.php?id=<?= (int)$c['id'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                  <a href="delete.php?id=<?= (int)$c['id'] ?>" class="btn btn-sm btn-outline-danger"
                     onclick="return confirm('¿Eliminar este cliente?')">Borrar</a>
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
