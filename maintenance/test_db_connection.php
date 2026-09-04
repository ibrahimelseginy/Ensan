<?php
/**
 * Test MySQL Connection
 * Run: http://127.0.0.1:8000/test_db_connection.php
 */

echo "<h2>🔍 اختبار الاتصال بقاعدة البيانات</h2>";

// Database configurations to try
$configs = [
    [
        'host' => '127.0.0.1',
        'port' => '3306',
        'dbname' => 'ensan',
        'username' => 'root',
        'password' => 'root'
    ],
    [
        'host' => '127.0.0.1',
        'port' => '8889',
        'dbname' => 'ensan',
        'username' => 'root',
        'password' => 'root'
    ],
    [
        'host' => 'localhost',
        'port' => '3306',
        'dbname' => 'ensan',
        'username' => 'root',
        'password' => ''
    ],
    [
        'host' => '127.0.0.1',
        'port' => '3306',
        'dbname' => 'ensan_db',
        'username' => 'root',
        'password' => 'root'
    ]
];

$connected = false;
$workingConfig = null;

foreach ($configs as $index => $config) {
    echo "<hr>";
    echo "<h3>محاولة " . ($index + 1) . ":</h3>";
    echo "<pre>";
    echo "Host: {$config['host']}\n";
    echo "Port: {$config['port']}\n";
    echo "Database: {$config['dbname']}\n";
    echo "Username: {$config['username']}\n";
    echo "</pre>";
    
    try {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['username'], $config['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<p style='color: green; font-weight: bold;'>✅ نجح الاتصال!</p>";
        
        // Test query
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>عدد المستخدمين: {$result['count']}</p>";
        
        $connected = true;
        $workingConfig = $config;
        break;
        
    } catch (PDOException $e) {
        echo "<p style='color: red;'>❌ فشل الاتصال: " . $e->getMessage() . "</p>";
    }
}

if ($connected && $workingConfig) {
    echo "<hr>";
    echo "<h2 style='color: green;'>🎉 تم العثور على الإعدادات الصحيحة!</h2>";
    echo "<h3>استخدم هذه الإعدادات في ملف .env:</h3>";
    echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>";
    echo "DB_CONNECTION=mysql\n";
    echo "DB_HOST={$workingConfig['host']}\n";
    echo "DB_PORT={$workingConfig['port']}\n";
    echo "DB_DATABASE={$workingConfig['dbname']}\n";
    echo "DB_USERNAME={$workingConfig['username']}\n";
    echo "DB_PASSWORD={$workingConfig['password']}\n";
    echo "</pre>";
    
    echo "<p><a href='/login' style='display: inline-block; padding: 10px 20px; background: #10b981; color: white; text-decoration: none; border-radius: 5px;'>← الذهاب لتسجيل الدخول</a></p>";
} else {
    echo "<hr>";
    echo "<h2 style='color: red;'>❌ لم يتم الاتصال بأي قاعدة بيانات</h2>";
    echo "<h3>تحقق من:</h3>";
    echo "<ol>";
    echo "<li>✅ MAMP مشغل</li>";
    echo "<li>✅ MySQL يعمل (الضوء أخضر)</li>";
    echo "<li>✅ قاعدة البيانات موجودة</li>";
    echo "<li>✅ اسم المستخدم وكلمة المرور صحيحة</li>";
    echo "</ol>";
}
?>
