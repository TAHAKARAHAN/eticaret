<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Seo Ayarları</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?=SITE?>">Anasayfa</a></li>
              <li class="breadcrumb-item active">Seo Ayarları</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
      <div class="container-fluid">
     
       <?php
       // Include AIHelper if it's not already included
       if(!class_exists('AIHelper')) {
           include_once($_SERVER['DOCUMENT_ROOT'].'/eticaret/admin/class/AIHelper.php');
       }
       
	   if($_POST)
	   {
           // Process AI SEO Analysis request
           if(isset($_POST["ai_seo_analysis"]) && $_POST["ai_seo_analysis"] == 1) {
               $aiHelper = new AIHelper($VT);
               $recommendations = $aiHelper->getSiteWideSEORecommendations();
               
               if($recommendations !== false) {
                   ?>
                   <div class="alert alert-info">
                       <h5><i class="icon fas fa-info"></i> AI SEO Önerileri</h5>
                       <p>Yapay zeka site genelinde SEO önerileri oluşturdu.</p>
                       
                       <?php if(!empty($recommendations["site_title"])): ?>
                       <div class="mt-3">
                           <strong>Önerilen Site Başlığı:</strong> 
                           <button type="button" class="btn btn-xs btn-success ml-2 apply-ai-suggestion" 
                                  data-target="baslik" data-value="<?=htmlspecialchars($recommendations["site_title"])?>">
                               Uygula <i class="fas fa-check"></i>
                           </button><br>
                           <?=htmlspecialchars($recommendations["site_title"])?>
                       </div>
                       <?php endif; ?>
                       
                       <?php if(!empty($recommendations["site_description"])): ?>
                       <div class="mt-2">
                           <strong>Önerilen Site Açıklaması:</strong>
                           <button type="button" class="btn btn-xs btn-success ml-2 apply-ai-suggestion" 
                                  data-target="description" data-value="<?=htmlspecialchars($recommendations["site_description"])?>">
                               Uygula <i class="fas fa-check"></i>
                           </button><br>
                           <?=htmlspecialchars($recommendations["site_description"])?>
                       </div>
                       <?php endif; ?>
                       
                       <?php if(!empty($recommendations["site_keywords"]) && is_array($recommendations["site_keywords"])): ?>
                       <div class="mt-2">
                           <strong>Önerilen Anahtar Kelimeler:</strong>
                           <button type="button" class="btn btn-xs btn-success ml-2 apply-ai-suggestion" 
                                  data-target="anahtar" data-value="<?=htmlspecialchars(implode(', ', $recommendations["site_keywords"]))?>">
                               Uygula <i class="fas fa-check"></i>
                           </button><br>
                           <?=htmlspecialchars(implode(", ", $recommendations["site_keywords"]))?>
                       </div>
                       <?php endif; ?>
                       
                       <?php if(!empty($recommendations["technical_improvements"]) && is_array($recommendations["technical_improvements"])): ?>
                       <div class="mt-3">
                           <strong>Teknik SEO Önerileri:</strong>
                           <ul>
                               <?php foreach($recommendations["technical_improvements"] as $improvement): ?>
                               <li><?=htmlspecialchars($improvement)?></li>
                               <?php endforeach; ?>
                           </ul>
                       </div>
                       <?php endif; ?>
                       
                       <?php if(!empty($recommendations["meta_tags"]) && is_array($recommendations["meta_tags"])): ?>
                       <div class="mt-2">
                           <strong>Önerilen Meta Etiketleri:</strong>
                           <ul>
                               <?php foreach($recommendations["meta_tags"] as $tag => $value): ?>
                               <li><code><?=htmlspecialchars($tag)?></code>: <?=htmlspecialchars($value)?></li>
                               <?php endforeach; ?>
                           </ul>
                       </div>
                       <?php endif; ?>
                   </div>
                   
                   <script>
                   document.addEventListener('DOMContentLoaded', function() {
                       const buttons = document.querySelectorAll('.apply-ai-suggestion');
                       buttons.forEach(button => {
                           button.addEventListener('click', function() {
                               const target = this.dataset.target;
                               const value = this.dataset.value;
                               
                               const targetField = document.querySelector(`[name="${target}"]`);
                               if(targetField) {
                                   targetField.value = value;
                                   this.innerHTML = 'Uygulandı <i class="fas fa-check"></i>';
                                   this.disabled = true;
                               }
                           });
                       });
                   });
                   </script>
                   <?php
               } else {
                   ?>
                   <div class="alert alert-danger">
                       <h5><i class="icon fas fa-ban"></i> Hata!</h5>
                       Yapay zeka SEO analizi yapılamadı. Lütfen API ayarlarınızı kontrol edin ve tekrar deneyin.
                   </div>
                   <?php
               }
           }
           
           // Process enable scheduled SEO updates
           if(isset($_POST["enable_ai_seo_updates"])) {
               $enableAiUpdates = isset($_POST["enable_ai_seo_updates"]) ? 1 : 0;
               $updateFrequency = $VT->filter($_POST["ai_update_frequency"] ?? "weekly");
               $aiApiKey = $VT->filter($_POST["ai_api_key"] ?? "");
               $aiEndpoint = $VT->filter($_POST["ai_endpoint"] ?? "https://api.openai.com/v1/chat/completions");
               $aiModel = $VT->filter($_POST["ai_model"] ?? "gpt-3.5-turbo");
               
               $guncelle = $VT->SorguCalistir(
                   "UPDATE ayarlar",
                   "SET enable_ai_seo=?, ai_update_frequency=?, ai_api_key=?, ai_endpoint=?, ai_model=? WHERE ID=?",
                   array($enableAiUpdates, $updateFrequency, $aiApiKey, $aiEndpoint, $aiModel, 1)
               );
               
               if($guncelle != false) {
                   ?>
                   <div class="alert alert-success">Yapay zeka SEO ayarları başarıyla güncellendi.</div>
                   <?php
                   
                   // If enabled, schedule first auto-update
                   if($enableAiUpdates) {
                       // Create an entry in the scheduled_tasks table
                       $nextRun = date('Y-m-d H:i:s', strtotime('+1 day'));
                       $VT->SorguCalistir(
                           "INSERT INTO scheduled_tasks",
                           "SET task_name=?, frequency=?, last_run=?, next_run=?, status=?",
                           array("ai_seo_update", $updateFrequency, date('Y-m-d H:i:s'), $nextRun, 'pending')
                       );
                   }
               }
           }
           
		   if(!empty($_POST["baslik"]) && !empty($_POST["anahtar"]) && !empty($_POST["description"]))
		   {
			   $baslik = $VT->filter($_POST["baslik"]);
			   $anahtar = $VT->filter($_POST["anahtar"]);
			   $description = $VT->filter($_POST["description"]);
			   $googleAnalytics = $VT->filter($_POST["google_analytics"] ?? "");
			   $searchConsole = $VT->filter($_POST["search_console"] ?? "");
			   $robots = $VT->filter($_POST["robots"] ?? "");
			   $canonical = isset($_POST["canonical"]) ? 1 : 0;
			   $ogTitle = $VT->filter($_POST["og_title"] ?? "");
			   $ogDescription = $VT->filter($_POST["og_description"] ?? "");
			   $twitterCard = $VT->filter($_POST["twitter_card"] ?? "");
			   $twitterSite = $VT->filter($_POST["twitter_site"] ?? "");
			   $sitemapFreq = $VT->filter($_POST["sitemap_freq"] ?? "weekly");
               
               // If favicon is uploaded, process it
               $faviconPath = "";
               if(!empty($_FILES["favicon"]["name"]))
               {
                   $faviconInfo = pathinfo($_FILES["favicon"]["name"]);
                   $faviconExtension = $faviconInfo["extension"];
                   if($faviconExtension == "ico" || $faviconExtension == "png")
                   {
                       $faviconName = "favicon.".$faviconExtension;
                       $faviconPath = "images/".$faviconName;
                       if(move_uploaded_file($_FILES["favicon"]["tmp_name"], $_SERVER["DOCUMENT_ROOT"]."/eticaret/".$faviconPath))
                       {
                           // Favicon uploaded successfully
                       }
                       else
                       {
                           $faviconPath = "";
                       }
                   }
               }
               
               // Update the current fields in the database
               $guncelle = $VT->SorguCalistir(
                   "UPDATE ayarlar",
                   "SET baslik=?, anahtar=?, aciklama=?, analytics=?, searchconsole=?, robots=?, canonical=?, og_title=?, og_description=?, twitter_card=?, twitter_site=?, sitemap_freq=?" . (!empty($faviconPath) ? ", favicon=?" : "") . " WHERE ID=?",
                   array_filter([
                       $baslik, $anahtar, $description, $googleAnalytics, $searchConsole,
                       $robots, $canonical, $ogTitle, $ogDescription, $twitterCard,
                       $twitterSite, $sitemapFreq, (!empty($faviconPath) ? $faviconPath : null), 1
                   ], function($val) { return $val !== null; }),
                   1
               );
			  
			   if($guncelle!=false)
			   {
                   // Generate robots.txt if option is selected
                   if(isset($_POST["generate_robots"]) && $_POST["generate_robots"] == 1)
                   {
                       $robotsContent = "User-agent: *\n";
                       $robotsContent .= isset($_POST["robots_disallow_admin"]) ? "Disallow: /admin/\n" : "";
                       $robotsContent .= "Sitemap: " . rtrim(SITE, '/') . "/sitemap.xml\n";
                       file_put_contents($_SERVER["DOCUMENT_ROOT"]."/eticaret/robots.txt", $robotsContent);
                   }
                   
                   // Generate sitemap.xml if option is selected
                   if(isset($_POST["generate_sitemap"]) && $_POST["generate_sitemap"] == 1)
                   {
                       // This is a simplified sitemap generation. For complex sites, consider using a library or scheduled task
                       $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
                       $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
                       
                       // Add homepage
                       $sitemap .= '<url>' . "\n";
                       $sitemap .= '<loc>' . rtrim(SITE, '/') . '</loc>' . "\n";
                       $sitemap .= '<lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
                       $sitemap .= '<changefreq>' . $sitemapFreq . '</changefreq>' . "\n";
                       $sitemap .= '<priority>1.0</priority>' . "\n";
                       $sitemap .= '</url>' . "\n";
                       
                       // Add categories
                       $kategoriler = $VT->VeriGetir("kategoriler", "WHERE durum=?", array(1));
                       if($kategoriler != false) {
                           foreach($kategoriler as $kategori) {
                               $sitemap .= '<url>' . "\n";
                               $sitemap .= '<loc>' . rtrim(SITE, '/') . '/kategori/' . $kategori["seflink"] . '</loc>' . "\n";
                               $sitemap .= '<lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
                               $sitemap .= '<changefreq>' . $sitemapFreq . '</changefreq>' . "\n";
                               $sitemap .= '<priority>0.8</priority>' . "\n";
                               $sitemap .= '</url>' . "\n";
                           }
                       }
                       
                       // Add products
                       $urunler = $VT->VeriGetir("urunler", "WHERE durum=?", array(1));
                       if($urunler != false) {
                           foreach($urunler as $urun) {
                               $sitemap .= '<url>' . "\n";
                               $sitemap .= '<loc>' . rtrim(SITE, '/') . '/urun/' . $urun["seflink"] . '</loc>' . "\n";
                               $sitemap .= '<lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
                               $sitemap .= '<changefreq>' . $sitemapFreq . '</changefreq>' . "\n";
                               $sitemap .= '<priority>0.9</priority>' . "\n";
                               $sitemap .= '</url>' . "\n";
                           }
                       }
                       
                       $sitemap .= '</urlset>';
                       file_put_contents($_SERVER["DOCUMENT_ROOT"]."/eticaret/sitemap.xml", $sitemap);
                   }
                   
				    ?>
                   <div class="alert alert-success">SEO ayarlarınız başarıyla güncellendi.</div>
                   <meta http-equiv="refresh" content="2;url=<?=SITE?>seo-ayarlari" />
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
               <div class="alert alert-danger">Site Başlık, Anahtar ve Açıklama alanları boş bırakılamaz.</div>
               <?php
		   }
	   }
       
       // Fetch current SEO settings from database
       $seoAyarlari = $VT->VeriGetir("ayarlar", "WHERE ID=?", array(1), "ORDER BY ID ASC", 1);
	   ?>
       
       <form action="#" method="post" enctype="multipart/form-data">
       <div class="row">
           <div class="col-md-8">
               <!-- Temel SEO Ayarları -->
               <div class="card card-primary">
                   <div class="card-header">
                       <h3 class="card-title">Temel SEO Ayarları</h3>
                   </div>
                   <div class="card-body">
                       <div class="form-group">
                           <label>Site Başlık</label>
                           <input type="text" class="form-control" placeholder="Site Başlık ..." name="baslik" value="<?=$sitebaslik?>">
                           <small class="form-text text-muted">Sitenizin başlığı. Arama sonuçlarında görünen başlıktır (50-60 karakter).</small>
                       </div>
                       <div class="form-group">
                           <label>Anahtar Kelimeler</label>
                           <input type="text" class="form-control" placeholder="Anahtar kelimeler ..." name="anahtar" value="<?=$siteanahtar?>">
                           <small class="form-text text-muted">Sitenizi tanımlayan anahtar kelimeler. Virgül ile ayırın.</small>
                       </div>
                       <div class="form-group">
                           <label>Site Açıklaması</label>
                           <textarea class="form-control" rows="3" placeholder="Site açıklama ..." name="description"><?=$siteaciklama?></textarea>
                           <small class="form-text text-muted">Sitenizin kısa açıklaması. Arama sonuçlarında gösterilir (150-160 karakter).</small>
                       </div>
                       <div class="form-group">
                           <label>Favicon</label>
                           <div class="input-group">
                               <div class="custom-file">
                                   <input type="file" class="custom-file-input" name="favicon">
                                   <label class="custom-file-label">Dosya seçin (ICO veya PNG)</label>
                               </div>
                           </div>
                           <?php if(!empty($seoAyarlari[0]["favicon"])): ?>
                           <div class="mt-2">
                               <img src="<?=SITE?>../<?=$seoAyarlari[0]["favicon"]?>" alt="Mevcut Favicon" height="32">
                               <span class="ml-2">Mevcut Favicon</span>
                           </div>
                           <?php endif; ?>
                           <small class="form-text text-muted">Tarayıcı sekmesinde görünecek site simgesi (16x16 px).</small>
                       </div>
                       <div class="form-group">
                           <label>Canonical URL</label>
                           <div class="custom-control custom-switch">
                               <input type="checkbox" class="custom-control-input" id="canonical" name="canonical" value="1" <?=(!empty($seoAyarlari[0]["canonical"]) && $seoAyarlari[0]["canonical"]==1) ? 'checked' : ''?>>
                               <label class="custom-control-label" for="canonical">Canonical URL'leri Otomatik Ekle</label>
                           </div>
                           <small class="form-text text-muted">Benzer içeriğe sahip sayfalar arasında arama motorları için tercih edilen URL'yi belirtir.</small>
                       </div>
                   </div>
               </div>
               
               <!-- Google Entegrasyonları -->
               <div class="card card-primary">
                   <div class="card-header">
                       <h3 class="card-title">Google Entegrasyonları</h3>
                   </div>
                   <div class="card-body">
                       <div class="form-group">
                           <label>Google Analytics Takip Kodu</label>
                           <textarea class="form-control" rows="3" placeholder="Google Analytics kodu ..." name="google_analytics"><?=!empty($seoAyarlari[0]["analytics"]) ? $seoAyarlari[0]["analytics"] : ''?></textarea>
                           <small class="form-text text-muted">Google Analytics kodunuzu buraya yapıştırın. (UA-XXXXX-X veya G-XXXXXXXX)</small>
                       </div>
                       <div class="form-group">
                           <label>Google Search Console Doğrulama Etiketi</label>
                           <input type="text" class="form-control" placeholder="Google site doğrulama kodu ..." name="search_console" value="<?=!empty($seoAyarlari[0]["searchconsole"]) ? $seoAyarlari[0]["searchconsole"] : ''?>">
                           <small class="form-text text-muted">Google Search Console meta tag doğrulama kodu.</small>
                       </div>
                   </div>
               </div>
               
               <!-- Yapay Zeka SEO İyileştirmeleri -->
               <div class="card card-success">
                   <div class="card-header">
                       <h3 class="card-title">Yapay Zeka SEO İyileştirmeleri</h3>
                   </div>
                   <div class="card-body">
                       <div class="form-group">
                           <button type="submit" name="ai_seo_analysis" value="1" class="btn btn-info">
                               <i class="fas fa-robot"></i> Yapay Zeka ile SEO Analizi Başlat
                           </button>
                           <small class="form-text text-muted">Bu işlem, yapay zeka kullanarak siteniz için SEO önerileri oluşturur.</small>
                       </div>
                       
                       <hr>
                       
                       <div class="form-group">
                           <label>Otomatik SEO İyileştirmeleri</label>
                           <div class="custom-control custom-switch">
                               <input type="checkbox" class="custom-control-input" id="enable_ai_seo_updates" name="enable_ai_seo_updates" value="1" <?=(!empty($seoAyarlari[0]["enable_ai_seo"]) && $seoAyarlari[0]["enable_ai_seo"]==1) ? 'checked' : ''?>>
                               <label class="custom-control-label" for="enable_ai_seo_updates">Yapay zeka ile otomatik SEO güncellemelerini etkinleştir</label>
                           </div>
                           <small class="form-text text-muted">Bu özellik, belirli aralıklarla sitenizin SEO'sunu otomatik olarak iyileştirir.</small>
                       </div>
                       
                       <div class="form-group">
                           <label>Güncelleme Sıklığı</label>
                           <select class="form-control" name="ai_update_frequency">
                               <option value="daily" <?=(!empty($seoAyarlari[0]["ai_update_frequency"]) && $seoAyarlari[0]["ai_update_frequency"]=="daily") ? 'selected' : ''?>>Günlük</option>
                               <option value="weekly" <?=(!empty($seoAyarlari[0]["ai_update_frequency"]) && $seoAyarlari[0]["ai_update_frequency"]=="weekly") ? 'selected' : '' || empty($seoAyarlari[0]["ai_update_frequency"])?>>Haftalık</option>
                               <option value="biweekly" <?=(!empty($seoAyarlari[0]["ai_update_frequency"]) && $seoAyarlari[0]["ai_update_frequency"]=="biweekly") ? 'selected' : ''?>>İki haftada bir</option>
                               <option value="monthly" <?=(!empty($seoAyarlari[0]["ai_update_frequency"]) && $seoAyarlari[0]["ai_update_frequency"]=="monthly") ? 'selected' : ''?>>Aylık</option>
                           </select>
                           <small class="form-text text-muted">Yapay zeka ne sıklıkta SEO önerileri oluşturup uygulayacak.</small>
                       </div>
                       
                       <div class="form-group">
                           <label>AI API Anahtarı</label>
                           <input type="password" class="form-control" name="ai_api_key" value="<?=!empty($seoAyarlari[0]["ai_api_key"]) ? $seoAyarlari[0]["ai_api_key"] : ''?>" placeholder="API Anahtarı">
                           <small class="form-text text-muted">OpenAI veya diğer AI servis sağlayıcılarının API anahtarı.</small>
                       </div>
                       
                       <div class="form-group">
                           <label>AI Endpoint</label>
                           <input type="text" class="form-control" name="ai_endpoint" value="<?=!empty($seoAyarlari[0]["ai_endpoint"]) ? $seoAyarlari[0]["ai_endpoint"] : 'https://api.openai.com/v1/chat/completions'?>" placeholder="API Endpoint">
                           <small class="form-text text-muted">AI servisinin API endpoint'i. Standart OpenAI için değiştirmenize gerek yoktur.</small>
                       </div>
                       
                       <div class="form-group">
                           <label>AI Model</label>
                           <select class="form-control" name="ai_model">
                               <option value="gpt-3.5-turbo" <?=(!empty($seoAyarlari[0]["ai_model"]) && $seoAyarlari[0]["ai_model"]=="gpt-3.5-turbo") ? 'selected' : '' || empty($seoAyarlari[0]["ai_model"])?>>GPT-3.5 Turbo</option>
                               <option value="gpt-4" <?=(!empty($seoAyarlari[0]["ai_model"]) && $seoAyarlari[0]["ai_model"]=="gpt-4") ? 'selected' : ''?>>GPT-4</option>
                               <option value="gpt-4-turbo" <?=(!empty($seoAyarlari[0]["ai_model"]) && $seoAyarlari[0]["ai_model"]=="gpt-4-turbo") ? 'selected' : ''?>>GPT-4 Turbo</option>
                           </select>
                           <small class="form-text text-muted">Kullanılacak AI modeli. Daha güçlü modeller daha iyi SEO önerileri üretir.</small>
                       </div>
                   </div>
               </div>
           </div>
           
           <div class="col-md-4">
               <!-- Sosyal Medya Meta Etiketleri -->
               <div class="card card-info">
                   <div class="card-header">
                       <h3 class="card-title">Sosyal Medya Meta Etiketleri</h3>
                   </div>
                   <div class="card-body">
                       <div class="form-group">
                           <label>Open Graph Başlık</label>
                           <input type="text" class="form-control" placeholder="OG Başlık ..." name="og_title" value="<?=!empty($seoAyarlari[0]["og_title"]) ? $seoAyarlari[0]["og_title"] : ''?>">
                           <small class="form-text text-muted">Facebook ve diğer sosyal platformlarda paylaşıldığında gösterilecek başlık.</small>
                       </div>
                       <div class="form-group">
                           <label>Open Graph Açıklama</label>
                           <textarea class="form-control" rows="3" placeholder="OG Açıklama ..." name="og_description"><?=!empty($seoAyarlari[0]["og_description"]) ? $seoAyarlari[0]["og_description"] : ''?></textarea>
                           <small class="form-text text-muted">Facebook ve diğer sosyal platformlarda gösterilecek açıklama.</small>
                       </div>
                       <div class="form-group">
                           <label>Twitter Card Tipi</label>
                           <select class="form-control" name="twitter_card">
                               <option value="summary" <?=(!empty($seoAyarlari[0]["twitter_card"]) && $seoAyarlari[0]["twitter_card"]=="summary") ? 'selected' : ''?>>Summary</option>
                               <option value="summary_large_image" <?=(!empty($seoAyarlari[0]["twitter_card"]) && $seoAyarlari[0]["twitter_card"]=="summary_large_image") ? 'selected' : ''?>>Summary Large Image</option>
                           </select>
                           <small class="form-text text-muted">Twitter'da nasıl paylaşılacağını belirler.</small>
                       </div>
                       <div class="form-group">
                           <label>Twitter Kullanıcı Adı</label>
                           <div class="input-group">
                               <div class="input-group-prepend">
                                   <span class="input-group-text">@</span>
                               </div>
                               <input type="text" class="form-control" placeholder="TwitterKullaniciAdi" name="twitter_site" value="<?=!empty($seoAyarlari[0]["twitter_site"]) ? $seoAyarlari[0]["twitter_site"] : ''?>">
                           </div>
                           <small class="form-text text-muted">@ işareti olmadan Twitter kullanıcı adınızı girin.</small>
                       </div>
                   </div>
               </div>
               
               <!-- Robots.txt ve Sitemap -->
               <div class="card card-warning">
                   <div class="card-header">
                       <h3 class="card-title">Robots.txt ve Sitemap</h3>
                   </div>
                   <div class="card-body">
                       <div class="form-group">
                           <label>Robots.txt İçeriği</label>
                           <textarea class="form-control" rows="4" placeholder="Robots.txt içeriği ..." name="robots"><?=!empty($seoAyarlari[0]["robots"]) ? $seoAyarlari[0]["robots"] : "User-agent: *\nAllow: /\nDisallow: /admin/\nSitemap: ".rtrim(SITE, '/')."/"."sitemap.xml"?></textarea>
                           <small class="form-text text-muted">Hangi sayfaların arama motorları tarafından taranması gerektiğini belirtir.</small>
                       </div>
                       <div class="form-group">
                           <div class="custom-control custom-checkbox">
                               <input type="checkbox" class="custom-control-input" id="generate_robots" name="generate_robots" value="1">
                               <label class="custom-control-label" for="generate_robots">Robots.txt dosyası oluştur</label>
                           </div>
                       </div>
                       <div class="form-group">
                           <div class="custom-control custom-checkbox">
                               <input type="checkbox" class="custom-control-input" id="robots_disallow_admin" name="robots_disallow_admin" value="1" checked>
                               <label class="custom-control-label" for="robots_disallow_admin">Admin klasörünü engelle</label>
                           </div>
                       </div>
                       <hr>
                       <div class="form-group">
                           <label>Sitemap Güncelleme Sıklığı</label>
                           <select class="form-control" name="sitemap_freq">
                               <option value="always" <?=(!empty($seoAyarlari[0]["sitemap_freq"]) && $seoAyarlari[0]["sitemap_freq"]=="always") ? 'selected' : ''?>>Her zaman (always)</option>
                               <option value="hourly" <?=(!empty($seoAyarlari[0]["sitemap_freq"]) && $seoAyarlari[0]["sitemap_freq"]=="hourly") ? 'selected' : ''?>>Saatlik (hourly)</option>
                               <option value="daily" <?=(!empty($seoAyarlari[0]["sitemap_freq"]) && $seoAyarlari[0]["sitemap_freq"]=="daily") ? 'selected' : ''?>>Günlük (daily)</option>
                               <option value="weekly" <?=(!empty($seoAyarlari[0]["sitemap_freq"]) && $seoAyarlari[0]["sitemap_freq"]=="weekly") ? 'selected' : '' || empty($seoAyarlari[0]["sitemap_freq"])?>>Haftalık (weekly)</option>
                               <option value="monthly" <?=(!empty($seoAyarlari[0]["sitemap_freq"]) && $seoAyarlari[0]["sitemap_freq"]=="monthly") ? 'selected' : ''?>>Aylık (monthly)</option>
                               <option value="yearly" <?=(!empty($seoAyarlari[0]["sitemap_freq"]) && $seoAyarlari[0]["sitemap_freq"]=="yearly") ? 'selected' : ''?>>Yıllık (yearly)</option>
                               <option value="never" <?=(!empty($seoAyarlari[0]["sitemap_freq"]) && $seoAyarlari[0]["sitemap_freq"]=="never") ? 'selected' : ''?>>Hiçbir zaman (never)</option>
                           </select>
                           <small class="form-text text-muted">Arama motorlarına içeriğinizin değişme sıklığını bildirir.</small>
                       </div>
                       <div class="form-group">
                           <div class="custom-control custom-checkbox">
                               <input type="checkbox" class="custom-control-input" id="generate_sitemap" name="generate_sitemap" value="1">
                               <label class="custom-control-label" for="generate_sitemap">Sitemap.xml dosyası oluştur</label>
                           </div>
                           <small class="form-text text-muted">Sitemapiniz otomatik olarak oluşturulacak ve robots.txt dosyanıza eklenecektir.</small>
                       </div>
                   </div>
               </div>
               
               <!-- SEO Değişiklikleri Geçmişi -->
               <div class="card card-info">
                   <div class="card-header">
                       <h3 class="card-title">SEO Değişiklikleri Geçmişi</h3>
                   </div>
                   <div class="card-body">
                       <?php
                       // Check if seo_changes table exists
                       $tableCheck = $VT->tekSorgu("SHOW TABLES LIKE 'seo_changes'");
                       if($tableCheck != false && count($tableCheck) > 0) {
                           $changes = $VT->VeriGetir("seo_changes", "", array(), "ORDER BY change_date DESC", 5);
                           if($changes != false) {
                               echo '<ul class="list-group">';
                               foreach($changes as $change) {
                                   $statusClass = ($change["status"] == 'applied') ? 'success' : (($change["status"] == 'rejected') ? 'danger' : 'warning');
                                   $statusText = ($change["status"] == 'applied') ? 'Uygulandı' : (($change["status"] == 'rejected') ? 'Reddedildi' : 'Bekliyor');
                                   
                                   echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                                   echo $change["table_name"] . ' #' . $change["item_id"];
                                   echo '<span class="badge badge-'.$statusClass.' badge-pill">'.$statusText.'</span>';
                                   echo '</li>';
                               }
                               echo '</ul>';
                               
                               echo '<div class="mt-3"><a href="'.SITE.'seo-degisiklikler" class="btn btn-sm btn-block btn-info">Tüm Değişiklikleri Gör</a></div>';
                           } else {
                               echo '<p class="text-muted">Henüz kaydedilmiş SEO değişikliği yok.</p>';
                           }
                       } else {
                           echo '<p class="text-danger">SEO değişiklik tablosu henüz oluşturulmamış. Otomatik SEO özelliğini etkinleştirdiğinizde oluşturulacaktır.</p>';
                       }
                       ?>
                   </div>
               </div>
           </div>
           
           <div class="col-md-12">
               <div class="card">
                   <div class="card-body">
                       <button type="submit" class="btn btn-block btn-primary">SEO AYARLARINI GÜNCELLE</button>
                   </div>
               </div>
           </div>
       </div>
       </form>
       
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
