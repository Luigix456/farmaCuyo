<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!doctype html>
<html lang="es">
  <head>
    <title>FarmaCuyo - Admin</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- estilos del proyecto -->
    <link rel="stylesheet" href="/farmaCuyo/css/bootstrap.min.css">
    <link rel="stylesheet" href="/farmaCuyo/css/style.css">
  </head>
  <body>
    <!-- navbar simple arriba -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-0">
      <div class="container">
        <a class="navbar-brand" href="/farmaCuyo/admin/index.php">FarmaCuyo Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNav">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item"><a class="nav-link" href="/farmaCuyo/admin/usuarios/">Usuarios</a></li>
            <li class="nav-item"><a class="nav-link" href="/farmaCuyo/admin/productos/">Productos</a></li>
            <li class="nav-item"><a class="nav-link" href="/farmaCuyo/admin/categorias/">Categorías</a></li>
            <li class="nav-item"><a class="nav-link" href="/farmaCuyo/admin/clientes/">Clientes</a></li>
          </ul>
          <span class="navbar-text text-white me-2">
            <?= htmlspecialchars($_SESSION["user_nombre"] ?? "") ?>
          </span>
          <a href="/farmaCuyo/admin/logout.php" class="btn btn-outline-light btn-sm">Salir</a>
        </div>
      </div>
    </nav>

    <!-- contenido -->
    <div class="site-section py-4">
      <div class="container">
