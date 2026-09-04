<?php
/**
 * List all databases and find the correct one
 * Run: http://127.0.0.1:8000/find_database.php
 */

echo "<h2>🔍 البحث عن قاعدة البيانات الصحيحة</h2>";

$configs = [
    ['host' => '127.0.0.1', 'port' => '3306', 'user' => 'root', 'pass' => 'root'],
    ['host' => '127.0.0.1', 'port' => '8889', 'user' => 'root', 'pass' => 'root'],
    ['host' => 'localhost', 'port' => '3306', 'user' => 'root', 'pass' => ''],
    ['host' => '127.0.0.1', 'port' => '3306', 'user' => 'root', 'pass' => ''],
];

$foundDatabases = [];
$workingConfig = null;

foreach ($configs as $index => $config) {
    try {
        $dsn = "mysql:host={$config['host']};port={$config['port']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['user'], $config['pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<h3 style='color: green;'>✅ اتصال ناجح - {$config['host']}:{$config['port']}</h3>";
        
        // Get all databases
        $stmt = $pdo->query("SHOW DATABASES");
        $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "<h4>قواعد البيانات المتاحة:</h4>";
        echo "<ul>";
        foreach ($databases as $db) {
            // Skip system databases
            if (in_array($db, ['information_schema', 'mysql', 'performance_schema', 'sys'])) {
                continue;
            }
            
            echo "<li><strong>$db</strong>";
            
            // Check if it has users table (likely our database)
            try {
                $pdo->exec("USE `$db`");
                $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
                if ($stmt->rowCount() > 0) {
                    echo " <span style='color: green; font-weight: bold;'>← هذه على الأرجح قاعدة بيانات النظام! 🎯</span>";
                    $foundDatabases[] = [
                        'name' => $db,
                        'config' => $config
                    ];
                }
            } catch (Exception $e) {
                // Ignore
            }
            
            echo "</li>";
        }
        echo "</ul>";
        
        $workingConfig = $config;
        break; // Use first working connection
        
    } catch (PDOException $e) {
        echo "<p style='color: gray;'>⚠️ فشل الاتصال - {$config['host']}:{$config['port']}</p>";
    }
}

if (count($foundDatabases) > 0) {
    echo "<hr>";
    echo "<h2 style='color: green;'>🎉 تم العثور على قاعدة البيانات!</h2>";
    
    foreach ($foundDatabases as $db) {
        echo "<div style='background: #10b981; color: white; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
        echo "<h3>استخدم هذه الإعدادات:</h3>";
        echo "<p><strong>اسم قاعدة البيانات:</strong> {$db['name']}</p>";
        echo "<p><strong>Host:</strong> {$db['config']['host']}</p>";
        echo "<p><strong>Port:</strong> {$db['config']['port']}</p>";
        echo "<p><strong>Username:</strong> {$db['config']['user']}</p>";
        echo "<p><strong>Password:</strong> {$db['config']['pass']}</p>";
        echo "</div>";
        
        // Create admin button
        echo "<form method='GET' action='create_admin_auto.php'>";
        echo "<input type='hidden' name='dbname' value='{$db['name']}'>";
        echo "<input type='hidden' name='host' value='{$db['config']['host']}'>";
        echo "<input type='hidden' name='port' value='{$db['config']['port']}'>";
        echo "<input type='hidden' name='user' value='{$db['config']['user']}'>";
        echo "<input type='hidden' name='pass' value='{$db['config']['pass']}'>";
        echo "<button type='submit' style='padding: 15px 30px; background: #3b82f6; color: white; border: none; border-radius: 5px; font-size: 18px; cursor: pointer;'>إنشاء حساب Admin الآن →</button>";
        echo "</form>";
    }
} else {
    echo "<hr>";
    echo "<h2 style='color: red;'>❌ لم يتم العثور على قاعدة بيانات النظام</h2>";
    echo "<p>تأكد من:</p>";
    echo "<ol>";
    echo "<li>تشغيل MAMP</li>";
    echo "<li>تشغيل MySQL</li>";
    echo "<li>إنشاء قاعدة البيانات</li>";
    echo "<li>تشغيل migrations: <code>php artisan migrate</code></li>";
    echo "</ol>";
}
?>
