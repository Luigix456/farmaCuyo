<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../db.php";

$roles = $pdo->query("SELECT * FROM roles ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$err = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre'] ?? "");
    $email = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";
    $rol_id = (int)($_POST['rol_id'] ?? 1);

    if ($nombre !== "" && $email !== "" && $password !== "") {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre_completo, email, password, rol_id, estado) VALUES (:n, :e, :p, :r, 1)");
        $stmt->execute([
            "n" => $nombre,
            "e" => $email,
            "p" => $hash,
            "r" => $rol_id
        ]);
        header("Location: index.php");
        exit;
    } else {
        $err = "Completa todos los campos.";
    }
}

require_once __DIR__ . "/../includes/header.php";
?>
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb mb-1">
    <li class="breadcrumb-item"><a href="index.php">Users</a></li>
    <li class="breadcrumb-item active" aria-current="page">New user</li>
  </ol>
</nav>
<h1 class="h4 mb-3">New user</h1>

<div class="card">
  <div class="card-body">
    <?php if ($err): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>
    <form method="post" class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Nombre completo</label>
        <input type="text" name="nombre" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Contraseña</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Rol</label>
        <select name="rol_id" class="form-select">
          <?php foreach ($roles as $r): ?>
            <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12">
        <button class="btn btn-primary">Guardar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>
<?php require_once __DIR__ . "/../includes/footer.php"; ?>
