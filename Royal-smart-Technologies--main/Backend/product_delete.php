<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
$pdo = require __DIR__ . '/db.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id>0){
    $stmt = $pdo->prepare('DELETE FROM products WHERE id=?');
    $stmt->execute([$id]);
}
header('Location: products.php');
exit;

