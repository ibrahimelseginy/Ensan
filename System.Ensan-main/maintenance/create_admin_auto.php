<?php
/**
 * Create Admin with auto-detected database
 */

$dbname = $_GET['dbname'] ?? '';
$host = $_GET['host'] ?? '127.0.0.1';
$port = $_GET['port'] ?? '3306';
$user = $_GET['user'] ?? 'root';
$pass = $_GET['pass'] ?? 'root';

if (!$dbname) {
    die("<h2>❌ خطأ: لم يتم تحديد قاعدة البيانات</h2><p><a href='find_database.php'>← العودة</a></p>");
}

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>🔧 إنشاء حساب مدير النظام</h2>";
    echo "<p>قاعدة البيانات: <strong>$dbname</strong></p>";
    
    // Check if admin role exists
    $stmt = $pdo->query("SELECT * FROM roles WHERE `key` = 'admin' LIMIT 1");
    $adminRole = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$adminRole) {
        $pdo->exec("INSERT INTO roles (name, `key`, created_at, updated_at) VALUES ('مدير النظام', 'admin', NOW(), NOW())");
        $adminRoleId = $pdo->lastInsertId();
        echo "<p style='color: green;'>✅ تم إنشاء دور مدير النظام</p>";
    } else {
        $adminRoleId = $adminRole['id'];
        echo "<p style='color: blue;'>ℹ️ دور مدير النظام موجود (ID: {$adminRoleId})</p>";
    }
    
    // Check if admin user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute(['admin@ensan.local']);
    $adminUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $hashedPassword = password_hash('admin123', PASSWORD_BCRYPT);
    
    if (!$adminUser) {
        $stmt = $pdo->prepare("
            INSERT INTO users (name, email, password, is_employee, active, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute(['مدير النظام', 'admin@ensan.local', $hashedPassword, 1, 1]);
        $adminUserId = $pdo->lastInsertId();
        echo "<p style='color: green;'>✅ تم إنشاء حساب مدير النظام</p>";
    } else {
        $adminUserId = $adminUser['id'];
        $stmt = $pdo->prepare("UPDATE users SET password = ?, active = 1 WHERE id = ?");
        $stmt->execute([$hashedPassword, $adminUserId]);
        echo "<p style='color: blue;'>ℹ️ تم تحديث كلمة المرور</p>";
    }
    
    // Attach role
    $stmt = $pdo->prepare("SELECT * FROM role_user WHERE user_id = ? AND role_id = ?");
    $stmt->execute([$adminUserId, $adminRoleId]);
    
    if (!$stmt->fetch()) {
        $pdo->exec("INSERT INTO role_user (user_id, role_id) VALUES ($adminUserId, $adminRoleId)");
        echo "<p style='color: green;'>✅ تم ربط الدور</p>";
    }
    
    // Give all permissions
    $stmt = $pdo->query("SELECT id FROM permissions");
    $permissions = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($permissions) > 0) {
        foreach ($permissions as $permissionId) {
            $stmt = $pdo->prepare("SELECT * FROM permission_role WHERE permission_id = ? AND role_id = ?");
            $stmt->execute([$permissionId, $adminRoleId]);
            
            if (!$stmt->fetch()) {
                $pdo->exec("INSERT INTO permission_role (permission_id, role_id) VALUES ($permissionId, $adminRoleId)");
            }
        }
        echo "<p style='color: green;'>✅ تم منح جميع الصلاحيات (" . count($permissions) . ")</p>";
    }
    
    echo "<hr>";
    echo "<div style='background: #10b981; color: white; padding: 20px; border-radius: 10px;'>";
    echo "<h2>🎉 تم بنجاح!</h2>";
    echo "<h3>بيانات تسجيل الدخول:</h3>";
    echo "<p style='font-size: 20px;'><strong>البريد:</strong> admin@ensan.local</p>";
    echo "<p style='font-size: 20px;'><strong>كلمة المرور:</strong> admin123</p>";
    echo "</div>";
    
    echo "<p style='margin-top: 20px;'><a href='/login' style='display: inline-block; padding: 15px 30px; background: #3b82f6; color: white; text-decoration: none; border-radius: 5px; font-size: 18px;'>← تسجيل الدخول الآن</a></p>";
    
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>❌ خطأ</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
