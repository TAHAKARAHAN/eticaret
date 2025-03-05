<?php
// Simple test page to check database connectivity and query execution
// This page is for debugging purposes only

if(!empty($_SESSION["ID"]) && !empty($_SESSION["adsoyad"]) && !empty($_SESSION["mail"])) {
?>
<div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Database Test</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?=SITE?>">Anasayfa</a></li>
              <li class="breadcrumb-item active">Test</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Database Connection Test</h3>
          </div>
          <div class="card-body">
            <?php
            // Test database connection
            try {
                $VT->baglanti->query("SELECT 1");
                echo '<div class="alert alert-success">Database connection successful!</div>';
            } catch (PDOException $e) {
                echo '<div class="alert alert-danger">Database connection failed: ' . $e->getMessage() . '</div>';
            }
            
            // Test simple query
            try {
                $test = $VT->VeriGetir("kategoriler", "", array(), "ORDER BY ID ASC", 1);
                if ($test !== false) {
                    echo '<div class="alert alert-success">Simple query test successful!</div>';
                    echo '<pre>';
                    print_r($test);
                    echo '</pre>';
                } else {
                    echo '<div class="alert alert-warning">Query returned no results.</div>';
                }
            } catch (Exception $e) {
                echo '<div class="alert alert-danger">Query test failed: ' . $e->getMessage() . '</div>';
            }
            
            // Test tekSorgu function
            echo '<h4>Testing tekSorgu function:</h4>';
            try {
                $result = $VT->tekSorgu("SELECT * FROM kategoriler LIMIT 1");
                echo '<pre>';
                print_r($result);
                echo '</pre>';
                if ($result !== false) {
                    echo '<div class="alert alert-success">tekSorgu function working properly!</div>';
                } else {
                    echo '<div class="alert alert-warning">tekSorgu function returned no results.</div>';
                }
            } catch (Exception $e) {
                echo '<div class="alert alert-danger">tekSorgu test failed: ' . $e->getMessage() . '</div>';
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
