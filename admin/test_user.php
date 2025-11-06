<?php
require_once __DIR__ . "/db.php";

$stmt = $pdo->query("SELECT id, nombre_completo, email, password, estado FROM usuarios");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>";
print_r($rows);
echo "</pre>";
