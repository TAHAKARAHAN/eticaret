<?php
// Start with a clean session
if (session_status() !== PHP_SESSION_DISABLED) {
    session_start();
    session_regenerate_id(true); // Generate a new session ID for security
}

// Set timezone for consistent timestamps
date_default_timezone_set('Europe/Istanbul');

// Function to get PHP's session configuration
function getSessionConfig() {
    return array(
        'session.save_path' => ini_get('session.save_path'),
        'session.name' => ini_get('session.name'),
        'session.save_handler' => ini_get('session.save_handler'),
        'session.cookie_lifetime' => ini_get('session.cookie_lifetime'),
        'session.cookie_path' => ini_get('session.cookie_path'),
        'session.cookie_domain' => ini_get('session.cookie_domain'),
        'session.cookie_secure' => ini_get('session.cookie_secure'),
        'session.cookie_httponly' => ini_get('session.cookie_httponly'),
        'session.use_cookies' => ini_get('session.use_cookies'),
        'session.use_only_cookies' => ini_get('session.use_only_cookies'),
        'session.gc_maxlifetime' => ini_get('session.gc_maxlifetime'),
        'session.gc_probability' => ini_get('session.gc_probability'),
        'session.gc_divisor' => ini_get('session.gc_divisor')
    );
}

// Function to check for write permissions
function checkWritePermissions($path) {
    if (empty($path)) {
        return array('status' => 'warning', 'message' => 'Path is empty');
    }

    if (!file_exists($path)) {
        return array('status' => 'warning', 'message' => 'Path does not exist');
    }

    if (is_writable($path)) {
        return array('status' => 'success', 'message' => 'Directory is writable');
    } else {
        return array('status' => 'danger', 'message' => 'Directory is not writable');
    }
}

// Get the session directory write permission status
$sessionPath = session_save_path();
$sessionWriteStatus = checkWritePermissions($sessionPath);

// Get the current working directory write permission status
$currentPath = getcwd();
$currentWriteStatus = checkWritePermissions($currentPath);

// Create a message for the user
if (isset($_POST['reset_session'])) {
    // Clear the session data but keep the session active
    $_SESSION = array();
    $message = array('status' => 'success', 'text' => 'Session data has been cleared.');
} elseif (isset($_POST['destroy_session'])) {
    // Completely destroy the session
    session_destroy();
    $message = array('status' => 'warning', 'text' => 'Session has been destroyed. Refresh to create a new session.');
} elseif (isset($_POST['test_cart'])) {
    // Add a test item to cart
    if (!isset($_SESSION['sepet'])) {
        $_SESSION['sepet'] = array();
    }
    $testProductId = 999;
    $_SESSION['sepet'][$testProductId] = array(
        'adet' => 1,
        'varyasyondurumu' => false,
        'fiyat' => '99.99',
        'baslik' => 'Test Ürün',
        'resim' => 'default.jpg'
    );
    $message = array('status' => 'success', 'text' => 'Test item added to cart.');
} elseif (isset($_POST['check_ajax'])) {
    // Will perform an AJAX test
    $ajaxTest = true;
}

// If there's a permission issue
if ($sessionWriteStatus['status'] === 'danger') {
    $message = array('status' => 'danger', 'text' => 'PHP cannot write to session storage path! Please fix permissions or change session path.');
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Fix Tool</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { padding: 20px; }
        .card { margin-bottom: 20px; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Session Fix Tool</h1>
        
        <?php if (isset($message)): ?>
        <div class="alert alert-<?php echo $message['status']; ?> alert-dismissible fade show" role="alert">
            <?php echo $message['text']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Session Status
                    </div>
                    <div class="card-body">
                        <p><strong>Session ID:</strong> <?php echo session_id(); ?></p>
                        <p><strong>Session Status:</strong> 
                            <?php 
                            switch (session_status()) {
                                case PHP_SESSION_DISABLED:
                                    echo '<span class="text-danger">Sessions are disabled</span>';
                                    break;
                                case PHP_SESSION_NONE:
                                    echo '<span class="text-warning">Sessions are enabled but none exists</span>';
                                    break;
                                case PHP_SESSION_ACTIVE:
                                    echo '<span class="text-success">Sessions are enabled and active</span>';
                                    break;
                            }
                            ?>
                        </p>
                        <p>
                            <strong>Session Path:</strong>
                            <span class="text-<?php echo $sessionWriteStatus['status']; ?>">
                                <?php echo empty($sessionPath) ? 'Default' : $sessionPath; ?>
                                (<?php echo $sessionWriteStatus['message']; ?>)
                            </span>
                        </p>
                        <p>
                            <strong>Current Directory:</strong>
                            <span class="text-<?php echo $currentWriteStatus['status']; ?>">
                                <?php echo $currentPath; ?>
                                (<?php echo $currentWriteStatus['message']; ?>)
                            </span>
                        </p>
                        
                        <form method="post" class="mt-3">
                            <div class="d-flex gap-2">
                                <button type="submit" name="reset_session" class="btn btn-warning">Clear Session Data</button>
                                <button type="submit" name="destroy_session" class="btn btn-danger">Destroy Session</button>
                                <button type="submit" name="test_cart" class="btn btn-primary">Add Test Cart Item</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        AJAX Test
                    </div>
                    <div class="card-body">
                        <p>Test adding to cart via AJAX:</p>
                        <button id="testAjaxBtn" class="btn btn-success">Test AJAX Cart</button>
                        <div id="ajaxResult" class="mt-3"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Session Data
                    </div>
                    <div class="card-body">
                        <pre><?php print_r($_SESSION); ?></pre>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        Session Configuration
                    </div>
                    <div class="card-body">
                        <pre><?php print_r(getSessionConfig()); ?></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    $(document).ready(function() {
        // Test AJAX functionality
        $("#testAjaxBtn").click(function() {
            $("#ajaxResult").html('<div class="alert alert-info">Testing...</div>');
            
            $.ajax({
                type: "POST",
                url: "ajax.php",
                data: {
                    "islemtipi": "sepeteEkle",
                    "urunID": "1",
                    "adet": "1"
                },
                success: function(response) {
                    $("#ajaxResult").html('<div class="alert alert-success">Response: ' + response + '</div>');
                    console.log("AJAX Success:", response);
                },
                error: function(xhr, status, error) {
                    $("#ajaxResult").html('<div class="alert alert-danger">Error: ' + status + ' - ' + error + '</div>');
                    console.error("AJAX Error:", error);
                }
            });
        });
    });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
