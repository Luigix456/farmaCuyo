<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../db.php";

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
$stmt->execute(["id" => $id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Usuario no encontrado.");
}

$roles = $pdo->query("SELECT * FROM roles ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre'] ?? "");
    $email = trim($_POST['email'] ?? "");
    $rol_id = (int)($_POST['rol_id'] ?? 1);
    $estado = isset($_POST['estado']) ? 1 : 0;

    if (!empty($_POST['password'])) {
        $hash = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $sql = "UPDATE usuarios SET nombre_completo=:n, email=:e, rol_id=:r, estado=:est, password=:p WHERE id=:id";
        $params = ["n"=>$nombre,"e"=>$email,"r"=>$rol_id,"est"=>$estado,"p"=>$hash,"id"=>$id];
    } else {
        $sql = "UPDATE usuarios SET nombre_completo=:n, email=:e, rol_id=:r, estado=:est WHERE id=:id";
        $params = ["n"=>$nombre,"e"=>$email,"r"=>$rol_id,"est"=>$estado,"id"=>$id];
    }
    $upd = $pdo->prepare($sql);
    $upd->execute($params);

    header("Location: index.php");
    exit;
}

require_once __DIR__ . "/../includes/header.php";
?>
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb mb-1">
    <li class="breadcrumb-item"><a href="index.php">Users</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit user</li>
  </ol>
</nav>
<h1 class="h4 mb-3">Edit user</h1>

<div class="card">
  <div class="card-body">
    <form method="post" class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Nombre completo</label>
        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($user['nombre_completo']) ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Contraseña (dejar vacío para no cambiar)</label>
        <input type="password" name="password" class="form-control">
      </div>
      <div class="col-md-4">
        <label class="form-label">Rol</label>
        <select name="rol_id" class="form-select">
          <?php foreach ($roles as $r): ?>
            <option value="<?= $r['id'] ?>" <?= $r['id']==$user['rol_id'] ? 'selected':'' ?>>
              <?= htmlspecialchars($r['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2 d-flex align-items-center">
        <div class="form-check mt-4">
          <input class="form-check-input" type="checkbox" name="estado" id="estado" <?= $user['estado'] ? 'checked':''; ?>>
          <label class="form-check-label" for="estado">Activo</label>
        </div>
      </div>
      <div class="col-12">
        <button class="btn btn-primary">Guardar cambios</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>
<?php require_once __DIR__ . "/../includes/footer.php"; ?>
