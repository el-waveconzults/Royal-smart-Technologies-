<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
$pdo = require __DIR__ . '/db.php';
$categories = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
$products = $pdo->query('SELECT p.id, p.name, p.price, p.original_price, p.image, c.name AS category_name FROM products p JOIN categories c ON p.category_id=c.id ORDER BY p.created_at DESC')->fetchAll();
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Manage Products</title>
<link rel="icon" href="../assets/images/logos/Royal smart logo.jpg">
<style>
body{font-family:system-ui, -apple-system, Segoe UI, Roboto, Arial;background:#f5f6fa;margin:0}
.wrap{max-width:1000px;margin:32px auto;background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.08);padding:24px}
.top{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
table{width:100%;border-collapse:collapse}
th,td{border-bottom:1px solid #e5e7eb;padding:10px;text-align:left}
.btn{display:inline-block;padding:8px 12px;border-radius:8px;text-decoration:none}
.primary{background:#111827;color:#fff}
.danger{background:#b91c1c;color:#fff}
.secondary{background:#374151;color:#fff}
.actions a{margin-right:8px}
form.inline{display:flex;gap:8px;align-items:flex-end;margin-top:16px}
input,select{padding:8px 10px;border:1px solid #d1d5db;border-radius:8px}
</style>
</head>
<body>
<div class="wrap">
  <div class="top"><h2>Products</h2><div><a class="btn secondary" href="AdminDashboard.php">Dashboard</a> <a class="btn primary" href="product_create.php">Add Product</a> <a class="btn" href="logout.php">Logout</a></div></div>
  <table>
    <thead><tr><th>Name</th><th>Price</th><th>Original</th><th>Category</th><th>Image</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach($products as $p){$img=$p['image']?('<img src="../'.htmlspecialchars($p['image']).'" style="width:60px;height:60px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb">'):'';echo '<tr><td>'.htmlspecialchars($p['name']).'</td><td>'.number_format((float)$p['price'],2).'</td><td>'.($p['original_price']!==null?number_format((float)$p['original_price'],2):'').'</td><td>'.htmlspecialchars($p['category_name']).'</td><td>'.$img.'</td><td class="actions"><a class="btn primary" href="product_edit.php?id='.$p['id'].'">Edit</a><a class="btn danger" href="product_delete.php?id='.$p['id'].'" onclick="return confirm(\'Delete this product?\')">Delete</a></td></tr>';}
      ?>
    </tbody>
  </table>
</div>
</body>
</html>
