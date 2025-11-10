<?php
require_once __DIR__ . "/admin/db.php";
if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_SESSION['cliente_id'])) {
  header("Location: index.php");
  exit;
}

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $pass  = $_POST['password'] ?? '';
  $redirect = $_POST['redirect'] ?? 'index.php';

  if ($email === '' || $pass === '') {
    $errores[] = "Completá email y contraseña.";
  } else {
    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$cliente || !password_verify($pass, $cliente['password_hash'])) {
      $errores[] = "Email o contraseña incorrectos.";
    } else {
      // login ok
      $_SESSION['cliente_id'] = (int)$cliente['id'];
      $_SESSION['cliente_nombre'] = $cliente['nombre'];
      $_SESSION['cliente_email'] = $cliente['email'];

      // si había un producto pendiente, lo agregamos ahora
      if (!empty($_SESSION['pending_add'])) {
        $pend = $_SESSION['pending_add'];
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        $_SESSION['cart'][$pend['id']] = ($_SESSION['cart'][$pend['id']] ?? 0) + $pend['qty'];
        unset($_SESSION['pending_add']);
      }

      header("Location: " . $redirect);
      exit;
    }
  }
}

$redirect = $_GET['redirect'] ?? ($_POST['redirect'] ?? 'index.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar sesión</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <h3 class="mb-4">Iniciar sesión</h3>

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
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Contraseña</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <button class="btn btn-primary w-100">Ingresar</button>
      </form>
      <p class="mt-3">¿No tenés cuenta? <a href="register.php?redirect=<?= urlencode($redirect) ?>">Registrate</a></p>
    </div>
  </div>
</div>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
