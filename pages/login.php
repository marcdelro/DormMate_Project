<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once '../config/database.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] == 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: user_dashboard.php");
    }
    exit();
}

// Login processing logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $database = new Database();
    $db = $database->getConnection();
    
    $email = trim($_POST['login_email']);
    $password = $_POST['login_password'];
    
    $errors = [];
    
    if (empty($email)) $errors[] = "Email is required";
    if (empty($password)) $errors[] = "Password is required";
    
    if (empty($errors)) {
        $query = "SELECT id, first_name, last_name, email, password, role FROM users WHERE email = :email";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['user_role'] = $user['role'];
                
                // Redirect based on role
                if ($user['role'] == 'admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: user_dashboard.php");
                }
                exit();
            } else {
                $errors[] = "Invalid email or password.";
            }
        } else {
            $errors[] = "Invalid email or password.";
        }
    }
}

$page_title = "Login - DormMate";
$body_class = "auth-page login-page split-layout";
include '../includes/header.php';
?>

<div class="auth-container">
    <!-- Left Side - Branding -->
    <div class="auth-branding">
        <div class="brand-content">
            <div class="brand-logo">DormMate</div>
            <div class="brand-tagline">Find Your Perfect Home Away From Home</div>
            <ul class="brand-features">
                <li>Verified Dormitory Listings</li>
                <li>Secure Application Process</li>
                <li>24/7 Support System</li>
                <li>Student Community Platform</li>
            </ul>
        </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="auth-form-section">
        <div class="auth-form-wrapper">
            <div class="form-container active">
                <div class="header">
                    <h1>Welcome Back</h1>
                    <p>Sign in to your account</p>
                </div>

                <?php if (!empty($errors)): ?>
                    <?php foreach ($errors as $error): ?>
                        <div class="message error"><?php echo htmlspecialchars($error); ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- LOGIN FORM -->
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="login_email">Email Address</label>
                        <input type="email" id="login_email" name="login_email" 
                               value="<?php echo isset($_POST['login_email']) ? htmlspecialchars($_POST['login_email']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="login_password">Password</label>
                        <input type="password" id="login_password" name="login_password" required>
                    </div>
                    
                    <button type="submit" name="login" class="btn">Sign In</button>
                </form>
                
                    
                <div class="toggle-form">
                    <a href="forgot_password.php">Forgot Password?</a><br/>
                    Don't have an account? <a href="signup.php">Create Account</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>