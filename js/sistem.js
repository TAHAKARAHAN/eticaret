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
                alert("Ürün sepete eklendi.");
                
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