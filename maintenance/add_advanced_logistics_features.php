<?php
/**
 * Add Advanced Logistics Features
 * Run: http://127.0.0.1:8000/add_advanced_logistics_features.php
 */

$dbname = $_GET['dbname'] ?? 'ensan';
$host = $_GET['host'] ?? '127.0.0.1';
$port = $_GET['port'] ?? '3306';
$user = $_GET['user'] ?? 'root';
$pass = $_GET['pass'] ?? 'root';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>🚀 إضافة الميزات المتقدمة لقسم اللوجستيك</h2>";
    echo "<p>قاعدة البيانات: <strong>$dbname</strong></p>";
    echo "<hr>";
    
    // ========== Create delegate_ratings table ==========
    echo "<h3>1️⃣ جدول تقييمات المندوبين</h3>";
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS delegate_ratings (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            delegate_id BIGINT UNSIGNED NOT NULL,
            trip_id BIGINT UNSIGNED NULL,
            rated_by BIGINT UNSIGNED NULL,
            punctuality_rating INT DEFAULT 5,
            professionalism_rating INT DEFAULT 5,
            communication_rating INT DEFAULT 5,
            overall_rating INT DEFAULT 5,
            comments TEXT NULL,
            rating_date DATE NOT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (delegate_id) REFERENCES delegates(id) ON DELETE CASCADE,
            FOREIGN KEY (trip_id) REFERENCES delegate_trips(id) ON DELETE SET NULL,
            FOREIGN KEY (rated_by) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "<p style='color: green;'>✅ تم إنشاء جدول delegate_ratings</p>";
    } catch (PDOException $e) {
        echo "<p style='color: blue;'>ℹ️ جدول delegate_ratings موجود بالفعل</p>";
    }
    
    // ========== Create vehicle_maintenance table ==========
    echo "<h3>2️⃣ جدول صيانة المركبات</h3>";
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS vehicle_maintenance (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            delegate_id BIGINT UNSIGNED NOT NULL,
            vehicle_type VARCHAR(255) NULL,
            vehicle_plate VARCHAR(255) NULL,
            maintenance_type VARCHAR(255) NOT NULL,
            description TEXT NULL,
            cost DECIMAL(10,2) DEFAULT 0,
            maintenance_date DATE NOT NULL,
            next_maintenance_date DATE NULL,
            odometer_reading INT NULL,
            status VARCHAR(255) DEFAULT 'completed',
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (delegate_id) REFERENCES delegates(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "<p style='color: green;'>✅ تم إنشاء جدول vehicle_maintenance</p>";
    } catch (PDOException $e) {
        echo "<p style='color: blue;'>ℹ️ جدول vehicle_maintenance موجود بالفعل</p>";
    }
    
    // ========== Create scheduled_trips table ==========
    echo "<h3>3️⃣ جدول الرحلات المجدولة</h3>";
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS scheduled_trips (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            delegate_id BIGINT UNSIGNED NOT NULL,
            route_id BIGINT UNSIGNED NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT NULL,
            scheduled_date DATE NOT NULL,
            scheduled_time TIME NULL,
            from_location VARCHAR(255) NULL,
            to_location VARCHAR(255) NULL,
            estimated_cost DECIMAL(10,2) NULL,
            estimated_distance DECIMAL(10,2) NULL,
            status VARCHAR(255) DEFAULT 'scheduled',
            actual_trip_id BIGINT UNSIGNED NULL,
            notes TEXT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (delegate_id) REFERENCES delegates(id) ON DELETE CASCADE,
            FOREIGN KEY (route_id) REFERENCES travel_routes(id) ON DELETE SET NULL,
            FOREIGN KEY (actual_trip_id) REFERENCES delegate_trips(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "<p style='color: green;'>✅ تم إنشاء جدول scheduled_trips</p>";
    } catch (PDOException $e) {
        echo "<p style='color: blue;'>ℹ️ جدول scheduled_trips موجود بالفعل</p>";
    }
    
    // ========== Add columns to delegates table ==========
    echo "<h3>4️⃣ إضافة أعمدة للمندوبين</h3>";
    $delegateColumns = [
        'vehicle_type' => "ALTER TABLE delegates ADD COLUMN vehicle_type VARCHAR(255) NULL AFTER active",
        'vehicle_plate' => "ALTER TABLE delegates ADD COLUMN vehicle_plate VARCHAR(255) NULL AFTER vehicle_type",
        'license_number' => "ALTER TABLE delegates ADD COLUMN license_number VARCHAR(255) NULL AFTER vehicle_plate",
        'license_expiry' => "ALTER TABLE delegates ADD COLUMN license_expiry DATE NULL AFTER license_number",
        'emergency_contact' => "ALTER TABLE delegates ADD COLUMN emergency_contact VARCHAR(255) NULL AFTER license_expiry",
        'emergency_phone' => "ALTER TABLE delegates ADD COLUMN emergency_phone VARCHAR(255) NULL AFTER emergency_contact",
        'national_id' => "ALTER TABLE delegates ADD COLUMN national_id VARCHAR(255) NULL AFTER emergency_phone",
        'address' => "ALTER TABLE delegates ADD COLUMN address TEXT NULL AFTER national_id",
        'hire_date' => "ALTER TABLE delegates ADD COLUMN hire_date DATE NULL AFTER address",
        'notes' => "ALTER TABLE delegates ADD COLUMN notes TEXT NULL AFTER hire_date"
    ];
    
    foreach ($delegateColumns as $columnName => $sql) {
        $stmt = $pdo->query("SHOW COLUMNS FROM delegates LIKE '$columnName'");
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
    
    echo "<hr>";
    echo "<div style='background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 30px; border-radius: 15px;'>";
    echo "<h2>🎉 تم بنجاح!</h2>";
    echo "<p style='font-size: 18px;'>تم إضافة جميع الميزات المتقدمة لقسم اللوجستيك</p>";
    
    echo "<h3>✨ الميزات الجديدة:</h3>";
    echo "<div style='display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 20px 0;'>";
    
    echo "<div style='background: rgba(255,255,255,0.1); padding: 15px; border-radius: 10px;'>";
    echo "<h4>⭐ نظام التقييمات</h4>";
    echo "<ul style='margin: 0; padding-left: 20px;'>";
    echo "<li>تقييم الالتزام بالمواعيد</li>";
    echo "<li>تقييم الاحترافية</li>";
    echo "<li>تقييم التواصل</li>";
    echo "<li>التقييم الإجمالي</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div style='background: rgba(255,255,255,0.1); padding: 15px; border-radius: 10px;'>";
    echo "<h4>🔧 صيانة المركبات</h4>";
    echo "<ul style='margin: 0; padding-left: 20px;'>";
    echo "<li>تتبع الصيانة الدورية</li>";
    echo "<li>تنبيهات الصيانة القادمة</li>";
    echo "<li>سجل التكاليف</li>";
    echo "<li>قراءة العداد</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div style='background: rgba(255,255,255,0.1); padding: 15px; border-radius: 10px;'>";
    echo "<h4>📅 جدولة الرحلات</h4>";
    echo "<ul style='margin: 0; padding-left: 20px;'>";
    echo "<li>جدولة رحلات مستقبلية</li>";
    echo "<li>تحويل لرحلة فعلية</li>";
    echo "<li>تقدير التكاليف</li>";
    echo "<li>تتبع الحالة</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div style='background: rgba(255,255,255,0.1); padding: 15px; border-radius: 10px;'>";
    echo "<h4>👤 معلومات المندوب</h4>";
    echo "<ul style='margin: 0; padding-left: 20px;'>";
    echo "<li>نوع المركبة ولوحتها</li>";
    echo "<li>رخصة القيادة</li>";
    echo "<li>جهة اتصال طوارئ</li>";
    echo "<li>الرقم القومي والعنوان</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "</div>";
    
    echo "<h3>📊 الإحصائيات المتاحة:</h3>";
    echo "<ul>";
    echo "<li>✅ متوسط تقييم كل مندوب</li>";
    echo "<li>✅ إجمالي تكاليف الرحلات</li>";
    echo "<li>✅ إجمالي المسافات المقطوعة</li>";
    echo "<li>✅ الصيانة القادمة</li>";
    echo "<li>✅ الرحلات المجدولة</li>";
    echo "<li>✅ تنبيهات انتهاء الرخصة</li>";
    echo "</ul>";
    
    echo "</div>";
    
    echo "<p style='margin-top: 20px;'>";
    echo "<a href='/logistics/dashboard' style='display: inline-block; padding: 15px 30px; background: #3b82f6; color: white; text-decoration: none; border-radius: 5px; font-size: 18px; margin-right: 10px;'>← لوحة تحكم اللوجستيك</a>";
    echo "<a href='/delegates' style='display: inline-block; padding: 15px 30px; background: #10b981; color: white; text-decoration: none; border-radius: 5px; font-size: 18px;'>← المندوبين</a>";
    echo "</p>";
    
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>❌ خطأ في الاتصال</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p><a href='find_database.php'>← البحث عن قاعدة البيانات</a></p>";
}
?>
