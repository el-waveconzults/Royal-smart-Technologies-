<?php
header('Content-Type: application/json');
$pdo = require __DIR__ . '/db.php';
$stmt = $pdo->query('SELECT p.id, p.name, p.price, p.original_price, p.image, p.category_id, c.name AS category_name FROM products p JOIN categories c ON p.category_id=c.id ORDER BY p.created_at DESC');
echo json_encode($stmt->fetchAll());

