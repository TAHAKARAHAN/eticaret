<?php
// This file displays the last PHP errors to help diagnose issues
// Only accessible to administrators

if(!empty($_SESSION["ID"]) && !empty($_SESSION["adsoyad"]) && !empty($_SESSION["mail"])) {
?>
<div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Hata Günlüğü</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?=SITE?>">Anasayfa</a></li>
              <li class="breadcrumb-item active">Hata Günlüğü</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Son PHP Hataları</h3>
          </div>
          <div class="card-body">
            <?php
            // Path to PHP error log
            $error_log_path = ini_get('error_log');
            
            if (file_exists($error_log_path) && is_readable($error_log_path)) {
                // Get the last 50 lines from the error log
                $log_lines = shell_exec("tail -n 50 " . escapeshellarg($error_log_path));
                
                if (!empty($log_lines)) {
                    echo '<pre class="bg-dark text-white p-3" style="max-height:500px;overflow:auto;">';
                    echo htmlentities($log_lines);
                    echo '</pre>';
                } else {
                    echo '<div class="alert alert-info">Error log is empty.</div>';
                }
            } else {
                echo '<div class="alert alert-warning">Error log not found or not readable at: ' . htmlentities($error_log_path) . '</div>';
                
                // Show PHP configuration information
                echo '<h4>PHP Configuration:</h4>';
                echo '<pre>';
                echo 'PHP Version: ' . phpversion() . "\n";
                echo 'error_reporting = ' . ini_get('error_reporting') . "\n";
                echo 'display_errors = ' . ini_get('display_errors') . "\n";
                echo 'log_errors = ' . ini_get('log_errors') . "\n";
                echo 'error_log = ' . ini_get('error_log') . "\n";
                echo '</pre>';
            }
            
            // Show database connection status
            echo '<h4>Database Connection Status:</h4>';
            try {
                $testQuery = $VT->baglanti->query("SELECT 1");
                echo '<div class="alert alert-success">Database connection is working properly.</div>';
            } catch (PDOException $e) {
                echo '<div class="alert alert-danger">Database connection error: ' . htmlentities($e->getMessage()) . '</div>';
            }
            
            // Show last executed query if available
            if (method_exists($VT, 'getLastQuery') && method_exists($VT, 'getLastError')) {
                echo '<h4>Last SQL Query:</h4>';
                echo '<pre>' . htmlentities($VT->getLastQuery()) . '</pre>';
                
                if (!empty($VT->getLastError())) {
                    echo '<h4>Last SQL Error:</h4>';
                    echo '<div class="alert alert-danger">' . htmlentities($VT->getLastError()) . '</div>';
                }
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
