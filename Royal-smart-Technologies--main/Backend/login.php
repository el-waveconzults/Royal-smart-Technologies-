<?php
session_start();
if (isset($_SESSION['admin_id'])) {
    header('Location: AdminDashboard.php');
    exit;
}
$error = '';
$pdo = require __DIR__ . '/db.php';
try {
    $pdo->query('SELECT 1 FROM admins LIMIT 1');
} catch (PDOException $e) {
    if ($e->getCode() === '42S02') {
        $pdo->exec("CREATE TABLE IF NOT EXISTS admins (\n            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,\n            username VARCHAR(50) NOT NULL UNIQUE,\n            password_hash VARCHAR(255) NOT NULL,\n            email VARCHAR(255) NULL UNIQUE,\n            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $hash = password_hash('12345', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT IGNORE INTO admins (username, password_hash, email) VALUES (?, ?, ?)');
        $stmt->execute(['Admin', $hash, 'admin@example.com']);
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($username !== '' && $password !== '') {
        $stmt = $pdo->prepare('SELECT id, username, password_hash FROM admins WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: AdminDashboard.php');
            exit;
        }
    }
    $error = 'Invalid credentials';
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="icon" href="../assets/images/logos/Royal smart logo.jpg">
    <style>
        body{font-family:system-ui, -apple-system, Segoe UI, Roboto, Arial;display:flex;align-items:center;justify-content:center;height:100vh;background:#f5f6fa;margin:0}
        .card{width:360px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.08);padding:24px}
        .title{font-size:20px;font-weight:600;margin-bottom:16px}
        .field{margin-bottom:12px}
        label{display:block;font-size:13px;color:#374151;margin-bottom:6px}
        input{width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px}
        button{width:100%;padding:10px 12px;border:none;border-radius:8px;background:#111827;color:#fff;font-weight:600;cursor:pointer}
        .error{color:#b91c1c;font-size:13px;margin-bottom:10px}
        .link{display:block;text-align:center;margin-top:12px;font-size:13px}
    </style>
</head>
<body>
    <div class="card">
        <div class="title">Admin Login</div>
        <?php if($error){echo '<div class="error">'.$error.'</div>';}
        ?>
        <form method="post" action="login.php">
            <div class="field">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="field">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        
    </div>
</body>
</html>
