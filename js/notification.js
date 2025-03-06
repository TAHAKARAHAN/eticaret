// Notification system
function showNotification(title, message, type = 'success', duration = 5000) {
    // Create notification element if it doesn't exist
    let notification = document.getElementById('cart-notification');
    
    if (!notification) {
        notification = document.createElement('div');
        notification.id = 'cart-notification';
        notification.className = 'notification';
        
        notification.innerHTML = `
            <i class="ti-check"></i>
            <div class="notification-content">
                <div class="notification-title"></div>
                <div class="notification-message"></div>
                <div class="notification-actions">
                    <a href="${SITE}sepet" class="goto-cart">Sepete Git</a>
                    <a href="#" class="continue-shopping">Alışverişe Devam Et</a>
                </div>
            </div>
            <button class="notification-close">&times;</button>
        `;
        
        document.body.appendChild(notification);
        
        // Add event listener for close button
        notification.querySelector('.notification-close').addEventListener('click', function() {
            notification.classList.remove('show');
        });
        
        // Add event listener for continue shopping button
        notification.querySelector('.continue-shopping').addEventListener('click', function(e) {
            e.preventDefault();
            notification.classList.remove('show');
        });
    }
    
    // Set content
    notification.querySelector('.notification-title').textContent = title;
    notification.querySelector('.notification-message').textContent = message;
    
    // Set type (changes the color)
    notification.className = 'notification ' + type;
    
    // Show notification
    setTimeout(() => notification.classList.add('show'), 10);
    
    // Hide after duration
    if (duration) {
        setTimeout(() => {
            if (notification.classList.contains('show')) {
                notification.classList.remove('show');
            }
        }, duration);
    }
}

// Wrap the original sepeteEkle function to add notification
const originalSepeteEkle = window.sepeteEkle || function() {};
window.sepeteEkle = function(site, urunID) {
    // Call the original function
    const result = originalSepeteEkle(site, urunID);
    
    // Show notification
    showNotification(
        "Ürün Sepete Eklendi", 
        "Ürün başarıyla sepetinize eklendi.",
        "success"
    );
    
    return result;
};
