<?php
// Place this code at the very beginning of index.php
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}
@ob_start();
define("DATA", "data/");
define("SAYFA", "include/");
define("SINIF", "admin/class/");
include_once(DATA . "baglanti.php");
define("SITE", $siteurl);
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>
		<?= $sitebaslik ?>
	</title>
	<meta name="description" content="<?= $sitedescription ?>">
	<meta name="keywords" content="<?= $siteanahtar ?>">
	<meta name="author" content="eticaret projesi">


	<!-- Favicons-->
	<link rel="shortcut icon" href="<?=SITE?>img/m.png" type="image/x-icon">
	<link rel="apple-touch-icon" type="image/x-icon" href="<?= SITE ?>img/apple-touch-icon-57x57-precomposed.png">
	<link rel="apple-touch-icon" type="image/x-icon" sizes="72x72"
		href="<?= SITE ?>img/apple-touch-icon-72x72-precomposed.png">
	<link rel="apple-touch-icon" type="image/x-icon" sizes="114x114"
		href="<?= SITE ?>img/apple-touch-icon-114x114-precomposed.png">
	<link rel="apple-touch-icon" type="image/x-icon" sizes="144x144"
		href="<?= SITE ?>img/apple-touch-icon-144x144-precomposed.png">

	<!-- GOOGLE WEB FONT -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900&display=swap" rel="stylesheet">

	<!-- BASE CSS -->
	<link href="<?= SITE ?>css/bootstrap.custom.min.css" rel="stylesheet">
	<link href="<?= SITE ?>css/style.css" rel="stylesheet">

	<!-- SPECIFIC CSS -->
	<link href="<?= SITE ?>css/home_1.css" rel="stylesheet">
	<link href="<?= SITE ?>css/listing.css" rel="stylesheet">

	<!-- YOUR CUSTOM CSS -->
	<link href="<?= SITE ?>css/custom.css" rel="stylesheet">
	
	<!-- Add notification CSS -->
	<style>
		.notification {
			position: fixed;
			top: 20px;
			right: 20px;
			background-color: #4caf50;
			color: white;
			padding: 15px 20px;
			border-radius: 4px;
			box-shadow: 0 2px 10px rgba(0,0,0,0.2);
			z-index: 9999;
			transform: translateX(150%);
			transition: transform 0.3s ease-in-out;
			display: flex;
			align-items: center;
			max-width: 350px;
		}

		.notification.show {
			transform: translateX(0);
		}

		.notification i {
			margin-right: 10px;
			font-size: 20px;
		}

		.notification-content {
			flex-grow: 1;
		}

		.notification-title {
			font-weight: bold;
			margin-bottom: 5px;
		}

		.notification-message {
			font-size: 14px;
		}

		.notification-close {
			background: none;
			border: none;
			color: white;
			font-size: 20px;
			cursor: pointer;
			padding: 0;
			margin-left: 15px;
		}

		.notification-actions {
			margin-top: 10px;
		}

		.notification-actions a {
			display: inline-block;
			background-color: white;
			color: #4caf50;
			padding: 5px 10px;
			border-radius: 3px;
			text-decoration: none;
			margin-right: 10px;
			font-size: 12px;
			font-weight: bold;
		}
	</style>

</head>

<body>

	<div id="page">

		<?php
		include_once(DATA . "ust.php");


		if ($_GET && !empty($_GET["sayfa"])) {
			$sayfa = $_GET["sayfa"] . ".php";
			if (file_exists(SAYFA . $sayfa)) {
				include_once(SAYFA . $sayfa);
			} else {
				include_once(SAYFA . "home.php");
			}

		} else {
			include_once(SAYFA . "home.php");
		}


		include_once(DATA . "footer.php");
		?>





	</div>
	<!-- page -->

	<div id="toTop"></div><!-- Back to top button -->

	<!-- COMMON SCRIPTS -->
	<script src="<?= SITE ?>js/common_scripts.min.js"></script>
	<script src="<?= SITE ?>js/main.js"></script>
	<script src="<?= SITE ?>js/sistem.js"></script>

	<!-- SPECIFIC SCRIPTS -->
	<script src="<?= SITE ?>js/carousel-home.min.js"></script>
	<script  src="<?=SITE?>js/carousel_with_thumbs.js"></script>
	


	<script>
    	// Client type Panel
		$('input[name="client_type"]').on("click", function() {
		    var inputValue = $(this).attr("value");
		    var targetBox = $("." + inputValue);
		    $(".box").not(targetBox).hide();
		    $(targetBox).show();
		});
	</script>

</body>

</html>