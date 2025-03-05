<?php
session_start();
define("DATA", "data/");
include_once(DATA . "baglanti.php");
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sepet Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 5px; overflow: auto; }
        .actions { margin: 20px 0; }
        .btn { padding: 8px 15px; background: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .btn-danger { background: #f44336; }
        .card { border: 1px solid #ddd; border-radius: 4px; padding: 15px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Sepet Debug Sayfası</h1>
    
    <div class="card">
        <h2>Sepet İçeriği</h2>
        <?php if(isset($_SESSION["sepet"]) && !empty($_SESSION["sepet"])): ?>
            <pre><?php print_r($_SESSION["sepet"]); ?></pre>
            
            <div class="actions">
                <a href="?temizle=1" class="btn btn-danger" onclick="return confirm('Sepeti temizlemek istediğinizden emin misiniz?');">Sepeti Temizle</a>
            </div>
        <?php else: ?>
            <p>Sepet boş veya tanımlanmamış.</p>
        <?php endif; ?>
    </div>
    
    <div class="card">
        <h2>Test Ürün Ekle</h2>
        <form method="post" action="">
            <div>
                <label for="urunID">Ürün ID:</label>
                <input type="number" name="urunID" id="urunID" value="1" min="1" required>
            </div>
            <div style="margin-top: 10px;">
                <label for="adet">Adet:</label>
                <input type="number" name="adet" id="adet" value="1" min="1" required>
            </div>
            <div style="margin-top: 10px;">
                <button type="submit" name="ekle" class="btn">Sepete Ekle</button>
            </div>
        </form>
    </div>
    
    <?php
    // Sepeti temizleme
    if(isset($_GET["temizle"]) && $_GET["temizle"] == 1) {
        unset($_SESSION["sepet"]);
        echo '<script>alert("Sepet temizlendi!"); window.location.href="debug-cart.php";</script>';
    }
    
    // Test ürün ekleme
    if(isset($_POST["ekle"])) {
        $urunID = (int)$_POST["urunID"];
        $adet = (int)$_POST["adet"];
        
        if($urunID > 0 && $adet > 0) {
            // Ürün bilgilerini al
            $urunbilgisi = $VT->VeriGetir("urunler", "WHERE ID=?", array($urunID), "ORDER BY ID ASC", 1);
            
            if($urunbilgisi != false) {
                if(!isset($_SESSION["sepet"])) {
                    $_SESSION["sepet"] = array();
                }
                
                $_SESSION["sepet"][$urunID] = array(
                    "adet" => $adet,
                    "varyasyondurumu" => false,
                    "fiyat" => $urunbilgisi[0]["fiyat"] . "." . $urunbilgisi[0]["kurus"],
                    "baslik" => $urunbilgisi[0]["baslik"],
                    "resim" => $urunbilgisi[0]["resim"]
                );
                
                echo '<script>alert("Ürün sepete eklendi!"); window.location.href="debug-cart.php";</script>';
            } else {
                echo '<script>alert("Ürün bulunamadı!");</script>';
            }
        }
    }
    ?>
    
    <div class="card">
        <h2>Session Bilgileri</h2>
        <pre><?php print_r($_SESSION); ?></pre>
    </div>
    
    <div class="actions">
        <a href="index.php" class="btn">Ana Sayfaya Dön</a>
        <a href="sepet" class="btn">Sepete Git</a>
    </div>
</body>
</html>
