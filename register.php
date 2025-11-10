<?php
require_once __DIR__ . "/admin/db.php";
if (session_status() === PHP_SESSION_NONE) session_start();

// si ya está logueado, lo mandamos a inicio
if (isset($_SESSION['cliente_id'])) {
  header("Location: index.php");
  exit;
}

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre   = trim($_POST['nombre'] ?? '');
  $email    = trim($_POST['email'] ?? '');
  $pass     = $_POST['password'] ?? '';
  $pass2    = $_POST['password2'] ?? '';
  $telefono = trim($_POST['telefono'] ?? '');
  $direccion= trim($_POST['direccion'] ?? '');
  $redirect = $_POST['redirect'] ?? 'index.php';

  if ($nombre === '' || $email === '' || $pass === '' || $pass2 === '') {
    $errores[] = "Todos los campos con * son obligatorios.";
  }
  if ($pass !== $pass2) {
    $errores[] = "Las contraseñas no coinciden.";
  }

  // ¿ya existe el email?
  if (!$errores) {
    $stmt = $pdo->prepare("SELECT id FROM clientes WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
      $errores[] = "Ya existe un cliente con ese email.";
    }
  }

  if (!$errores) {
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $ins = $pdo->prepare("INSERT INTO clientes (nombre, email, password_hash, telefono, direccion) 
                          VALUES (:n,:e,:p,:t,:d)");
    $ins->execute([
      'n' => $nombre,
      'e' => $email,
      'p' => $hash,
      't' => $telefono,
      'd' => $direccion
    ]);
    $cliente_id = (int)$pdo->lastInsertId();

    // loguear automáticamente
    $_SESSION['cliente_id'] = $cliente_id;
    $_SESSION['cliente_nombre'] = $nombre;
    $_SESSION['cliente_email'] = $email;

    // si había un producto pendiente de agregar antes de registrarse
    if (!empty($_SESSION['pending_add'])) {
      $pend = $_SESSION['pending_add'];
      // inicializar carrito si no estaba
      if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
      $_SESSION['cart'][$pend['id']] = ($_SESSION['cart'][$pend['id']] ?? 0) + $pend['qty'];
      unset($_SESSION['pending_add']);
    }

    header("Location: " . $redirect);
    exit;
  }
}

$redirect = $_GET['redirect'] ?? ($_POST['redirect'] ?? 'index.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro de cliente</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <h3 class="mb-4">Crear cuenta</h3>

      <?php if ($errores): ?>
        <div class="alert alert-danger">
          <?php foreach ($errores as $e): ?>
            <div><?= htmlspecialchars($e) ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="post">
        <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
        <div class="mb-3">
          <label class="form-label">Nombre completo *</label>
          <input type="text" name="nombre" class="form-control" required value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Email *</label>
          <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Contraseña *</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Repetir contraseña *</label>
          <input type="password" name="password2" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Teléfono</label>
          <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Dirección</label>
          <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($_POST['direccion'] ?? '') ?>">
        </div>
        <button class="btn btn-primary w-100">Registrarme</button>
      </form>

      <p class="mt-3">¿Ya tenés cuenta? <a href="login.php?redirect=<?= urlencode($redirect) ?>">Iniciar sesión</a></p>
    </div>
  </div>
</div>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
