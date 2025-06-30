<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config/database.php';

echo "<h2>Admin User Setup</h2>";

// Default admin credentials
$admin_email = "admin@dormmate.com";
$admin_password = "admin123";  // You should change this to a secure password
$admin_first_name = "System";
$admin_last_name = "Administrator";
$admin_birthday = "1990-01-01";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    if (isset($_POST['create_admin'])) {
        // Use form data if provided, otherwise use defaults
        $email = !empty($_POST['admin_email']) ? trim($_POST['admin_email']) : $admin_email;
        $password = !empty($_POST['admin_password']) ? $_POST['admin_password'] : $admin_password;
        $first_name = !empty($_POST['admin_first_name']) ? trim($_POST['admin_first_name']) : $admin_first_name;
        $last_name = !empty($_POST['admin_last_name']) ? trim($_POST['admin_last_name']) : $admin_last_name;
        $birthday = !empty($_POST['admin_birthday']) ? $_POST['admin_birthday'] : $admin_birthday;
        
        // Check if admin already exists
        $checkQuery = "SELECT id FROM users WHERE email = :email OR role = 'admin'";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindParam(':email', $email);
        $checkStmt->execute();
        
        if ($checkStmt->rowCount() > 0) {
            echo "<div style='background: #f8d7da; padding: 15px; border-radius: 8px; margin: 20px 0; color: #721c24;'>";
            echo "<strong>Admin user already exists!</strong> Check the existing users below.";
            echo "</div>";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert admin user
            $insertQuery = "INSERT INTO users (first_name, last_name, email, password, role, birthday) 
                           VALUES (:first_name, :last_name, :email, :password, 'admin', :birthday)";
            
            $insertStmt = $db->prepare($insertQuery);
            $insertStmt->bindParam(':first_name', $first_name);
            $insertStmt->bindParam(':last_name', $last_name);
            $insertStmt->bindParam(':email', $email);
            $insertStmt->bindParam(':password', $hashed_password);
            $insertStmt->bindParam(':birthday', $birthday);
            
            if ($insertStmt->execute()) {
                echo "<div style='background: #d4edda; padding: 15px; border-radius: 8px; margin: 20px 0; color: #155724;'>";
                echo "<h3>✅ Admin User Created Successfully!</h3>";
                echo "<p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>";
                echo "<p><strong>Password:</strong> " . htmlspecialchars($password) . "</p>";
                echo "<p><strong>Name:</strong> " . htmlspecialchars($first_name . ' ' . $last_name) . "</p>";
                echo "<p style='color: #d9534f;'><strong>⚠️ Important:</strong> Please change the default password after first login!</p>";
                echo "</div>";
            } else {
                echo "<div style='background: #f8d7da; padding: 15px; border-radius: 8px; margin: 20px 0; color: #721c24;'>";
                echo "Error creating admin user. Please try again.";
                echo "</div>";
            }
        }
    }
    
    if (isset($_POST['update_existing'])) {
        $user_id = (int)$_POST['user_id'];
        
        $updateQuery = "UPDATE users SET role = 'admin' WHERE id = :user_id";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bindParam(':user_id', $user_id);
        
        if ($updateStmt->execute()) {
            echo "<div style='background: #d4edda; padding: 15px; border-radius: 8px; margin: 20px 0; color: #155724;'>";
            echo "✅ User has been promoted to admin successfully!";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; padding: 15px; border-radius: 8px; margin: 20px 0; color: #721c24;'>";
            echo "Error updating user role. Please try again.";
            echo "</div>";
        }
    }
}

// Display existing users
$database = new Database();
$db = $database->getConnection();

$query = "SELECT id, first_name, last_name, email, role, created_at FROM users ORDER BY role DESC, created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Create New Admin Form -->
<div style="max-width: 600px; margin: 20px 0; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <h3>Create New Admin User</h3>
    <form method="POST" action="">
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Email:</label>
            <input type="email" name="admin_email" value="<?php echo $admin_email; ?>" 
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        </div>
        
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Password:</label>
            <input type="password" name="admin_password" value="<?php echo $admin_password; ?>" 
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            <small style="color: #666;">Default: admin123 (Please change after first login)</small>
        </div>
        
        <div style="display: flex; gap: 15px; margin-bottom: 15px;">
            <div style="flex: 1;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">First Name:</label>
                <input type="text" name="admin_first_name" value="<?php echo $admin_first_name; ?>" 
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div style="flex: 1;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Last Name:</label>
                <input type="text" name="admin_last_name" value="<?php echo $admin_last_name; ?>" 
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
        </div>
        
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Birthday:</label>
            <input type="date" name="admin_birthday" value="<?php echo $admin_birthday; ?>" 
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        </div>
        
        <button type="submit" name="create_admin" 
                style="background: #007bff; color: white; padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px;">
            Create Admin User
        </button>
    </form>
</div>

<!-- Existing Users -->
<div style="max-width: 800px; margin: 20px 0;">
    <h3>Existing Users</h3>
    
    <?php if (count($users) > 0): ?>
        <table style="width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6;">ID</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6;">Name</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6;">Email</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6;">Role</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6;">Created</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr style="border-bottom: 1px solid #dee2e6;">
                        <td style="padding: 12px;"><?php echo $user['id']; ?></td>
                        <td style="padding: 12px;"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                        <td style="padding: 12px;"><?php echo htmlspecialchars($user['email']); ?></td>
                        <td style="padding: 12px;">
                            <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; 
                                         background: <?php echo $user['role'] == 'admin' ? '#d4edda' : '#e2e3e5'; ?>; 
                                         color: <?php echo $user['role'] == 'admin' ? '#155724' : '#495057'; ?>;">
                                <?php echo strtoupper($user['role']); ?>
                            </span>
                        </td>
                        <td style="padding: 12px;"><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                        <td style="padding: 12px;">
                            <?php if ($user['role'] != 'admin'): ?>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" name="update_existing" 
                                            onclick="return confirm('Are you sure you want to make this user an admin?')"
                                            style="background: #28a745; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;">
                                        Make Admin
                                    </button>
                                </form>
                            <?php else: ?>
                                <span style="color: #28a745; font-weight: bold;">✓ Admin</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="padding: 20px; background: #f8f9fa; border-radius: 8px; text-align: center;">No users found in the database.</p>
    <?php endif; ?>
</div>

<!-- Instructions -->
<div style="max-width: 600px; margin: 20px 0; background: #e7f3ff; padding: 20px; border-radius: 8px; border-left: 4px solid #007bff;">
    <h4 style="color: #004085; margin-top: 0;">Admin Login Instructions:</h4>
    <ol style="color: #004085;">
        <li>Create an admin user using the form above (or promote an existing user)</li>
        <li>Go to the <a href="pages/login.php" style="color: #007bff;">login page</a></li>
        <li>Use the admin email and password to login</li>
        <li>You will be automatically redirected to the admin dashboard</li>
        <li><strong>Important:</strong> Change the default password after first login!</li>
    </ol>
    
    <h4 style="color: #004085;">Default Admin Credentials:</h4>
    <ul style="color: #004085; font-family: monospace; background: #fff; padding: 10px; border-radius: 4px;">
        <li><strong>Email:</strong> admin@dormmate.com</li>
        <li><strong>Password:</strong> admin123</li>
    </ul>
</div>

<div style="margin-top: 30px;">
    <a href="pages/login.php" style="background: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; margin-right: 10px;">
        Go to Login Page
    </a>
    <a href="pages/admin_dashboard.php" style="background: #28a745; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px;">
        Go to Admin Dashboard
    </a>
</div>
