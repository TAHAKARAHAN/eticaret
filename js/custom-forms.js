
/**
 * Advanced Form Handling
 * This file contains specialized JavaScript functions for form manipulation,
 * validation and enhanced user experience.
 */

// Password strength meter
function initPasswordStrengthMeter(passwordField, meterElement, feedbackElement) {
    const passwordInput = document.querySelector(passwordField);
    const strengthMeter = document.querySelector(meterElement);
    const feedbackText = document.querySelector(feedbackElement);
    
    if (!passwordInput || !strengthMeter || !feedbackText) return;
    
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const strength = calculatePasswordStrength(password);
        
        // Remove all classes
        strengthMeter.classList.remove(
            'strength-very-weak',
            'strength-weak',
            'strength-medium',
            'strength-strong',
            'strength-very-strong'
        );
        
        // Add appropriate class based on strength
        if (password.length === 0) {
            feedbackText.textContent = '';
        } else if (strength < 20) {
            strengthMeter.classList.add('strength-very-weak');
            feedbackText.textContent = 'Çok zayıf';
        } else if (strength < 40) {
            strengthMeter.classList.add('strength-weak');
            feedbackText.textContent = 'Zayıf';
        } else if (strength < 60) {
            strengthMeter.classList.add('strength-medium');
            feedbackText.textContent = 'Orta';
        } else if (strength < 80) {
            strengthMeter.classList.add('strength-strong');
            feedbackText.textContent = 'İyi';
        } else {
            strengthMeter.classList.add('strength-very-strong');
            feedbackText.textContent = 'Çok güçlü';
        }
    });
}

function calculatePasswordStrength(password) {
    if (!password) return 0;
    
    // Starting score
    let score = 0;
    
    // Length contribution (up to 25 points)
    if (password.length > 6) {
        score += 10;
        score += Math.min((password.length - 6) * 2, 15); // Up to 15 more points for length
    }
    
    // Complexity checks
    const hasLower = /[a-z]/.test(password);
    const hasUpper = /[A-Z]/.test(password);
    const hasDigit = /\d/.test(password);
    const hasSpecial = /[^a-zA-Z0-9]/.test(password);
    
    // Add points for complexity (up to 60 points)
    if (hasLower) score += 10;
    if (hasUpper) score += 15;
    if (hasDigit) score += 10;
    if (hasSpecial) score += 15;
    if (hasLower && hasUpper) score += 5;
    if (hasLower && hasDigit) score += 5;
    if (hasUpper && hasDigit) score += 5;
    if (hasSpecial && (hasLower || hasUpper || hasDigit)) score += 5;
    
    // Pattern checks (reduce score if patterns are found)
    const repeatingChars = /(.)\1{2,}/g; // Same character repeated 3+ times
    const sequentialLetters = /(abc|bcd|cde|def|efg|fgh|ghi|hij|ijk|jkl|klm|lmn|mno|nop|opq|pqr|qrs|rst|stu|tuv|uvw|vwx|wxy|xyz)/i;
    const sequentialNumbers = /(012|123|234|345|456|567|678|789|987|876|765|654|543|432|321|210)/i;
    const commonWords = /(password|123456|qwerty|admin|welcome|abc123)/i;
    
    if (repeatingChars.test(password)) score -= 15;
    if (sequentialLetters.test(password)) score -= 10;
    if (sequentialNumbers.test(password)) score -= 10;
    if (commonWords.test(password)) score -= 20;
    
    // Ensure score stays within 0-100 range
    return Math.max(0, Math.min(100, score));
}

// Form step navigation
function initFormSteps() {
    const steps = document.querySelectorAll('.form-steps .step');
    const stepContents = document.querySelectorAll('.form-steps .step-content');
    const nextButtons = document.querySelectorAll('.form-steps .btn-next');
    const prevButtons = document.querySelectorAll('.form-steps .btn-prev');
    
    if (!steps.length || !stepContents.length) return;
    
    function goToStep(stepIndex) {
        // Validate current active step before proceeding
        const currentActiveStep = document.querySelector('.step-content.active');
        const currentStepIndex = Array.from(stepContents).indexOf(currentActiveStep);
        
        // Only validate if moving to next step
        if (stepIndex > currentStepIndex) {
            const isValid = validateStepFields(currentActiveStep);
            if (!isValid) return false;
        }
        
        // Update step indicators
        steps.forEach((step, i) => {
            if (i < stepIndex) {
                step.classList.remove('active');
                step.classList.add('completed');
            } else if (i === stepIndex) {
                step.classList.add('active');
                step.classList.remove('completed');
            } else {
                step.classList.remove('active', 'completed');
            }
        });
        
        // Update step content visibility
        stepContents.forEach((content, i) => {
            if (i === stepIndex) {
                content.classList.add('active');
                setTimeout(() => {
                    content.style.opacity = 1;
                }, 50);
            } else {
                content.classList.remove('active');
                content.style.opacity = 0;
            }
        });
        
        return true;
    }
    
    function validateStepFields(stepElement) {
        const inputs = stepElement.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('is-invalid');
                
                // Add shake animation
                input.classList.add('shake');
                setTimeout(() => {
                    input.classList.remove('shake');
                }, 500);
            } else {
                input.classList.remove('is-invalid');
            }
        });
        
        return isValid;
    }
    
    // Set up event listeners for next buttons
    nextButtons.forEach(button => {
        button.addEventListener('click', function() {
            const stepIndex = parseInt(this.dataset.step);
            goToStep(stepIndex);
        });
    });
    
    // Set up event listeners for previous buttons
    prevButtons.forEach(button => {
        button.addEventListener('click', function() {
            const stepIndex = parseInt(this.dataset.step);
            goToStep(stepIndex);
        });
    });
    
    // Initialize with first step active
    goToStep(0);
}

// Live form validation
function initLiveValidation() {
    const forms = document.querySelectorAll('.needs-live-validation');
    
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            // Validate on blur
            input.addEventListener('blur', function() {
                validateInput(this);
            });
            
            // Clear error on focus
            input.addEventListener('focus', function() {
                this.classList.remove('is-invalid');
                const errorMsg = this.parentElement.querySelector('.invalid-feedback');
                if (errorMsg) errorMsg.style.display = 'none';
            });
        });
        
        // Prevent form submission if invalid
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            inputs.forEach(input => {
                if (!validateInput(input)) {
                    isValid = false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                e.stopPropagation();
                
                // Scroll to first error
                const firstError = form.querySelector('.is-invalid');
                if (firstError) {
                    firstError.focus();
                    window.scrollTo({
                        top: firstError.getBoundingClientRect().top + window.pageYOffset - 100,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
}

function validateInput(input) {
    // Skip if input is not required and empty
    if (!input.required && !input.value) {
        return true;
    }
    
    let isValid = true;
    
    // Check if empty but required
    if (input.required && !input.value.trim()) {
        isValid = false;
        setInvalid(input, 'Bu alan boş bırakılamaz');
    }
    
    // Email validation
    else if (input.type === 'email' && input.value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(input.value)) {
            isValid = false;
            setInvalid(input, 'Geçerli bir e-posta adresi giriniz');
        }
    }
    
    // Phone validation
    else if (input.name === 'telefon' || input.name === 'firmatelefon') {
        const phoneRegex = /^[0-9]{10}$/;
        if (input.value && !phoneRegex.test(input.value.replace(/\s+/g, ''))) {
            isValid = false;
            setInvalid(input, 'Geçerli bir telefon numarası giriniz');
        }
    }
    
    // Password validation
    else if (input.type === 'password' && input.value) {
        if (input.value.length < 6) {
            isValid = false;
            setInvalid(input, 'Şifre en az 6 karakter olmalıdır');
        }
    }
    
    // If valid, mark as valid
    if (isValid) {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        
        const errorMsg = input.parentElement.querySelector('.invalid-feedback');
        if (errorMsg) errorMsg.style.display = 'none';
    }
    
    return isValid;
}

function setInvalid(input, message) {
    input.classList.add('is-invalid');
    input.classList.remove('is-valid');
    
    // Find or create error message element
    let errorMsg = input.parentElement.querySelector('.invalid-feedback');
    
    if (!errorMsg) {
        errorMsg = document.createElement('div');
        errorMsg.className = 'invalid-feedback';
        input.parentElement.appendChild(errorMsg);
    }
    
    errorMsg.textContent = message;
    errorMsg.style.display = 'block';
}

// Address autocomplete
function initAddressAutocomplete() {
    const addressFields = document.querySelectorAll('input[name="adres"], input[name="firmaadres"]');
    
    addressFields.forEach(field => {
        field.addEventListener('focus', function() {
            // You would typically initialize a Google Places or similar API here
            console.log('Address field focused - autocomplete would start');
        });
    });
}

// Toggle password visibility
function initPasswordToggles() {
    const toggles = document.querySelectorAll('.toggle-password');
    
    toggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const targetId = this.getAttribute('data-toggle');
            const targetInput = document.querySelector(targetId);
            
            if (targetInput) {
                if (targetInput.type === 'password') {
                    targetInput.type = 'text';
                    this.classList.remove('fa-eye');
                    this.classList.add('fa-eye-slash');
                } else {
                    targetInput.type = 'password';
                    this.classList.remove('fa-eye-slash');
                    this.classList.add('fa-eye');
                }
            }
        });
    });
}

// Accordion functionality
function initAccordions() {
    const accordionHeaders = document.querySelectorAll('.accordion-header');
    
    accordionHeaders.forEach(header => {
        header.addEventListener('click', function() {
            // Toggle active class on header
            this.classList.toggle('active');
            
            // Find the associated body
            const body = this.nextElementSibling;
            
            // Toggle active class on body
            body.classList.toggle('active');
        });
    });
}

// Initialize all form enhancements
document.addEventListener('DOMContentLoaded', function() {
    initPasswordStrengthMeter('#password_in_2', '.password-strength-bar-fill', '.password-strength-text');
    initFormSteps();
    initLiveValidation();
    initAddressAutocomplete();
    initPasswordToggles();
    initAccordions();
    
    // Phone number formatting
    const phoneInputs = document.querySelectorAll('input[name="telefon"], input[name="firmatelefon"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
            e.target.value = !x[2] ? x[1] : x[1] + ' ' + x[2] + (x[3] ? ' ' + x[3] : '');
        });
    });
    
    // Dark mode toggle if it exists
    const darkModeToggle = document.querySelector('#dark-mode-toggle');
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            
            // Store preference in localStorage
            const isDarkMode = document.body.classList.contains('dark-mode');
            localStorage.setItem('darkMode', isDarkMode ? 'enabled' : 'disabled');
        });
        
        // Check for saved preference
        if (localStorage.getItem('darkMode') === 'enabled') {
            document.body.classList.add('dark-mode');
        }
    }
});
