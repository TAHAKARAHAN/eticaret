function sepeteEkle(SITE, urunID) {
    // Validate product ID
    if (!urunID || isNaN(urunID)) {
        alert("Geçersiz ürün ID!");
        return false;
    }
    
    // Show loading state
    const btnAddToCart = document.querySelector('.btn_add_to_cart a');
    if (btnAddToCart) {
        const originalText = btnAddToCart.innerText;
        btnAddToCart.innerText = "Ekleniyor...";
        btnAddToCart.style.pointerEvents = "none";
    }

    // Serialize form data for submission
    const formData = $("#urunbilgisi").serialize();
    
    // Make AJAX request
    $.ajax({
        method: "POST",
        url: SITE + "ajax.php",
        data: formData + "&urunID=" + urunID + "&islemtipi=sepeteEkle",
        success: function(response) {
            // Reset button state
            if (btnAddToCart) {
                btnAddToCart.innerText = "Sepete Ekle";
                btnAddToCart.style.pointerEvents = "";
            }
            
            // Handle response
            if (response.trim() === "TAMAM") {
                // alert("Ürün sepete eklendi.");
                
                // Optional: Refresh the page or update cart counter
                // location.reload();
            } else if (response.trim() === "STOK") {
                alert("Bu ürün stokta bulunmuyor.");
            } else {
                alert("İşleminiz şuan geçersizdir. Lütfen daha sonra tekrar deneyiniz.");
                console.log("Error response:", response);
            }
        },
        error: function(xhr, status, error) {
            // Reset button state
            if (btnAddToCart) {
                btnAddToCart.innerText = "Sepete Ekle";
                btnAddToCart.style.pointerEvents = "";
            }
            
            alert("İşleminiz şuan geçersizdir. Lütfen daha sonra tekrar deneyiniz.");
            console.log("AJAX Error:", error);
        }
    });
    
    return false;
}

// Enhanced notification system with product info and total price
function showNotification(title, message, type = 'success', duration = 3000, productInfo = null) {
    // Create notification element if it doesn't exist
    let notification = document.getElementById('cart-notification');
    
    if (!notification) {
        notification = document.createElement('div');
        notification.id = 'cart-notification';
        notification.className = 'notification';
        
        // Select appropriate icon based on type
        let icon = 'ti-check';
        if (type === 'error') icon = 'ti-close';
        if (type === 'warning') icon = 'ti-alert';
        if (type === 'info') icon = 'ti-info-alt';
        
        notification.innerHTML = `
            <i class="${icon}"></i>
            <div class="notification-content">
                <div class="notification-title"></div>
                <div class="notification-message"></div>
                <div class="notification-product-container"></div>
                <div class="notification-actions">
                    <a href="${SITE}sepet" class="goto-cart"><span>Sepete Git</span></a>
                    <a href="#" class="continue-shopping"><span>Alışverişe Devam Et</span></a>
                </div>
            </div>
            <button class="notification-close">&times;</button>
        `;
        
        document.body.appendChild(notification);
        
        // Add event listener for close button
        notification.querySelector('.notification-close').addEventListener('click', function() {
            hideNotification(notification);
        });
        
        // Add event listener for continue shopping button
        notification.querySelector('.continue-shopping').addEventListener('click', function(e) {
            e.preventDefault();
            hideNotification(notification);
        });
    }
    
    // Set content
    notification.querySelector('.notification-title').textContent = title;
    notification.querySelector('.notification-message').textContent = message;
    
    // Update icon based on type
    let icon = 'ti-check';
    if (type === 'error') icon = 'ti-close';
    if (type === 'warning') icon = 'ti-alert';
    if (type === 'info') icon = 'ti-info-alt';
    notification.querySelector('i').className = icon;
    
    // Add product info if provided
    const productContainer = notification.querySelector('.notification-product-container');
    productContainer.innerHTML = '';
    
    if (productInfo) {
        // Calculate total price if possible
        let totalPrice = '';
        if (productInfo.rawPrice && productInfo.quantity) {
            let unitPrice = parseFloat(productInfo.rawPrice);
            let quantity = parseInt(productInfo.quantity);
            let total = unitPrice * quantity;
            totalPrice = formatPrice(total);
        }
        
        const productElement = document.createElement('div');
        productElement.className = 'notification-product';
        
        productElement.innerHTML = `
            <div class="notification-product-image" style="background-image: url(${productInfo.image})"></div>
            <div class="notification-product-info">
                <div class="notification-product-name">${productInfo.name}</div>
                <div class="notification-product-details">
                    <span class="notification-product-price">${productInfo.price}</span>
                    <span class="notification-product-quantity">Adet: ${productInfo.quantity}</span>
                </div>
                ${totalPrice ? `
                <div class="notification-total">
                    <span class="notification-total-label">Toplam:</span>
                    <span class="notification-total-price">${totalPrice}</span>
                </div>` : ''}
            </div>
        `;
        
        productContainer.appendChild(productElement);
    }
    
    // Set type (changes the color)
    notification.className = 'notification ' + type;
    
    // Show notification with animation
    setTimeout(() => notification.classList.add('show'), 10);
    
    // Hide after duration
    if (duration) {
        setTimeout(() => {
            if (notification.classList.contains('show')) {
                hideNotification(notification);
            }
        }, duration);
    }
}

// Helper function to format price consistently
function formatPrice(price) {
    return price.toLocaleString('tr-TR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }) + ' TL';
}

function hideNotification(notification) {
    notification.style.animation = 'slideOut 0.3s forwards';
    
    setTimeout(() => {
        notification.classList.remove('show');
        notification.style.animation = '';
    }, 300);
}

// Store original sepeteEkle function if it exists
const originalSepeteEkle = window.sepeteEkle;

// Replace sepeteEkle with our enhanced version
window.sepeteEkle = function(site, urunID) {
    // Make sure we have SITE defined
    if (typeof SITE === 'undefined') {
        window.SITE = site;
    }

    // Show loading indicator
    const addToCartButton = document.querySelector('.btn_add_to_cart a');
    const originalButtonText = addToCartButton ? addToCartButton.textContent : 'Sepete Ekle';
    
    if (addToCartButton) {
        addToCartButton.textContent = "Ekleniyor...";
        addToCartButton.style.opacity = "0.7";
    }
    
    // Get product information from the page
    let productName = document.querySelector('.prod_info h1') ? 
        document.querySelector('.prod_info h1').textContent.trim() : 'Ürün';
    
    let productPrice = document.querySelector('.new_price') ? 
        document.querySelector('.new_price').textContent.trim() : '';
    
    // Extract raw price for calculations (assuming format is "123,45 TL")
    let rawPrice = 0;
    if (productPrice) {
        // Remove thousand separators, replace comma with dot, and remove TL
        rawPrice = productPrice.replace(/\./g, '').replace(',', '.').replace(/[^\d.]/g, '');
    }
    
    let productImage = document.querySelector('.item-box') ? 
        document.querySelector('.item-box').style.backgroundImage.replace('url(', '').replace(')', '').replace(/"/g, '') : 
        `${SITE}img/product_placeholder.jpg`;
    
    let quantity = document.querySelector('#adet') ? 
        document.querySelector('#adet').value : '1';
    
    let productInfo = {
        name: productName,
        price: productPrice,
        rawPrice: rawPrice,
        image: productImage,
        quantity: quantity
    };

    // Call original function if it exists
    if (typeof originalSepeteEkle === 'function') {
        originalSepeteEkle(site, urunID);
    } else {
        // If original function doesn't exist, implement basic functionality
        const formData = $("#urunbilgisi").serialize();
        
        $.ajax({
            method: "POST",
            url: site + "ajax.php",
            data: formData + "&urunID=" + urunID + "&islemtipi=sepeteEkle",
            success: function(response) {
                if(response.trim() === "TAMAM") {
                    console.log("Ürün sepete eklendi");
                } else {
                    console.log("Hata oluştu:", response);
                }
            }
        });
    }

    // Reset button
    setTimeout(() => {
        if (addToCartButton) {
            addToCartButton.textContent = originalButtonText;
            addToCartButton.style.opacity = "1";
        }
    }, 500);

    // Show notification with product info
    showNotification(
        "Ürün Sepete Eklendi", 
        "Ürün başarıyla sepetinize eklendi.",
        "success",
        5000,
        productInfo
    );
};

function sifreIste(SITE) {

    var mailadresi=$(".sifremail").val();
    $.ajax({
        method:"POST",
        url:SITE+"ajax.php",
        data:{"mailadresi":mailadresi,"islemtipi":"sifreIste"},
        success: function(sonuc)
        {
            if(sonuc=="TAMAM")
            {
               window.location.href=SITE+"sifre-belirle/dogrulama";
            }
            else
            {
                alert("İşleminiz şuan geçersizdir. Lütfen daha sonra tekrar deneyiniz.");
            }
           
            
         }
         
        });
    
}


function favoriyeEkle(SITE,urunID,key)
{
    $.ajax({
        method:"POST",
        url:SITE+"ajax.php",
        data:{"urunID":urunID,"urunKey":key,"islemtipi":"favoriyeEkle"},
        success: function(sonuc)
        {
            if(sonuc=="TAMAM")
            {
               alert("Ürününüz Favoriye Eklendi.");
            }
            else if (sonuc=="VAR")
            {
                alert("Bu ürün zaten favorinizde!");
            }
            else if(sonuc=="GUVENLIK")
            {
                alert("Güvenlik ihlali tespit edildi!");
            }
            else
            {
                alert("İşleminiz şuan geçersizdir. Üyelik girişi yapınız.");
            }
           
            
         }
         
        });
}