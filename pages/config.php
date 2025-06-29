<?php
// config.php - Database configuration
class Database {
    private $host = 'localhost';
    private $db_name = 'user_system';
    private $username = 'root';
    private $password = '';
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Authentication System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #fafafa;
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 450px;
            border: 1px solid #f0f0f0;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #2c3e50;
            font-size: 28px;
            font-weight: 300;
            margin-bottom: 8px;
        }

        .header p {
            color: #7f8c8d;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-row {
            display: flex;
            gap: 15px;
        }

        .form-row .form-group {
            flex: 1;
        }

        label {
            display: block;
            margin-bottom: 6px;
            color: #555;
            font-size: 14px;
            font-weight: 500;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="tel"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #f5f5f5;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            background-color: #fcfcfc;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="tel"]:focus {
            outline: none;
            border-color: #3498db;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .btn {
            width: 100%;
            padding: 14px;
            background-color: #2c3e50;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn:hover {
            background-color: #34495e;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(44, 62, 80, 0.3);
        }

        .btn:active {
            transform: translateY(0);
        }

        .toggle-form {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #f0f0f0;
        }

        .toggle-form a {
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .toggle-form a:hover {
            color: #2980b9;
            text-decoration: underline;
        }

        .message {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .form-container {
            display: none;
        }

        .form-container.active {
            display: block;
        }

        .password-strength {
            font-size: 12px;
            margin-top: 5px;
            padding: 5px 0;
        }

        .strength-weak { color: #e74c3c; }
        .strength-medium { color: #f39c12; }
        .strength-strong { color: #27ae60; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sign Up Form -->
        <div id="signup-form" class="form-container active">
            <div class="header">
                <h1>Create Account</h1>
                <p>Join us today and get started</p>
            </div>

            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signup'])) {
                // Sign up processing
                $database = new Database();
                $db = $database->getConnection();
                
                $first_name = trim($_POST['first_name']);
                $last_name = trim($_POST['last_name']);
                $middle_name = trim($_POST['middle_name']);
                $email = trim($_POST['email']);
                $contact_number = trim($_POST['contact_number']);
                $password = $_POST['password'];
                $confirm_password = $_POST['confirm_password'];
                
                $errors = [];
                
                // Validation
                if (empty($first_name)) $errors[] = "First name is required";
                if (empty($last_name)) $errors[] = "Last name is required";
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
                    
                    $query = "INSERT INTO users (first_name, last_name, middle_name, email, contact_number, password, role) 
                              VALUES (:first_name, :last_name, :middle_name, :email, :contact_number, :password, 'user')";
                    
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':first_name', $first_name);
                    $stmt->bindParam(':last_name', $last_name);
                    $stmt->bindParam(':middle_name', $middle_name);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':contact_number', $contact_number);
                    $stmt->bindParam(':password', $hashed_password);
                    
                    if ($stmt->execute()) {
                        echo '<div class="message success">Account created successfully! Please login.</div>';
                        echo '<script>
                                setTimeout(function() {
                                    showLogin();
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
            ?>

            <form method="POST" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="middle_name">Middle Name (Optional)</label>
                    <input type="text" id="middle_name" name="middle_name">
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="contact_number">Contact Number</label>
                    <input type="tel" id="contact_number" name="contact_number">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required onkeyup="checkPasswordStrength()">
                    <div id="password-strength" class="password-strength"></div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" name="signup" class="btn">Create Account</button>
            </form>

            <div class="toggle-form">
                Already have an account? <a href="#" onclick="showLogin()">Sign In</a>
            </div>
        </div>

        <!-- Login Form -->
        <div id="login-form" class="form-container">
            <div class="header">
                <h1>Welcome Back</h1>
                <p>Sign in to your account</p>
            </div>

            <?php
            session_start();
            
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
                // Login processing
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
                            echo '<div class="message error">Invalid email or password.</div>';
                        }
                    } else {
                        echo '<div class="message error">Invalid email or password.</div>';
                    }
                } else {
                    foreach ($errors as $error) {
                        echo '<div class="message error">' . htmlspecialchars($error) . '</div>';
                    }
                }
            }
            ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="login_email">Email Address</label>
                    <input type="email" id="login_email" name="login_email" required>
                </div>

                <div class="form-group">
                    <label for="login_password">Password</label>
                    <input type="password" id="login_password" name="login_password" required>
                </div>

                <button type="submit" name="login" class="btn">Sign In</button>
            </form>

            <div class="toggle-form">
                Don't have an account? <a href="#" onclick="showSignup()">Create Account</a>
            </div>
        </div>
    </div>

    <script>
        function showLogin() {
            document.getElementById('signup-form').classList.remove('active');
            document.getElementById('login-form').classList.add('active');
        }

        function showSignup() {
            document.getElementById('login-form').classList.remove('active');
            document.getElementById('signup-form').classList.add('active');
        }

        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthDiv = document.getElementById('password-strength');
            
            if (password.length === 0) {
                strengthDiv.innerHTML = '';
                return;
            }
            
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            if (strength < 3) {
                strengthDiv.innerHTML = '<span class="strength-weak">Weak password</span>';
            } else if (strength < 5) {
                strengthDiv.innerHTML = '<span class="strength-medium">Medium password</span>';
            } else {
                strengthDiv.innerHTML = '<span class="strength-strong">Strong password</span>';
            }
        }
    </script>
</body>
</html>

<?php
?> 