<?php
class VT
{

	var $sunucu = "localhost";
	var $user = "root";
	var $password = "";
	var $dbname = "eticaret";
	var $baglanti;
	public $lastError = '';
	private $lastQuery = '';
    private $lastParams = array();

	function __construct()
	{
		try {

			$this->baglanti = new PDO("mysql:host=" . $this->sunucu . ";dbname=" . $this->dbname . ";charset=utf8;", $this->user, $this->password);

		} catch (PDOException $error) {

			echo $error->getMessage();
			exit();
		}
	}

	public function VeriGetir($tablo, $wherealanlar = "", $wherearraydeger = "", $ordeby = "ORDER BY ID ASC", $limit = "")
	{
		$this->baglanti->query("SET CHARACTER SET utf8");
		$sql = "SELECT * FROM " . $tablo; /*SELECT * FROM ayarlar*/
		if (!empty($wherealanlar) && !empty($wherearraydeger)) {
			$sql .= " " . $wherealanlar; /*SELECT * FROM ayarlarWHERE */
			if (!empty($ordeby)) {
				$sql .= " " . $ordeby;
			}
			if (!empty($limit)) {
				$sql .= " LIMIT " . $limit;
			}
			$calistir = $this->baglanti->prepare($sql);
			$sonuc = $calistir->execute($wherearraydeger);
			$veri = $calistir->fetchAll(PDO::FETCH_ASSOC);
		} else {
			if (!empty($ordeby)) {
				$sql .= " " . $ordeby;
			}
			if (!empty($limit)) {
				$sql .= " LIMIT " . $limit;
			}
			$veri = $this->baglanti->query($sql, PDO::FETCH_ASSOC);
		}

		if ($veri != false && !empty($veri)) {
			$datalar = array();
			foreach ($veri as $bilgiler) {
				$datalar[] = $bilgiler;
			}
			return $datalar;
		} else {
			return false;
		}

	}

	public function SorguCalistir($tablo, $alanlar = "", $degerlerarray = "", $limit = "")
	{
		try {
			$this->baglanti->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->baglanti->query("SET CHARACTER SET utf8mb4");
			
			if (!empty($alanlar) && !empty($degerlerarray)) {
				$sql = $tablo . " " . $alanlar;
				if (!empty($limit)) {
					$sql .= " LIMIT " . $limit;
				}
				
				 // Store query info for debugging
                $this->lastQuery = $sql;
                $this->lastParams = $degerlerarray;
				
				try {
					$calistir = $this->baglanti->prepare($sql);
					$sonuc = $calistir->execute($degerlerarray);
					
					if (!$sonuc) {
						$this->lastError = "SQL Error: " . implode(" ", $calistir->errorInfo());
						error_log($this->lastError);
						return false;
					}
				} catch (PDOException $e) {
					$this->lastError = "SQL Error: " . $e->getMessage();
					error_log($this->lastError . "\nSQL: " . $sql . "\nParams: " . print_r($degerlerarray, true));
					return false;
				}
			} else {
				$sql = $tablo;
				if (!empty($limit)) {
					$sql .= " LIMIT " . $limit;
				}
				$sonuc = $this->baglanti->exec($sql);
			}
			
			$this->lastError = '';
			return true;
			
		} catch (PDOException $e) {
			$this->lastError = "Database Error: " . $e->getMessage();
			error_log($this->lastError);
			return false;
		}
	}

	public function getLastError() {
		return $this->lastError;
	}

	public function getLastQuery() {
        return $this->lastQuery;
    }

    public function getLastParams() {
        return $this->lastParams;
    }

	public function seflink($val)
	{
		$find = array('Ç', 'Ş', 'Ğ', 'Ü', 'İ', 'Ö', 'ç', 'ş', 'ğ', 'ü', 'ö', 'ı', '+', '#', '?', '*', '!', '.', '(', ')');
		$replace = array('c', 's', 'g', 'u', 'i', 'o', 'c', 's', 'g', 'u', 'o', 'i', 'plus', 'sharp', '', '', '', '', '', '');
		$string = strtolower(str_replace($find, $replace, $val));
		$string = preg_replace("@[^A-Za-z0-9\-_\.\+]@i", ' ', $string);
		$string = trim(preg_replace('/\s+/', ' ', $string));
		$string = str_replace(' ', '-', $string);
		return $string;
	}

	public function uploadMulti($nesnename, $tablo = 'nan', $KID = 1, $yuklenecekyer = 'images/', $tur = 'img', $w = '', $h = '', $resimyazisi = '')
	{
		if ($tur == "img") {
			if (!empty($_FILES[$nesnename]["name"][0])) {
				$dosyanizinadi = $_FILES[$nesnename]["name"][0];
				$tmp_name = $_FILES[$nesnename]["tmp_name"][0];
				$uzanti = $this->uzanti($dosyanizinadi);
				if ($uzanti == "png" || $uzanti == "jpg" || $uzanti == "jpeg" || $uzanti == "gif") {
					$resimler = array();
					foreach ($_FILES[$nesnename] as $k => $l) {
						foreach ($l as $i => $v) {
							if (!array_key_exists($i, $resimler))
								$resimler[$i] = array();
							$resimler[$i][$k] = $v;
						}
					}

					foreach ($resimler as $resim) {
						$uzanti = $this->uzanti($resim["name"]);
						if ($uzanti == "png" || $uzanti == "jpg" || $uzanti == "jpeg" || $uzanti == "gif") {
							$handle = new Upload($resim);
							if ($handle->uploaded) {

								/* Resmi Yeniden Adlandır */
								$rand = uniqid(true);
								$handle->file_new_name_body = $rand;

								/* Resmi Yeniden Boyutlandır */
								if (!empty($w)) {
									if (!empty($h)) {

										$handle->image_resize = true;
										$handle->image_x = $w;
										$handle->image_y = $h;

									} else {
										if ($handle->image_src_x > $w) {
											$handle->image_resize = true;
											$handle->image_x = $w;
											$handle->image_ratio_y = true;
										}
									}
								} else if (!empty($h)) {
									if ($handle->image_src_h > $h) {
										$handle->image_resize = true;
										$handle->image_y = $h;
										$handle->image_ratio_x = true;
									}
								}

								//üzerine yazı yazdırma
								if (!empty($resimyazisi)) {
									$handle->image_text = $resimyazisi;
									$handle->image_text_color = '#FFFFFF';
									$handle->image_text_opacity = 80;
									//$handle->image_text_background = '#FFFFFF';
									$handle->image_text_background_opacity = 70;
									$handle->image_text_font = 5;
									$handle->image_text_padding = 1;
								}


								/* Resim Yükleme İzni */
								$handle->allowed = array('image/*');

								/* Resmi İşle */
								//$handle->Process(realpath("../")."/upload/resim/");
								$handle->Process($yuklenecekyer);
								if ($handle->processed) {
									$yukleme = $rand . "." . $handle->image_src_type;
									if (!empty($yukleme)) {
										//$yuklemekontrol=$fnk->DKontrol("../images/resimler/".$yukleme);
										$sira = $this->IDGetir("resimler");

										$sql = $this->SorguCalistir("INSERT INTO resimler", "SET tablo=?, KID=?, resim=?, tarih=?", array($tablo, $KID, $yukleme, date("Y-m-d")));


									} else {
										return false;
									}

								} else {
									return false;
								}

								$handle->Clean();

							} else {
								return false;
							}


						}
					}
					return true;


				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}




	public function ModulEkle()
	{
		if (!empty($_POST["baslik"])) {
			$baslik = $_POST["baslik"];
			if (!empty($_POST["durum"])) {
				$durum = 1;
			} else {
				$durum = 2;
			}
			$tablo = str_replace("-", "", $this->seflink($baslik));
			$kontrol = $this->VeriGetir("moduller", "WHERE tablo=?", array($tablo), "ORDER BY ID ASC", 1);
			if ($kontrol != false) {
				return false;
			} else {
				$tabloOlustur = $this->SorguCalistir('CREATE TABLE IF NOT EXISTS `' . $tablo . '` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `baslik` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL,
  `seflink` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL,
  `kategori` int(11) DEFAULT NULL,
  `metin` text COLLATE utf8_turkish_ci,
  `resim` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL,
  `anahtar` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL,
  `durum` int(5) DEFAULT NULL,
  `sirano` int(11) DEFAULT NULL,
  `tarih` date DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=1 ;');
				$modulekle = $this->SorguCalistir("INSERT INTO moduller", "SET baslik=?, tablo=?, durum=?, tarih=?", array($baslik, $tablo, $durum, date("Y-m-d")));
				$kategoriekle = $this->SorguCalistir("INSERT INTO kategoriler", "SET baslik=?, seflink=?, tablo=?, durum=?, tarih=?", array($baslik, $tablo, 'modul', 1, date("Y-m-d")));
				if ($modulekle != false) {
					return true;
				} else {
					return false;
				}
			}

		} else {
			return false;
		}
	}

	public function filter($val, $tf = false) {
    try {
        if ($tf == false) {
            $val = strip_tags($val);
        }
        $val = addslashes(trim($val));
        return $val;
    } catch (Exception $e) {
        error_log("Filter error: " . $e->getMessage());
        return false;
    }
}

	public function uzanti($dosyaadi)
	{
		$parca = explode(".", $dosyaadi);
		$uzanti = end($parca);
		$donustur = strtolower($uzanti);
		return $donustur;
	}

	public function upload($nesnename, $yuklenecekyer = 'images/', $tur = 'img', $w = '', $h = '', $resimyazisi = '')
	{
		if ($tur == "img") {
			if (!empty($_FILES[$nesnename]["name"])) {
				$dosyanizinadi = $_FILES[$nesnename]["name"];
				$tmp_name = $_FILES[$nesnename]["tmp_name"];
				$uzanti = $this->uzanti($dosyanizinadi);
				if ($uzanti == "png" || $uzanti == "jpg" || $uzanti == "jpeg" || $uzanti == "gif") {
					$classIMG = new upload($_FILES[$nesnename]);
					if ($classIMG->uploaded) {
						if (!empty($w)) {
							if (!empty($h)) {
								$classIMG->image_resize = true;
								$classIMG->image_x = $w;
								$classIMG->image_y = $h;
							} else {
								if ($classIMG->image_src_x > $w) {
									$classIMG->image_resize = true;
									$classIMG->image_ratio_y = true;
									$classIMG->image_x = $w;
								}
							}
						} else if (!empty($h)) {
							if ($classIMG->image_src_h > $h) {
								$classIMG->image_resize = true;
								$classIMG->image_ratio_x = true;
								$classIMG->image_y = $h;
							}
						}

						if (!empty($resimyazisi)) {
							$classIMG->image_text = $resimyazisi;

							$classIMG->image_text_direction = 'v';

							$classIMG->image_text_color = '#FFFFFF';

							$classIMG->image_text_position = 'BL';
						}
						$rand = uniqid(true);
						$classIMG->file_new_name_body = $rand;
						$classIMG->Process($yuklenecekyer);
						if ($classIMG->processed) {
							$resimadi = $rand . "." . $uzanti;
							return $resimadi;
						} else {
							return false;
						}
					} else {
						return false;
					}
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else if ($tur == "ds") {

			if (!empty($_FILES[$nesnename]["name"])) {

				$dosyanizinadi = $_FILES[$nesnename]["name"];
				$tmp_name = $_FILES[$nesnename]["tmp_name"];
				$uzanti = $this->uzanti($dosyanizinadi);
				if ($uzanti == "doc" || $uzanti == "docx" || $uzanti == "pdf" || $uzanti == "xlsx" || $uzanti == "xls" || $uzanti == "ppt" || $uzanti == "xml" || $uzanti == "mp4" || $uzanti == "avi" || $uzanti == "mov") {

					$classIMG = new upload($_FILES[$nesnename]);
					if ($classIMG->uploaded) {
						$rand = uniqid(true);
						$classIMG->file_new_name_body = $rand;
						$classIMG->Process($yuklenecekyer);
						if ($classIMG->processed) {
							$dokuman = $rand . "." . $uzanti;
							return $dokuman;
						} else {
							return false;
						}
					}
				}
			}
		} else {
			return false;
		}
	}

	public function kategoriGetir($tablo, $secID = "", $uz = -1)
	{
		$uz++;
		$kategori = $this->VeriGetir("kategoriler", "WHERE tablo=?", array($tablo), "ORDER BY ID ASC");
		if ($kategori != false) {
			for ($q = 0; $q < count($kategori); $q++) {
				$kategoriseflink = $kategori[$q]["seflink"];
				$kategoriID = $kategori[$q]["ID"];
				if ($secID == $kategoriID) {
					echo '<option value="' . $kategoriID . '" selected="selected">' . str_repeat("&nbsp;&nbsp;&nbsp;", $uz) . stripslashes($kategori[$q]["baslik"]) . '</option>';
				} else {
					echo '<option value="' . $kategoriID . '">' . str_repeat("&nbsp;&nbsp;&nbsp;", $uz) . stripslashes($kategori[$q]["baslik"]) . '</option>';
				}
				if ($kategoriseflink == $tablo) {
					break;
				}
				$this->kategoriGetir($kategoriseflink, $secID, $uz);
			}
		} else {
			return false;
		}
	}

	public function tekKategori($tablo, $secID = "", $uz = -1)
	{
		$uz++;
		$kategori = $this->VeriGetir("kategoriler", "WHERE seflink=? AND tablo=?", array($tablo, "modul"), "ORDER BY ID ASC");
		if ($kategori != false) {
			for ($q = 0; $q < count($kategori); $q++) {
				$kategoriseflink = $kategori[$q]["seflink"];
				$kategoriID = $kategori[$q]["ID"];
				if ($secID == $kategoriID) {
					echo '<option value="' . $kategoriID . '" selected="selected">' . str_repeat("&nbsp;&nbsp;&nbsp;", $uz) . stripslashes($kategori[$q]["baslik"]) . '</option>';
				} else {
					echo '<option value="' . $kategoriID . '">' . str_repeat("&nbsp;&nbsp;&nbsp;", $uz) . stripslashes($kategori[$q]["baslik"]) . '</option>';
				}

			}
		} else {
			return false;
		}
	}


	//Kategori İşlemleri
	public function kategoriGetir2($tablo, $secID = "", $editID = "") {
    // This improved function will generate a proper dropdown with all categories
    $data = "";
    
    // Get all main categories
    $kategoriler = $this->VeriGetir("kategoriler", "WHERE tablo=?", array($tablo), "ORDER BY ID ASC");
    
    if ($kategoriler != false) {
        for ($i = 0; $i < count($kategoriler); $i++) {
            // Check if the current category is selected
            $selected = ($secID == $kategoriler[$i]["seflink"]) ? ' selected' : '';
            
            // Skip the current category we're editing to avoid circular references
            if ($editID == $kategoriler[$i]["ID"]) {
                continue;
            }
            
            $data .= '<option value="'.$kategoriler[$i]["seflink"].'"'.$selected.'>'.$kategoriler[$i]["baslik"].'</option>';
            
            // Add subcategories
            $data .= $this->altKategoriGetir($kategoriler[$i]["seflink"], $secID, $editID, 0);
        }
        
        return $data;
    } else {
        return false;
    }
}

public function altKategoriGetir($tablo, $secID = "", $editID = "", $level = 0) {
    // Function to get sub-categories with proper indentation
    $data = "";
    $level++;
    $indent = str_repeat("— ", $level);
    
    // Get subcategories
    $kategoriler = $this->VeriGetir("kategoriler", "WHERE tablo=?", array($tablo), "ORDER BY sirano ASC");
    
    if ($kategoriler != false) {
        for ($i = 0; $i < count($kategoriler); $i++) {
            // Skip the current category we're editing to avoid circular references
            if ($editID == $kategoriler[$i]["ID"]) {
                continue;
            }
            
            // Check if the current category is selected
            $selected = ($secID == $kategoriler[$i]["seflink"]) ? ' selected' : '';
            
            $data .= '<option value="'.$kategoriler[$i]["seflink"].'"'.$selected.'>'.$indent.$kategoriler[$i]["baslik"].'</option>';
            
            // Recursively add deeper subcategories
            $data .= $this->altKategoriGetir($kategoriler[$i]["seflink"], $secID, $editID, $level);
        }
        
        return $data;
    } else {
        return "";
    }
}

public function tekSorgu($query, $params = []) {
    try {
        $stmt = $this->baglanti->prepare($query);
        
        if ($stmt->execute($params)) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    } catch (Exception $e) {
        // Log the error but don't display it on the page
        error_log("SQL Error in tekSorgu: " . $e->getMessage());
        return false;
    }
}

    public function sepetUrunSayisi($uyeID = null) {
        $toplamUrun = 0;
        
        // Check session-based cart first
        if (isset($_SESSION["sepet"]) && is_array($_SESSION["sepet"])) {
            foreach ($_SESSION["sepet"] as $urunID => $bilgi) {
                if ($bilgi["varyasyondurumu"] == false) {
                    // Regular product
                    if (isset($bilgi["adet"]) && is_numeric($bilgi["adet"])) {
                        $toplamUrun += $bilgi["adet"];
                    } else {
                        $toplamUrun++;
                    }
                } else {
                    // Product with variations
                    if (isset($_SESSION["sepetVaryasyon"][$urunID]) && is_array($_SESSION["sepetVaryasyon"][$urunID])) {
                        foreach ($_SESSION["sepetVaryasyon"][$urunID] as $secenekID => $secenekAdet) {
                            if (isset($secenekAdet["adet"]) && is_numeric($secenekAdet["adet"])) {
                                $toplamUrun += $secenekAdet["adet"];
                            } else {
                                $toplamUrun++;
                            }
                        }
                    }
                }
            }
        }
        
        // If user is logged in, also check database-stored cart items
        if (!empty($uyeID)) {
            try {
                $sorgu = $this->baglanti->prepare("SELECT COUNT(*) as urun_sayisi FROM sepet WHERE uyeID = ?");
                $sorgu->execute([$uyeID]);
                $sonuc = $sorgu->fetch(PDO::FETCH_ASSOC);
                if ($sonuc && isset($sonuc['urun_sayisi'])) {
                    $toplamUrun += intval($sonuc['urun_sayisi']);
                }
            } catch (Exception $e) {
                error_log("Sepet ürün sayısı hatası: " . $e->getMessage());
                // Continue with the session count if the database query fails
            }
        }
        
        return $toplamUrun;
    }

	/*Ektra Bonus Fonksiyonlar*/
	/*
	 * Sitenize gelen ziyaretçilerin rapoarlarını kaydedebilir ve hangi tarayıcıdan kaç ziyaretçinin sitenizi ziyaret ettiğini görebilirsiniz.
	 * Buradaki fonksiyonlar eğitim serimizin 2. Etebında kurumsal firma ve e-ticaret siteleri oluşturulurken kullanılacaktır.
	 */
	public function TekilCogul()
	{
		date_default_timezone_set('Europe/Istanbul');
		$tarih = date("Y-m-d");
		$IP = $this->ipGetir();
		$TARAYICI = $this->tarayiciGetir();
		$tarayicistatistic = $this->VeriGetir("ziyarettarayici", "", "", "ORDER BY ID ASC");

		$konts = $this->VeriGetir("ziyaretciler", "WHERE tarih=? AND IP=?", array($tarih, $IP), "ORDER BY ID ASC", 1);
		if (count($konts) > 0 && $konts != false) {
			$c = ($konts[0]["cogul"] + 1);
			$ID = $konts[0]["ID"];
			$gunc = $this->SorguCalistir("UPDATE ziyaretciler", "SET cogul=? WHERE ID=?", array($c, $ID), 1);
		} else {
			if (!empty($_COOKIE["siteSettingsUse"])) {
			} else {
				$eks = $this->SorguCalistir("INSERT INTO ziyaretciler", "SET IP=?, tarayici=?, tekil=?, cogul=?, xr=?, tarih=?", array($IP, $TARAYICI, 1, 1, 1, $tarih));
				@setcookie("siteSettingsUse", md5(rand(1, 99999)), time() + (60 * 60 * 8));
				if ($TARAYICI == "Google Chrome") {
					$tbl = ($tarayicistatistic[0]["ziyaret"] + 1);
					$tiid = $tarayicistatistic[0]["ID"];
					$ersa = $this->SorguCalistir("UPDATE ziyarettarayici", "SET ziyaret=? WHERE ID=?", array($tbl, $tiid), 1);
				} else if ($TARAYICI == "İnternet Explorer") {
					$tbl = ($tarayicistatistic[1]["ziyaret"] + 1);
					$tiid = $tarayicistatistic[1]["ID"];
					$ersa = $this->SorguCalistir("UPDATE ziyarettarayici", "SET ziyaret=? WHERE ID=?", array($tbl, $tiid), 1);
				} else if ($TARAYICI == "Firefox") {
					$tbl = ($tarayicistatistic[2]["ziyaret"] + 1);
					$tiid = $tarayicistatistic[2]["ID"];
					$ersa = $this->SorguCalistir("UPDATE ziyarettarayici", "SET ziyaret=? WHERE ID=?", array($tbl, $tiid), 1);
				} else {
					$tbl = ($tarayicistatistic[3]["ziyaret"] + 1);
					$tiid = $tarayicistatistic[3]["ID"];
					$ersa = $this->SorguCalistir("UPDATE ziyarettarayici", "SET ziyaret=? WHERE ID=?", array($tbl, $tiid), 1);
				}
			}
		}

		/*sayfa hızı hesaplama*/
		$start = microtime(true);
		$end = microtime(true);
		$time = mb_substr(($end - $start), 0, 4);
		$tarah = $this->SorguCalistir("UPDATE ziyarettarayici", "SET hiz=? WHERE ID=?", array($time, 5), 1);
	}
	public function tarayiciGetir()
	{
		$tarayiciBul = $_SERVER["HTTP_USER_AGENT"];
		$msie = strpos($tarayiciBul, 'MSIE') ? true : false;
		$firefox = strpos($tarayiciBul, 'Firefox') ? true : false;
		$chrome = strpos($tarayiciBul, 'Chrome') ? true : false;
		if (!empty($msie) && $msie != false) {
			$tarayici = "İnternet Explorer";
		} else if (!empty($firefox) && $firefox != false) {
			$tarayici = "Firefox";
		} else if (!empty($chrome) && $chrome != false) {
			$tarayici = "Google Chrome";
		} else {
			$tarayici = "Diğer";
		}
		return $tarayici;
	}
	public function ipGetir()
	{
		if (getenv("HTTP_CLIENT_IP")) {
			$ip = getenv("HTTP_CLIENT_IP");
		} elseif (getenv("HTTP_X_FORWARDED_FOR")) {
			$ip = getenv("HTTP_X_FORWARDED_FOR");
			if (strstr($ip, ',')) {
				$tmp = explode(',', $ip);
				$ip = trim($tmp[0]);
			}
		} else {
			$ip = getenv("REMOTE_ADDR");
		}
		return $ip;
	}

	public function IDGetir($tablo)
	{
		$sql = $this->baglanti->query("SHOW TABLE STATUS FROM `" . $this->dbname . "` LIKE '" . $tablo . "'");
		if ($sql != false) {
			foreach ($sql as $sonuc) {

				$IDbilgisi = $sonuc["Auto_increment"];
				return $IDbilgisi;
				break;
			}
		} else {
			return false;
		}
	}

	public function MailGonder($mail, $mesaj, $konu=null)
	{
		if($konu==null) {
			$konu = "E-posta Bilgilendirme";
		}
		$posta = new PHPMailer();
		$posta->CharSet = "UTF-8";
		$posta->IsSMTP(true); // send via SMTP
		$posta->Host = "smtp.gmail.com"; // SMTP servers
		$posta->SMTPAuth = true;
		$posta->SMTPSecure = "none"; // turn on SMTP authentication
		$posta->Username = "mailadresi@gmail.com"; // SMTP username
		$posta->Password = "password"; // SMTP password
		$posta->SMTPSecure = "ssl"; //yada tls
		$posta->Port = 465;
		$posta->From = "mailadresi@gmail.com"; // smtp kullanýcý adýnýz ile ayný olmalý
		$posta->Fromname = "Admin";
		$posta->AddAddress($mail, "mailadresi@gmail.com");
		$posta->Subject = $konu;
		$posta->Body = $mesaj;

		if (!$posta->Send()) {
			echo '<!--' . $posta->ErrorInfo . '-->';
			return false;
		} else {
			return true;
		}
	}


}



?>