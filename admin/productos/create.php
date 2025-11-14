<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db.php';

$errores = [];

// catálogos
$cats   = $pdo->query("SELECT id, nombre FROM categorias ORDER BY nombre")->fetchAll();
$paises = $pdo->query("SELECT id, nombre FROM paises ORDER BY nombre")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre       = trim($_POST['nombre'] ?? '');
  $descripcion  = trim($_POST['descripcion'] ?? '');
  $precio       = (float)($_POST['precio'] ?? 0);
  $stock        = (int)($_POST['stock'] ?? 0);
  $categoria_id = (int)($_POST['categoria_id'] ?? 0);
  $pais_id      = (int)($_POST['pais_id'] ?? 0);
  $imagen       = trim($_POST['imagen'] ?? '');

  if ($nombre === '' || $precio <= 0) {
    $errores[] = "Nombre y precio son obligatorios.";
  }

  if (!$errores) {
    $sql = "INSERT INTO productos (nombre, descripcion, precio, stock, categoria_id, pais_id, imagen)
            VALUES (:n, :d, :p, :s, :c, :pa, :img)";
    $st = $pdo->prepare($sql);
    $st->execute([
      'n'   => $nombre,
      'd'   => $descripcion,
      'p'   => $precio,
      's'   => $stock,
      'c'   => $categoria_id ?: null,
      'pa'  => $pais_id ?: null,
      'img' => $imagen ?: null
    ]);
    header("Location: index.php");
    exit;
  }
}

require_once __DIR__ . '/../includes/header.php';
?>
<div class="container py-4">
  <h4 class="mb-3">Nuevo producto</h4>

  <?php if ($errores): ?>
    <div class="alert alert-danger">
      <?php foreach ($errores as $e): ?><div><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="post">
    <div class="row">
      <div class="col-md-8">
        <div class="mb-3">
          <label class="form-label">Nombre</label>
          <input type="text" name="nombre" class="form-control" required value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Descripción</label>
          <textarea name="descripcion" class="form-control" rows="4"><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
        </div>
        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="form-label">Precio</label>
            <input type="number" step="0.01" name="precio" class="form-control" required value="<?= htmlspecialchars($_POST['precio'] ?? '') ?>">
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Stock</label>
            <input type="number" name="stock" class="form-control" value="<?= htmlspecialchars($_POST['stock'] ?? '0') ?>">
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Imagen (URL)</label>
            <input type="text" name="imagen" class="form-control" value="<?= htmlspecialchars($_POST['imagen'] ?? '') ?>">
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="mb-3">
          <label class="form-label">Categoría</label>
          <select name="categoria_id" class="form-select">
            <option value="">— Sin categoría —</option>
            <?php foreach($cats as $c): ?>
              <option value="<?= (int)$c['id'] ?>" <?= (($_POST['categoria_id'] ?? '')==$c['id'])?'selected':'' ?>>
                <?= htmlspecialchars($c['nombre']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">País de origen</label>
          <select name="pais_id" class="form-select">
            <option value="">— Seleccionar —</option>
            <?php foreach($paises as $pa): ?>
              <option value="<?= (int)$pa['id'] ?>" <?= (($_POST['pais_id'] ?? '')==$pa['id'])?'selected':'' ?>>
                <?= htmlspecialchars($pa['nombre']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="d-grid">
          <button class="btn btn-primary">Guardar</button>
          <a href="index.php" class="btn btn-secondary mt-2">Volver</a>
        </div>
      </div>
    </div>
  </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
