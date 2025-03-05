document.addEventListener('DOMContentLoaded', function() {
    // Initialize quantity buttons
    initQuantityButtons();
    
    // Initialize product image slider if it exists
    if (document.querySelector('.owl-carousel')) {
        initProductSlider();
    }
    
    console.log('Product detail JS loaded successfully');
});

// Handle quantity buttons
function initQuantityButtons() {
    // This button will increment the value
    $('.inc').click(function(e) {
        e.preventDefault();
        var $input = $(this).parents('.numbers-row').find('.qty2');
        var currentVal = parseInt($input.val(), 10);
        if (!isNaN(currentVal)) {
            $input.val(currentVal + 1);
        }
    });

    // This button will decrement the value
    $('.dec').click(function(e) {
        e.preventDefault();
        var $input = $(this).parents('.numbers-row').find('.qty2');
        var currentVal = parseInt($input.val(), 10);
        if (!isNaN(currentVal) && currentVal > 1) {
            $input.val(currentVal - 1);
        }
    });
}

// Initialize product slider
function initProductSlider() {
    // Main slider
    $('.main').owlCarousel({
        items: 1,
        loop: false,
        margin: 0,
        nav: true,
        dots: false,
        navText: ['<i class="ti-angle-left"></i>', '<i class="ti-angle-right"></i>']
    });
    
    // Thumbnails
    $('.thumbs').owlCarousel({
        items: 4,
        loop: false,
        margin: 10,
        nav: true,
        dots: false,
        navText: ['<i class="ti-angle-left"></i>', '<i class="ti-angle-right"></i>']
    });
    
    // Sync thumbnails with main slider
    $('.thumbs .item').on('click', function() {
        var index = $(this).index();
        $('.main').trigger('to.owl.carousel', [index, 300]);
    });
}

// Add to cart function
function sepeteEkle(siteUrl, urunID) {
    const adet = document.getElementById('adet').value;
    if (!adet || adet < 1) {
        alert('Lütfen geçerli bir adet girin.');
        return;
    }
    
    // Collect variation options if any
    let varyasyonlar = {};
    const varyasyonSelects = document.querySelectorAll('select[name^="varyasyon"]');
    varyasyonSelects.forEach(select => {
        const varyasyonID = select.name.match(/\d+/)[0];
        varyasyonlar[varyasyonID] = select.value;
    });
    
    // AJAX request to add to cart
    $.ajax({
        url: siteUrl + 'ajax.php',
        type: 'POST',
        data: {
            islem: 'sepeteEkle',
            urunID: urunID,
            adet: adet,
            varyasyonlar: varyasyonlar
        },
        success: function(response) {
            try {
                const result = JSON.parse(response);
                if (result.success) {
                    alert('Ürün sepete eklendi.');
                    // You might want to update cart count here
                } else {
                    alert(result.message || 'Bir hata oluştu. Lütfen tekrar deneyin.');
                }
            } catch (e) {
                console.error('JSON parse error:', e);
                alert('Bir hata oluştu. Lütfen tekrar deneyin.');
            }
        },
        error: function() {
            alert('Sunucu hatası. Lütfen tekrar deneyin.');
        }
    });
}

// Add to favorites function
function favoriyeEkle(siteUrl, urunID, hash) {
    $.ajax({
        url: siteUrl + 'ajax.php',
        type: 'POST',
        data: {
            islem: 'favoriyeEkle',
            urunID: urunID,
            hash: hash
        },
        success: function(response) {
            try {
                const result = JSON.parse(response);
                alert(result.message);
            } catch (e) {
                console.error('JSON parse error:', e);
                alert('Bir hata oluştu. Lütfen tekrar deneyin.');
            }
        },
        error: function() {
            alert('Sunucu hatası. Lütfen tekrar deneyin.');
        }
    });
}
