<?php
session_start();
include_once("admin/class/VT.php");
$VT = new VT();

// Get cart count
$uyeID = !empty($_SESSION["uyeID"]) ? $_SESSION["uyeID"] : null;
$sepetSayisi = $VT->sepetUrunSayisi($uyeID);

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'count' => $sepetSayisi
]);
?>
