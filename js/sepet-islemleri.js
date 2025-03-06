/**
 * Cart operations handler
 * Updates cart count and provides notifications for cart interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize cart event listeners
    setupCartEvents();
});

/**
 * Set up event listeners for cart interactions
 */
function setupCartEvents() {
    // Listen for "Add to Cart" clicks
    const addToCartButtons = document.querySelectorAll('.btn_add_to_cart');
    
    if (addToCartButtons.length > 0) {
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                // Wait a moment for the cart to be updated server-side
                setTimeout(updateHeaderCartCount, 500);
            });
        });
    }
    
    // Listen for cart remove actions
    const removeButtons = document.querySelectorAll('a[href*="sepet-sil"]');
    
    if (removeButtons.length > 0) {
        removeButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                // We'll update the count after the page reloads
            });
        });
    }
}

/**
 * Make an AJAX request to get the current cart count
 */
function updateHeaderCartCount() {
    // Create a new XMLHttpRequest
    const xhr = new XMLHttpRequest();
    
    // Configure it: GET-request to get the cart count
    xhr.open('GET', `${window.location.origin}/eticaret/ajax-sepet-sayisi.php`, true);
    
    // What to do when response is ready
    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    // Update all cart count elements
                    const cartCountElements = document.querySelectorAll('.cart_bt strong');
                    cartCountElements.forEach(element => {
                        element.textContent = response.count;
                    });
                    
                    // Update the text in dropdown
                    const cartDropdownText = document.querySelector('.dropdown-cart .total_drop .clearfix strong');
                    if (cartDropdownText) {
                        if (response.count > 0) {
                            cartDropdownText.textContent = `Sepetinizde ${response.count} ürün var`;
                        } else {
                            cartDropdownText.textContent = "Sepetiniz boş";
                        }
                    }
                }
            } catch (error) {
                console.error('Error parsing cart count response:', error);
            }
        }
    };
    
    // Send the request
    xhr.send();
}
