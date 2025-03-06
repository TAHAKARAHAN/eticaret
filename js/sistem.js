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

// Notification system
function showNotification(title, message, type = 'success', duration = 3000) {
    // Create notification element if it doesn't exist
    let notification = document.getElementById('cart-notification');
    
    if (!notification) {
        notification = document.createElement('div');
        notification.id = 'cart-notification';
        notification.className = 'notification';
        
        notification.innerHTML = `
            <i class="ti-check"></i>
            <div class="notification-content">
                <div class="notification-title">${title}</div>
                <div class="notification-message">${message}</div>
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
    } else {
        notification.querySelector('.notification-title').textContent = title;
        notification.querySelector('.notification-message').textContent = message;
    }
    
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

// Store original sepeteEkle function
const originalSepeteEkle = window.sepeteEkle;

// Replace sepeteEkle with our version
window.sepeteEkle = function(site, urunID) {
    // Make sure we have SITE defined
    if (typeof SITE === 'undefined') {
        window.SITE = site;
    }

    // Call original function if it exists
    if (typeof originalSepeteEkle === 'function') {
        originalSepeteEkle(site, urunID);
    } else {
        // If original function doesn't exist, implement basic functionality
        $.ajax({
            method: "POST",
            url: site + "ajax.php",
            data: { "sepeteEkle": "true", "urunID": urunID },
            success: function(sonuc) {
                if(sonuc.indexOf("TAMAM") > 0) {
                    console.log("Ürün sepete eklendi");
                } else {
                    console.log("Hata oluştu");
                }
            }
        });
    }

    // Show notification regardless of what happens with original function
    showNotification(
        "Ürün Sepete Eklendi", 
        "Ürün başarıyla sepetinize eklendi.",
        "success"
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