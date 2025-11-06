<?php
$host = "localhost";
$dbname = "farmacia_db";
$user = "root";
$pass = ""; // poné tu pass si tu XAMPP la tiene

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
