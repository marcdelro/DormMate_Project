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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signup'])) {
    // Sign up processing
    $database = new Database();
    $db = $database->getConnection();
    
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $middle_name = trim($_POST['middle_name']);
    $birthday = $_POST['birthday'];
    $email = trim($_POST['email']);
    $contact_number = trim($_POST['contact_number']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    $errors = [];
    
    // Validation
    if (empty($first_name)) $errors[] = "First name is required";
    if (empty($last_name)) $errors[] = "Last name is required";
    if (empty($birthday)) $errors[] = "Birthday is required";
    else {
        $birthDate = new DateTime($birthday);
        $today = new DateTime();
        $age = $today->diff($birthDate)->y;

        if ($age < 18) {
            $errors[] = "You must be at least 18 years old to sign up.";
        }
    }
    
    if (empty($email)) $errors[] = "Email is required";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
    if (empty($password)) $errors[] = "Password is required";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match";
    
    // Check if email already exists
    if (empty($errors)) {
        $query = "SELECT id FROM users WHERE email = :email";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $errors[] = "Email already exists";
        }
    }
    
    if (empty($errors)) {
        // Hash password and insert user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $query = "INSERT INTO users (first_name, last_name, middle_name, email, contact_number, password, role, birthday) 
                  VALUES (:first_name, :last_name, :middle_name, :email, :contact_number, :password, 'user', :birthday)";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':middle_name', $middle_name);
        $stmt->bindParam(':birthday', $birthday);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':contact_number', $contact_number);
        $stmt->bindParam(':password', $hashed_password);
        
        if ($stmt->execute()) {
            echo '<div class="message success">Account created successfully! Please login.</div>';
            echo '<script>
                    setTimeout(function() {
                        window.location.href = "login.php";
                    }, 2000);
                  </script>';
        } else {
            echo '<div class="message error">Something went wrong. Please try again.</div>';
        }
    } else {
        foreach ($errors as $error) {
            echo '<div class="message error">' . htmlspecialchars($error) . '</div>';
        }
    }
}

$page_title = "Sign Up - User Authentication System";
include '../includes/header.php';
?>

<!--  HTML form -->
<div class="container">
    <div id="signup-form" class="form-container active">
        <div class="header">
            <h1>Create Account</h1>
            <p>Find the perfect dorm for you</p>
        </div>

        <div id="form-error" class="message error" style="display: none;"></div>

        <form method="POST" action="" class="compact-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" 
                           value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" 
                           value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="middle_name">Middle Name</label>
                    <input type="text" id="middle_name" name="middle_name" 
                           value="<?php echo isset($_POST['middle_name']) ? htmlspecialchars($_POST['middle_name']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="contact_number">Contact Number</label>
                    <input type="tel" id="contact_number" name="contact_number" 
                           value="<?php echo isset($_POST['contact_number']) ? htmlspecialchars($_POST['contact_number']) : ''; ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="birthday">Birthday</label>
                <input type="date" id="birthday" name="birthday" 
                    value="<?php echo isset($_POST['birthday']) ? htmlspecialchars($_POST['birthday']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required onkeyup="checkPasswordStrength()">
                    <div id="password-strength" class="password-strength"></div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
            </div>

            <button type="submit" name="signup" class="btn">Create Account</button>
        </form>

        <div class="toggle-form">
            Already have an account? <a href="login.php">Sign In</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>