<?php
require_once("includes/config.php");

// Check if we have session data, if not redirect to homepage
if(empty($_SESSION["registration_data"])) {
    header("Location: ".SITE);
    exit;
}

// Get the registration data from session
$regData = $_SESSION["registration_data"];
$userType = $regData["type"]; // 1 for individual, 2 for corporate

// Set the page title
$title = "Kayıt Başarılı - " . SITE_NAME;
?>
<!DOCTYPE html>
<html lang="tr">
<?php include("include/header.php"); ?>

<body>
    <?php include("data/ust.php"); ?>
    
    <main class="bg_gray">
        <div class="success-overlay">
            <div class="registration-success">
                <div class="success-icon">
                    <div class="circle">
                        <i class="ti-check"></i>
                    </div>
                </div>
                <div class="success-message">
                    <?php if($userType == 1): // Individual user ?>
                    <h3>Tebrikler! Hesabınız Başarıyla Oluşturuldu</h3>
                    <p>Hoş geldiniz <strong><?=$regData["ad"]?> <?=$regData["soyad"]?></strong>, artık sitemizin tüm özelliklerinden yararlanabilirsiniz.</p>
                    <?php else: // Corporate user ?>
                    <h3>Tebrikler! Kurumsal Hesabınız Başarıyla Oluşturuldu</h3>
                    <p>Hoş geldiniz <strong><?=$regData["firmaadi"]?></strong>, artık sitemizin tüm özelliklerinden yararlanabilirsiniz.</p>
                    <?php endif; ?>
                </div>
                
                <!-- Display appropriate user details based on user type -->
                <?php if($userType == 1): // Individual user ?>
                <div class="user-details">
                    <div class="detail-item">
                        <i class="ti-user"></i>
                        <span class="detail-label">Kullanıcı Adı:</span>
                        <span class="detail-value"><?=$regData["ad"]?> <?=$regData["soyad"]?></span>
                    </div>
                    <div class="detail-item">
                        <i class="ti-email"></i>
                        <span class="detail-label">E-posta:</span>
                        <span class="detail-value"><?=$regData["mail"]?></span>
                    </div>
                    <div class="detail-item">
                        <i class="ti-location-pin"></i>
                        <span class="detail-label">Adres:</span>
                        <span class="detail-value"><?=$regData["adres"]?>, <?=$regData["ilce"]?></span>
                    </div>
                </div>
                
                <div class="next-steps">
                    <h4>Şimdi Ne Yapabilirsiniz?</h4>
                    <div class="steps-container">
                        <div class="step-item">
                            <a href="<?=SITE?>"><i class="ti-shopping-cart"></i>
                            <p>Alışverişe Başla</p></a>
                        </div>
                        <div class="step-item">
                            <a href="<?=SITE?>hesabim"><i class="ti-user"></i>
                            <p>Profilini Düzenle</p></a>
                        </div>
                        <div class="step-item">
                            <a href="<?=SITE?>favorilerim"><i class="ti-heart"></i>
                            <p>Favori Ürünler Ekle</p></a>
                        </div>
                    </div>
                </div>
                
                <?php else: // Corporate user ?>
                <div class="user-details">
                    <div class="detail-item">
                        <i class="ti-briefcase"></i>
                        <span class="detail-label">Firma Adı:</span>
                        <span class="detail-value"><?=$regData["firmaadi"]?></span>
                    </div>
                    <div class="detail-item">
                        <i class="ti-credit-card"></i>
                        <span class="detail-label">Vergi No:</span>
                        <span class="detail-value"><?=$regData["vergino"]?></span>
                    </div>
                    <div class "detail-item">
                        <i class="ti-home"></i>
                        <span class="detail-label">Vergi Dairesi:</span>
                        <span class="detail-value"><?=$regData["vergidairesi"]?></span>
                    </div>
                    <div class="detail-item">
                        <i class="ti-email"></i>
                        <span class="detail-label">E-posta:</span>
                        <span class="detail-value"><?=$regData["mail"]?></span>
                    </div>
                    <div class="detail-item">
                        <i class="ti-location-pin"></i>
                        <span class="detail-label">Adres:</span>
                        <span class="detail-value"><?=$regData["adres"]?>, <?=$regData["ilce"]?></span>
                    </div>
                </div>
                
                <div class="next-steps">
                    <h4>Şimdi Ne Yapabilirsiniz?</h4>
                    <div class="steps-container">
                        <div class="step-item">
                            <a href="<?=SITE?>"><i class="ti-shopping-cart"></i>
                            <p>Alışverişe Başla</p></a>
                        </div>
                        <div class="step-item">
                            <a href="<?=SITE?>hesabim"><i class="ti-user"></i>
                            <p>Firma Profilini Düzenle</p></a>
                        </div>
                        <div class="step-item">
                            <a href="<?=SITE?>siparislerim"><i class="ti-receipt"></i>
                            <p>Fatura Bilgilerini Yönet</p></a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="redirect-notice">
                    <p>Otomatik olarak giriş sayfasına yönlendirileceksiniz. Lütfen bekleyiniz...</p>
                    <div class="countdown-container">
                        <div class="countdown" id="countdown">5</div>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="<?=SITE?>uyelik" class="btn_1">Hemen Giriş Yap</a>
                    <a href="<?=SITE?>" class="btn_1 outline">Ana Sayfaya Dön</a>
                </div>
            </div>
        </div>
    </main>
    
    <link href="<?=SITE?>css/registration-success.css" rel="stylesheet">
    <script>
    // Countdown Timer
    let seconds = 5;
    const countdownElement = document.getElementById('countdown');
    
    function updateCountdown() {
        countdownElement.innerHTML = seconds;
        if (seconds <= 0) {
            window.location.href = '<?=SITE?>uyelik';
        } else {
            seconds--;
            setTimeout(updateCountdown, 1000);
        }
    }
    
    updateCountdown();
    
    // Create confetti animation
    function createConfetti() {
        const colors = ['#f2d74e', '#95c3de', '#ff9a9e', '#a8edea', '#fed6e3'];
        const confettiCount = 100;
        const container = document.querySelector('.registration-success');
        
        for (let i = 0; i < confettiCount; i++) {
            const confetti = document.createElement('div');
            confetti.className = 'confetti';
            confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.left = Math.random() * 100 + '%';
            confetti.style.top = -10 + 'px';
            confetti.style.width = Math.random() * 8 + 5 + 'px';
            confetti.style.height = Math.random() * 4 + 8 + 'px';
            confetti.style.opacity = Math.random() + 0.5;
            confetti.style.transform = 'rotate(' + Math.random() * 360 + 'deg)';
            container.appendChild(confetti);
            
            // Animate confetti
            const animationDuration = Math.random() * 3 + 2;
            confetti.style.animation = `fall ${animationDuration}s linear forwards`;
            confetti.style.animationDelay = Math.random() * 5 + 's';
            
            // Add keyframes for confetti animation
            const keyframes = document.createElement('style');
            keyframes.innerHTML = `
                @keyframes fall {
                    from {
                        transform: translateY(-10px) rotate(${Math.random() * 360}deg);
                    }
                    to {
                        transform: translateY(${container.offsetHeight + 100}px) rotate(${Math.random() * 360}deg);
                    }
                }
            `;
            document.head.appendChild(keyframes);
            
            // Remove confetti after animation
            setTimeout(() => {
                confetti.remove();
                keyframes.remove();
            }, animationDuration * 1000);
        }
    }
    
    // Initialize confetti on page load
    window.addEventListener('load', createConfetti);
    </script>
    <?php
    // Clear registration data after displaying it
    unset($_SESSION["registration_data"]);
    ?>
    
    <!-- Add site footer if needed -->
    <?php //include("data/alt.php"); ?>
</body>
</html>
