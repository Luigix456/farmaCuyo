<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../db.php";

$categorias = $pdo->query("SELECT * FROM categorias ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$err = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre'] ?? "");
    $descripcion = trim($_POST['descripcion'] ?? "");
    $imagen = trim($_POST['imagen'] ?? ""); // NUEVO
    $categoria_id = !empty($_POST['categoria_id']) ? (int)$_POST['categoria_id'] : null;
    $precio = (float)($_POST['precio'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $habilitado = isset($_POST['habilitado']) ? 1 : 0;

    if ($nombre !== "") {
        $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, imagen, categoria_id, precio, stock, habilitado) 
                               VALUES (:n, :d, :img, :c, :p, :s, :h)");
        $stmt->execute([
            "n" => $nombre,
            "d" => $descripcion,
            "img" => $imagen,
            "c" => $categoria_id,
            "p" => $precio,
            "s" => $stock,
            "h" => $habilitado
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
    <li class="breadcrumb-item"><a href="index.php">Products</a></li>
    <li class="breadcrumb-item active" aria-current="page">New product</li>
  </ol>
</nav>
<h1 class="h4 mb-3">New product</h1>

<div class="card">
  <div class="card-body">
    <?php if ($err): ?><div class="alert alert-danger"><?= htmlspecialchars($err) ?></div><?php endif; ?>
    <form method="post" class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Categoría</label>
        <select name="categoria_id" class="form-select">
          <option value="">-- Sin categoría --</option>
          <?php foreach ($categorias as $c): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-12">
        <label class="form-label">Descripción</label>
        <textarea name="descripcion" class="form-control" rows="3"></textarea>
      </div>
      <div class="col-md-12">
        <label class="form-label">URL / ruta de imagen</label>
        <input type="text" name="imagen" class="form-control" placeholder="/farmaCuyo/images/mi-producto.jpg">
      </div>
      <div class="col-md-4">
        <label class="form-label">Precio</label>
        <input type="number" step="0.01" name="precio" class="form-control" value="0">
      </div>
      <div class="col-md-4">
        <label class="form-label">Stock</label>
        <input type="number" name="stock" class="form-control" value="0">
      </div>
      <div class="col-md-4 d-flex align-items-center">
        <div class="form-check mt-4">
          <input class="form-check-input" type="checkbox" name="habilitado" id="habilitado" checked>
          <label class="form-check-label" for="habilitado">Habilitado</label>
        </div>
      </div>
      <div class="col-12">
        <button class="btn btn-primary">Guardar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>
<?php require_once __DIR__ . "/../includes/footer.php"; ?>
