<?php
session_start();
require_once __DIR__ . "/db.php";

$error = "";
$debug_info = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email_in = trim($_POST["email"] ?? "");
    $pass_in  = $_POST["password"] ?? ($_POST["contrasena"] ?? "");

    // Traemos todos los usuarios activos
    $stmt = $pdo->query("SELECT id, nombre_completo, email, password, rol_id, estado FROM usuarios WHERE estado = 1");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $email_in_norm = strtolower($email_in);
    $user_found = null;

    foreach ($usuarios as $u) {
        if (strtolower(trim($u["email"])) === $email_in_norm) {
            $user_found = $u;
            break;
        }
    }

    if ($user_found) {
        $ok = false;

        // 1) puerta de desarrollo: si pone 123456 lo dejamos pasar
        if ($pass_in === '123456') {
            $ok = true;
        } else {
            // 2) intento normal con password_verify
            if (password_verify($pass_in, $user_found["password"])) {
                $ok = true;
            } else {
                // 3) por si en tu base quedó en texto plano
                if ($pass_in === $user_found["password"]) {
                    $ok = true;
                }
            }
        }

        if ($ok) {
            $_SESSION["user_id"] = $user_found["id"];
            $_SESSION["user_nombre"] = $user_found["nombre_completo"];
            $_SESSION["user_rol"] = $user_found["rol_id"];
            header("Location: index.php");
            exit;
        } else {
            $error = "Usuario o contraseña incorrectos";
        }
    } else {
        $error = "Usuario o contraseña incorrectos";
    }

    // debug visible
    $debug_info .= "Email recibido: '" . $email_in . "'\n";
    $debug_info .= "Password longitud: " . strlen($pass_in) . "\n";
    $debug_info .= "Usuarios en BD:\n";
    foreach ($usuarios as $u) {
        $debug_info .= "- " . $u["email"] . "\n";
    }
}
?>
<!doctype html>
<html lang="es">
  <head>
    <title>Login - FarmaCuyo</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/farmaCuyo/css/bootstrap.min.css">
    <link rel="stylesheet" href="/farmaCuyo/css/style.css">
  </head>
  <body class="bg-light">
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-md-5">
          <div class="card shadow-sm">
            <div class="card-body">
              <h2 class="h4 mb-4 text-center">Acceso al sistema</h2>
              <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
              <?php endif; ?>
              <form method="POST">
                <div class="mb-3">
                  <label class="form-label">Email</label>
                  <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Contraseña</label>
                  <input type="password" name="password" class="form-control" required>
                </div>
                <button class="btn btn-success w-100">INGRESAR</button>
              </form>
            </div>
          </div>
          <p class="text-center mt-3">
            <a href="/farmaCuyo/index.html">← Volver a la tienda</a>
          </p>

          <?php if (!empty($debug_info)): ?>
          <pre style="background:#f8f9fa;border:1px solid #dee2e6;padding:10px;margin-top:15px;font-size:12px;white-space:pre-wrap;">
<?= htmlspecialchars($debug_info) ?>
          </pre>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <script src="/farmaCuyo/js/bootstrap.min.js"></script>
  </body>
</html>
