<?php
ob_start(); // Start output buffering
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Get statistics with error handling
try {
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

    // Get all users for active session monitoring
    $allUsersQuery = "SELECT id, first_name, last_name, email, role, created_at, 
                      CASE WHEN role = 'admin' THEN '👑' ELSE '👤' END as role_icon
                      FROM users 
                      ORDER BY role DESC, created_at DESC";
    $allUsersStmt = $db->prepare($allUsersQuery);
    $allUsersStmt->execute();
    $allUsers = $allUsersStmt->fetchAll(PDO::FETCH_ASSOC);

    // Simulate active sessions (in a real app, you'd track this in a sessions table)
    $activeSessionsQuery = "SELECT COUNT(DISTINCT id) as active_count FROM users WHERE role != 'admin'";
    $activeStmt = $db->prepare($activeSessionsQuery);
    $activeStmt->execute();
    $activeSessions = $activeStmt->fetch(PDO::FETCH_ASSOC);

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
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; margin: 20px; border-radius: 5px;'>";
    echo "<strong>Database Error:</strong> " . $e->getMessage();
    echo "</div>";
    $userStats = ['total_users' => 0, 'admin_count' => 0, 'user_count' => 0];
    $recentUsers = [];
    $backupStats = ['total_codes' => 0];
    $backupCodesExists = false;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - DormMate</title>
    <!-- Link main CSS -->
    <link rel="stylesheet" href="../assets/css/admin_dashboard.css">
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
    </style>
</head>
<body class="dashboard admin-dashboard">

<!-- Navigation Bar -->
<div class="top-navbar">
    <div class="navbar-left">
        <h1 class="logo">DormMate</h1>
        <span class="admin-badge">👑 ADMIN</span>
    </div>
    <div class="navbar-center">
        <div class="nav-links">
            <a href="admin_dashboard.php" class="nav-link active">Dashboard</a>
            <a href="../create_admin.php" class="nav-link">Users</a>
            <a href="units.php" class="nav-link">Units</a>
            <a href="manage_reservations.php" class="nav-link">Reservations</a>
            <a href="#" class="nav-link">Reports</a>
        </div>
    </div>
    <div class="navbar-right">
        <div class="user-greeting">
            <span class="greeting-text">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
        </div>
        <a href="logout.php" onclick="return confirmLogout()" class="logout-btn">Logout</a>
    </div>
</div>
    <div class="dashboard-content">
        <h2>🛠️ Admin Dashboard</h2>
        <p>Manage users, system settings, and monitor DormMate platform activities.</p>
        
        <!-- Live System Monitoring -->
        <div class="stats-grid">
            <div class="stat-card live-stat">
                <div class="stat-number" id="totalUsers"><?php echo $userStats['total_users']; ?></div>
                <div class="stat-label">👥 Total Users</div>
                <div class="stat-trend">+<?php echo $userStats['user_count']; ?> regular users</div>
            </div>
            <div class="stat-card live-stat">
                <div class="stat-number" id="activeUsers"><?php echo $activeSessions['active_count']; ?></div>
                <div class="stat-label">🟢 Potential Active Users</div>
                <div class="stat-trend">System monitoring</div>
            </div>
            <div class="stat-card live-stat">
                <div class="stat-number"><?php echo $userStats['admin_count']; ?></div>
                <div class="stat-label">👑 Administrators</div>
                <div class="stat-trend">System managers</div>
            </div>
            <div class="stat-card live-stat">
                <div class="stat-number" id="systemLoad"><?php echo date('H:i'); ?></div>
                <div class="stat-label">🕒 System Time</div>
                <div class="stat-trend">Live monitoring</div>
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
    </div>
    
    <!-- Admin Footer -->
    <footer class="admin-footer">
        <div class="footer-content">
            <div class="footer-section">
                <h4>DormMate Admin</h4>
                <p>Administrative control panel for managing dormitory units, users, and system operations.</p>
            </div>
            <div class="footer-section">
                <h4>Admin Tools</h4>
                <ul>
                    <li><a href="#" onclick="refreshStats()">Refresh Statistics</a></li>
                    <li><a href="#" onclick="location.reload()">Reload Dashboard</a></li>
                    <li><a href="logout.php">Secure Logout</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>System Info</h4>
                <p>
                    <a href="https://github.com/search?q=DormMate&type=repositories" target="_blank" rel="noopener noreferrer" class="github-link">
                        <span class="github-icon">👨‍💻</span> Source Code
                    </a>
                </p>
            </div>
            <div class="footer-section">
                <h4>Project Details</h4>
                <p class="footer-info">
                    Admin Panel v1.0<br>
                    Created: December 2024<br>
                    PHP Admin Dashboard
                </p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 DormMate Admin Panel. Secure dormitory management system.</p>
        </div>
    </footer>

    <script>
        function confirmLogout() {
            return confirm('Are you sure you want to logout?');
        }
        
        function refreshStats() {
            // Show loading animation
            document.querySelectorAll('.live-stat').forEach(card => {
                card.style.animation = 'statCardPulse 1s ease-in-out infinite';
            });
            
            // Simulate refresh (in real app, make AJAX call)
            setTimeout(() => {
                location.reload();
            }, 1000);
        }
        
        // Live system monitoring
        function updateSystemStats() {
            // Update system time
            const now = new Date();
            const timeElement = document.querySelector('#systemLoad .stat-number');
            if (timeElement) {
                timeElement.textContent = now.toLocaleTimeString('en-US', {
                    hour12: false,
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }
            
            // Add pulse animation to live stats
            document.querySelectorAll('.live-stat').forEach(card => {
                card.style.animation = 'statCardPulse 0.5s ease-in-out';
                setTimeout(() => {
                    card.style.animation = '';
                }, 500);
            });
        }
        
        // Auto-refresh system stats every 30 seconds
        setInterval(updateSystemStats, 30000);
        
        // Add smooth transitions to stat cards
        document.addEventListener('DOMContentLoaded', function() {
            // Fix scrolling issue
            document.body.style.overflow = 'auto';
            document.body.style.height = 'auto';
            document.documentElement.style.overflow = 'auto';
            
            const statCards = document.querySelectorAll('.stat-card');
            
            statCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px) scale(1.02)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
            
            // Initial stats update
            updateSystemStats();
        });
        
        // Add CSS animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes statCardPulse {
                0% { box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); }
                50% { box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3); }
                100% { box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); }
            }
            
            @keyframes fadeIn {
                from { opacity: 0; transform: translateX(-20px); }
                to { opacity: 1; transform: translateX(0); }
            }
            
            .fade-in {
                animation: fadeIn 0.5s ease-out forwards;
                opacity: 0;
            }
        `;
        document.head.appendChild(style);
    </script>

</body>
</html>
