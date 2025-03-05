<?php
session_start();
include_once("include/baglan.php");
include_once("include/ayar.php");

// Debug function for viewing cart contents
function displayCart() {
    echo "<h2>Cart Contents</h2>";
    
    if (isset($_SESSION["sepet"]) && !empty($_SESSION["sepet"])) {
        echo "<h3>Main Cart</h3>";
        echo "<pre>";
        print_r($_SESSION["sepet"]);
        echo "</pre>";
    } else {
        echo "<p>Main cart is empty</p>";
    }
    
    if (isset($_SESSION["sepetVaryasyon"]) && !empty($_SESSION["sepetVaryasyon"])) {
        echo "<h3>Variations Cart</h3>";
        echo "<pre>";
        print_r($_SESSION["sepetVaryasyon"]);
        echo "</pre>";
    } else {
        echo "<p>Variations cart is empty</p>";
    }
}

// Add or clear test product
if (isset($_GET["action"]) && $_GET["action"] == "add") {
    $testProductID = 1; // Change this to a valid product ID in your database
    if (!isset($_SESSION["sepet"])) {
        $_SESSION["sepet"] = array();
    }
    $_SESSION["sepet"][$testProductID] = array(
        "adet" => 1,
        "fiyat" => "100.00",
        "varyasyondurumu" => false
    );
    echo "<div style='color: green;'>Test product added to cart!</div>";
}

if (isset($_GET["action"]) && $_GET["action"] == "clear") {
    unset($_SESSION["sepet"]);
    unset($_SESSION["sepetVaryasyon"]);
    echo "<div style='color: red;'>Cart cleared!</div>";
}

// Basic styling
echo '<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    pre { background: #f5f5f5; padding: 10px; border: 1px solid #ddd; }
    .actions { margin: 20px 0; }
    .actions a { 
        display: inline-block; 
        padding: 10px 15px; 
        background: #004dda; 
        color: white; 
        text-decoration: none; 
        margin-right: 10px; 
    }
    .actions a.clear { background: #ff0000; }
</style>';

// Display page
echo "<h1>Cart Testing Page</h1>";
echo "<div class='actions'>";
echo "<a href='?action=add'>Add Test Product</a>";
echo "<a href='?action=clear' class='clear'>Clear Cart</a>";
echo "<a href='" . SITE . "sepet'>View Cart Page</a>";
echo "</div>";

// Display current cart contents
displayCart();
?>
