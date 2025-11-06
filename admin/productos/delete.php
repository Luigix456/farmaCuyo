<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../db.php";

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("DELETE FROM productos WHERE id = :id");
$stmt->execute(["id" => $id]);

header("Location: index.php");
exit;
