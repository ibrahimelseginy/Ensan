<?php
$host = '127.0.0.1';
$db   = 'ensan_db';
$user = 'root';
$pass = '';

try {
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    echo "PDO connection successful!\n";
    $stmt = $pdo->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        echo "- " . $row[0] . "\n";
    }
} catch (PDOException $e) {
    echo "PDO Error: " . $e->getMessage();
}
