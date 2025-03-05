<?php
include("include/baglan.php");
include("include/fonksiyonlar.php");
include_once("INC/ayarlar.php");

// Check if provider is specified
if(!isset($_GET['provider'])) {
    header("Location: " . SITE);
    exit;
}

$provider = $_GET['provider'];

// Initialize session if not already started
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

switch($provider) {
    case 'facebook':
        handleFacebookLogin();
        break;
    case 'google':
        handleGoogleLogin();
        break;
    default:
        header("Location: " . SITE);
        exit;
}

// Facebook Login Handler
function handleFacebookLogin() {
    global $VT;
    
    // Check if this is a callback from Facebook
    if(isset($_GET['code'])) {
        // Process Facebook response
        $fb_app_id = ''; // Facebook App ID
        $fb_app_secret = ''; // Facebook App Secret
        $fb_redirect_uri = SITE . 'social-auth.php?provider=facebook';
        
        // Exchange code for access token
        $token_url = "https://graph.facebook.com/v12.0/oauth/access_token";
        $token_url .= "?client_id=" . $fb_app_id;
        $token_url .= "&redirect_uri=" . urlencode($fb_redirect_uri);
        $token_url .= "&client_secret=" . $fb_app_secret;
        $token_url .= "&code=" . $_GET['code'];
        
        $response = file_get_contents($token_url);
        $params = json_decode($response, true);
        
        if(isset($params['access_token'])) {
            // Get user profile data
            $graph_url = "https://graph.facebook.com/v12.0/me?fields=id,name,email";
            $graph_url .= "&access_token=" . $params['access_token'];
            
            $user_info = file_get_contents($graph_url);
            $user = json_decode($user_info, true);
            
            if(isset($user['email'])) {
                processUserSocialLogin($user['email'], $user['name'], 'facebook', $user['id']);
            } else {
                // No email provided
                header("Location: " . SITE . "uyelik?error=no_email");
                exit;
            }
        } else {
            // Error getting access token
            header("Location: " . SITE . "uyelik?error=token");
            exit;
        }
    } else {
        // Redirect to Facebook login
        $fb_app_id = ''; // Facebook App ID
        $fb_redirect_uri = SITE . 'social-auth.php?provider=facebook';
        
        $dialog_url = "https://www.facebook.com/v12.0/dialog/oauth";
        $dialog_url .= "?client_id=" . $fb_app_id;
        $dialog_url .= "&redirect_uri=" . urlencode($fb_redirect_uri);
        $dialog_url .= "&scope=email";
        
        header("Location: " . $dialog_url);
        exit;
    }
}

// Google Login Handler
function handleGoogleLogin() {
    global $VT;
    
    // Check if this is a callback from Google
    if(isset($_GET['code'])) {
        // Process Google response
        $google_client_id = ''; // Google Client ID
        $google_client_secret = ''; // Google Client Secret
        $google_redirect_uri = SITE . 'social-auth.php?provider=google';
        
        // Exchange code for access token
        $token_url = 'https://oauth2.googleapis.com/token';
        $post_data = array(
            'code' => $_GET['code'],
            'client_id' => $google_client_id,
            'client_secret' => $google_client_secret,
            'redirect_uri' => $google_redirect_uri,
            'grant_type' => 'authorization_code'
        );
        
        $ch = curl_init($token_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        curl_setopt($ch, CURLOPT_POST, true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $token_data = json_decode($response, true);
        
        if(isset($token_data['access_token'])) {
            // Get user profile data
            $user_info_url = 'https://www.googleapis.com/oauth2/v2/userinfo';
            $ch = curl_init($user_info_url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token_data['access_token']));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $user_info = curl_exec($ch);
            curl_close($ch);
            
            $user = json_decode($user_info, true);
            
            if(isset($user['email'])) {
                processUserSocialLogin($user['email'], $user['name'], 'google', $user['id']);
            } else {
                // No email provided
                header("Location: " . SITE . "uyelik?error=no_email");
                exit;
            }
        } else {
            // Error getting access token
            header("Location: " . SITE . "uyelik?error=token");
            exit;
        }
    } else {
        // Redirect to Google login
        $google_client_id = ''; // Google Client ID
        $google_redirect_uri = SITE . 'social-auth.php?provider=google';
        
        $auth_url = 'https://accounts.google.com/o/oauth2/v2/auth';
        $auth_url .= '?client_id=' . urlencode($google_client_id);
        $auth_url .= '&redirect_uri=' . urlencode($google_redirect_uri);
        $auth_url .= '&response_type=code';
        $auth_url .= '&scope=email profile';
        $auth_url .= '&access_type=online';
        
        header("Location: " . $auth_url);
        exit;
    }
}

// Process user login or registration from social media
function processUserSocialLogin($email, $name, $provider, $provider_id) {
    global $VT;
    
    // Check if user exists
    $kontrol = $VT->VeriGetir("uyeler", "WHERE mail=? AND durum=?", array($email, 1), "ORDER BY ID ASC", 1);
    
    if($kontrol != false) {
        // User exists, log them in
        $_SESSION["uyeID"] = $kontrol[0]["ID"];
        $_SESSION["uyeTipi"] = $kontrol[0]["tipi"];
        
        if($kontrol[0]["tipi"] == 1) {
            $_SESSION["uyeAdi"] = $kontrol[0]["ad"];
        } else {
            $_SESSION["uyeAdi"] = $kontrol[0]["firmaadi"];
        }
        
        // Update social media reference if not already set
        if(empty($kontrol[0]["{$provider}_id"])) {
            $VT->SorguCalistir("UPDATE uyeler", "SET {$provider}_id=? WHERE ID=?", array($provider_id, $kontrol[0]["ID"]));
        }
        
        header("Location: " . SITE . "hesabim");
        exit;
    } else {
        // User doesn't exist, create new account
        $name_parts = explode(' ', $name);
        $ad = $name_parts[0];
        $soyad = isset($name_parts[1]) ? $name_parts[1] : '';
        
        // Generate a random password
        $random_password = md5(uniqid(rand(), true));
        
        // Create new user
        $ekle = $VT->SorguCalistir("INSERT INTO uyeler", 
            "SET ad=?, soyad=?, mail=?, sifre=?, tipi=?, durum=?, tarih=?, {$provider}_id=?",
            array($ad, $soyad, $email, $random_password, 1, 1, date("Y-m-d"), $provider_id));
        
        if($ekle) {
            // Get newly created user
            $yeni_uye = $VT->VeriGetir("uyeler", "WHERE mail=?", array($email), "ORDER BY ID DESC", 1);
            
            if($yeni_uye != false) {
                $_SESSION["uyeID"] = $yeni_uye[0]["ID"];
                $_SESSION["uyeTipi"] = 1;
                $_SESSION["uyeAdi"] = $ad;
                
                header("Location: " . SITE . "hesabim?yeni=1");
                exit;
            }
        }
        
        // If we get here, something went wrong
        header("Location: " . SITE . "uyelik?error=register");
        exit;
    }
}
?>
