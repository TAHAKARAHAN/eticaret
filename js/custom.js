function sepeteEkle(site, urunID) {
    // Validate Product ID
    if (!urunID || isNaN(urunID)) {
        console.error("Invalid product ID:", urunID);
        if (window.cartNotification) {
            cartNotification.error("Geçersiz ürün ID. Lütfen sayfayı yenileyip tekrar deneyiniz.");
        } else {
            showNotification("Geçersiz ürün ID. Lütfen sayfayı yenileyip tekrar deneyiniz.", "error");
        }
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
    formData.append('islem', 'sepeteEkle');
    formData.append('urunID', urunID.toString());
    formData.append('adet', adet.toString());
    
    // Send AJAX request - using fetch API for modern browsers
    fetch(site + "ajax.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log("AJAX Success:", data);
        
        if (data.success) {
            // Use custom notification system if available
            if (window.cartNotification) {
                cartNotification.success(data.message || "Ürün sepete eklendi");
            } else {
                showNotification(data.message || "Ürün sepete eklendi", "success");
            }
            setTimeout(() => {
                window.location.href = site + "sepet";
            }, 1500);
        } else {
            // Show error notification
            if (window.cartNotification) {
                cartNotification.error(data.message || "Ürün eklenirken bir hata oluştu");
            } else {
                showNotification(data.message || "Ürün eklenirken bir hata oluştu", "error");
            }
            buttonElement.html(originalText);
            buttonElement.prop('disabled', false);
        }
    })
    .catch(error => {
        console.error("AJAX Error:", error);
        
        if (window.cartNotification) {
            cartNotification.error("Bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.");
        } else {
            showNotification("Bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.", "error");
        }
        
        buttonElement.html(originalText);
        buttonElement.prop('disabled', false);
    });
}

// Simple notification fallback if the main one isn't loaded
function showNotification(message, type) {
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
    notification.style.backgroundColor = type === 'error' ? '#f44336' : '#4CAF50';
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
