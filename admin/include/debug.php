<?php
if(!empty($_SESSION["ID"]) && !empty($_SESSION["adsoyad"]) && !empty($_SESSION["mail"])) {
?>
<div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Kategori Yapısı Hata Ayıklama</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?=SITE?>">Anasayfa</a></li>
              <li class="breadcrumb-item active">Hata Ayıklama</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Kategori Hiyerarşisi</h3>
          </div>
          <div class="card-body">
            <?php
            // Get all categories
            $kategoriler = $VT->VeriGetir("kategoriler", "", array(), "ORDER BY tablo ASC, ID ASC");
            
            if($kategoriler != false) {
                echo '<table class="table table-bordered table-striped">';
                echo '<thead><tr><th>ID</th><th>Başlık</th><th>SEF Link</th><th>Tablo</th><th>İlişkili Kategoriler</th></tr></thead>';
                echo '<tbody>';
                
                foreach($kategoriler as $kategori) {
                    echo '<tr>';
                    echo '<td>'.$kategori["ID"].'</td>';
                    echo '<td>'.$kategori["baslik"].'</td>';
                    echo '<td>'.$kategori["seflink"].'</td>';
                    echo '<td>'.$kategori["tablo"].'</td>';
                    
                    // Get child categories
                    $altKategoriler = $VT->VeriGetir("kategoriler", "WHERE tablo=?", array($kategori["seflink"]), "ORDER BY ID ASC");
                    
                    echo '<td>';
                    if($altKategoriler != false) {
                        echo '<ul>';
                        foreach($altKategoriler as $alt) {
                            echo '<li>'.$alt["baslik"].' (ID: '.$alt["ID"].')</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo 'Alt kategori yok';
                    }
                    echo '</td>';
                    
                    echo '</tr>';
                }
                
                echo '</tbody>';
                echo '</table>';
            } else {
                echo '<div class="alert alert-warning">Kategori bulunamadı!</div>';
            }
            ?>
          </div>
        </div>
      </div>
    </section>
</div>
<?php
} else {
    echo '<meta http-equiv="refresh" content="0;url='.SITE.'giris-yap">';
}
?>
