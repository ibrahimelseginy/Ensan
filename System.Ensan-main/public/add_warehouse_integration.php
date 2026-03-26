<?php
/**
 * Add Warehouse Integration System
 * Run: http://127.0.0.1:8000/add_warehouse_integration.php
 */

$dbname = $_GET['dbname'] ?? 'ensan';
$host = $_GET['host'] ?? '127.0.0.1';
$port = $_GET['port'] ?? '3306';
$user = $_GET['user'] ?? 'root';
$pass = $_GET['pass'] ?? 'root';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>🏭 نظام ربط المخازن المتكامل</h2>";
    echo "<p>قاعدة البيانات: <strong>$dbname</strong></p>";
    echo "<hr>";
    
    // ========== Update inventory_transactions table ==========
    echo "<h3>1️⃣ تحديث جدول المعاملات المخزنية</h3>";
    $transactionColumns = [
        'unit_cost' => "ALTER TABLE inventory_transactions ADD COLUMN unit_cost DECIMAL(10,2) DEFAULT 0 AFTER quantity",
        'total_cost' => "ALTER TABLE inventory_transactions ADD COLUMN total_cost DECIMAL(10,2) DEFAULT 0 AFTER unit_cost",
        'expense_id' => "ALTER TABLE inventory_transactions ADD COLUMN expense_id BIGINT UNSIGNED NULL AFTER campaign_id",
        'delegate_id' => "ALTER TABLE inventory_transactions ADD COLUMN delegate_id BIGINT UNSIGNED NULL AFTER expense_id",
        'user_id' => "ALTER TABLE inventory_transactions ADD COLUMN user_id BIGINT UNSIGNED NULL AFTER delegate_id",
        'journal_entry_id' => "ALTER TABLE inventory_transactions ADD COLUMN journal_entry_id BIGINT UNSIGNED NULL AFTER user_id",
        'transaction_date' => "ALTER TABLE inventory_transactions ADD COLUMN transaction_date DATE NULL AFTER journal_entry_id",
        'status' => "ALTER TABLE inventory_transactions ADD COLUMN status VARCHAR(255) DEFAULT 'pending' AFTER transaction_date",
        'approved_by' => "ALTER TABLE inventory_transactions ADD COLUMN approved_by BIGINT UNSIGNED NULL AFTER status",
        'approved_at' => "ALTER TABLE inventory_transactions ADD COLUMN approved_at TIMESTAMP NULL AFTER approved_by",
        'batch_number' => "ALTER TABLE inventory_transactions ADD COLUMN batch_number VARCHAR(255) NULL AFTER approved_at",
        'expiry_date' => "ALTER TABLE inventory_transactions ADD COLUMN expiry_date DATE NULL AFTER batch_number",
        'location_in_warehouse' => "ALTER TABLE inventory_transactions ADD COLUMN location_in_warehouse VARCHAR(255) NULL AFTER expiry_date"
    ];
    
    foreach ($transactionColumns as $columnName => $sql) {
        $stmt = $pdo->query("SHOW COLUMNS FROM inventory_transactions LIKE '$columnName'");
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
        $pdo->exec("ALTER TABLE inventory_transactions ADD CONSTRAINT fk_inv_expense FOREIGN KEY (expense_id) REFERENCES expenses(id) ON DELETE SET NULL");
        echo "<p style='color: green;'>✅ تم إضافة Foreign Key: expense_id</p>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate') === false) {
            echo "<p style='color: orange;'>⚠️ expense_id FK: " . $e->getMessage() . "</p>";
        }
    }
    
    try {
        $pdo->exec("ALTER TABLE inventory_transactions ADD CONSTRAINT fk_inv_delegate FOREIGN KEY (delegate_id) REFERENCES delegates(id) ON DELETE SET NULL");
        echo "<p style='color: green;'>✅ تم إضافة Foreign Key: delegate_id</p>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate') === false) {
            echo "<p style='color: orange;'>⚠️ delegate_id FK: " . $e->getMessage() . "</p>";
        }
    }
    
    try {
        $pdo->exec("ALTER TABLE inventory_transactions ADD CONSTRAINT fk_inv_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL");
        echo "<p style='color: green;'>✅ تم إضافة Foreign Key: user_id</p>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate') === false) {
            echo "<p style='color: orange;'>⚠️ user_id FK: " . $e->getMessage() . "</p>";
        }
    }
    
    try {
        $pdo->exec("ALTER TABLE inventory_transactions ADD CONSTRAINT fk_inv_journal FOREIGN KEY (journal_entry_id) REFERENCES journal_entries(id) ON DELETE SET NULL");
        echo "<p style='color: green;'>✅ تم إضافة Foreign Key: journal_entry_id</p>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate') === false) {
            echo "<p style='color: orange;'>⚠️ journal_entry_id FK: " . $e->getMessage() . "</p>";
        }
    }
    
    try {
        $pdo->exec("ALTER TABLE inventory_transactions ADD CONSTRAINT fk_inv_approved_by FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL");
        echo "<p style='color: green;'>✅ تم إضافة Foreign Key: approved_by</p>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate') === false) {
            echo "<p style='color: orange;'>⚠️ approved_by FK: " . $e->getMessage() . "</p>";
        }
    }
    
    // ========== Update items table ==========
    echo "<h3>2️⃣ تحديث جدول الأصناف</h3>";
    $itemColumns = [
        'category' => "ALTER TABLE items ADD COLUMN category VARCHAR(255) NULL AFTER unit",
        'min_stock_level' => "ALTER TABLE items ADD COLUMN min_stock_level INT DEFAULT 10 AFTER category",
        'max_stock_level' => "ALTER TABLE items ADD COLUMN max_stock_level INT NULL AFTER min_stock_level",
        'reorder_point' => "ALTER TABLE items ADD COLUMN reorder_point INT NULL AFTER max_stock_level",
        'barcode' => "ALTER TABLE items ADD COLUMN barcode VARCHAR(255) NULL UNIQUE AFTER sku",
        'image_path' => "ALTER TABLE items ADD COLUMN image_path VARCHAR(255) NULL AFTER barcode",
        'is_active' => "ALTER TABLE items ADD COLUMN is_active BOOLEAN DEFAULT TRUE AFTER image_path"
    ];
    
    foreach ($itemColumns as $columnName => $sql) {
        $stmt = $pdo->query("SHOW COLUMNS FROM items LIKE '$columnName'");
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
    
    // ========== Update warehouses table ==========
    echo "<h3>3️⃣ تحديث جدول المخازن</h3>";
    $warehouseColumns = [
        'manager_id' => "ALTER TABLE warehouses ADD COLUMN manager_id BIGINT UNSIGNED NULL AFTER location",
        'phone' => "ALTER TABLE warehouses ADD COLUMN phone VARCHAR(255) NULL AFTER manager_id",
        'address' => "ALTER TABLE warehouses ADD COLUMN address TEXT NULL AFTER phone",
        'capacity' => "ALTER TABLE warehouses ADD COLUMN capacity INT NULL AFTER address",
        'is_active' => "ALTER TABLE warehouses ADD COLUMN is_active BOOLEAN DEFAULT TRUE AFTER capacity"
    ];
    
    foreach ($warehouseColumns as $columnName => $sql) {
        $stmt = $pdo->query("SHOW COLUMNS FROM warehouses LIKE '$columnName'");
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
    
    try {
        $pdo->exec("ALTER TABLE warehouses ADD CONSTRAINT fk_warehouse_manager FOREIGN KEY (manager_id) REFERENCES users(id) ON DELETE SET NULL");
        echo "<p style='color: green;'>✅ تم إضافة Foreign Key: manager_id</p>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate') === false) {
            echo "<p style='color: orange;'>⚠️ manager_id FK: " . $e->getMessage() . "</p>";
        }
    }
    
    echo "<hr>";
    echo "<div style='background: linear-gradient(135deg, #f59e0b, #d97706); color: white; padding: 30px; border-radius: 15px;'>";
    echo "<h2>🎉 تم بنجاح!</h2>";
    echo "<p style='font-size: 18px;'>تم إضافة نظام ربط المخازن المتكامل</p>";
    
    echo "<h3>🔗 الربط مع الأقسام:</h3>";
    echo "<div style='display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin: 20px 0;'>";
    
    echo "<div style='background: rgba(255,255,255,0.1); padding: 15px; border-radius: 10px; text-align: center;'>";
    echo "<h4>💰 التبرعات</h4>";
    echo "<p style='font-size: 14px;'>استلام تبرعات عينية</p>";
    echo "</div>";
    
    echo "<div style='background: rgba(255,255,255,0.1); padding: 15px; border-radius: 10px; text-align: center;'>";
    echo "<h4>👥 المستفيدين</h4>";
    echo "<p style='font-size: 14px;'>توزيع على المستفيدين</p>";
    echo "</div>";
    
    echo "<div style='background: rgba(255,255,255,0.1); padding: 15px; border-radius: 10px; text-align: center;'>";
    echo "<h4>🏗️ المشاريع</h4>";
    echo "<p style='font-size: 14px;'>ربط بالمشاريع</p>";
    echo "</div>";
    
    echo "<div style='background: rgba(255,255,255,0.1); padding: 15px; border-radius: 10px; text-align: center;'>";
    echo "<h4>📢 الحملات</h4>";
    echo "<p style='font-size: 14px;'>ربط بالحملات</p>";
    echo "</div>";
    
    echo "<div style='background: rgba(255,255,255,0.1); padding: 15px; border-radius: 10px; text-align: center;'>";
    echo "<h4>💵 المصروفات</h4>";
    echo "<p style='font-size: 14px;'>ربط بالمشتريات</p>";
    echo "</div>";
    
    echo "<div style='background: rgba(255,255,255,0.1); padding: 15px; border-radius: 10px; text-align: center;'>";
    echo "<h4>📊 الحسابات</h4>";
    echo "<p style='font-size: 14px;'>قيود محاسبية تلقائية</p>";
    echo "</div>";
    
    echo "</div>";
    
    echo "<h3>✨ الميزات الجديدة:</h3>";
    echo "<ul>";
    echo "<li>✅ تتبع التكلفة (وحدة + إجمالي)</li>";
    echo "<li>✅ نظام الموافقات</li>";
    echo "<li>✅ تواريخ الصلاحية</li>";
    echo "<li>✅ أرقام الدفعات</li>";
    echo "<li>✅ المواقع داخل المخزن</li>";
    echo "<li>✅ حدود المخزون (أدنى/أقصى/إعادة طلب)</li>";
    echo "<li>✅ الباركود</li>";
    echo "<li>✅ صور الأصناف</li>";
    echo "<li>✅ مدير المخزن</li>";
    echo "<li>✅ سعة المخزن</li>";
    echo "</ul>";
    
    echo "</div>";
    
    echo "<p style='margin-top: 20px;'>";
    echo "<a href='/warehouses' style='display: inline-block; padding: 15px 30px; background: #3b82f6; color: white; text-decoration: none; border-radius: 5px; font-size: 18px; margin-right: 10px;'>← المخازن</a>";
    echo "<a href='/items' style='display: inline-block; padding: 15px 30px; background: #10b981; color: white; text-decoration: none; border-radius: 5px; font-size: 18px;'>← الأصناف</a>";
    echo "</p>";
    
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>❌ خطأ في الاتصال</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p><a href='find_database.php'>← البحث عن قاعدة البيانات</a></p>";
}
?>
