// Form validation functions
function validateRegister() {
    var username = document.getElementById('username');
    var email = document.getElementById('email');
    var password = document.getElementById('password');
    var confirmPassword = document.getElementById('confirm_password');
    
    var errors = [];
    
    // Clear previous errors
    document.querySelectorAll('.error').forEach(function(el) {
        el.textContent = '';
    });
    
    // Username validation
    if (username.value.length < 3) {
        document.getElementById('usernameError').textContent = 'Username must be at least 3 characters';
        errors.push('username');
    }
    
    if (username.value.length > 50) {
        document.getElementById('usernameError').textContent = 'Username cannot exceed 50 characters';
        errors.push('username');
    }
    
    // Email validation
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email.value)) {
        document.getElementById('emailError').textContent = 'Please enter a valid email address';
        errors.push('email');
    }
    
    // Password validation
    if (password.value.length < 8) {
        document.getElementById('passwordError').textContent = 'Password must be at least 8 characters';
        errors.push('password');
    }
    
    // Confirm password
    if (password.value !== confirmPassword.value) {
        document.getElementById('confirmPasswordError').textContent = 'Passwords do not match';
        errors.push('confirm_password');
    }
    
    if (errors.length > 0) {
        errors[0].focus();
        return false;
    }
    
    return true;
}

function validateLogin() {
    var username = document.getElementById('username');
    var password = document.getElementById('password');
    
    if (!username.value.trim()) {
        alert('Please enter username or email');
        username.focus();
        return false;
    }
    
    if (!password.value.trim()) {
        alert('Please enter password');
        password.focus();
        return false;
    }
    
    return true;
}

function validateProductForm() {
    var name = document.getElementById('name');
    var price = document.getElementById('price');
    var stock = document.getElementById('stock');
    
    if (!name.value.trim()) {
        alert('Please enter product name');
        name.focus();
        return false;
    }
    
    if (!price.value || parseFloat(price.value) <= 0) {
        alert('Please enter a valid price');
        price.focus();
        return false;
    }
    
    if (!stock.value || parseInt(stock.value) < 0) {
        alert('Please enter a valid stock quantity');
        stock.focus();
        return false;
    }
    
    return true;
}

// Real-time validation
document.addEventListener('DOMContentLoaded', function() {
    // Password strength indicator
    var passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            var strengthText = '';
            var strength = 0;
            
            if (this.value.length >= 8) strength++;
            if (/[A-Z]/.test(this.value)) strength++;
            if (/[0-9]/.test(this.value)) strength++;
            if (/[^A-Za-z0-9]/.test(this.value)) strength++;
            
            switch(strength) {
                case 0:
                case 1:
                    strengthText = 'Weak';
                    break;
                case 2:
                    strengthText = 'Moderate';
                    break;
                case 3:
                    strengthText = 'Strong';
                    break;
                case 4:
                    strengthText = 'Very Strong';
                    break;
            }
            
            var strengthIndicator = document.getElementById('passwordStrength');
            if (!strengthIndicator && this.value) {
                strengthIndicator = document.createElement('small');
                strengthIndicator.id = 'passwordStrength';
                strengthIndicator.style.display = 'block';
                strengthIndicator.style.marginTop = '5px';
                this.parentNode.appendChild(strengthIndicator);
            }
            
            if (strengthIndicator) {
                strengthIndicator.textContent = 'Strength: ' + strengthText;
                strengthIndicator.style.color = 
                    strength <= 1 ? '#dc3545' : 
                    strength == 2 ? '#ffc107' : 
                    strength == 3 ? '#28a745' : '#007bff';
            }
        });
    }
    
    // Confirm password real-time check
    var confirmPassword = document.getElementById('confirm_password');
    if (confirmPassword) {
        confirmPassword.addEventListener('input', function() {
            var password = document.getElementById('password');
            var errorSpan = document.getElementById('confirmPasswordError');
            
            if (password && errorSpan) {
                if (this.value !== password.value) {
                    errorSpan.textContent = 'Passwords do not match';
                } else {
                    errorSpan.textContent = '';
                }
            }
        });
    }
});