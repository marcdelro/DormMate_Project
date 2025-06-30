<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] == 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: user_dashboard.php");
    }
    exit();
}

// Work in progress message
$wip_message = "Password reset functionality is currently under development.";

$page_title = "Reset Password - DormMate";
$body_class = "auth-page forgot-password-page";
include '../includes/header.php';
?>

<div class="container">
    <div id="forgot-password-form" class="form-container active">
        <div class="header">
            <h1>Reset Password</h1>
            <p>Secure password recovery system</p>
        </div>

        <!-- Work in Progress Message -->
        <div class="wip-message">
            <div class="wip-icon">🔧</div>
            <h3>Under Development</h3>
            <p><?php echo htmlspecialchars($wip_message); ?></p>
            <p class="contact-info">
                For password assistance, please contact your system administrator.
            </p>
        </div>

        <div class="toggle-form">
            Remember your password? <a href="login.php">Sign In</a><br/>
            Need an account? <a href="signup.php">Create Account</a>
        </div>
    </div>
</div>

<style>
    /* Work in Progress Styling */
    .wip-message {
        background: linear-gradient(135deg, #fff3cd, #ffeaa7);
        border: 2px solid #ffc107;
        border-radius: 12px;
        padding: 40px;
        text-align: center;
        margin: 30px 0;
        position: relative;
    }

    .wip-icon {
        font-size: 48px;
        margin-bottom: 20px;
        display: block;
        animation: bounce 2s infinite;
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-10px);
        }
        60% {
            transform: translateY(-5px);
        }
    }

    .wip-message h3 {
        color: #856404;
        margin-bottom: 15px;
        font-size: 24px;
        font-weight: 600;
    }

    .wip-message p {
        color: #856404;
        font-size: 16px;
        line-height: 1.6;
        margin-bottom: 10px;
    }

    .contact-info {
        margin-top: 15px;
        font-size: 14px !important;
        color: #666 !important;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .wip-message {
            padding: 30px 20px;
        }
        
        .wip-icon {
            font-size: 36px;
        }
        
        .wip-message h3 {
            font-size: 20px;
        }
        
        .wip-message p {
            font-size: 14px;
        }
    }
</style>

<?php include '../includes/footer.php'; ?>
