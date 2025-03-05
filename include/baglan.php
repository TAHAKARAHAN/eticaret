<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create a more robust database connection class
class VT {
    private $VTHOST = "localhost";
    private $VTNAME = "eticaret";  // Make sure this matches your database name
    private $VTUSER = "root";      // Default XAMPP username
    private $VTPASS = "";          // Default XAMPP password (empty)
    
    private $CHARSET = "UTF8";
    private $COLLATION = "utf8_general_ci";
    
    private $db = null;
    public $lastInsertID = 0;
    
    function __construct() {
        try {
            $this->db = new PDO("mysql:host=".$this->VTHOST.";dbname=".$this->VTNAME.";charset=".$this->CHARSET, $this->VTUSER, $this->VTPASS);
            $this->db->query("SET CHARACTER SET ".$this->CHARSET);
            $this->db->query("SET NAMES ".$this->CHARSET);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    // Sanitize input
    public function filter($val, $tf = false) {
        if (is_array($val)) {
            $newArray = array();
            foreach ($val as $key => $value) {
                $newArray[$key] = $this->filter($value, $tf);
            }
            return $newArray;
        } else {
            if ($tf == false) {
                $val = strip_tags($val);
            }
            $val = addslashes(trim($val));
            return $val;
        }
    }
    
    // Get data from database with better error handling
    public function VeriGetir($tablo, $whereKosul = "", $parametreler = array(), $orderBy = "", $limit = "") {
        try {
            $sql = "SELECT * FROM " . $tablo;
            
            if(!empty($whereKosul)) {
                $sql .= " " . $whereKosul;
            }
            
            if(!empty($orderBy)) {
                $sql .= " " . $orderBy;
            }
            
            if(!empty($limit)) {
                $sql .= " LIMIT " . $limit;
            }
            
            if(count($parametreler) > 0) {
                $stmt = $this->db->prepare($sql);
                $stmt->execute($parametreler);
            } else {
                $stmt = $this->db->query($sql);
            }
            
            if($stmt->rowCount() > 0) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return false;
            }
        } catch(PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }
    
    // Run SQL query with better error handling
    public function SorguCalistir($tablo, $islemTipi = "", $parametreler = array(), $limit = "") {
        try {
            $sql = $tablo . " " . $islemTipi;
            
            if(!empty($limit)) {
                $sql .= " LIMIT " . $limit;
            }
            
            $stmt = $this->db->prepare($sql);
            $sonuc = $stmt->execute($parametreler);
            
            if($sonuc) {
                return true;
            } else {
                return false;
            }
        } catch(PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }
    
    // Safe function to access array elements with default value
    public function safeArrayGet($array, $key, $default = null) {
        return isset($array[$key]) ? $array[$key] : $default;
    }
    
    // Check connection status
    public function isConnected() {
        return ($this->db !== null);
    }
    
    // Log a test query to verify connection
    public function testConnection() {
        try {
            $result = $this->db->query("SELECT 1")->fetch();
            return ($result[0] == 1);
        } catch(PDOException $e) {
            error_log("Test Connection Error: " . $e->getMessage());
            return false;
        }
    }
}

// Initialize database connection and make it available globally
global $VT;
$VT = new VT();

// Test the connection
if($VT->isConnected() && $VT->testConnection()) {
    // Connected successfully
    if(isset($_GET["debug"]) && $_GET["debug"] == "1") {
        echo "<!-- Database connected successfully -->";
    }
} else {
    // Connection failed
    if(isset($_GET["debug"]) && $_GET["debug"] == "1") {
        echo "<!-- Database connection failed -->";
    }
}
?>
