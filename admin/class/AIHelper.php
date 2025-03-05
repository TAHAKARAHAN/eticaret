<?php
class AIHelper {
    private $apiKey;
    private $endpoint;
    private $model;
    private $VT;

    public function __construct($VT) {
        // Get settings from database
        $ayarlar = $VT->VeriGetir("ayarlar", "WHERE ID=?", array(1), "ORDER BY ID ASC", 1);
        if($ayarlar != false) {
            $this->apiKey = !empty($ayarlar[0]["ai_api_key"]) ? $ayarlar[0]["ai_api_key"] : "";
            $this->endpoint = !empty($ayarlar[0]["ai_endpoint"]) ? $ayarlar[0]["ai_endpoint"] : "https://api.openai.com/v1/chat/completions";
            $this->model = !empty($ayarlar[0]["ai_model"]) ? $ayarlar[0]["ai_model"] : "gpt-3.5-turbo";
        }
        $this->VT = $VT;
    }

    /**
     * Send a request to the AI API
     * 
     * @param string $prompt The prompt to send to the AI
     * @return array|false The AI response or false on failure
     */
    public function sendRequest($prompt) {
        if(empty($this->apiKey)) {
            error_log("AI API key is not set");
            return false;
        }

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ];

        $data = [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a helpful SEO assistant. Respond with JSON containing SEO suggestions.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.7
        ];

        $ch = curl_init($this->endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if($err) {
            error_log("cURL Error: " . $err);
            return false;
        }

        $decoded = json_decode($response, true);
        if(isset($decoded['choices'][0]['message']['content'])) {
            try {
                // The AI response should be in JSON format
                $contentJson = json_decode($decoded['choices'][0]['message']['content'], true);
                return $contentJson;
            } catch(Exception $e) {
                error_log("Error parsing AI JSON response: " . $e->getMessage());
                return false;
            }
        }

        return false;
    }

    /**
     * Generate SEO suggestions for a specific page
     * 
     * @param string $url The URL to analyze
     * @param string $title Current title
     * @param string $description Current description
     * @param array $keywords Current keywords
     * @return array|false Suggestions or false on failure
     */
    public function getSEOSuggestions($url, $title, $description, $keywords) {
        // Get the HTML content of the page
        $pageContent = @file_get_contents($url);
        if($pageContent === false) {
            error_log("Could not fetch page content for SEO analysis: " . $url);
            return false;
        }

        // Strip HTML tags to get plain text
        $plainText = strip_tags($pageContent);
        $plainText = substr($plainText, 0, 3000); // Limit text for API

        $prompt = "Analyze this web page and provide SEO recommendations in JSON format. 
        Current title: \"$title\"
        Current description: \"$description\" 
        Current keywords: \"$keywords\"
        
        Page content excerpt: \"$plainText\"
        
        Return the JSON in this format:
        {
          \"title\": \"Suggested improved title\",
          \"description\": \"Suggested meta description\",
          \"keywords\": [\"keyword1\", \"keyword2\", \"keyword3\"],
          \"h1_suggestions\": [\"suggestion1\", \"suggestion2\"],
          \"content_recommendations\": [\"recommendation1\", \"recommendation2\"],
          \"missing_elements\": [\"element1\", \"element2\"],
          \"additional_tips\": [\"tip1\", \"tip2\"]
        }";

        return $this->sendRequest($prompt);
    }

    /**
     * Automatically optimize a page's SEO
     * 
     * @param string $url URL to optimize
     * @return bool Success status
     */
    public function autoOptimizeSEO($table, $id) {
        // Get page data
        $page = $this->VT->VeriGetir($table, "WHERE ID=?", array($id), "ORDER BY ID ASC", 1);
        if($page === false) {
            return false;
        }

        $url = SITE . $table . "/" . $page[0]["seflink"];
        $title = $page[0]["baslik"];
        $description = $page[0]["description"] ?? "";
        $keywords = $page[0]["anahtar"] ?? "";

        $suggestions = $this->getSEOSuggestions($url, $title, $description, $keywords);
        if($suggestions === false) {
            return false;
        }

        // Apply suggestions to database
        $updateData = [
            "baslik" => $suggestions["title"] ?? $title,
            "anahtar" => is_array($suggestions["keywords"]) ? implode(", ", $suggestions["keywords"]) : $keywords,
            "description" => $suggestions["description"] ?? $description
        ];

        // Log the changes
        $this->logSEOChanges($table, $id, $page[0], $updateData);

        // Update the database
        $update = $this->VT->SorguCalistir(
            "UPDATE " . $table, 
            "SET baslik=?, anahtar=?, description=? WHERE ID=?", 
            array($updateData["baslik"], $updateData["anahtar"], $updateData["description"], $id)
        );

        return $update !== false;
    }

    /**
     * Log SEO changes for review
     */
    private function logSEOChanges($table, $id, $oldData, $newData) {
        $changeLog = [
            'table' => $table,
            'item_id' => $id,
            'old_data' => json_encode($oldData),
            'new_data' => json_encode($newData),
            'date' => date('Y-m-d H:i:s'),
            'status' => 'pending' // pending, applied, rejected
        ];

        $this->VT->SorguCalistir(
            "INSERT INTO seo_changes", 
            "SET table_name=?, item_id=?, old_data=?, new_data=?, change_date=?, status=?", 
            array($table, $id, $changeLog['old_data'], $changeLog['new_data'], $changeLog['date'], $changeLog['status'])
        );
    }

    /**
     * Generate site-wide SEO recommendations
     */
    public function getSiteWideSEORecommendations() {
        // Get site info
        $siteInfo = $this->VT->VeriGetir("ayarlar", "WHERE ID=?", array(1), "ORDER BY ID ASC", 1);
        $title = $siteInfo[0]["baslik"] ?? "";
        $description = $siteInfo[0]["aciklama"] ?? "";
        $keywords = $siteInfo[0]["anahtar"] ?? "";

        $urls = [SITE]; // Start with homepage
        
        // Add some important category pages
        $kategoriler = $this->VT->VeriGetir("kategoriler", "WHERE durum=?", array(1), "ORDER BY ID ASC", 5);
        if($kategoriler !== false) {
            foreach($kategoriler as $kategori) {
                $urls[] = SITE . "kategori/" . $kategori["seflink"];
            }
        }

        // Add some important product pages
        $urunler = $this->VT->VeriGetir("urunler", "WHERE durum=?", array(1), "ORDER BY ID ASC", 5);
        if($urunler !== false) {
            foreach($urunler as $urun) {
                $urls[] = SITE . "urun/" . $urun["seflink"];
            }
        }

        $urlsStr = implode(", ", $urls);
        
        $prompt = "Generate site-wide SEO recommendations for an e-commerce website.
        Site title: \"$title\"
        Site description: \"$description\"
        Site keywords: \"$keywords\"
        
        Important URLs: $urlsStr
        
        Return recommendations in JSON format:
        {
          \"site_title\": \"Improved site title\",
          \"site_description\": \"Improved site description\",
          \"site_keywords\": [\"keyword1\", \"keyword2\"],
          \"technical_improvements\": [\"improvement1\", \"improvement2\"],
          \"content_strategy\": [\"strategy1\", \"strategy2\"],
          \"structural_recommendations\": [\"recommendation1\", \"recommendation2\"],
          \"meta_tags\": {\"og:title\": \"value\", \"twitter:card\": \"value\"}
        }";

        return $this->sendRequest($prompt);
    }
}
?>
