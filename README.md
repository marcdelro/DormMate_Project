# DormMate - User Authentication System

A PHP-based user authentication system with registration, login, and dashboard functionality.

## 📋 Table of Contents
- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Database Setup](#database-setup)
- [Configuration](#configuration)
- [Running the Application](#running-the-application)
- [Project Structure](#project-structure)
- [Usage](#usage)
- [Troubleshooting](#troubleshooting)

## ✨ Features

- User registration with validation
- Secure login/logout functionality
- Password hashing with PHP's `password_hash()`
- Session management
- User and admin roles
- Responsive design
- MySQL database integration
- PDO for secure database operations

## 🔧 Requirements

- **XAMPP** (or similar local server environment)
  - Apache Web Server
  - MySQL Database
  - PHP 7.4 or higher
- Web browser (Chrome, Firefox, Safari, etc.)

## 🚀 Installation

### Step 1: Install XAMPP
1. Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Install XAMPP following the installation wizard
3. Start XAMPP Control Panel

### Step 2: Clone/Download Project
1. Place the `DormMate` folder in your XAMPP's `htdocs` directory:
   ```
   /Applications/XAMPP/xamppfiles/htdocs/DormMate/
   ```

### Step 3: Start Services
1. Open XAMPP Control Panel
2. Start **Apache** service
3. Start **MySQL** service

## 🗃️ Database Setup

### Step 1: Access phpMyAdmin
1. Open your web browser
2. Navigate to: `http://localhost/phpmyadmin`

### Step 2: Create Database
1. Click "New" to create a new database
2. Name it: `user_system`
3. Click "Create"

### Step 3: Import Database Structure
1. Select the `user_system` database
2. Click on the "Import" tab
3. Choose file: `sql/user_system.sql` from your project folder
4. Click "Go" to import

**Alternative: Manual Database Creation**
```sql
CREATE DATABASE user_system;
USE user_system;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `birthday` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

## ⚙️ Configuration

### Database Configuration
The database configuration is located in `config/database.php`. Default settings:

```php
private $host = 'localhost';
private $db_name = 'user_system';
private $username = 'root';
private $password = '';
```

**If you need to modify these settings:**
1. Open `config/database.php`
2. Update the database credentials as needed
3. Save the file

## 🎯 Running the Application

### Step 1: Start XAMPP Services
Make sure both Apache and MySQL are running in XAMPP Control Panel.

### Step 2: Access the Application
Open your web browser and navigate to:
```
http://localhost/DormMate
```

### Step 3: Use the Application
- **First Visit**: You'll be redirected to the login page
- **Register**: Click "Sign Up" to create a new account
- **Login**: Use your email and password to log in
- **Dashboard**: After login, you'll see your user dashboard

## 📁 Project Structure

```
DormMate/
├── index.php                    # Main entry point (redirects to login)
├── README.md                    # Project documentation
├── .git/                        # Git version control
├── assets/                      # Static assets
│   ├── css/                     # Stylesheets
│   │   ├── README.md           # CSS documentation
│   │   ├── animations.css      # CSS animations and transitions
│   │   ├── auth-branding.css   # Authentication page branding
│   │   ├── base.css           # Base styles and resets
│   │   ├── dashboard.css      # Dashboard-specific styles
│   │   ├── forms.css          # Form styling and validation
│   │   ├── messages.css       # Message/notification styles
│   │   ├── responsiveness.css # Responsive design rules
│   │   ├── signup.css         # Signup page specific styles
│   │   └── split_layout.css   # Split-screen layout system
│   ├── images/                 # Image assets
│   │   └── README.md          # Image usage documentation
│   └── js/                     # JavaScript files
│       └── main.js            # Main JavaScript functionality
├── config/                     # Configuration files
│   └── database.php           # Database connection class
├── includes/                   # Reusable PHP components
│   ├── header.php             # Common header with CSS/JS includes
│   └── footer.php             # Common footer
├── pages/                      # Application pages
│   ├── login.php              # User login page
│   ├── logout.php             # Logout functionality
│   ├── signup.php             # User registration page
│   └── user_dashboard.php     # User dashboard
├── sql/                        # Database files
│   ├── user_system.sql        # Main database structure
│   └── Units_System           # Additional database components
└── src/                        # Source code (future development)
```

### 📂 Directory Descriptions

**`assets/`** - Contains all static files (CSS, JavaScript, images)
- **`css/`** - Modular CSS architecture with separate files for different components
- **`js/`** - JavaScript files for client-side functionality
- **`images/`** - Image assets for the application

**`config/`** - Configuration files for database and application settings

**`includes/`** - Reusable PHP components included across multiple pages

**`pages/`** - Main application pages for user interaction

**`sql/`** - Database schema and related SQL files

**`src/`** - Reserved for additional source code and future development

## 💡 Usage

### Registration
1. Navigate to the signup page
2. Fill in all required fields:
   - First Name
   - Last Name
   - Email (must be unique)
   - Password
   - Contact Number
   - Birthday
3. Click "Sign Up"

### Login
1. Enter your registered email and password
2. Click "Login"
3. You'll be redirected to your dashboard

### Admin vs User Roles
- **User**: Regular user access to user dashboard
- **Admin**: Administrative access (can be set manually in database)

## 🔧 Troubleshooting

### Common Issues

**1. "Connection error" message**
- Check if MySQL service is running in XAMPP
- Verify database credentials in `config/database.php`
- Ensure the `user_system` database exists

**2. "404 Not Found" error**
- Verify the project is in the correct directory: `htdocs/DormMate/`
- Check if Apache service is running
- Ensure you're accessing `http://localhost/DormMate`

**3. CSS/JS not loading**
- Check file paths in `includes/header.php`
- Verify files exist in `assets/css/` and `assets/js/` directories

**4. Session issues**
- Ensure cookies are enabled in your browser
- Check if the `session_start()` is called at the top of PHP files

### Database Issues

**Reset Database:**
1. Drop the `user_system` database in phpMyAdmin
2. Create a new `user_system` database
3. Import the `sql/user_system.sql` file again

### Permission Issues (macOS/Linux)
If you encounter permission issues:
```bash
sudo chmod -R 755 /Applications/XAMPP/xamppfiles/htdocs/DormMate/
```

## 📝 Development Notes

- Passwords are hashed using PHP's `password_hash()` function
- PDO is used for database operations to prevent SQL injection
- Sessions are used for user authentication state management
- The application follows a simple MVC-like structure

## 🤝 Contributing

1. Fork the project
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Open a Pull Request

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

---
