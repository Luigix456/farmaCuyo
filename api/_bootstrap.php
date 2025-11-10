<?php
// Siempre JSON
header("Content-Type: application/json; charset=utf-8");
// Sesión para carrito
if (session_status() === PHP_SESSION_NONE) session_start();
// Conexión (usa tu misma DB del admin)
require_once __DIR__ . "/../admin/db.php";

function json_ok($data = [], $extra = []) {
  echo json_encode(array_merge(["ok" => true, "data" => $data], $extra), JSON_UNESCAPED_UNICODE);
  exit;
}
function json_error($msg, $code = 400) {
  http_response_code($code);
  echo json_encode(["ok" => false, "error" => $msg], JSON_UNESCAPED_UNICODE);
  exit;
}
