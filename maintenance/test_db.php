<?php
$host = '127.0.0.1';
$db   = 'ensan_db';
$user = 'root';
$pass = '';

try {
    $mysqli = new mysqli($host, $user, $pass, $db);
    if ($mysqli->connect_error) {
        die("Connect Error (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
    }
    echo "Database connection successful!\n";
    $result = $mysqli->query("SHOW TABLES");
    echo "Tables in database:\n";
    while ($row = $result->fetch_row()) {
        echo "- " . $row[0] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
