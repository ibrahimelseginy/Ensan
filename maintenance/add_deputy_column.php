<?php
/**
 * Add deputy_user_id column to campaigns table
 * Run this file once in browser: http://127.0.0.1:8000/add_deputy_column.php
 */

// Database configuration
$host = '127.0.0.1';
$dbname = 'enasn';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>🔧 Adding Deputy Column to Campaigns Table</h2>";
    
    // Check if column exists
    $checkColumn = $pdo->query("SHOW COLUMNS FROM campaigns LIKE 'deputy_user_id'");
    
    if ($checkColumn->rowCount() == 0) {
        // Add deputy_user_id column
        $pdo->exec("ALTER TABLE campaigns ADD COLUMN deputy_user_id BIGINT UNSIGNED NULL AFTER manager_user_id");
        echo "<p style='color: green;'>✅ Column 'deputy_user_id' added successfully!</p>";
    } else {
        echo "<p style='color: blue;'>ℹ️ Column 'deputy_user_id' already exists.</p>";
    }
    
    // Check if deputy_photo_url column exists
    $checkPhotoColumn = $pdo->query("SHOW COLUMNS FROM campaigns LIKE 'deputy_photo_url'");
    
    if ($checkPhotoColumn->rowCount() == 0) {
        // Add deputy_photo_url column
        $pdo->exec("ALTER TABLE campaigns ADD COLUMN deputy_photo_url VARCHAR(255) NULL AFTER deputy_user_id");
        echo "<p style='color: green;'>✅ Column 'deputy_photo_url' added successfully!</p>";
    } else {
        echo "<p style='color: blue;'>ℹ️ Column 'deputy_photo_url' already exists.</p>";
    }
    
    echo "<h3>✅ Done!</h3>";
    echo "<p><a href='/dashboard'>← Go to Dashboard</a></p>";
    
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>❌ Error</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
