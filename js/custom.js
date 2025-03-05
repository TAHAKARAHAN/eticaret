function sepeteEkle(site, urunID) {
    // Validate Product ID
    if (!urunID || isNaN(urunID)) {
        console.error("Invalid product ID:", urunID);
        alert("Geçersiz ürün ID. Lütfen sayfayı yenileyip tekrar deneyiniz.");
        return;
    }
    
    // Safely get quantity - with fallback
    var adet = 1; // Default value
    
    try {
        var adetInput = document.getElementById('adet');
        if (adetInput) {
            var inputValue = adetInput.value;
            console.log("Raw quantity value:", inputValue);
            
            // Parse and validate
            var parsedValue = parseInt(inputValue);
            if (!isNaN(parsedValue) && parsedValue > 0) {
                adet = parsedValue;
            } else {
                console.warn("Invalid quantity value, using default:", adet);
            }
        } else {
            console.warn("Quantity input not found, using default:", adet);
        }
    } catch (e) {
        console.error("Error getting quantity:", e);
    }
    
    console.log("Final values - Product ID:", urunID, "Quantity:", adet);
    
    // Show loading indicator
    var buttonElement = $(".btn_add_to_cart button");
    var originalText = buttonElement.text();
    buttonElement.html('<i class="fa fa-spinner fa-spin"></i> İşleniyor...');
    buttonElement.prop('disabled', true);
    
    // Create form data
    var formData = new FormData();
    formData.append('islemtipi', 'sepeteEkle');
    formData.append('urunID', urunID.toString());
    formData.append('adet', adet.toString());
    
    // Send AJAX request
    $.ajax({
        type: "POST",
        url: site + "ajax.php",
        data: {
            islemtipi: "sepeteEkle",
            urunID: urunID,
            adet: adet
        },
        timeout: 30000, // 30 second timeout
        success: function(response) {
            console.log("AJAX Success - Raw response:", response);
            
            // Clean the response
            response = response.trim();
            console.log("AJAX Success - Cleaned response:", response);
            
            switch(response) {
                case "TAMAM":
                    alert("Ürün sepete eklendi");
                    window.location.href = site + "sepet";
                    break;
                case "STOK":
                    alert("Stok yetersiz");
                    buttonElement.html(originalText);
                    buttonElement.prop('disabled', false);
                    break;
                case "URUN_YOK":
                    alert("Ürün bulunamadı");
                    buttonElement.html(originalText);
                    buttonElement.prop('disabled', false);
                    break;
                case "EKSIK_PARAMETRE":
                    console.error("Missing parameter error. Sent data:", {
                        islemtipi: "sepeteEkle",
                        urunID: urunID,
                        adet: adet
                    });
                    alert("Eksik parametre hatası. Lütfen sayfayı yenileyip tekrar deneyiniz.");
                    buttonElement.html(originalText);
                    buttonElement.prop('disabled', false);
                    break;
                default:
                    console.error("Unknown response:", response);
                    alert("İşlem şuan geçersizdir. Lütfen daha sonra tekrar deneyiniz.");
                    buttonElement.html(originalText);
                    buttonElement.prop('disabled', false);
                    break;
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", error);
            console.error("Status:", status);
            console.error("Response:", xhr.responseText);
            
            alert("Bir hata oluştu. Lütfen daha sonra tekrar deneyiniz. (Hata: " + status + ")");
            
            buttonElement.html(originalText);
            buttonElement.prop('disabled', false);
        }
    });
}

// Document ready function
$(document).ready(function() {
    
    // Handle client type switching (Bireysel/Kurumsal)
    $('input[name="client_type"]').on('change', function() {
        const selectedValue = $(this).val();
        if (selectedValue === 'private') {
            $('.private.box').slideDown(300);
            $('.company.box').slideUp(300);
        } else {
            $('.company.box').slideDown(300);
            $('.private.box').slideUp(300);
        }
    });

    // Show/Hide password functionality
    $('.toggle-password').on('click', function() {
        const input = $($(this).data('toggle'));
        const icon = $(this);
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Forgot password toggle
    $('#forgot').on('click', function(e) {
        e.preventDefault();
        $('#forgot_pw').slideToggle(300);
    });

    // Form validation
    $('.needs-validation').on('submit', function(event) {
        if (this.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
            $(this).addClass('was-validated');
            
            // Scroll to the first error
            const firstError = $('.form-control:invalid').first();
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
                firstError.parent().addClass('shake');
            }
        }
        $(this).addClass('was-validated');
    });

    // Add floating label effect
    $('.floating .form-control').on('focus blur', function (e) {
        $(this).parents('.form-group').toggleClass('focused', (e.type === 'focus'));
    }).trigger('blur');
    
    // Add ripple effect to buttons
    $('.btn').addClass('ripple');
    
    // Input field animations
    $('.form-control').on('focus', function() {
        $(this).parent('.form-group').addClass('focused');
    }).on('blur', function() {
        if (!$(this).val()) {
            $(this).parent('.form-group').removeClass('focused');
        }
    });
    
    // Initialize any filled fields
    $('.form-control').each(function() {
        if ($(this).val()) {
            $(this).parent('.form-group').addClass('focused');
        }
    });
});

// Password strength checker
function checkPasswordStrength(password) {
    const progressBar = $('#password-strength');
    const feedback = $('#password-feedback');
    
    if (!password) {
        progressBar.width('0%').removeClass('bg-danger bg-warning bg-info bg-success');
        feedback.text('');
        return;
    }
    
    // Password strength criteria
    const hasUpperCase = /[A-Z]/.test(password);
    const hasLowerCase = /[a-z]/.test(password);
    const hasNumbers = /\d/.test(password);
    const hasSpecialChars = /[!@#$%^&*(),.?":{}|<>]/.test(password);
    const length = password.length;
    
    let strength = 0;
    let message = '';
    let barClass = '';
    
    // Calculate strength
    if (length > 7) strength += 1;
    if (length > 10) strength += 1;
    if (hasUpperCase && hasLowerCase) strength += 1;
    if (hasNumbers) strength += 1;
    if (hasSpecialChars) strength += 1;
    
    // Update UI based on strength
    switch (strength) {
        case 0:
            barClass = 'bg-danger';
            message = 'Çok zayıf';
            progressBar.width('20%');
            break;
        case 1:
        case 2:
            barClass = 'bg-warning';
            message = 'Zayıf';
            progressBar.width('40%');
            break;
        case 3:
            barClass = 'bg-info';
            message = 'Orta';
            progressBar.width('60%');
            break;
        case 4:
            barClass = 'bg-primary';
            message = 'İyi';
            progressBar.width('80%');
            break;
        case 5:
            barClass = 'bg-success';
            message = 'Güçlü';
            progressBar.width('100%');
            break;
    }
    
    progressBar.removeClass('bg-danger bg-warning bg-info bg-primary bg-success').addClass(barClass);
    feedback.text(message);
}

// Function to handle password reset request
function sifreIste(site) {
    const email = $('.sifremail').val();
    if (!email) {
        alert('Lütfen e-mail adresinizi giriniz.');
        return;
    }
    
    // Check if email is valid
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        alert('Lütfen geçerli bir e-mail adresi giriniz.');
        return;
    }
    
    // Show loading indicator
    $('#forgot_pw .btn_1').html('<span class="loading"></span> İşleniyor...');
    
    // AJAX request for password reset
    $.ajax({
        url: site + 'ajax/sifre-sifirla',
        type: 'POST',
        data: {email: email},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#forgot_pw').html('<div class="alert alert-success">Şifre sıfırlama linki e-mail adresinize gönderildi.</div>');
            } else {
                $('#forgot_pw').html('<div class="alert alert-danger">' + response.message + '</div>');
            }
        },
        error: function() {
            $('#forgot_pw').html('<div class="alert alert-danger">Bir hata oluştu. Lütfen tekrar deneyiniz.</div>');
        }
    });
}
