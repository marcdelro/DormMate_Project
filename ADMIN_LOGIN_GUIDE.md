# DormMate Admin Login Guide

## 🔑 Admin Authentication System

The DormMate system has a **complete admin authentication system** that allows you to log in with admin credentials and automatically redirects you to the admin dashboard.

## ✅ How Admin Login Works

### 1. **Database Setup**
- Users table has a `role` column with values: `'user'` or `'admin'`
- Admin users are created with `role = 'admin'`
- Regular signups default to `role = 'user'`

### 2. **Login Process**
The login.php file handles both user and admin authentication:

```php
// Login checks user credentials
$query = "SELECT id, first_name, last_name, email, password, role FROM users WHERE email = :email";

// After successful authentication:
if ($user['role'] == 'admin') {
    header("Location: admin_dashboard.php");  // Admin Dashboard
} else {
    header("Location: user_dashboard.php");   // User Dashboard
}
```

### 3. **Session Management**
When logging in, the system sets these session variables:
- `$_SESSION['user_id']` - User's database ID
- `$_SESSION['user_email']` - User's email address
- `$_SESSION['user_name']` - User's full name
- `$_SESSION['user_role']` - User's role ('admin' or 'user')

## 🚀 Quick Start: Admin Login

### **Default Admin Credentials**
```
Email: admin@dormmate.com
Password: admin123
```

### **Steps to Login as Admin:**

1. **Create Admin Account** (if not exists):
   - Visit: `http://localhost/DormMate/create_admin.php`
   - Use the form to create admin user
   - Or run: `http://localhost/DormMate/test_admin_login.php`

2. **Login as Admin**:
   - Go to: `http://localhost/DormMate/pages/login.php`
   - Enter email: `admin@dormmate.com`
   - Enter password: `admin123`
   - Click "Sign In"

3. **Automatic Redirect**:
   - System detects admin role
   - Redirects to: `pages/admin_dashboard.php`
   - Admin dashboard loads with full privileges

## 🛡️ Security Features

### **Role-Based Access Control**
- Admin pages check: `$_SESSION['user_role'] != 'admin'`
- User pages check: `$_SESSION['user_role'] != 'user'`
- Unauthorized access redirects to login

### **Password Security**
- All passwords hashed with `password_hash()`
- Verification with `password_verify()`
- Secure session management

### **Admin Privileges**
Admins have access to:
- ✅ User management (view, promote users)
- ✅ System statistics and analytics
- ✅ Backup code management
- ✅ Admin creation tools
- ✅ All user features

## 📋 Admin Management Options

### **Create New Admin Users**
1. **Via Web Interface**: `/create_admin.php`
2. **Promote Existing Users**: Use "Make Admin" button
3. **Database Direct**: Update `role` column to `'admin'`

### **Multiple Admin Support**
- System supports multiple admin users
- Each admin has same privileges
- Independent login sessions

## 🔧 Testing & Verification

### **Test Admin Login**
Visit: `http://localhost/DormMate/test_admin_login.php`
- Creates admin user if needed
- Tests login functionality
- Shows all users and roles
- Provides direct links to dashboards

### **Verify Role Assignment**
Check the database `users` table:
```sql
SELECT id, first_name, last_name, email, role FROM users WHERE role = 'admin';
```

## 🎯 Key Features

### **Seamless Experience**
- ✅ **Single login form** for both users and admins
- ✅ **Automatic role detection** and redirection
- ✅ **Session persistence** across admin areas
- ✅ **Secure authentication** with proper validation

### **Admin Dashboard Access**
Once logged in as admin, you get:
- 👑 **Admin badge** in header
- 📊 **System statistics** overview
- 👥 **User management** capabilities
- ⚙️ **Administrative tools** access
- 🔧 **Quick action buttons**

## 🚨 Important Notes

1. **Change Default Password**: After first login, change the default password
2. **Secure Credentials**: Use strong passwords for admin accounts
3. **Limited Access**: Only trusted personnel should have admin access
4. **Regular Monitoring**: Check admin activities and user management regularly

## 📍 File Structure

```
/DormMate/
├── pages/
│   ├── login.php              # Handles both user/admin login
│   ├── admin_dashboard.php    # Admin-only dashboard
│   └── user_dashboard.php     # User-only dashboard
├── create_admin.php           # Admin creation tool
├── test_admin_login.php       # Testing and verification
└── config/
    └── database.php           # Database connection
```

The admin login system is **fully functional** and ready to use! 🎉
