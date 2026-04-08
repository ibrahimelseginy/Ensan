<?php
/**
 * Add Enhanced Donation System with Treasury
 * Run: http://127.0.0.1:8000/add_enhanced_donation_system.php
 */

$dbname = $_GET['dbname'] ?? 'ensan';
$host = $_GET['host'] ?? '127.0.0.1';
$port = $_GET['port'] ?? '3306';
$user = $_GET['user'] ?? 'root';
$pass = $_GET['pass'] ?? 'root';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>💰 نظام التبرعات المتطور مع الخزينة</h2>";
    echo "<p>قاعدة البيانات: <strong>$dbname</strong></p>";
    echo "<hr>";
    
    // ========== Create treasuries table ==========
    echo "<h3>1️⃣ إنشاء جدول الخزائن</h3>";
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS treasuries (
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
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (manager_id) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "<p style='color: green;'>✅ تم إنشاء جدول treasuries</p>";
    } catch (PDOException $e) {
        echo "<p style='color: blue;'>ℹ️ جدول treasuries موجود بالفعل</p>";
    }
    
    // ========== Create treasury_transactions table ==========
    echo "<h3>2️⃣ إنشاء جدول حركات الخزينة</h3>";
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS treasury_transactions (
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
            FOREIGN KEY (treasury_id) REFERENCES treasuries(id) ON DELETE CASCADE,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
            FOREIGN KEY (donation_id) REFERENCES donations(id) ON DELETE SET NULL,
            FOREIGN KEY (journal_entry_id) REFERENCES journal_entries(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "<p style='color: green;'>✅ تم إنشاء جدول treasury_transactions</p>";
    } catch (PDOException $e) {
        echo "<p style='color: blue;'>ℹ️ جدول treasury_transactions موجود بالفعل</p>";
    }
    
    // ========== Add columns to donations table ==========
    echo "<h3>3️⃣ تحديث جدول التبرعات</h3>";
    $donationColumns = [
        'treasury_id' => "ALTER TABLE donations ADD COLUMN treasury_id BIGINT UNSIGNED NULL AFTER warehouse_id",
        'item_id' => "ALTER TABLE donations ADD COLUMN item_id BIGINT UNSIGNED NULL AFTER treasury_id",
        'quantity' => "ALTER TABLE donations ADD COLUMN quantity DECIMAL(10,2) NULL AFTER item_id",
        'created_by' => "ALTER TABLE donations ADD COLUMN created_by BIGINT UNSIGNED NULL AFTER received_at",
        'auto_added_to_inventory' => "ALTER TABLE donations ADD COLUMN auto_added_to_inventory BOOLEAN DEFAULT FALSE AFTER created_by"
    ];
    
    foreach ($donationColumns as $columnName => $sql) {
        $stmt = $pdo->query("SHOW COLUMNS FROM donations LIKE '$columnName'");
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
    
    // Add foreign keys
    try {
        $pdo->exec("ALTER TABLE donations ADD CONSTRAINT fk_donations_treasury FOREIGN KEY (treasury_id) REFERENCES treasuries(id) ON DELETE SET NULL");
        echo "<p style='color: green;'>✅ تم إضافة Foreign Key: treasury_id</p>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate') === false) {
            echo "<p style='color: orange;'>⚠️ treasury_id FK</p>";
        }
    }
    
    try {
        $pdo->exec("ALTER TABLE donations ADD CONSTRAINT fk_donations_item FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE SET NULL");
        echo "<p style='color: green;'>✅ تم إضافة Foreign Key: item_id</p>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate') === false) {
            echo "<p style='color: orange;'>⚠️ item_id FK</p>";
        }
    }
    
    try {
        $pdo->exec("ALTER TABLE donations ADD CONSTRAINT fk_donations_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL");
        echo "<p style='color: green;'>✅ تم إضافة Foreign Key: created_by</p>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate') === false) {
            echo "<p style='color: orange;'>⚠️ created_by FK</p>";
        }
    }
    
    // ========== Create default treasury ==========
    echo "<h3>4️⃣ إنشاء خزينة افتراضية</h3>";
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM treasuries");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] == 0) {
            $pdo->exec("INSERT INTO treasuries (name, code, description, currency, opening_balance, current_balance, is_active, created_at, updated_at) 
                       VALUES ('الخزينة الرئيسية', 'MAIN-001', 'الخزينة الرئيسية للمؤسسة', 'EGP', 0, 0, TRUE, NOW(), NOW())");
            echo "<p style='color: green;'>✅ تم إنشاء الخزينة الرئيسية</p>";
        } else {
            echo "<p style='color: blue;'>ℹ️ توجد خزائن بالفعل</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color: orange;'>⚠️ " . $e->getMessage() . "</p>";
    }
    
    echo "<hr>";
    echo "<div style='background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 30px; border-radius: 15px;'>";
    echo "<h2>🎉 تم بنجاح!</h2>";
    echo "<p style='font-size: 18px;'>تم تفعيل نظام التبرعات المتطور</p>";
    
    echo "<h3>✨ الميزات الجديدة:</h3>";
    echo "<div style='display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 20px 0;'>";
    
    echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px;'>";
    echo "<h4>💵 تبرعات نقدية</h4>";
    echo "<ul style='margin: 0; padding-left: 20px;'>";
    echo "<li>اختيار الخزينة</li>";
    echo "<li>إضافة تلقائية لرصيد الخزينة</li>";
    echo "<li>تتبع حركات الخزينة</li>";
    echo "<li>قيود محاسبية تلقائية</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px;'>";
    echo "<h4>📦 تبرعات عينية</h4>";
    echo "<ul style='margin: 0; padding-left: 20px;'>";
    echo "<li>اختيار المخزن</li>";
    echo "<li>اختيار الصنف</li>";
    echo "<li>تحديد الكمية</li>";
    echo "<li>إضافة تلقائية للمخزون</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "</div>";
    
    echo "<h3>🔄 سير العمل:</h3>";
    echo "<ol style='font-size: 16px;'>";
    echo "<li><strong>تبرع نقدي:</strong> اختر نوع التبرع → أدخل المبلغ → اختر الخزينة → يُضاف تلقائياً لرصيد الخزينة</li>";
    echo "<li><strong>تبرع عيني:</strong> اختر نوع التبرع → اختر الصنف → أدخل الكمية → اختر المخزن → يُضاف تلقائياً للمخزون</li>";
    echo "</ol>";
    
    echo "</div>";
    
    echo "<p style='margin-top: 20px;'>";
    echo "<a href='/donations/create' style='display: inline-block; padding: 15px 30px; background: #3b82f6; color: white; text-decoration: none; border-radius: 5px; font-size: 18px; margin-right: 10px;'>← إضافة تبرع جديد</a>";
    echo "<a href='/treasuries' style='display: inline-block; padding: 15px 30px; background: #10b981; color: white; text-decoration: none; border-radius: 5px; font-size: 18px;'>← الخزائن</a>";
    echo "</p>";
    
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>❌ خطأ في الاتصال</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p><a href='find_database.php'>← البحث عن قاعدة البيانات</a></p>";
}
?>
