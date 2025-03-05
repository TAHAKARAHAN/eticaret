<?php
// Common functions file with better error handling

// Function to make SEF URLs from Turkish titles
function seo($s) {
    try {
        $tr = array('ş','Ş','ı','İ','ğ','Ğ','ü','Ü','ö','Ö','Ç','ç','+','#','*','!',',','?');
        $eng = array('s','s','i','i','g','g','u','u','o','o','c','c','plus','sharp','star','!','-','?');
        $s = str_replace($tr,$eng,$s);
        $s = strtolower($s);
        $s = preg_replace('/[^a-z0-9\-<>]/', '-', $s);
        $s = preg_replace('/-+/', '-', $s); // Replace multiple dashes with single dash
        $s = trim($s, '-'); // Trim dashes from start and end
        return $s;
    } catch (Exception $e) {
        error_log("SEO function error: " . $e->getMessage());
        return "error-in-seo-conversion";
    }
}

// Simple money format function
function number_format_turkish($number, $decimal_count=0) {
    try {
        return number_format($number, $decimal_count, ',', '.');
    } catch (Exception $e) {
        error_log("Number format error: " . $e->getMessage());
        return $number;
    }
}

// Get category hierarchy for breadcrumbs
function getKategoriTree($VT, $id) {
    try {
        $tree = [];
        $kategori = $VT->VeriGetir("kategoriler", "WHERE ID=? AND durum=?", array($id, 1), "ORDER BY ID ASC", 1);
        
        if ($kategori != false) {
            $tree[] = array(
                "isim" => $kategori[0]["baslik"],
                "seflink" => $kategori[0]["seflink"]
            );
            
            if ($kategori[0]["ustID"] > 0) {
                $ustKategoriler = getKategoriTree($VT, $kategori[0]["ustID"]);
                $tree = array_merge($ustKategoriler, $tree);
            }
        }
        
        return $tree;
    } catch (Exception $e) {
        error_log("getKategoriTree error: " . $e->getMessage());
        return [];
    }
}

// Format date in Turkish format
function tarihFormatiDuzenle($date) {
    try {
        return date("d.m.Y", strtotime($date));
    } catch (Exception $e) {
        error_log("Date format error: " . $e->getMessage());
        return $date;
    }
}

// Check if user is logged in
function isLoggedIn() {
    return (isset($_SESSION["uyeID"]) && !empty($_SESSION["uyeID"]));
}

// Check if user is admin
function isAdmin() {
    return (isset($_SESSION["admin"]) && $_SESSION["admin"] === true);
}

// Get category name by ID
function getKategoriAdi($VT, $id) {
    try {
        $kategori = $VT->VeriGetir("kategoriler", "WHERE ID=?", array($id), "ORDER BY ID ASC", 1);
        if ($kategori != false) {
            return $kategori[0]["baslik"];
        }
        return "Kategori Bulunamadı";
    } catch (Exception $e) {
        error_log("getKategoriAdi error: " . $e->getMessage());
        return "Kategori Hatası";
    }
}

// Get cart item count
function getCartItemCount() {
    $count = 0;
    if (isset($_SESSION["sepet"]) && is_array($_SESSION["sepet"])) {
        foreach ($_SESSION["sepet"] as $item) {
            $count += isset($item["adet"]) ? $item["adet"] : 0;
        }
    }
    return $count;
}

// Check if database table exists
function tableExists($VT, $tableName) {
    try {
        $result = $VT->VeriGetir("INFORMATION_SCHEMA.TABLES", "WHERE TABLE_SCHEMA = 'eticaret' AND TABLE_NAME = ?", array($tableName));
        return ($result !== false && count($result) > 0);
    } catch (Exception $e) {
        error_log("tableExists error: " . $e->getMessage());
        return false;
    }
}

// Add a file system logging function for PHP errors
function fs_log($message, $level = 'INFO') {
    $logFile = $_SERVER['DOCUMENT_ROOT'] . '/eticaret/error_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $formattedMessage = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
    
    // Ensure log directory exists and is writable
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    // Append to log file
    file_put_contents($logFile, $formattedMessage, FILE_APPEND);
}
?>
