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

// Always use JSON responses for clean handling and avoiding alerts
function sendJSONResponse($success, $message, $code = '', $data = []) {
    header('Content-Type: application/json');
    $response = [
        'success' => $success,
        'message' => $message,
        'code' => $code
    ];
    
    if (!empty($data)) {
        $response = array_merge($response, $data);
    }
    
    echo json_encode($response);
    exit;
}

// Handle different AJAX requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle new sepeteEkle format
    if (isset($_POST["islem"]) && $_POST["islem"] == "sepeteEkle") {
        $urunID = (int)$_POST["urunID"];
        $adet = (int)$_POST["adet"];
        $varyasyonlar = isset($_POST["varyasyonlar"]) ? json_decode($_POST["varyasyonlar"], true) : [];
        
        // Validate inputs
        if ($urunID <= 0 || $adet <= 0) {
            sendJSONResponse(false, 'Geçersiz ürün veya adet bilgisi', 'HATA');
        }
        
        // Get product info from database
        $urunbilgisi = $VT->VeriGetir("urunler", "WHERE ID=? AND durum=?", array($urunID, 1), "ORDER BY ID ASC", 1);
        
        if ($urunbilgisi != false) {
            // Initialize cart if not exists
            if (!isset($_SESSION["sepet"])) {
                $_SESSION["sepet"] = array();
            }
            
            // Add to cart
            $_SESSION["sepet"][$urunID] = array(
                "adet" => $adet,
                "varyasyondurumu" => !empty($varyasyonlar),
                "varyasyonlar" => $varyasyonlar,
                "fiyat" => $urunbilgisi[0]["fiyat"] . "." . $urunbilgisi[0]["kurus"],
                "baslik" => $urunbilgisi[0]["baslik"],
                "resim" => $urunbilgisi[0]["resim"]
            );
            
            sendJSONResponse(true, $urunbilgisi[0]["baslik"] . ' sepete eklendi', 'TAMAM', [
                'cartCount' => count($_SESSION["sepet"])
            ]);
        } else {
            sendJSONResponse(false, 'Ürün bulunamadı', 'URUN_YOK');
        }
    }
    
    // Handle legacy format
    else if (isset($_POST["islemtipi"])) {
        $islemtipi = $VT->filter($_POST["islemtipi"]);
        
        // "Add to Cart" operation with legacy format
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
                                    "varyasyondurumu" => $varyasyondurumu,
                                    "baslik" => $urunBilgisi[0]["baslik"],
                                    "resim" => $urunBilgisi[0]["resim"]
                                );
                            }
                            
                            // Return JSON instead of plain text to prevent alerts
                            sendJSONResponse(true, $urunBilgisi[0]["baslik"] . ' sepete eklendi', 'TAMAM', [
                                'cartCount' => count($_SESSION["sepet"])
                            ]);
                        } else {
                            sendJSONResponse(false, 'Üzgünüz, bu ürün stokta bulunmamaktadır', 'STOK');
                        }
                    } else {
                        sendJSONResponse(false, 'Ürün bulunamadı', 'URUN_YOK');
                    }
                } else {
                    sendJSONResponse(false, 'Geçersiz ürün ID', 'HATA');
                }
            } catch (Exception $e) {
                debug_log("Cart Error: " . $e->getMessage());
                sendJSONResponse(false, 'Bir hata oluştu', 'HATA');
            }
        }
        
        // Handle adding to favorites with JSON response
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
                            sendJSONResponse(true, 'Ürün favorilere eklendi', 'TAMAM');
                        } else {
                            sendJSONResponse(false, 'Favorilere eklenirken bir hata oluştu', 'HATA');
                        }
                    } else {
                        sendJSONResponse(true, 'Bu ürün zaten favorilerinizde', 'VAR');
                    }
                } else {
                    sendJSONResponse(false, 'Güvenlik doğrulaması başarısız', 'GUVENLIK');
                }
            } else {
                sendJSONResponse(false, 'Favorilere eklemek için giriş yapmalısınız', 'HATA');
            }
        }
        
        // Handle other operations
        else {
            sendJSONResponse(false, 'Geçersiz işlem tipi', 'ISLEMYOK');
        }
    } else {
        sendJSONResponse(false, 'İşlem tipi belirtilmedi', 'ISLEMYOK');
    }
} else {
    sendJSONResponse(false, 'Geçersiz istek metodu', 'HATALIISTEK');
}
?>