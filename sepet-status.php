<?php
// Start session
session_start();

// Include database connection
define("DATA", "data/");
include_once(DATA . "baglanti.php");

// Create a test cart item if requested
if (isset($_GET['add_test'])) {
    if (!isset($_SESSION['sepet']) || !is_array($_SESSION['sepet'])) {
        $_SESSION['sepet'] = array();
    }
    
    $testID = 999;
    $_SESSION['sepet'][$testID] = array(
        "adet" => 1,
        "varyasyondurumu" => false,
        "fiyat" => "99.99",
        "baslik" => "Test Ürün",
        "resim" => "default.jpg"
    );
    
    $message = "Test ürün sepete eklendi!";
    $alert_class = "success";
}

// Clear cart if requested
if (isset($_GET['clear'])) {
    unset($_SESSION['sepet']);
    $message = "Sepet temizlendi!";
    $alert_class = "warning";
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sepet Durumu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
        .card { margin-bottom: 20px; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 4px; }
        .cart-item { border-bottom: 1px solid #eee; padding: 10px 0; }
        .cart-item:last-child { border-bottom: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Sepet Durumu</h1>
        
        <?php if (isset($message)): ?>
        <div class="alert alert-<?php echo $alert_class; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Sepet İçeriği</span>
                        <div>
                            <a href="?add_test=1" class="btn btn-sm btn-primary me-2">Test Ürün Ekle</a>
                            <a href="?clear=1" class="btn btn-sm btn-danger">Sepeti Temizle</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['sepet']) && !empty($_SESSION['sepet'])): ?>
                            <div class="cart-items">
                                <?php foreach($_SESSION['sepet'] as $urunID => $urun): ?>
                                <div class="cart-item">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <strong>ID:</strong> <?php echo $urunID; ?>
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Ürün:</strong> <?php echo isset($urun['baslik']) ? $urun['baslik'] : 'İsimsiz Ürün'; ?>
                                        </div>
                                        <div class="col-md-2">
                                            <strong>Adet:</strong> <?php echo $urun['adet']; ?>
                                        </div>
                                        <div class="col-md-2">
                                            <strong>Fiyat:</strong> <?php echo isset($urun['fiyat']) ? $urun['fiyat'] : 'Belirtilmemiş'; ?> TL
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-center my-4">Sepetinizde ürün bulunmuyor.</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        Sepet Raw Data
                    </div>
                    <div class="card-body">
                        <pre><?php print_r($_SESSION['sepet'] ?? 'Sepet boş'); ?></pre>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        Session Bilgileri
                    </div>
                    <div class="card-body">
                        <p><strong>Session ID:</strong> <?php echo session_id(); ?></p>
                        <p><strong>Session Path:</strong> <?php echo session_save_path(); ?></p>
                        <p><strong>Session Storage:</strong> <?php echo is_writable(session_save_path()) ? 'Yazılabilir' : 'Yazılamaz'; ?></p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        Bağlantılar
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="sepet" class="btn btn-primary">Sepete Git</a>
                            <a href="index.php" class="btn btn-secondary">Ana Sayfa</a>
                            <a href="session-fix.php" class="btn btn-info">Session Fix Tool</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
