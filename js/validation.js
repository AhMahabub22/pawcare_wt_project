// Validation for all forms
document.addEventListener('DOMContentLoaded', function() {
    console.log('Validation script loaded');
    
    // Add required attributes to all form inputs
    const requiredInputs = document.querySelectorAll('input[required], select[required], textarea[required]');
    requiredInputs.forEach(input => {
        input.setAttribute('aria-required', 'true');
    });
    
    // Add labels to all buttons without text
    const iconButtons = document.querySelectorAll('.btn:not(:has(span)), .btn:not(:has(img))');
    iconButtons.forEach(button => {
        if (!button.textContent.trim() && !button.hasAttribute('title')) {
            const icon = button.querySelector('i');
            if (icon) {
                let title = 'Button';
                if (icon.classList.contains('fa-camera')) title = 'Upload photo';
                else if (icon.classList.contains('fa-save')) title = 'Save';
                else if (icon.classList.contains('fa-trash')) title = 'Delete';
                else if (icon.classList.contains('fa-edit')) title = 'Edit';
                button.setAttribute('title', title);
                button.setAttribute('aria-label', title);
            }
        }
    });
    
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let valid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    field.style.borderColor = '#dc3545';
                    
                    // Add error message
                    let errorSpan = field.nextElementSibling;
                    if (!errorSpan || !errorSpan.classList.contains('error')) {
                        errorSpan = document.createElement('span');
                        errorSpan.className = 'error';
                        errorSpan.textContent = 'This field is required';
                        field.parentNode.appendChild(errorSpan);
                    }
                } else {
                    field.style.borderColor = '';
                    const errorSpan = field.nextElementSibling;
                    if (errorSpan && errorSpan.classList.contains('error')) {
                        errorSpan.remove();
                    }
                }
            });
            
            if (!valid) {
                e.preventDefault();
                alert('Please fill all required fields.');
            }
        });
    });
    
    // Clear errors on input
    document.addEventListener('input', function(e) {
        if (e.target.hasAttribute('required')) {
            e.target.style.borderColor = '';
            const errorSpan = e.target.nextElementSibling;
            if (errorSpan && errorSpan.classList.contains('error')) {
                errorSpan.remove();
            }
        }
    });
    
    // Password validation
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    passwordInputs.forEach(input => {
        input.addEventListener('input', function() {
            const value = this.value;
            let strength = 0;
            
            if (value.length >= 8) strength++;
            if (/[A-Z]/.test(value)) strength++;
            if (/[0-9]/.test(value)) strength++;
            if (/[^A-Za-z0-9]/.test(value)) strength++;
            
            const strengthText = document.getElementById('passwordStrength');
            if (strengthText) {
                const labels = ['Very Weak', 'Weak', 'Fair', 'Strong', 'Very Strong'];
                strengthText.textContent = 'Strength: ' + labels[strength];
                strengthText.style.color = 
                    strength <= 1 ? '#dc3545' : 
                    strength == 2 ? '#ffc107' : 
                    strength == 3 ? '#28a745' : '#007bff';
            }
        });
    });
});