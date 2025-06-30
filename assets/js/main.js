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

function validateConfirmPassword() {
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    const errorDiv = document.getElementById('form-error');

    if (password.value !== confirmPassword.value) {
        confirmPassword.setCustomValidity("Passwords do not match");
        errorDiv.innerText = "Passwords do not match";
        errorDiv.style.display = "block";
    } else {
        confirmPassword.setCustomValidity("");
        errorDiv.style.display = "none";
    }
}

// Function to check login fields and add glow effect
function checkLoginFields() {
    const emailField = document.getElementById('login_email') || document.getElementById('email') || document.getElementById('username');
    const passwordField = document.getElementById('login_password') || document.getElementById('password');
    const signInButton = document.querySelector('button[name="login"]') || document.querySelector('.btn') || document.querySelector('.auth-btn');
    
    console.log('Checking login fields:', {
        email: emailField?.value || 'not found',
        password: passwordField?.value ? 'filled' : 'empty',
        button: !!signInButton
    });
    
    if (emailField && passwordField && signInButton) {
        if (emailField.value.trim() !== '' && passwordField.value.trim() !== '') {
            signInButton.classList.add('glow-effect');
            console.log('Glow effect added');
        } else {
            signInButton.classList.remove('glow-effect');
            console.log('Glow effect removed');
        }
    }
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('DormMate JavaScript loaded successfully');
    
    // Login page animations
    if (document.body.classList.contains('login-page')) {
        console.log('Login page detected - initializing animations');
        
        // Add interactive hover effects to form fields
        const formGroups = document.querySelectorAll('.form-group');
        formGroups.forEach((group) => {
            const input = group.querySelector('input');
            if (input) {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                    this.parentElement.style.transition = 'transform 0.3s ease';
                    this.style.boxShadow = '0 4px 15px rgba(52, 152, 219, 0.2)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                    this.style.boxShadow = '';
                });
            }
        });
        
        // Add form submission animation
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                console.log('Login form submitted - adding exit animation');
                // Use the same exit animation as signup page
                const authContainer = document.querySelector('.auth-container');
                if (authContainer) {
                    authContainer.style.animation = 'slideOutDown 0.5s ease-in forwards';
                }
            });
        }
    }
    
    // Signup page animations
    if (document.body.classList.contains('signup-page')) {
        console.log('Signup page detected - initializing animations');
        
        const container = document.querySelector('.container');
        console.log('Container found:', !!container);
        
        if (container) {
            console.log('Container classes:', container.className);
            
            // Add hover effects to form fields
            const formGroups = document.querySelectorAll('.form-group');
            formGroups.forEach((group, index) => {
                const input = group.querySelector('input');
                if (input) {
                    input.addEventListener('focus', function() {
                        this.parentElement.style.transform = 'translateY(-2px)';
                        this.parentElement.style.transition = 'transform 0.3s ease';
                    });
                    
                    input.addEventListener('blur', function() {
                        this.parentElement.style.transform = 'translateY(0)';
                    });
                }
            });
        }
        
        // Add form submission animation
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                console.log('Form submitted - adding exit animation');
                const container = document.querySelector('.container');
                if (container) {
                    container.style.animation = 'slideOutDown 0.5s ease-in forwards';
                }
            });
        }
    }
    
    try {
        // Find login form elements (prioritize login page specific IDs)
        const emailField = document.getElementById('login_email') || document.getElementById('email') || document.getElementById('username');
        const passwordField = document.getElementById('login_password') || document.getElementById('password');
        const signInButton = document.querySelector('button[name="login"]') || document.querySelector('.btn') || document.querySelector('.auth-btn');
        
        console.log('Elements found:', {
            email: !!emailField,
            password: !!passwordField,
            button: !!signInButton
        });
        
        // Only add glow effect on login page
        if (emailField && passwordField && document.body.classList.contains('login-page')) {
            emailField.addEventListener('input', checkLoginFields);
            passwordField.addEventListener('input', checkLoginFields);
            console.log('Login glow effect listeners added successfully');
            
            // Initial check
            checkLoginFields();
        }
        
        // Auto-hide success messages
        const successMessages = document.querySelectorAll('.message.success');
        successMessages.forEach(function(message) {
            setTimeout(function() {
                if (message.parentNode) {
                    message.style.animation = 'fadeOut 0.5s ease-out forwards';
                    setTimeout(() => message.remove(), 500);
                }
            }, 5000);
        });
        
    } catch (error) {
        console.error('Error initializing JavaScript:', error);
    }
});