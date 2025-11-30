<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
$pdo = require __DIR__ . '/db.php';
$error = '';
function saveUploadedImage(): string {
    if (!isset($_FILES['image_file']) || $_FILES['image_file']['error'] !== UPLOAD_ERR_OK) {
        return '';
    }
    $base = __DIR__ . '/../assets/images/uploads/products';
    if (!is_dir($base)) {
        mkdir($base, 0775, true);
    }
    $tmp = $_FILES['image_file']['tmp_name'];
    $mime = mime_content_type($tmp);
    $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
    if (!isset($allowed[$mime])) {
        return '';
    }
    $ext = $allowed[$mime];
    $name = 'p_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    $dest = $base . '/' . $name;
    if (move_uploaded_file($tmp, $dest)) {
        return 'assets/images/uploads/products/' . $name;
    }
    return '';
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = $_POST['price'] ?? '';
    $original_price = $_POST['original_price'] !== '' ? $_POST['original_price'] : null;
    $image = saveUploadedImage();
    $category_id = $_POST['category_id'] ?? '';
    if ($name !== '' && $price !== '' && $category_id !== '' ) {
        $stmt = $pdo->prepare('INSERT INTO products (name, price, original_price, image, category_id) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$name, $price, $original_price, $image, $category_id]);
        header('Location: products.php');
        exit;
    } else {
        $error = 'Required fields are missing';
    }
}
$categories = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Add Product</title>
<link rel="icon" href="../assets/images/logos/Royal smart logo.jpg">
<style>
body{font-family:system-ui, -apple-system, Segoe UI, Roboto, Arial;background:#f5f6fa;margin:0}
.wrap{max-width:600px;margin:32px auto;background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.08);padding:24px}
.field{margin-bottom:12px}
label{display:block;font-size:13px;color:#374151;margin-bottom:6px}
input,select{width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px}
button{padding:10px 12px;border:none;border-radius:8px;background:#111827;color:#fff;font-weight:600;cursor:pointer}
.error{color:#b91c1c;font-size:13px;margin-bottom:10px}
.top a{margin-left:8px}
</style>
</head>
<body>
<div class="wrap">
  <div class="top"><h2>Add Product</h2><a href="products.php">Back</a></div>
  <?php if($error){echo '<div class="error">'.$error.'</div>';} ?>
  <form method="post" enctype="multipart/form-data">
    <div class="field"><label>Name</label><input type="text" name="name" required></div>
    <div class="field"><label>Price</label><input type="number" step="0.01" name="price" required></div>
    <div class="field"><label>Original Price</label><input type="number" step="0.01" name="original_price"></div>
    <div class="field"><label>Image Upload</label><input type="file" name="image_file" accept="image/*"></div>
    <div class="field"><label>Category</label><select name="category_id" required><?php foreach($categories as $c){echo '<option value="'.$c['id'].'">'.htmlspecialchars($c['name']).'</option>'; } ?></select></div>
    <button type="submit">Create</button>
  </form>
</div>
</body>
</html>
