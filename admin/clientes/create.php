<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db.php';

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

  // comprobar email único
  if (!$errores) {
    $st = $pdo->prepare("SELECT id FROM clientes WHERE email = :email LIMIT 1");
    $st->execute(['email' => $email]);
    if ($st->fetch()) {
      $errores[] = "Ya existe un cliente con ese email.";
    }
  }

  if (!$errores) {
    // si no cargan password, generamos una
    $hash = $password !== '' ? password_hash($password, PASSWORD_DEFAULT)
                             : password_hash(bin2hex(random_bytes(4)), PASSWORD_DEFAULT);

    $ins = $pdo->prepare("INSERT INTO clientes (nombre, email, password_hash, telefono, direccion)
                          VALUES (:n, :e, :p, :t, :d)");
    $ins->execute([
      'n' => $nombre,
      'e' => $email,
      'p' => $hash,
      't' => $telefono,
      'd' => $direccion
    ]);
    header("Location: index.php");
    exit;
  }
}

require_once __DIR__ . '/../includes/header.php';
?>
<div class="container py-4">
  <h4 class="mb-3">Nuevo cliente</h4>

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
      <input type="text" name="nombre" class="form-control" required value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Contraseña (opcional)</label>
      <input type="password" name="password" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Teléfono</label>
      <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Dirección</label>
      <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($_POST['direccion'] ?? '') ?>">
    </div>
    <button class="btn btn-primary">Guardar</button>
    <a href="index.php" class="btn btn-secondary">Volver</a>
  </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
