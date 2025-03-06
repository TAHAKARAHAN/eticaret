/**
 * Sepet işlemlerini yönetir
 */
function sepeteEkle(siteURL, urunID) {
    // Show loading animation
    cartNotification.showLoading();
    
    // Get quantity and variations
    let adet = 1;
    const adetElement = document.querySelector('.adet');
    if (adetElement && !isNaN(parseInt(adetElement.value))) {
        adet = parseInt(adetElement.value);
    }
    
    let varyasyonlar = [];
    const varyasyonSelects = document.querySelectorAll('select[name="varyasyon[]"]');
    if (varyasyonSelects.length > 0) {
        varyasyonSelects.forEach(select => {
            varyasyonlar.push(select.value);
        });
    }
    
    // Disable add to cart button
    const addButton = document.querySelector('.btn_add_to_cart');
    if (addButton) {
        addButton.style.pointerEvents = 'none';
        addButton.style.opacity = '0.7';
    }
    
    // Send request
    fetch(siteURL + 'ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            'islem': 'sepeteEkle',
            'urunID': urunID,
            'adet': adet,
            'varyasyonlar': JSON.stringify(varyasyonlar)
        })
    })
    .then(response => response.json())
    .then(data => {
        // Hide loading
        cartNotification.hideLoading();
        
        if (data.success) {
            // Show success notification with product details
            cartNotification.show({
                name: data.message,
                image: data.image || siteURL + 'images/urunler/' + data.resim,
                cartUrl: siteURL + 'sepet',
                cartCount: data.cartCount
            });
        } else {
            // Show error notification
            cartNotification.error(data.message || 'Bir hata oluştu');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        cartNotification.hideLoading();
        cartNotification.error('İşlem sırasında bir hata oluştu');
    })
    .finally(() => {
        // Re-enable add to cart button
        if (addButton) {
            addButton.style.pointerEvents = '';
            addButton.style.opacity = '';
        }
    });
}

// Fallback notification function
function showNotification(message, type = 'info') {
    // Check if our notification system is available
    if (window.cartNotification) {
        return cartNotification.show(message, type);
    }
    
    // Create a simple notification container if it doesn't exist
    let container = document.getElementById('notification-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'notification-container';
        container.style.position = 'fixed';
        container.style.top = '20px';
        container.style.right = '20px';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
    }

    // Create notification element
    const notification = document.createElement('div');
    notification.style.padding = '15px 20px';
    notification.style.marginBottom = '10px';
    notification.style.backgroundColor = type === 'error' ? '#f44336' : (type === 'success' ? '#4CAF50' : '#2196F3');
    notification.style.color = '#fff';
    notification.style.borderRadius = '4px';
    notification.style.boxShadow = '0 2px 4px rgba(0,0,0,0.2)';
    notification.innerHTML = message;

    // Add to container
    container.appendChild(notification);

    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Function to update cart count in UI
function updateCartCount(count) {
    const cartCountElements = document.querySelectorAll('.cart_bt .cart-count, .cart-amount');
    
    if (cartCountElements.length) {
        cartCountElements.forEach(element => {
            element.textContent = count;
        });
    }
}

// Function to handle favorites
function favoriyeEkle(siteURL, urunID, urunKey) {
    // Create form data for submission
    let formData = new FormData();
    formData.append('islemtipi', 'favoriyeEkle');
    formData.append('urunID', urunID);
    formData.append('urunKey', urunKey);
    
    // Send AJAX request
    fetch(siteURL + 'ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // Try to parse as JSON first
        return response.json().catch(() => {
            // If JSON parsing fails, return text response
            return response.text().then(text => {
                return { 
                    success: text === "TAMAM", 
                    message: text === "TAMAM" ? "Ürün favorilere eklendi" : "Bir hata oluştu",
                    code: text 
                };
            });
        });
    })
    .then(data => {
        if (data.success || data.code === "TAMAM") {
            if (window.cartNotification) {
                cartNotification.success("Ürün favorilere eklendi");
            } else {
                showNotification("Ürün favorilere eklendi", "success");
            }
        } else if (data.code === "VAR") {
            if (window.cartNotification) {
                cartNotification.warning("Bu ürün zaten favorilerinizde");
            } else {
                showNotification("Bu ürün zaten favorilerinizde", "warning");
            }
        } else {
            if (window.cartNotification) {
                cartNotification.error("İşlem için lütfen üye girişi yapınız");
            } else {
                showNotification("İşlem için lütfen üye girişi yapınız", "error");
            }
        }
    })
    .catch(error => {
        console.error("Error:", error);
        if (window.cartNotification) {
            cartNotification.error("İşlem sırasında bir hata oluştu");
        } else {
            showNotification("İşlem sırasında bir hata oluştu", "error");
        }
    });
}
