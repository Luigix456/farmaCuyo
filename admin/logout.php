<?php
session_start();
session_destroy();
header("Location: /farmaCuyo/admin/login.php");
exit;
