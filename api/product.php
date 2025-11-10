<?php
require __DIR__ . "/_bootstrap.php";
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) json_error("ID inválido");
$stmt = $pdo->prepare("SELECT p.id, p.nombre, p.descripcion, p.imagen, p.precio, p.stock, p.habilitado,
                              c.nombre AS categoria
                       FROM productos p
                       LEFT JOIN categorias c ON c.id = p.categoria_id
                       WHERE p.id = :id AND p.habilitado = 1");
$stmt->execute(["id" => $id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) json_error("No existe", 404);
json_ok($row);
