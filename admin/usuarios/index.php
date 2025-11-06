<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../db.php";
require_once __DIR__ . "/../includes/header.php";

$stmt = $pdo->query("SELECT u.*, r.nombre AS rol FROM usuarios u LEFT JOIN roles r ON u.rol_id = r.id");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Usuarios</h2>
  <a href="create.php" class="btn btn-primary">Nuevo usuario</a>
</div>
<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th>ID</th>
      <th>Nombre completo</th>
      <th>Email</th>
      <th>Rol</th>
      <th>Estado</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($usuarios as $u): ?>
      <tr>
        <td><?= $u["id"] ?></td>
        <td><?= htmlspecialchars($u["nombre_completo"]) ?></td>
        <td><?= htmlspecialchars($u["email"]) ?></td>
        <td><?= htmlspecialchars($u["rol"]) ?></td>
        <td><?= $u["estado"] ? "Activo" : "Inactivo" ?></td>
        <td>
          <a href="edit.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
          <a href="delete.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar?')">Eliminar</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php require_once __DIR__ . "/../includes/footer.php"; ?>
