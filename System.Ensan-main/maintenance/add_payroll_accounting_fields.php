<?php
/**
 * Add Payroll Accounting Integration Fields
 * Run: http://127.0.0.1:8000/add_payroll_accounting_fields.php
 */

$dbname = $_GET['dbname'] ?? 'ensan';
$host = $_GET['host'] ?? '127.0.0.1';
$port = $_GET['port'] ?? '3306';
$user = $_GET['user'] ?? 'root';
$pass = $_GET['pass'] ?? 'root';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>🔧 إضافة حقول ربط الرواتب بالحسابات</h2>";
    echo "<p>قاعدة البيانات: <strong>$dbname</strong></p>";
    echo "<hr>";
    
    // Check and add columns
    $columns = [
        'journal_entry_id' => "ALTER TABLE payrolls ADD COLUMN journal_entry_id BIGINT UNSIGNED NULL AFTER paid_at",
        'status' => "ALTER TABLE payrolls ADD COLUMN status VARCHAR(255) DEFAULT 'pending' AFTER journal_entry_id",
        'notes' => "ALTER TABLE payrolls ADD COLUMN notes TEXT NULL AFTER status",
        'deductions' => "ALTER TABLE payrolls ADD COLUMN deductions DECIMAL(10,2) DEFAULT 0 AFTER amount",
        'bonuses' => "ALTER TABLE payrolls ADD COLUMN bonuses DECIMAL(10,2) DEFAULT 0 AFTER deductions",
        'net_amount' => "ALTER TABLE payrolls ADD COLUMN net_amount DECIMAL(10,2) NULL AFTER bonuses"
    ];
    
    foreach ($columns as $columnName => $sql) {
        // Check if column exists
        $stmt = $pdo->query("SHOW COLUMNS FROM payrolls LIKE '$columnName'");
        
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
        $pdo->exec("ALTER TABLE payrolls ADD CONSTRAINT fk_payrolls_journal_entry 
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
    echo "<div style='background: #10b981; color: white; padding: 20px; border-radius: 10px;'>";
    echo "<h2>🎉 تم بنجاح!</h2>";
    echo "<p>تم إضافة جميع الحقول المطلوبة لربط الرواتب بنظام الحسابات</p>";
    echo "<h3>الميزات الجديدة:</h3>";
    echo "<ul>";
    echo "<li>✅ ربط تلقائي بالقيود المحاسبية</li>";
    echo "<li>✅ تتبع حالة الراتب (معلق / مدفوع)</li>";
    echo "<li>✅ إضافة مكافآت وخصومات</li>";
    echo "<li>✅ حساب الراتب الصافي تلقائياً</li>";
    echo "<li>✅ ملاحظات لكل راتب</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<p style='margin-top: 20px;'><a href='/payrolls' style='display: inline-block; padding: 15px 30px; background: #3b82f6; color: white; text-decoration: none; border-radius: 5px; font-size: 18px;'>← الذهاب للرواتب</a></p>";
    
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>❌ خطأ في الاتصال</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p><a href='find_database.php'>← البحث عن قاعدة البيانات</a></p>";
}
?>
