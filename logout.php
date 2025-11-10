<?php
if (session_status() === PHP_SESSION_NONE) session_start();
unset($_SESSION['cliente_id'], $_SESSION['cliente_nombre'], $_SESSION['cliente_email']);
header("Location: index.php");
exit;
