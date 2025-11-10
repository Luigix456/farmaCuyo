<?php
require __DIR__ . "/_bootstrap.php";

// Validar carrito
if (empty($_SESSION['cart'])) json_error("Carrito vacío", 400);

// Datos mínimos del checkout (puedes ampliar a gusto)
$nombre   = trim($_POST['nombre']   ?? '');
$email    = trim($_POST['email']    ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$direccion= trim($_POST['direccion']?? '');

if ($nombre === '' || $email === '' || $direccion === '') json_error("Faltan datos");

// Construir items desde carrito
$ids = array_keys($_SESSION['cart']);
$in  = implode(',', array_fill(0, count($ids), '?'));
$stmt = $pdo->prepare("SELECT id, nombre, precio FROM productos WHERE id IN ($in)");
$stmt->execute($ids);
$prods = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!$prods) json_error("Productos no encontrados", 400);

$subtotal = 0;
$items = [];

foreach ($prods as $p) {
  $qty = (int)$_SESSION['cart'][$p['id']];
  $line = $qty * (float)$p['precio'];
  $subtotal += $line;
  $items[] = [
    'producto_id' => (int)$p['id'],
    'nombre' => $p['nombre'],
    'precio' => (float)$p['precio'],
    'qty' => $qty,
    'subtotal' => $line
  ];
}

// Crear pedido
$ins = $pdo->prepare("INSERT INTO pedidos (nombre, email, telefono, direccion, total) VALUES (:n,:e,:t,:d,:tot)");
$ins->execute([
  "n"=>$nombre, "e"=>$email, "t"=>$telefono, "d"=>$direccion, "tot"=>$subtotal
]);
$pedido_id = (int)$pdo->lastInsertId();

// Items
$insI = $pdo->prepare("INSERT INTO pedido_items (pedido_id, producto_id, nombre, precio, qty, subtotal)
                       VALUES (:pid,:pidr,:n,:p,:q,:s)");
foreach ($items as $it) {
  $insI->execute([
    "pid"=>$pedido_id,
    "pidr"=>$it['producto_id'],
    "n"=>$it['nombre'],
    "p"=>$it['precio'],
    "q"=>$it['qty'],
    "s"=>$it['subtotal']
  ]);
}

$_SESSION['cart'] = []; // limpiar
json_ok(["pedido_id"=>$pedido_id, "total"=>$subtotal]);
