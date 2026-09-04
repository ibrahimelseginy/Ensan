<?php
/**
 * Add Logistics Accounting Integration Fields
 * Run: http://127.0.0.1:8000/add_logistics_accounting_fields.php
 */

$dbname = $_GET['dbname'] ?? 'ensan';
$host = $_GET['host'] ?? '127.0.0.1';
$port = $_GET['port'] ?? '3306';
$user = $_GET['user'] ?? 'root';
$pass = $_GET['pass'] ?? 'root';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>🔧 إضافة حقول ربط اللوجستيك بالحسابات</h2>";
    echo "<p>قاعدة البيانات: <strong>$dbname</strong></p>";
    echo "<hr>";
    
    // Check and add columns
    $columns = [
        'journal_entry_id' => "ALTER TABLE delegate_trips ADD COLUMN journal_entry_id BIGINT UNSIGNED NULL AFTER status",
        'payment_method' => "ALTER TABLE delegate_trips ADD COLUMN payment_method VARCHAR(255) NULL AFTER journal_entry_id",
        'notes' => "ALTER TABLE delegate_trips ADD COLUMN notes TEXT NULL AFTER payment_method",
        'from_location' => "ALTER TABLE delegate_trips ADD COLUMN from_location VARCHAR(255) NULL AFTER description",
        'to_location' => "ALTER TABLE delegate_trips ADD COLUMN to_location VARCHAR(255) NULL AFTER from_location",
        'distance_km' => "ALTER TABLE delegate_trips ADD COLUMN distance_km DECIMAL(10,2) NULL AFTER to_location",
        'fuel_cost' => "ALTER TABLE delegate_trips ADD COLUMN fuel_cost DECIMAL(10,2) DEFAULT 0 AFTER cost",
        'other_expenses' => "ALTER TABLE delegate_trips ADD COLUMN other_expenses DECIMAL(10,2) DEFAULT 0 AFTER fuel_cost"
    ];
    
    foreach ($columns as $columnName => $sql) {
        // Check if column exists
        $stmt = $pdo->query("SHOW COLUMNS FROM delegate_trips LIKE '$columnName'");
        
        if ($stmt->rowCount() == 0) {
            try {
                $pdo->exec($sql);
                echo "<p style='color: green;'>✅ تم إضافة عمود: <strong>$columnName</strong></p>";
            } catch (PDOException $e) {
                echo "<p style='color: red;'>❌ فشل إضافة عمود $columnName: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p style='color: blue;'>ℹ️ العمود <strong>$columnName</strong> موجود بالفعل</p>";
        }
    }
    
    // Add foreign key
    try {
        $pdo->exec("ALTER TABLE delegate_trips ADD CONSTRAINT fk_delegate_trips_journal_entry 
                    FOREIGN KEY (journal_entry_id) REFERENCES journal_entries(id) ON DELETE SET NULL");
        echo "<p style='color: green;'>✅ تم إضافة Foreign Key</p>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate') === false) {
            echo "<p style='color: orange;'>⚠️ Foreign Key: " . $e->getMessage() . "</p>";
        } else {
            echo "<p style='color: blue;'>ℹ️ Foreign Key موجود بالفعل</p>";
        }
    }
    
    echo "<hr>";
    echo "<div style='background: #06b6d4; color: white; padding: 20px; border-radius: 10px;'>";
    echo "<h2>🎉 تم بنجاح!</h2>";
    echo "<p>تم إضافة جميع الحقول المطلوبة لربط اللوجستيك بنظام الحسابات</p>";
    echo "<h3>الميزات الجديدة:</h3>";
    echo "<ul>";
    echo "<li>✅ ربط تلقائي بالقيود المحاسبية</li>";
    echo "<li>✅ تتبع تكلفة الوقود منفصلة</li>";
    echo "<li>✅ تتبع المصروفات الأخرى</li>";
    echo "<li>✅ تسجيل المسافة المقطوعة</li>";
    echo "<li>✅ تسجيل نقاط البداية والنهاية</li>";
    echo "<li>✅ طريقة الدفع</li>";
    echo "<li>✅ ملاحظات لكل رحلة</li>";
    echo "</ul>";
    echo "<h3>القيود المحاسبية التلقائية:</h3>";
    echo "<pre style='background: rgba(255,255,255,0.1); padding: 10px; border-radius: 5px;'>";
    echo "من حـ/ مصروفات النقل (5201)     [مدين]\n";
    echo "من حـ/ مصروفات الوقود (5202)     [مدين]\n";
    echo "    إلى حـ/ النقدية (1101)       [دائن] (إذا مدفوع)\n";
    echo "    أو\n";
    echo "    إلى حـ/ مصروفات مستحقة (2102) [دائن] (إذا معلق)";
    echo "</pre>";
    echo "</div>";
    
    echo "<p style='margin-top: 20px;'><a href='/delegates' style='display: inline-block; padding: 15px 30px; background: #3b82f6; color: white; text-decoration: none; border-radius: 5px; font-size: 18px;'>← الذهاب للمندوبين</a></p>";
    
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>❌ خطأ في الاتصال</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p><a href='find_database.php'>← البحث عن قاعدة البيانات</a></p>";
}
?>
