<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?=SITE?>css/custom_design.css" rel="stylesheet">
    <link href="<?=SITE?>css/account.css" rel="stylesheet">
    <link href="<?=SITE?>css/icon-fixes.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="<?=SITE?>js/jquery-3.6.0.min.js"></script>
    <script src="<?=SITE?>js/custom.js"></script>
    <script src="<?=SITE?>js/validate-fix.js"></script>
    <link href="<?=SITE?>css/select-without-icon.css" rel="stylesheet">
    <script src="<?=SITE?>js/remove-select-icons.js"></script>
    <script src="<?=SITE?>js/cart-badge.js"></script>
</head>

<!-- Replace the cart icon in your header with this updated version -->
<li>
    <div class="dropdown dropdown-cart">
        <a href="<?=SITE?>sepet" class="cart_bt">
            <span class="cart-count-badge">0</span>
        </a>
        <div class="dropdown-menu">
            <div class="total_drop">
                <a href="<?=SITE?>sepet" class="btn_1 outline">Sepeti Göster</a>
                <a href="<?=SITE?>odeme-tipi" class="btn_1">Ödemeye Geç</a>
            </div>
        </div>
    </div>
    <!-- /dropdown-cart-->
</li>

<?php // Rest of your header code... ?>
