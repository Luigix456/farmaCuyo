<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$st = $pdo->prepare("SELECT * FROM clientes WHERE id = :id");
$st->execute(['id' => $id]);
$cliente = $st->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
  header("Location: index.php");
  exit;
}

$errores = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre    = trim($_POST['nombre'] ?? '');
  $email     = trim($_POST['email'] ?? '');
  $telefono  = trim($_POST['telefono'] ?? '');
  $direccion = trim($_POST['direccion'] ?? '');
  $password  = $_POST['password'] ?? '';

  if ($nombre === '' || $email === '') {
    $errores[] = "Nombre y email son obligatorios.";
  }

  // email único
  if (!$errores) {
    $stc = $pdo->prepare("SELECT id FROM clientes WHERE email = :email AND id <> :id LIMIT 1");
    $stc->execute(['email' => $email, 'id' => $id]);
    if ($stc->fetch()) {
      $errores[] = "Ya existe otro cliente con ese email.";
    }
  }

  if (!$errores) {
    if ($password !== '') {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $up = $pdo->prepare("UPDATE clientes
                           SET nombre=:n, email=:e, telefono=:t, direccion=:d, password_hash=:p
                           WHERE id=:id");
      $up->execute([
        'n' => $nombre,
        'e' => $email,
        't' => $telefono,
        'd' => $direccion,
        'p' => $hash,
        'id'=> $id
      ]);
    } else {
      $up = $pdo->prepare("UPDATE clientes
                           SET nombre=:n, email=:e, telefono=:t, direccion=:d
                           WHERE id=:id");
      $up->execute([
        'n' => $nombre,
        'e' => $email,
        't' => $telefono,
        'd' => $direccion,
        'id'=> $id
      ]);
    }

    header("Location: index.php");
    exit;
  }
}

require_once __DIR__ . '/../includes/header.php';
?>
<div class="container py-4">
  <h4 class="mb-3">Editar cliente #<?= (int)$cliente['id'] ?></h4>

  <?php if ($errores): ?>
    <div class="alert alert-danger">
      <?php foreach ($errores as $e): ?>
        <div><?= htmlspecialchars($e) ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="post">
    <div class="mb-3">
      <label class="form-label">Nombre completo</label>
      <input type="text" name="nombre" class="form-control" required value="<?= htmlspecialchars($_POST['nombre'] ?? $cliente['nombre']) ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? $cliente['email']) ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Contraseña (dejar vacío para no cambiar)</label>
      <input type="password" name="password" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Teléfono</label>
      <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($_POST['telefono'] ?? $cliente['telefono']) ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Dirección</label>
      <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($_POST['direccion'] ?? $cliente['direccion']) ?>">
    </div>
    <button class="btn btn-primary">Guardar cambios</button>
    <a href="index.php" class="btn btn-secondary">Volver</a>
  </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
