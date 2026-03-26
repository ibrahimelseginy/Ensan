<?php
/**
 * Auto-detect database and create treasuries tables
 */

// Try to read from .env file
$envFile = __DIR__ . '/../.env';
$dbname = 'ensan_db';
$host = '127.0.0.1';
$port = '3306';
$user = 'root';
$pass = 'root';

if (file_exists($envFile)) {
    $env = file_get_contents($envFile);
    if (preg_match('/DB_DATABASE=(.+)/', $env, $matches)) {
        $dbname = trim($matches[1]);
    }
    if (preg_match('/DB_HOST=(.+)/', $env, $matches)) {
        $host = trim($matches[1]);
    }
    if (preg_match('/DB_PORT=(.+)/', $env, $matches)) {
        $port = trim($matches[1]);
    }
    if (preg_match('/DB_USERNAME=(.+)/', $env, $matches)) {
        $user = trim($matches[1]);
    }
    if (preg_match('/DB_PASSWORD=(.+)/', $env, $matches)) {
        $pass = trim($matches[1]);
    }
}

// Allow override from URL
$dbname = $_GET['dbname'] ?? $dbname;
$host = $_GET['host'] ?? $host;
$port = $_GET['port'] ?? $port;
$user = $_GET['user'] ?? $user;
$pass = $_GET['pass'] ?? $pass;

echo "<!DOCTYPE html>";
echo "<html dir='rtl'>";
echo "<head><meta charset='UTF-8'><title>إنشاء جداول الخزائن</title></head>";
echo "<body style='font-family: Arial; padding: 20px; background: #f5f5f5;'>";

echo "<div style='max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>";

echo "<h1 style='color: #10b981;'>🏦 إنشاء جداول الخزائن</h1>";
echo "<p>قاعدة البيانات: <strong>$dbname</strong></p>";
echo "<p>الخادم: <strong>$host:$port</strong></p>";
echo "<hr>";

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color: green;'>✅ تم الاتصال بقاعدة البيانات بنجاح</p>";
    echo "<hr>";
    
    // Create treasuries table
    echo "<h3>1️⃣ إنشاء جدول treasuries</h3>";
    $sql = "CREATE TABLE IF NOT EXISTS treasuries (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        code VARCHAR(255) UNIQUE NOT NULL,
        description TEXT NULL,
        manager_id BIGINT UNSIGNED NULL,
        location VARCHAR(255) NULL,
        currency VARCHAR(255) DEFAULT 'EGP',
        opening_balance DECIMAL(15,2) DEFAULT 0,
        current_balance DECIMAL(15,2) DEFAULT 0,
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "<p style='color: green;'>✅ تم إنشاء جدول treasuries</p>";
    
    // Create treasury_transactions table
    echo "<h3>2️⃣ إنشاء جدول treasury_transactions</h3>";
    $sql = "CREATE TABLE IF NOT EXISTS treasury_transactions (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        treasury_id BIGINT UNSIGNED NOT NULL,
        type VARCHAR(255) NOT NULL,
        amount DECIMAL(15,2) NOT NULL,
        currency VARCHAR(255) DEFAULT 'EGP',
        description TEXT NULL,
        reference VARCHAR(255) NULL,
        transaction_date DATE NOT NULL,
        created_by BIGINT UNSIGNED NULL,
        donation_id BIGINT UNSIGNED NULL,
        expense_id BIGINT UNSIGNED NULL,
        journal_entry_id BIGINT UNSIGNED NULL,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL,
        FOREIGN KEY (treasury_id) REFERENCES treasuries(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "<p style='color: green;'>✅ تم إنشاء جدول treasury_transactions</p>";
    
    // Add columns to donations table
    echo "<h3>3️⃣ تحديث جدول donations</h3>";
    
    $columns = [
        'treasury_id' => "ALTER TABLE donations ADD COLUMN treasury_id BIGINT UNSIGNED NULL",
        'item_id' => "ALTER TABLE donations ADD COLUMN item_id BIGINT UNSIGNED NULL",
        'quantity' => "ALTER TABLE donations ADD COLUMN quantity DECIMAL(10,2) NULL",
        'created_by' => "ALTER TABLE donations ADD COLUMN created_by BIGINT UNSIGNED NULL",
        'auto_added_to_inventory' => "ALTER TABLE donations ADD COLUMN auto_added_to_inventory BOOLEAN DEFAULT FALSE"
    ];
    
    foreach ($columns as $name => $sql) {
        $check = $pdo->query("SHOW COLUMNS FROM donations LIKE '$name'");
        if ($check->rowCount() == 0) {
            try {
                $pdo->exec($sql);
                echo "<p style='color: green;'>✅ تم إضافة عمود: $name</p>";
            } catch (PDOException $e) {
                echo "<p style='color: orange;'>⚠️ $name: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p style='color: blue;'>ℹ️ العمود $name موجود بالفعل</p>";
        }
    }
    
    // Create default treasury
    echo "<h3>4️⃣ إنشاء خزينة افتراضية</h3>";
    $check = $pdo->query("SELECT COUNT(*) as count FROM treasuries");
    $result = $check->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] == 0) {
        $pdo->exec("INSERT INTO treasuries (name, code, description, currency, opening_balance, current_balance, is_active, created_at, updated_at) 
                   VALUES ('الخزينة الرئيسية', 'MAIN-001', 'الخزينة الرئيسية للمؤسسة', 'EGP', 0, 0, TRUE, NOW(), NOW())");
        echo "<p style='color: green;'>✅ تم إنشاء الخزينة الرئيسية</p>";
    } else {
        echo "<p style='color: blue;'>ℹ️ توجد خزائن بالفعل ($result[count] خزينة)</p>";
    }
    
    echo "<hr>";
    echo "<div style='background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 30px; border-radius: 10px; text-align: center;'>";
    echo "<h2>🎉 تم بنجاح!</h2>";
    echo "<p style='font-size: 18px;'>تم إنشاء جداول الخزائن بنجاح</p>";
    echo "<div style='margin-top: 20px;'>";
    echo "<a href='/treasuries/dashboard' style='display: inline-block; padding: 15px 30px; background: white; color: #10b981; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 5px;'>لوحة تحكم الخزائن</a>";
    echo "<a href='/treasuries' style='display: inline-block; padding: 15px 30px; background: rgba(255,255,255,0.2); color: white; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 5px;'>قائمة الخزائن</a>";
    echo "</div>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style='background: #fee; border: 2px solid #f00; padding: 20px; border-radius: 10px;'>";
    echo "<h2 style='color: red;'>❌ خطأ في الاتصال</h2>";
    echo "<p><strong>الرسالة:</strong> " . $e->getMessage() . "</p>";
    echo "<hr>";
    echo "<h3>💡 الحلول المقترحة:</h3>";
    echo "<ol>";
    echo "<li>تأكد من أن MAMP يعمل</li>";
    echo "<li>تأكد من أن MySQL يعمل</li>";
    echo "<li>تحقق من اسم قاعدة البيانات</li>";
    echo "<li>تحقق من اسم المستخدم وكلمة المرور</li>";
    echo "</ol>";
    echo "<p><strong>جرب:</strong></p>";
    echo "<ul>";
    echo "<li><a href='?dbname=ensan'>استخدام ensan</a></li>";
    echo "<li><a href='?dbname=ensan_db'>استخدام ensan_db</a></li>";
    echo "<li><a href='?dbname=laravel'>استخدام laravel</a></li>";
    echo "</ul>";
    echo "</div>";
}

echo "</div>";
echo "</body>";
echo "</html>";
?>
