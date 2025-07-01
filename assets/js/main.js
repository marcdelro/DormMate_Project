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