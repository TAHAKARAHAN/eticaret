<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!empty($_GET["ID"])) {
    $kategoriID = $VT->filter($_GET["ID"]);
    $kontrol = $VT->VeriGetir("kategoriler", "WHERE ID = ?", array($kategoriID), "ORDER BY ID ASC", 1);
    if ($kontrol != false) {
?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Kategori Düzenle</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?=SITE?>">Anasayfa</a></li>
              <li class="breadcrumb-item active">Kategori Düzenle</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
      <div class="container-fluid">
      <div class="row">
      <div class="col-md-12">
      <a href="<?=SITE?>kategori-liste" class="btn btn-info" style="float:right; margin-bottom:10px; margin-left:10px;"><i class="fas fa-bars"></i> LİSTE</a> 
       <a href="<?=SITE?>kategori-ekle" class="btn btn-success" style="float:right; margin-bottom:10px;"><i class="fa fa-plus"></i> YENİ EKLE</a>
       </div>
       </div>
       <?php
	   if($_POST)
	   {
		   if(!empty($_POST["kategori"]) && !empty($_POST["baslik"]) && !empty($_POST["anahtar"]) && !empty($_POST["description"]) && !empty($_POST["sirano"]))
		   {
			   $kategori=$VT->filter($_POST["kategori"]);
			   $baslik=$VT->filter($_POST["baslik"]);
			   $anahtar=$VT->filter($_POST["anahtar"]);
			   $seflink=$VT->seflink($baslik);
			   $description=$VT->filter($_POST["description"]);
			   $sirano=$VT->filter($_POST["sirano"]);
			   if(!empty($_FILES["resim"]["name"]))
			   {
				   $yukle=$VT->upload("resim","../images/kategoriler/");
				   if($yukle!=false)
				   {
					   $ekle=$VT->SorguCalistir("UPDATE kategoriler","SET baslik=?, seflink=?, tablo=?, resim=?, anahtar=?, description=?, durum=?, sirano=?, tarih=? WHERE ID=?",array($baslik,$seflink,$kategori,$yukle,$anahtar,$description,1,$sirano,date("Y-m-d"),$kontrol[0]["ID"]),1);
				   }
				   else
				   {
					    ?>
                   <div class="alert alert-info">Resim yükleme işleminiz başarısız.</div>
                   <?php
				   }
			   }
			   else
			   {
				   $ekle=$VT->SorguCalistir("UPDATE kategoriler","SET baslik=?, seflink=?, tablo=?, anahtar=?, description=?, durum=?, sirano=?, tarih=? WHERE ID=?",array($baslik,$seflink,$kategori,$anahtar,$description,1,$sirano,date("Y-m-d"),$kontrol[0]["ID"]),1);
			   }
			   
			   if($ekle!=false)
			   {
				    ?>
                   <div class="alert alert-success">İşleminiz başarıyla kaydedildi.</div>
                   <script>
                       // Redirect after a short delay to show the success message
                       setTimeout(function() {
                           window.location.href = '<?=SITE?>kategori-duzenle/<?=$kontrol[0]["ID"]?>';
                       }, 1500);
                   </script>
                   <?php
			   }
			   else
			   {
				    ?>
                   <div class="alert alert-danger">İşleminiz sırasında bir sorunla karşılaşıldı. Lütfen daha sonra tekrar deneyiniz.</div>
                   <?php
			   }
		   }
		   else
		   {
			   ?>
               <div class="alert alert-danger">Boş bıraktığınız alanları doldurunuz.</div>
               <?php
		   }
	   }
	   ?>
       <form action="#" method="post" enctype="multipart/form-data">
       <div class="col-md-8">
       <div class="card-body card card-primary">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label>Kategori Seç</label>
                  <select class="form-control select2" style="width: 100%;" name="kategori">
                    <?php
                    // Simplified category selection
                    try {
                        // Get all available modules/tables
                        $moduller = $VT->VeriGetir("kategoriler", "WHERE tablo=?", array("modul"), "ORDER BY ID ASC");
                        
                        if($moduller != false) {
                            foreach($moduller as $modul) {
                                // Check if this is the selected category
                                $selected = ($kontrol[0]["tablo"] == $modul["seflink"]) ? ' selected' : '';
                                echo '<option value="'.$modul["seflink"].'"'.$selected.'>'.$modul["baslik"].'</option>';
                            }
                        }
                        
                        // Get main product categories
                        $urunler = $VT->VeriGetir("kategoriler", "WHERE tablo=?", array("urunler"), "ORDER BY ID ASC");
                        if($urunler != false) {
                            echo '<optgroup label="Ürün Kategorileri">';
                            foreach($urunler as $urun) {
                                $selected = ($kontrol[0]["tablo"] == $urun["seflink"]) ? ' selected' : '';
                                echo '<option value="'.$urun["seflink"].'"'.$selected.'>'.$urun["baslik"].'</option>';
                            }
                            echo '</optgroup>';
                        }
                    } catch (Exception $e) {
                        echo '<option value="">Hata: '.$e->getMessage().'</option>';
                    }
                    ?>
                  </select>
                </div>
              </div>
              <!-- /.col -->
            </div>
            
            <!-- Debug information -->
         
            <div class="col-md-12">
                <div class="form-group">
                <label>Başlık</label>
                <input type="text" class="form-control" placeholder="Başlık ..." name="baslik" value="<?=$kontrol[0]["baslik"]?>">
                </div>
            </div>
             <div class="col-md-12">
                <div class="form-group">
                <label>Anahtar</label>
                <input type="text" class="form-control" placeholder="Anahtar ..." name="anahtar" value="<?=$kontrol[0]["anahtar"]?>">
                </div>
            </div>
             <div class="col-md-12">
                <div class="form-group">
                <label>Açıklama</label>
                <input type="text" class="form-control" placeholder="Açıklama ..." name="description" value="<?=$kontrol[0]["description"]?>">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                <label>Resim</label>
                <input type="file" class="form-control" placeholder="Resim Seçiniz ..." name="resim">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                <label>Sıra No</label>
                <input type="number" class="form-control" placeholder="Sıra No ..." name="sirano" style="width:100px;" value="<?=$kontrol[0]["sirano"]?>">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                <button type="submit" class="btn btn-block btn-primary">KAYDET</button>
                </div>
            </div>
            
            <!-- /.row -->
          </div>
          <!-- /.card-body -->
        </div>
        </div>
       </form>
       
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>

  <?php
  } else {
    echo '<div class="alert alert-danger">Kategori bulunamadı!</div>';
  }
} else {
    echo '<div class="alert alert-danger">ID değeri bulunamadı!</div>';
}
  ?>