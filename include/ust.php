<?php
@session_start();
@ob_start();
define("DATA", "data/");
define("SAYFA", "include/");
define("SINIF", "class/");
include_once(DATA . "baglanti.php");
define("SITE", $VT->SiteAdresi());

if ($VT->IDGetir($_SERVER['REQUEST_URI']) != false) {
    $kimlik = $VT->IDGetir($_SERVER['REQUEST_URI']);
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?= $ayarlar[0]["aciklama"] ?>">
    <meta name="keyword" content="<?= $ayarlar[0]["keyword"] ?>">
    <meta name="author" content="Mehmet E.">
    <title><?= $ayarlar[0]["title"] ?></title>

    <!-- Favicons-->
    <link rel="shortcut icon" href="<?= SITE ?>img/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" type="image/x-icon" href="<?= SITE ?>img/apple-touch-icon-57x57-precomposed.png">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="72x72" href="<?= SITE ?>img/apple-touch-icon-72x72-precomposed.png">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="114x114" href="<?= SITE ?>img/apple-touch-icon-114x114-precomposed.png">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="144x144" href="<?= SITE ?>img/apple-touch-icon-144x144-precomposed.png">
	
    <!-- GOOGLE WEB FONT -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900&display=swap" rel="stylesheet">

    <!-- BASE CSS -->
    <link href="<?= SITE ?>css/bootstrap.custom.min.css" rel="stylesheet">
    <link href="<?= SITE ?>css/style.css" rel="stylesheet">

    <!-- SPECIFIC CSS -->
    <link href="<?= SITE ?>css/cart.css" rel="stylesheet">
    <link href="<?= SITE ?>css/cart-notification.css" rel="stylesheet">

    <!-- YOUR CUSTOM CSS -->
    <link href="<?= SITE ?>css/custom.css" rel="stylesheet">
    
    <!-- Load notification JS first to ensure it's available -->
    <script src="<?= SITE ?>js/cart-notification.js"></script>
</head>

<body>
    <?php include_once(DATA . "ust.php"); ?>

    <!-- Initialize notification system immediately -->
    <script>
    // Create global fallback notification function
    if (typeof showNotification !== 'function') {
        window.showNotification = function(message, type) {
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
        };
    }
    </script>

    <!-- Additional scripts below -->
    <script src="<?= SITE ?>js/common_scripts.min.js"></script>
    <script src="<?= SITE ?>js/main.js"></script>
    <script src="<?= SITE ?>js/sepet.js"></script>
    <script src="<?= SITE ?>js/custom.js"></script>
