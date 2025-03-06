// Functions to handle cart notifications
document.addEventListener('DOMContentLoaded', function() {
    // Initialize notification functionality
    setupCartNotification();
});

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
