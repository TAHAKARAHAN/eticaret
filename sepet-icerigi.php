<?php
session_start();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sepet İçeriği</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background: #f9f9f9;
        }
        h1, h2 {
            color: #333;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        pre {
            background: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
            overflow: auto;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .btn-red {
            background: #f44336;
        }
        .btn-blue {
            background: #2196F3;
        }
        .empty-cart {
            text-align: center;
            padding: 40px 0;
            color: #666;
        }
        .empty-cart i {
            font-size: 60px;
            margin-bottom: 20px;
            color: #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Sepet İçeriği</h1>
        
        <?php if(isset($_SESSION["sepet"]) && !empty($_SESSION["sepet"])): ?>
            <table>
                <thead>
                    <tr>
                        <th>Ürün ID</th>
                        <th>Ürün Adı</th>
                        <th>Adet</th>
                        <th>Fiyat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($_SESSION["sepet"] as $urunID => $urun): ?>
                    <tr>
                        <td><?= $urunID ?></td>
                        <td><?= isset($urun["baslik"]) ? $urun["baslik"] : "Belirtilmemiş" ?></td>
                        <td><?= $urun["adet"] ?></td>
                        <td><?= isset($urun["fiyat"]) ? $urun["fiyat"]." TL" : "Belirtilmemiş" ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <h2>Sepet Detayları</h2>
            <pre><?php print_r($_SESSION["sepet"]); ?></pre>
            
            <p>
                <a href="sepet" class="btn btn-blue">Sepete Git</a>
                <a href="?temizle=1" class="btn btn-red" onclick="return confirm('Sepeti temizlemek istediğinizden emin misiniz?')">Sepeti Temizle</a>
                <a href="index.php" class="btn">Ana Sayfa</a>
            </p>
        <?php else: ?>
            <div class="empty-cart">
                <i>Sepetinizde ürün bulunmuyor.</i>
                <p>Alışverişe başlamak için ana sayfaya dönebilirsiniz.</p>
                <a href="index.php" class="btn">Ana Sayfa</a>
            </div>
        <?php endif; ?>
    </div>

    <?php
    // Handle cart clearing
    if(isset($_GET["temizle"]) && $_GET["temizle"] == "1") {
        unset($_SESSION["sepet"]);
        echo "<script>alert('Sepet temizlendi!'); window.location.href='sepet-icerigi.php';</script>";
    }
    ?>
</body>
</html>
