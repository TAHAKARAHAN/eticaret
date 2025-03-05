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
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Registration Success">
    <title>Kayıt Başarılı - <?=SITE_NAME?></title>
    
    <!-- BASE CSS -->
    <link href="<?=SITE?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?=SITE?>css/style.css" rel="stylesheet">
    <link href="<?=SITE?>css/registration-success.css" rel="stylesheet">
    <link href="<?=SITE?>css/themify-icons.css" rel="stylesheet">
</head>
<body>
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
                <!-- Individual user details -->
                <!-- ...existing code for individual user details... -->
            </div>
            <?php else: // Corporate user ?>
            <div class="user-details">
                <!-- Corporate user details -->
                <!-- ...existing code for corporate user details... -->
            </div>
            <?php endif; ?>
            
            <!-- Rest of the success page content -->
            <!-- ...existing next steps, countdown, action buttons... -->
            
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
        // ...existing confetti code...
    }
    
    // Initialize confetti on page load
    window.addEventListener('load', createConfetti);
    </script>
</body>
</html>
