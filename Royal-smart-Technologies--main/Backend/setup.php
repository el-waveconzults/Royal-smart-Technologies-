<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'SmartRoyal_Tech';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $pdo->exec("\n        CREATE TABLE IF NOT EXISTS categories (\n            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,\n            name VARCHAR(100) NOT NULL UNIQUE,\n            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n    ");

    $pdo->exec("\n         CREATE TABLE IF NOT EXISTS products (\n             id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,\n             name VARCHAR(255) NOT NULL,\n             price DECIMAL(10,2) NOT NULL,\n             original_price DECIMAL(10,2) NULL,\n             image VARCHAR(255) NULL,\n             category_id INT UNSIGNED NOT NULL,\n             created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,\n             INDEX idx_category_id (category_id),\n             CONSTRAINT fk_products_category FOREIGN KEY (category_id)\n                 REFERENCES categories(id)\n                 ON DELETE RESTRICT\n                 ON UPDATE CASCADE\n         ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n     ");

    $pdo->exec("\n        CREATE TABLE IF NOT EXISTS admins (\n            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,\n            username VARCHAR(50) NOT NULL UNIQUE,\n            password_hash VARCHAR(255) NOT NULL,\n            email VARCHAR(255) NULL UNIQUE,\n            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n    ");

    $hash = password_hash('12345', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT IGNORE INTO admins (username, password_hash, email) VALUES (?, ?, ?)');
    $stmt->execute(['Admin', $hash, 'admin@example.com']);

    $pdo->exec("INSERT IGNORE INTO categories (name) VALUES\n        ('Our Categories'),\n        ('New Arrivals'),\n        ('Flash Sale'),\n        ('Top Selling Product'),\n        ('Best Selling This Week')\n    ");

    echo 'Database setup complete';
} catch (PDOException $e) {
    http_response_code(500);
    echo 'Error: ' . $e->getMessage();
}

