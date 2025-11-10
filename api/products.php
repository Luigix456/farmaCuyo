<?php
require __DIR__ . "/_bootstrap.php";
// Trae todos los productos habilitados
$stmt = $pdo->query("SELECT p.id, p.nombre, p.descripcion, p.imagen, p.precio, p.stock, p.habilitado,
                            c.nombre AS categoria
                     FROM productos p
                     LEFT JOIN categorias c ON c.id = p.categoria_id
                     WHERE p.habilitado = 1
                     ORDER BY p.id DESC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
json_ok($rows);


