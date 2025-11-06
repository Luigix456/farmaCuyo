<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../db.php";

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = :id");
$stmt->execute(["id" => $id]);
$categoria = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$categoria) {
    die("Categoría no encontrada");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre'] ?? "");
    $descripcion = trim($_POST['descripcion'] ?? "");

    if ($nombre !== "") {
        $upd = $pdo->prepare("UPDATE categorias SET nombre = :n, descripcion = :d WHERE id = :id");
        $upd->execute([
            "n" => $nombre,
            "d" => $descripcion,
            "id" => $id
        ]);
        header("Location: index.php");
        exit;
    } else {
        $err = "El nombre es obligatorio.";
    }
}

require_once __DIR__ . "/../includes/header.php";
?>
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb mb-1">
    <li class="breadcrumb-item"><a href="index.php">Categorías</a></li>
    <li class="breadcrumb-item active" aria-current="page">Editar categoría</li>
  </ol>
</nav>
<h1 class="h4 mb-3">Editar categoría</h1>

<div class="card">
  <div class="card-body">
    <?php if (!empty($err)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>
    <form method="post" class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control"
               value="<?= htmlspecialchars($categoria['nombre']) ?>" required>
      </div>
      <div class="col-md-12">
        <label class="form-label">Descripción</label>
        <textarea name="descripcion" rows="3" class="form-control"><?= htmlspecialchars($categoria['descripcion']) ?></textarea>
      </div>
      <div class="col-12">
        <button class="btn btn-primary">Guardar cambios</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
