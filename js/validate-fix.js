/**
 * Form validation and icon positioning fixes
 */
document.addEventListener('DOMContentLoaded', function() {
    // Fix invalid feedback display
    const invalidFeedbacks = document.querySelectorAll('.invalid-feedback');
    invalidFeedbacks.forEach(feedback => {
        feedback.style.display = 'none';
    });
    
    // Fix form validation behavior
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(form => {
        // Show feedback only when form is submitted
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                
                // Find all invalid inputs
                const invalidInputs = form.querySelectorAll(':invalid');
                invalidInputs.forEach(input => {
                    // Show feedback for invalid inputs
                    const feedback = input.nextElementSibling;
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.style.display = 'block';
                    }
                    
                    // Add shake animation
                    input.classList.add('shake');
                    setTimeout(() => {
                        input.classList.remove('shake');
                    }, 500);
                });
                
                // Scroll to first error
                if (invalidInputs.length > 0) {
                    invalidInputs[0].focus();
                    window.scrollTo({
                        top: invalidInputs[0].getBoundingClientRect().top + window.pageYOffset - 100,
                        behavior: 'smooth'
                    });
                }
            }
            form.classList.add('was-validated');
        });
        
        // Reset validation on input focus
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                // Hide feedback when input is focused
                const feedback = this.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.style.display = 'none';
                }
                this.classList.remove('is-invalid');
            });
        });
    });
    
    // Fix icon positioning
    const fixIconPositioning = () => {
        // Add icons to form fields if not already present
        document.querySelectorAll('input[type="email"]').forEach(input => {
            if (!input.parentElement.querySelector('i.fa-envelope')) {
                const icon = document.createElement('i');
                icon.className = 'fas fa-envelope';
                input.parentElement.appendChild(icon);
            }
        });
        
        document.querySelectorAll('input[type="password"]').forEach(input => {
            if (!input.parentElement.querySelector('i.toggle-password')) {
                const icon = document.createElement('i');
                icon.className = 'fas fa-eye toggle-password';
                icon.setAttribute('data-toggle', `#${input.id}`);
                input.parentElement.appendChild(icon);
                
                // Add toggle functionality
                icon.addEventListener('click', function() {
                    const target = document.querySelector(this.getAttribute('data-toggle'));
                    if (target.type === 'password') {
                        target.type = 'text';
                        this.classList.remove('fa-eye');
                        this.classList.add('fa-eye-slash');
                    } else {
                        target.type = 'password';
                        this.classList.remove('fa-eye-slash');
                        this.classList.add('fa-eye');
                    }
                });
            }
        });
        
        document.querySelectorAll('input[name="telefon"], input[name="firmatelefon"]').forEach(input => {
            if (!input.parentElement.querySelector('i.fa-phone')) {
                const icon = document.createElement('i');
                icon.className = 'fas fa-phone';
                input.parentElement.appendChild(icon);
            }
        });
        
        document.querySelectorAll('input[name="adres"], input[name="firmaadres"]').forEach(input => {
            if (!input.parentElement.querySelector('i.fa-map-marker-alt')) {
                const icon = document.createElement('i');
                icon.className = 'fas fa-map-marker-alt';
                input.parentElement.appendChild(icon);
            }
        });
        
        document.querySelectorAll('input[name="ad"], input[name="soyad"]').forEach(input => {
            if (!input.parentElement.querySelector('i.fa-user')) {
                const icon = document.createElement('i');
                icon.className = 'fas fa-user';
                input.parentElement.appendChild(icon);
            }
        });
        
        document.querySelectorAll('input[name="firmaadi"]').forEach(input => {
            if (!input.parentElement.querySelector('i.fa-building')) {
                const icon = document.createElement('i');
                icon.className = 'fas fa-building';
                input.parentElement.appendChild(icon);
            }
        });
        
        // Fix custom select appearance
        document.querySelectorAll('.custom-select-form').forEach(select => {
            if (!select.querySelector('i.fa-map-marker-alt')) {
                const icon = document.createElement('i');
                icon.className = 'fas fa-map-marker-alt';
                select.appendChild(icon);
            }
        });
    };
    
    // Run icon positioning fix
    fixIconPositioning();
    
    // Fix icon positioning after AJAX updates
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.addedNodes && mutation.addedNodes.length > 0) {
                // Check if any of the added nodes are form elements
                for (let i = 0; i < mutation.addedNodes.length; i++) {
                    const node = mutation.addedNodes[i];
                    if (node.nodeType === 1 && (
                        node.classList.contains('form-group') || 
                        node.querySelector('.form-group')
                    )) {
                        fixIconPositioning();
                        break;
                    }
                }
            }
        });
    });
    
    // Start observing the document body for changes
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});
