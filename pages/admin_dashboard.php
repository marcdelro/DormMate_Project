<!--for checking if the user is logged in and has the correct role-->

<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// Get statistics
$database = new Database();
$db = $database->getConnection();

// Count total users
$userQuery = "SELECT COUNT(*) as total_users, 
              SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admin_count,
              SUM(CASE WHEN role = 'user' THEN 1 ELSE 0 END) as user_count
              FROM users";
$userStmt = $db->prepare($userQuery);
$userStmt->execute();
$userStats = $userStmt->fetch(PDO::FETCH_ASSOC);

// Get recent users (last 5)
$recentQuery = "SELECT first_name, last_name, email, role, created_at 
                FROM users 
                ORDER BY created_at DESC 
                LIMIT 5";
$recentStmt = $db->prepare($recentQuery);
$recentStmt->execute();
$recentUsers = $recentStmt->fetchAll(PDO::FETCH_ASSOC);

// Check for backup codes table
$backupCodesExists = false;
try {
    $backupQuery = "SELECT COUNT(*) as total_codes FROM backup_codes";
    $backupStmt = $db->prepare($backupQuery);
    $backupStmt->execute();
    $backupStats = $backupStmt->fetch(PDO::FETCH_ASSOC);
    $backupCodesExists = true;
} catch (Exception $e) {
    $backupStats = ['total_codes' => 0];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - DormMate</title>
    <!-- Link main CSS -->
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/base.css">
    <link rel="stylesheet" href="../assets/css/animations.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .stat-card {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: 4px solid #2c3e50;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }
        
        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        
        .stat-label {
            font-size: 0.9em;
            color: #666;
        }
        
        .admin-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        
        .action-card {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .action-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }
        
        .action-icon {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .action-title {
            font-size: 1.2em;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        
        .action-description {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 15px;
            line-height: 1.4;
        }
        
        .action-btn {
            background: #2c3e50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background 0.3s ease;
            font-weight: 500;
        }
        
        .action-btn:hover {
            background: #34495e;
        }
        
        .recent-users {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .recent-users h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 1.3em;
        }
        
        .user-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .user-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        
        .user-item:last-child {
            border-bottom: none;
        }
        
        .user-role {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: bold;
        }
        
        .role-admin {
            background: #dc3545;
            color: white;
        }
        
        .role-user {
            background: #28a745;
            color: white;
        }
        
        .quick-actions {
            margin-top: 30px;
            text-align: center;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .quick-actions h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .quick-action-btn {
            background: #2c3e50;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 8px;
            margin: 0 10px;
            display: inline-block;
            transition: background 0.3s ease;
            font-weight: 500;
        }
        
        .quick-action-btn:hover {
            background: #34495e;
        }
        
        .quick-action-btn.success {
            background: #28a745;
        }
        
        .quick-action-btn.success:hover {
            background: #218838;
        }
        
        .quick-action-btn.secondary {
            background: #6c757d;
        }
        
        .quick-action-btn.secondary:hover {
            background: #545b62;
        }
    </style>
</head>
<body class="dashboard admin-dashboard">

    <div class="dashboard-header clearfix">
        <h1>
            Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!
            <span class="admin-badge">👑 ADMIN</span>
        </h1>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="dashboard-content">
        <h2>🛠️ Admin Dashboard</h2>
        <p>Manage users, system settings, and monitor DormMate platform activities.</p>
        
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $userStats['total_users']; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $userStats['user_count']; ?></div>
                <div class="stat-label">Regular Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $userStats['admin_count']; ?></div>
                <div class="stat-label">Administrators</div>
            </div>
            <?php if ($backupCodesExists): ?>
            <div class="stat-card">
                <div class="stat-number"><?php echo $backupStats['total_codes']; ?></div>
                <div class="stat-label">Backup Codes</div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Admin Actions -->
        <div class="admin-actions">
            <div class="action-card">
                <div class="action-icon">👥</div>
                <div class="action-title">User Management</div>
                <div class="action-description">View all users, manage roles, and handle user accounts</div>
                <a href="../create_admin.php" class="action-btn">Manage Users</a>
            </div>
            
            <div class="action-card">
                <div class="action-icon">🔑</div>
                <div class="action-title">Backup Codes</div>
                <div class="action-description">Generate and manage backup codes for user account recovery</div>
                <a href="../generate_backup_codes.php" class="action-btn">Manage Codes</a>
            </div>
            
            <div class="action-card">
                <div class="action-icon">🏠</div>
                <div class="action-title">Dormitory Units</div>
                <div class="action-description">Manage dormitory listings, availability, and reservations</div>
                <a href="reservation_page.php" class="action-btn">Manage Units</a>
            </div>
            
            <div class="action-card">
                <div class="action-icon">📊</div>
                <div class="action-title">System Reports</div>
                <div class="action-description">View platform analytics, user activity, and system health</div>
                <a href="#" class="action-btn" onclick="alert('Feature coming soon!')">View Reports</a>
            </div>
            
            <div class="action-card">
                <div class="action-icon">⚙️</div>
                <div class="action-title">System Settings</div>
                <div class="action-description">Configure platform settings, security, and maintenance</div>
                <a href="#" class="action-btn" onclick="alert('Feature coming soon!')">Settings</a>
            </div>
            
            <div class="action-card">
                <div class="action-icon">🔒</div>
                <div class="action-title">Security Center</div>
                <div class="action-description">Monitor security events, manage access, and audit logs</div>
                <a href="#" class="action-btn" onclick="alert('Feature coming soon!')">Security</a>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="recent-users">
            <h3>📋 Recent User Registrations</h3>
            <?php if (count($recentUsers) > 0): ?>
                <ul class="user-list">
                    <?php foreach ($recentUsers as $user): ?>
                        <li class="user-item">
                            <div>
                                <strong><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></strong><br>
                                <small style="color: #666;"><?php echo htmlspecialchars($user['email']); ?></small>
                            </div>
                            <div>
                                <span class="user-role role-<?php echo $user['role']; ?>">
                                    <?php echo strtoupper($user['role']); ?>
                                </span><br>
                                <small style="color: #999;">
                                    <?php echo date('M j, Y g:i A', strtotime($user['created_at'])); ?>
                                </small>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p style="text-align: center; color: #666; padding: 20px;">No users found in the system.</p>
            <?php endif; ?>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <h3>Quick Actions</h3>
            <a href="../create_admin.php" class="quick-action-btn success">
                👤 Create New Admin
            </a>
            <a href="user_dashboard.php" class="quick-action-btn secondary">
                👁️ View as User
            </a>
        </div>
    </div>

</body>
</html>
