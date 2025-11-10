<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
  $del = $pdo->prepare("DELETE FROM clientes WHERE id = :id");
  $del->execute(['id' => $id]);
}

header("Location: index.php");
exit;
