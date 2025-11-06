<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../db.php";

$err = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre'] ?? "");
    $descripcion = trim($_POST['descripcion'] ?? "");

    if ($nombre !== "") {
        $stmt = $pdo->prepare("INSERT INTO categorias (nombre, descripcion) VALUES (:n, :d)");
        $stmt->execute([
            "n" => $nombre,
            "d" => $descripcion
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
    <li class="breadcrumb-item active" aria-current="page">Nueva categoría</li>
  </ol>
</nav>
<h1 class="h4 mb-3">Nueva categoría</h1>

<div class="card">
  <div class="card-body">
    <?php if ($err): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>
    <form method="post" class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control" required>
      </div>
      <div class="col-md-12">
        <label class="form-label">Descripción</label>
        <textarea name="descripcion" rows="3" class="form-control"></textarea>
      </div>
      <div class="col-12">
        <button class="btn btn-primary">Guardar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
