<?php
// Simple database connection test script
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is requesting this file directly for security
$isDirectAccess = (basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__));

// Only allow direct access with the correct token
$accessAllowed = false;
if ($isDirectAccess) {
    $token = isset($_GET['token']) ? $_GET['token'] : '';
    $correctToken = md5('eticaret_test_'.date('Ymd'));
    $accessAllowed = ($token === $correctToken);
    
    if (!$accessAllowed) {
        header("HTTP/1.0 403 Forbidden");
        echo "<h1>Access Denied</h1>";
        exit;
    }
}

// Display header if direct access
if ($isDirectAccess && $accessAllowed) {
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Database Connection Test</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .success { color: green; }
            .error { color: red; }
            .warning { color: orange; }
            .section { border: 1px solid #ddd; padding: 10px; margin: 10px 0; border-radius: 5px; }
            h1 { color: #333; }
            pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow: auto; }
        </style>
    </head>
    <body>
        <h1>Database Connection Test</h1>";
}

// Test database connection directly
function testDirectConnection() {
    $result = [
        'success' => false,
        'message' => '',
        'details' => []
    ];
    
    try {
        $host = "localhost";
        $dbname = "eticaret";
        $username = "root";
        $password = "";
        
        $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check if we can run a simple query
        $stmt = $db->query("SELECT 1");
        $check = $stmt->fetch();
        
        if ($check[0] == 1) {
            $result['success'] = true;
            $result['message'] = "Connected successfully to database '$dbname' on '$host'";
            
            // Check if required tables exist
            $requiredTables = ['urunler', 'kategoriler', 'yorumlar', 'urunresimler', 'favoriler'];
            $existingTables = [];
            
            $stmt = $db->query("SHOW TABLES");
            while ($row = $stmt->fetch()) {
                $existingTables[] = $row[0];
            }
            
            $missingTables = array_diff($requiredTables, $existingTables);
            
            if (empty($missingTables)) {
                $result['details'][] = "All required tables exist";
                
                // Test a simple query on the products table
                try {
                    $stmt = $db->query("SELECT COUNT(*) FROM urunler");
                    $count = $stmt->fetchColumn();
                    $result['details'][] = "Products count: $count";
                } catch (PDOException $e) {
                    $result['details'][] = "Error querying products: " . $e->getMessage();
                }
            } else {
                $result['details'][] = "Missing tables: " . implode(", ", $missingTables);
            }
        } else {
            $result['message'] = "Connected to database but test query failed";
        }
    } catch (PDOException $e) {
        $result['message'] = "Connection failed: " . $e->getMessage();
    }
    
    return $result;
}

// Test connection via VT class
function testVTConnection() {
    $result = [
        'success' => false,
        'message' => '',
        'details' => []
    ];
    
    try {
        // Include the database connection file
        include_once("include/baglan.php");
        
        // Check if VT class is available
        if (!isset($VT) || !is_object($VT)) {
            $result['message'] = "VT object not available";
            return $result;
        }
        
        // Test connection
        if ($VT->isConnected() && $VT->testConnection()) {
            $result['success'] = true;
            $result['message'] = "VT class connected successfully";
            
            // Test a query
            $testData = $VT->VeriGetir("urunler", "LIMIT 1");
            if ($testData !== false) {
                $result['details'][] = "Successfully retrieved a product";
            } else {
                $result['details'][] = "Could not retrieve product data";
            }
        } else {
            $result['message'] = "VT class failed to connect";
        }
    } catch (Exception $e) {
        $result['message'] = "Error testing VT connection: " . $e->getMessage();
    }
    
    return $result;
}

// Run the tests
$directTest = testDirectConnection();
$vtTest = testVTConnection();

// Output results if direct access
if ($isDirectAccess && $accessAllowed) {
    echo "<div class='section'>";
    echo "<h2>Direct PDO Connection</h2>";
    echo "<p class='" . ($directTest['success'] ? "success" : "error") . "'><strong>" . $directTest['message'] . "</strong></p>";
    
    if (!empty($directTest['details'])) {
        echo "<ul>";
        foreach ($directTest['details'] as $detail) {
            echo "<li>$detail</li>";
        }
        echo "</ul>";
    }
    echo "</div>";
    
    echo "<div class='section'>";
    echo "<h2>VT Class Connection</h2>";
    echo "<p class='" . ($vtTest['success'] ? "success" : "error") . "'><strong>" . $vtTest['message'] . "</strong></p>";
    
    if (!empty($vtTest['details'])) {
        echo "<ul>";
        foreach ($vtTest['details'] as $detail) {
            echo "<li>$detail</li>";
        }
        echo "</ul>";
    }
    echo "</div>";
    
    echo "<div class='section'>";
    echo "<h2>PHP & Server Information</h2>";
    echo "<ul>";
    echo "<li>PHP Version: " . phpversion() . "</li>";
    echo "<li>PDO Drivers: " . implode(", ", PDO::getAvailableDrivers()) . "</li>";
    echo "<li>Server: " . $_SERVER['SERVER_SOFTWARE'] . "</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div class='section'>";
    echo "<h2>Recommendations</h2>";
    
    if (!$directTest['success']) {
        echo "<p class='error'>Fix the direct database connection issues first.</p>";
    } elseif (!$vtTest['success']) {
        echo "<p class='warning'>Direct connection works but VT class has issues. Check baglan.php file.</p>";
    } else {
        echo "<p class='success'>Database connections look good. If you're still having issues:</p>";
        echo "<ul>";
        echo "<li>Check file permissions</li>";
        echo "<li>Verify URL rewriting is working</li>";
        echo "<li>Check for PHP errors in other files</li>";
        echo "</ul>";
    }
    echo "</div>";
    
    echo "</body></html>";
} else {
    // Return data for AJAX calls or includes
    return [
        'direct' => $directTest,
        'vt' => $vtTest
    ];
}
?>
