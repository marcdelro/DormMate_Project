# DormMate CSS Stylesheets

This directory contains all the CSS files used to style the DormMate authentication system. Each file serves a specific purpose to ensure a modern, responsive, and visually appealing user experience.

## 📁 File Overview

### Core Stylesheets

- **base.css**: Foundation stylesheet with CSS resets, basic typography, and global styles. Includes universal box-sizing, body font-family setup, and minimum height configurations for consistent cross-browser rendering.

- **forms.css**: Comprehensive form styling including input fields, labels, form groups, and validation states. Features responsive form rows, focus effects, transitions, and styling for text, email, password, telephone, and date inputs.

- **messages.css**: Message system styling for error, success, warning, and info notifications. Includes positioning, colors, animations, and auto-dismiss functionality for user feedback.

### Layout & Authentication

- **auth-branding.css**: Branding and visual elements for authentication pages. Contains background gradients, logo positioning, branding text styling, and promotional content layouts for login/signup pages.

- **split_layout.css**: Split-screen layout system used primarily on the login page. Creates side-by-side branding and form sections with responsive breakpoints and flexible containers.

- **signup.css**: Specific styling for the signup page including centered layouts, form positioning, and signup-specific visual elements that differ from the login page design.

### Interactive Elements

- **animations.css**: Collection of CSS keyframe animations including gradient shifts, fade effects, and slide transitions. Contains `gradientShift`, `fadeOut`, and `fadeInDown` animations used throughout the application.

- **dashboard.css**: Complete dashboard styling including header layout, navigation, content areas, logout button positioning, and dashboard-specific spacing and typography.

### Responsive Design

- **responsiveness.css**: Media queries and responsive design rules to ensure the application works seamlessly across desktop, tablet, and mobile devices. Contains breakpoints and adaptive layouts.

## 🎨 Integration Guide

### Method 1: Complete Integration (Recommended)
Include the header.php file which automatically loads all necessary CSS:
```php
<?php include '../includes/header.php'; ?>
```

### Method 2: Manual CSS Linking
If you need to manually link stylesheets, add these in your `<head>` section:

**For Login Page:**
```html
<link rel="stylesheet" href="../assets/css/base.css">
<link rel="stylesheet" href="../assets/css/forms.css">
<link rel="stylesheet" href="../assets/css/auth-branding.css">
<link rel="stylesheet" href="../assets/css/split_layout.css">
<link rel="stylesheet" href="../assets/css/animations.css">
<link rel="stylesheet" href="../assets/css/messages.css">
<link rel="stylesheet" href="../assets/css/responsiveness.css">
```

**For Signup Page:**
```html
<link rel="stylesheet" href="../assets/css/base.css">
<link rel="stylesheet" href="../assets/css/forms.css">
<link rel="stylesheet" href="../assets/css/signup.css">
<link rel="stylesheet" href="../assets/css/animations.css">
<link rel="stylesheet" href="../assets/css/messages.css">
<link rel="stylesheet" href="../assets/css/responsiveness.css">
```

**For Dashboard:**
```html
<link rel="stylesheet" href="../assets/css/base.css">
<link rel="stylesheet" href="../assets/css/dashboard.css">
<link rel="stylesheet" href="../assets/css/messages.css">
<link rel="stylesheet" href="../assets/css/responsiveness.css">
```

## 🏗️ HTML Structure Examples

### Login Page Structure
```html
<body class="split-layout">
    <div class="auth-container">
        <div class="auth-branding">
            <!-- Branding content -->
        </div>
        <div class="auth-form-section">
            <form class="auth-form">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username">
                </div>
                <!-- More form fields -->
            </form>
        </div>
    </div>
</body>
```

### Signup Page Structure
```html
<body class="auth-page signup-page">
    <div class="container">
        <form class="auth-form">
            <div class="form-row">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name">
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name">
                </div>
            </div>
            <!-- More form rows -->
        </form>
    </div>
</body>
```

### Dashboard Structure
```html
<body class="dashboard">
    <div class="dashboard-header">
        <h1>Dashboard Title</h1>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
    <div class="dashboard-content">
        <!-- Dashboard content -->
    </div>
</body>
```

## 🎨 Key CSS Classes Reference

### Layout Classes
- `.split-layout` - Enables split-screen layout (login page)
- `.auth-page` - Base authentication page styling
- `.signup-page` - Specific signup page modifications
- `.dashboard` - Dashboard body styling
- `.auth-container` - Main container for auth pages
- `.auth-branding` - Left side branding section
- `.auth-form-section` - Right side form section

### Form Classes
- `.auth-form` - Main form container styling
- `.form-group` - Individual form field wrapper
- `.form-row` - Horizontal row for multiple form fields
- `.btn`, `.auth-btn` - Button styling
- `.logout-btn` - Specific logout button styling

### Message Classes
- `.message` - Base message styling
- `.success` - Success message styling
- `.error` - Error message styling
- `.warning` - Warning message styling

### Animation Classes
- Apply animations by referencing keyframes: `gradientShift`, `fadeOut`, `fadeInDown`

## 🛠️ Customization Guide

### Changing Colors
Edit the CSS custom properties (variables) in `base.css`:
```css
:root {
    --primary-color: #your-color;
    --secondary-color: #your-color;
    --accent-color: #your-color;
}
```

### Modifying Animations
Add custom animations to `animations.css`:
```css
@keyframes yourAnimation {
    /* Animation keyframes */
}
```

### Responsive Breakpoints
Modify breakpoints in `responsiveness.css`:
```css
@media (max-width: 768px) {
    /* Tablet styles */
}

@media (max-width: 480px) {
    /* Mobile styles */
}
```

## � Responsive Features
- **Mobile-first design**: All layouts work on mobile devices
- **Flexible grids**: Form rows adapt to screen size
- **Touch-friendly**: Buttons and inputs sized for touch interaction
- **Readable typography**: Scalable fonts for all devices

## 🚀 Performance Tips
- Load only necessary CSS files for each page
- `base.css` is required for all pages
- Use browser caching for CSS files
- Minify CSS files in production
- Combine CSS files when possible to reduce HTTP requests

## 🔧 Troubleshooting

### Common Issues
1. **Styles not loading**: Check file paths and ensure correct relative URLs
2. **Layout breaking**: Verify HTML structure matches expected class hierarchy
3. **Responsive issues**: Check viewport meta tag in HTML head
4. **Animation not working**: Ensure `animations.css` is loaded

### Debug Steps
1. Check browser developer tools for 404 errors on CSS files
2. Verify CSS file links are pointing to correct paths
3. Ensure proper HTML structure with required classes
4. Test responsive design using browser device simulation

---

