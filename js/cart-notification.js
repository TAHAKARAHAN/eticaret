/**
 * Cart notification system
 * Displays notifications when products are added to cart
 */

// Initialize notification system
document.addEventListener('DOMContentLoaded', function() {
    // Create notification container if it doesn't exist
    if (!document.getElementById('notification-container')) {
        const container = document.createElement('div');
        container.id = 'notification-container';
        document.body.appendChild(container);
    }
    // Initialize notification functionality
    setupCartNotification();
});

/**
 * Show a notification message
 * @param {string} message - The message to display
 * @param {string} type - The type of notification (success or error)
 */
function showNotification(message, type = 'success') {
    const container = document.getElementById('notification-container');
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'notification ' + (type === 'error' ? 'error' : '');
    notification.innerHTML = message;
    
    // Add to container
    container.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

/**
 * Update cart item count display
 * @param {number} count - The current cart item count
 */
function updateCartCount(count) {
    const countElements = document.querySelectorAll('.cart_bt strong');
    countElements.forEach(element => {
        element.textContent = count;
    });
}

function setupCartNotification() {
    // Find Add to Cart button
    const addToCartBtn = document.querySelector('.btn_add_to_cart a');
    
    if (addToCartBtn) {
        // Store the original onclick attribute
        const originalOnclick = addToCartBtn.getAttribute('onclick');
        
        // Remove the onclick attribute to prevent the original function from being called directly
        addToCartBtn.removeAttribute('onclick');
        
        // Add our event listener
        addToCartBtn.addEventListener('click', function(e) {
            // Get the product name from the page
            const productName = document.querySelector('h1').innerText;
            
            // Execute the original onclick function using eval
            if (originalOnclick) {
                eval(originalOnclick);
            }
            
            // Show our notification
            showCartNotification(productName);
        });
    }
}

function showCartNotification(productName) {
    // Get notification container
    const notification = document.getElementById('cartNotification');
    const productNameElement = document.getElementById('notificationProductName');
    
    if (!notification) {
        console.error('Cart notification element not found');
        return;
    }
    
    // Set product name if element exists
    if (productNameElement && productName) {
        productNameElement.textContent = productName;
    }
    
    // Show notification
    notification.classList.add('show');
    
    // Hide notification after 4 seconds
    setTimeout(function() {
        hideCartNotification();
    }, 4000);
    
    console.log('Cart notification shown for: ' + productName);
}

function hideCartNotification() {
    const notification = document.getElementById('cartNotification');
    if (notification) {
        notification.classList.remove('show');
    }
}
