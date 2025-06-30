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
            $success_message = "Account created successfully! Please login.";
            echo '<script>
                    setTimeout(function() {
                        window.location.href = "login.php";
                    }, 2000);
                  </script>';
        } else {
            $errors[] = "Something went wrong. Please try again.";
        }
    }
}

$page_title = "Forgot Password- DormMate";
$body_class = "auth-page signup-page";
include '../includes/header.php';
?>

<div class="container">
    <div id="signup-form" class="form-container active">
        <div class="header">
            <h1>Authenticate User</h1>
            <p>Enter email address associated with the account</p>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
                <div class="message error"><?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <form method="POST" action="" class="compact-form">
            <div class="form-group">
                <label for="contact_number">Email Address</label>
                <input type="tel" id="contact_number" name="contact_number" 
                    value="<?php echo isset($_POST['contact_number']) ? htmlspecialchars($_POST['contact_number']) : ''; ?>">
            </div>
            <button type="submit" name="signup" class="btn">Submit</button>
        </form>

        <div class="toggle-form">
            No issues? <a href="login.php">Sign In</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>