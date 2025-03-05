/**
 * Advanced icon positioning fix
 * This script ensures all form inputs have proper icon positioning
 */
document.addEventListener('DOMContentLoaded', function() {
    // Define icon mappings for different input types/names
    const iconMappings = {
        // Input types
        'email': 'fa-envelope',
        'password': 'fa-lock',
        'text': 'fa-font',
        'tel': 'fa-phone',
        'number': 'fa-hashtag',
        
        // Input names
        'mail': 'fa-envelope',
        'email': 'fa-envelope',
        'sifre': 'fa-lock',
        'ad': 'fa-user',
        'soyad': 'fa-user',
        'adres': 'fa-map-marker-alt',
        'firmaadres': 'fa-map-marker-alt',
        'ilce': 'fa-map-marker-alt',
        'firmailce': 'fa-map-marker-alt',
        'postakodu': 'fa-mail-bulk',
        'firmapostakodu': 'fa-mail-bulk',
        'telefon': 'fa-phone',
        'firmatelefon': 'fa-phone',
        'firmaadi': 'fa-building',
        'vergidairesi': 'fa-file-invoice',
        'vergino': 'fa-hashtag'
    };
    
    // Process all form fields
    function processFormFields() {
        // Process all form inputs (except checkboxes and radios)
        document.querySelectorAll('input:not([type="checkbox"]):not([type="radio"]):not([type="hidden"]), select, textarea').forEach(input => {
            const parentNode = input.parentNode;
            
            // If the parent is not an input-icon div, wrap it
            if (!parentNode.classList.contains('input-icon') && !parentNode.classList.contains('custom-select-form')) {
                wrapInputWithIconContainer(input);
                addAppropriateIcon(input);
            } 
            // If it's already in an input-icon but missing the icon
            else if (parentNode.classList.contains('input-icon') && !parentNode.querySelector('i:not(.toggle-password)')) {
                addAppropriateIcon(input);
            }
        });
        
        // Process all select elements
        document.querySelectorAll('select').forEach(select => {
            const parentNode = select.parentNode;
            
            // If parent is not a custom-select-form, wrap it
            if (!parentNode.classList.contains('custom-select-form')) {
                wrapSelectWithContainer(select);
            }
            // If it's already in a custom-select-form but missing the icon
            else if (parentNode.classList.contains('custom-select-form') && !parentNode.querySelector('i')) {
                addSelectIcon(select);
            }
        });
    }
    
    // Wrap an input with the input-icon container
    function wrapInputWithIconContainer(input) {
        const wrapper = document.createElement('div');
        wrapper.className = 'input-icon';
        
        // Get next sibling to preserve DOM order (for things like invalid-feedback)
        const nextSibling = input.nextSibling;
        const parent = input.parentNode;
        
        // Replace the input with the wrapper containing the input
        wrapper.appendChild(input);
        
        if (nextSibling) {
            parent.insertBefore(wrapper, nextSibling);
        } else {
            parent.appendChild(wrapper);
        }
        
        return wrapper;
    }
    
    // Wrap a select with the custom-select-form container
    function wrapSelectWithContainer(select) {
        const wrapper = document.createElement('div');
        wrapper.className = 'custom-select-form';
        
        // Get next sibling to preserve DOM order
        const nextSibling = select.nextSibling;
        const parent = select.parentNode;
        
        // Replace the select with the wrapper containing the select
        wrapper.appendChild(select);
        
        if (nextSibling) {
            parent.insertBefore(wrapper, nextSibling);
        } else {
            parent.appendChild(wrapper);
        }
        
        // Add icon to the select
        addSelectIcon(select);
        
        return wrapper;
    }
    
    // Add appropriate icon based on input type/name
    function addAppropriateIcon(input) {
        let iconClass = '';
        
        // Check by input name first
        if (input.name && iconMappings[input.name]) {
            iconClass = iconMappings[input.name];
        } 
        // Then check by input type
        else if (input.type && iconMappings[input.type]) {
            iconClass = iconMappings[input.type];
        } 
        // Default icon if no matches
        else {
            iconClass = 'fa-edit';
        }
        
        // Special case for password fields to add toggle
        if (input.type === 'password') {
            // Add the password toggle icon if it doesn't exist
            if (!input.parentNode.querySelector('.toggle-password')) {
                const toggleIcon = document.createElement('i');
                toggleIcon.className = 'fas fa-eye toggle-password';
                toggleIcon.setAttribute('data-toggle', `#${input.id}`);
                input.parentNode.appendChild(toggleIcon);
                
                // Add toggle event listener
                toggleIcon.addEventListener('click', function() {
                    const targetInput = document.querySelector(this.getAttribute('data-toggle'));
                    if (targetInput.type === 'password') {
                        targetInput.type = 'text';
                        this.classList.remove('fa-eye');
                        this.classList.add('fa-eye-slash');
                    } else {
                        targetInput.type = 'password';
                        this.classList.remove('fa-eye-slash');
                        this.classList.add('fa-eye');
                    }
                });
            }
        }
        
        // Add the main field icon if it doesn't exist
        if (!input.parentNode.querySelector(`i.fas.${iconClass}`)) {
            const icon = document.createElement('i');
            icon.className = `fas ${iconClass}`;
            input.parentNode.appendChild(icon);
        }
    }
    
    // Add icon to select element
    function addSelectIcon(select) {
        let iconClass = '';
        
        // Determine icon based on select name
        if (select.name.includes('il')) {
            iconClass = 'fa-map-marked-alt';
        } else {
            iconClass = 'fa-chevron-down';
        }
        
        // Add icon if it doesn't exist
        if (!select.parentNode.querySelector(`i.fas.${iconClass}`)) {
            const icon = document.createElement('i');
            icon.className = `fas ${iconClass}`;
            select.parentNode.appendChild(icon);
        }
    }
    
    // Run the processor initially
    processFormFields();
    
    // Set up observer to handle dynamically added content
    const observer = new MutationObserver((mutations) => {
        let shouldProcess = false;
        
        mutations.forEach(mutation => {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                mutation.addedNodes.forEach(node => {
                    // If the added node is an element and could contain form fields
                    if (node.nodeType === 1) {
                        if (node.tagName === 'INPUT' || node.tagName === 'SELECT' || node.tagName === 'TEXTAREA' ||
                            node.querySelector('input, select, textarea')) {
                            shouldProcess = true;
                        }
                    }
                });
            }
        });
        
        if (shouldProcess) {
            processFormFields();
        }
    });
    
    // Start observing document for DOM changes
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});
