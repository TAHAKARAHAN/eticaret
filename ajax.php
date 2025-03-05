<?php
session_start();
include_once("include/baglan.php");

// Debug function that logs but doesn't output to browser
function debug_log($message, $data = null) {
    $log = date('Y-m-d H:i:s') . " - " . $message;
    if ($data !== null) {
        $log .= " - " . print_r($data, true);
    }
    error_log($log);
}

// Simple function to ensure response is clean
function sendResponse($message) {
    echo $message;
    exit();
}

// Handle different AJAX requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["islemtipi"])) {
        $islemtipi = $VT->filter($_POST["islemtipi"]);
        
        // "Add to Cart" operation
        if ($islemtipi == "sepeteEkle") {
            try {
                if (isset($_POST["urunID"]) && is_numeric($_POST["urunID"])) {
                    $urunID = intval($_POST["urunID"]);
                    $adet = isset($_POST["adet"]) && is_numeric($_POST["adet"]) ? intval($_POST["adet"]) : 1;
                    if ($adet < 1) $adet = 1;
                    
                    // Get product information
                    $urunBilgisi = $VT->VeriGetir("urunler", "WHERE ID=? AND durum=?", array($urunID, 1), "ORDER BY ID ASC", 1);
                    
                    // Check if product exists
                    if ($urunBilgisi !== false && !empty($urunBilgisi)) {
                        // Initialize cart session if not exists
                        if (!isset($_SESSION["sepet"])) {
                            $_SESSION["sepet"] = array();
                        }
                        
                        // Now safely access the product data
                        $stok = isset($urunBilgisi[0]["stok"]) ? intval($urunBilgisi[0]["stok"]) : 0;
                        
                        if ($stok > 0) {
                            // Process product price safely
                            $fiyat = "0.00";
                            if (!empty($urunBilgisi[0]["indirimlifiyat"])) {
                                $fiyat = $urunBilgisi[0]["indirimlifiyat"] . 
                                    (isset($urunBilgisi[0]["indirimlikurus"]) ? "." . $urunBilgisi[0]["indirimlikurus"] : ".00");
                            } else {
                                $fiyat = $urunBilgisi[0]["fiyat"] . 
                                    (isset($urunBilgisi[0]["kurus"]) ? "." . $urunBilgisi[0]["kurus"] : ".00");
                            }
                            
                            // Apply KDV calculation if needed
                            if (isset($urunBilgisi[0]["kdvdurum"]) && $urunBilgisi[0]["kdvdurum"] == 1) {
                                $kdvoran = isset($urunBilgisi[0]["kdvoran"]) ? $urunBilgisi[0]["kdvoran"] : 0;
                                if ($kdvoran > 0) {
                                    if ($kdvoran > 9) {
                                        $oran = "1." . $kdvoran;
                                    } else {
                                        $oran = "1.0" . $kdvoran;
                                    }
                                    // Calculate price without KDV
                                    $fiyat = ($fiyat / floatval($oran));
                                }
                            }

                            // Check if product has variations
                            $varyasyondurumu = false;
                            if (isset($_POST["varyasyon"]) && is_array($_POST["varyasyon"])) {
                                $varyasyondurumu = true;
                                $formData["varyasyon"] = $_POST["varyasyon"];
                                
                                // Add product with variations to cart
                                if (!isset($_SESSION["sepetVaryasyon"])) {
                                    $_SESSION["sepetVaryasyon"] = array();
                                }
                                
                                if (!isset($_SESSION["sepetVaryasyon"][$urunID])) {
                                    $_SESSION["sepetVaryasyon"][$urunID] = array();
                                }
                                
                                // Create a unique key for this variation
                                $varyID = implode("_", $formData["varyasyon"]);
                                
                                // Add or update the variation in cart
                                if (isset($_SESSION["sepetVaryasyon"][$urunID][$varyID])) {
                                    $_SESSION["sepetVaryasyon"][$urunID][$varyID]["adet"] += $adet;
                                } else {
                                    $_SESSION["sepetVaryasyon"][$urunID][$varyID] = array(
                                        "adet" => $adet,
                                        "varyasyonID" => $formData["varyasyon"]
                                    );
                                }
                            }
                            
                            // Add or update the main product entry
                            if (isset($_SESSION["sepet"][$urunID])) {
                                if (!$varyasyondurumu) {
                                    $_SESSION["sepet"][$urunID]["adet"] += $adet;
                                }
                            } else {
                                $_SESSION["sepet"][$urunID] = array(
                                    "adet" => $varyasyondurumu ? 0 : $adet,
                                    "fiyat" => $fiyat,
                                    "varyasyondurumu" => $varyasyondurumu
                                );
                            }
                            
                            sendResponse("TAMAM");
                        } else {
                            sendResponse("STOK");
                        }
                    } else {
                        sendResponse("HATA");
                    }
                } else {
                    sendResponse("HATA");
                }
            } catch (Exception $e) {
                debug_log("Cart Error: " . $e->getMessage());
                sendResponse("HATA");
            }
        }
        
        // Handle adding to favorites
        else if ($islemtipi == "favoriyeEkle") {
            if (!empty($_SESSION["uyeID"]) && isset($_POST["urunID"]) && isset($_POST["urunKey"])) {
                $uyeID = $VT->filter($_SESSION["uyeID"]);
                $urunID = $VT->filter($_POST["urunID"]);
                $urunKey = $VT->filter($_POST["urunKey"]);
                
                // Verify security hash
                if ($urunKey == md5(sha1($urunID))) {
                    // Check if already in favorites
                    $kontrol = $VT->VeriGetir("favoriler", "WHERE uyeID=? AND urunID=?", array($uyeID, $urunID), "ORDER BY ID ASC");
                    
                    if ($kontrol === false) {
                        // Not in favorites, add it
                        $ekle = $VT->SorguCalistir("INSERT INTO favoriler", "SET uyeID=?, urunID=?, tarih=?", 
                            array($uyeID, $urunID, date("Y-m-d")));
                            
                        if ($ekle) {
                            sendResponse("TAMAM");
                        } else {
                            sendResponse("HATA");
                        }
                    } else {
                        sendResponse("VAR");
                    }
                } else {
                    sendResponse("GUVENLIK");
                }
            } else {
                sendResponse("HATA");
            }
        }
        
        // Handle other operations
        else {
            sendResponse("ISLEMYOK");
        }
    } else {
        sendResponse("ISLEMYOK");
    }
} else {
    sendResponse("HATALIISTEK");
}
?>