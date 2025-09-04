<?php
require_once 'config.php';

class SEOAnalyzer {
    private $api_key;
    
    public function __construct() {
        $this->api_key = CLAUDE_API_KEY;
    }
    
    public function fetchUrlContent($url) {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'timeout' => 30
            ]
        ]);
        
        $html = @file_get_contents($url, false, $context);
        if ($html === false) {
            throw new Exception("URL取得エラー: " . $url);
        }
        
        return $html;
    }
    
    public function extractPageInfo($html, $url) {
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        
        $pageInfo = [
            'url' => $url,
            'title' => '',
            'meta_description' => '',
            'h1_tags' => [],
            'h2_tags' => [],
            'h3_tags' => [],
            'meta_keywords' => '',
            'images_without_alt' => 0,
            'total_images' => 0,
            'internal_links' => 0,
            'external_links' => 0,
            'canonical_url' => '',
            'meta_robots' => '',
            'og_tags' => [],
            'twitter_tags' => [],
            'content_length' => 0
        ];
        
        // タイトル取得
        $titleNodes = $xpath->query('//title');
        if ($titleNodes->length > 0) {
            $pageInfo['title'] = trim($titleNodes->item(0)->textContent);
        }
        
        // メタディスクリプション
        $metaDesc = $xpath->query('//meta[@name="description"]');
        if ($metaDesc->length > 0) {
            $pageInfo['meta_description'] = $metaDesc->item(0)->getAttribute('content');
        }
        
        // メタキーワード
        $metaKeywords = $xpath->query('//meta[@name="keywords"]');
        if ($metaKeywords->length > 0) {
            $pageInfo['meta_keywords'] = $metaKeywords->item(0)->getAttribute('content');
        }
        
        // ロボッツメタ
        $metaRobots = $xpath->query('//meta[@name="robots"]');
        if ($metaRobots->length > 0) {
            $pageInfo['meta_robots'] = $metaRobots->item(0)->getAttribute('content');
        }
        
        // カノニカルURL
        $canonical = $xpath->query('//link[@rel="canonical"]');
        if ($canonical->length > 0) {
            $pageInfo['canonical_url'] = $canonical->item(0)->getAttribute('href');
        }
        
        // 見出しタグ
        $h1Tags = $xpath->query('//h1');
        foreach ($h1Tags as $h1) {
            $pageInfo['h1_tags'][] = trim($h1->textContent);
        }
        
        $h2Tags = $xpath->query('//h2');
        foreach ($h2Tags as $h2) {
            $pageInfo['h2_tags'][] = trim($h2->textContent);
        }
        
        $h3Tags = $xpath->query('//h3');
        foreach ($h3Tags as $h3) {
            $pageInfo['h3_tags'][] = trim($h3->textContent);
        }
        
        // 画像分析
        $images = $xpath->query('//img');
        $pageInfo['total_images'] = $images->length;
        foreach ($images as $img) {
            if (!$img->getAttribute('alt')) {
                $pageInfo['images_without_alt']++;
            }
        }
        
        // リンク分析
        $links = $xpath->query('//a[@href]');
        $domain = parse_url($url, PHP_URL_HOST);
        
        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            if (strpos($href, 'http') === 0) {
                $linkDomain = parse_url($href, PHP_URL_HOST);
                if ($linkDomain === $domain) {
                    $pageInfo['internal_links']++;
                } else {
                    $pageInfo['external_links']++;
                }
            } elseif (strpos($href, '/') === 0) {
                $pageInfo['internal_links']++;
            }
        }
        
        // OGタグ
        $ogTags = $xpath->query('//meta[starts-with(@property, "og:")]');
        foreach ($ogTags as $tag) {
            $property = $tag->getAttribute('property');
            $content = $tag->getAttribute('content');
            if ($property && $content) {
                $pageInfo['og_tags'][$property] = $content;
            }
        }
        
        // Twitterタグ
        $twitterTags = $xpath->query('//meta[starts-with(@name, "twitter:")]');
        foreach ($twitterTags as $tag) {
            $name = $tag->getAttribute('name');
            $content = $tag->getAttribute('content');
            if ($name && $content) {
                $pageInfo['twitter_tags'][$name] = $content;
            }
        }
        
        // コンテンツ長
        $body = $xpath->query('//body');
        if ($body->length > 0) {
            $pageInfo['content_length'] = strlen(trim($body->item(0)->textContent));
        }
        
        return $pageInfo;
    }
    
    public function getCompetitorUrls($mainKeywords, $currentUrl, $count = 5) {
        // 主要キーワードから検索クエリを作成
        $searchQuery = implode(' ', array_slice($mainKeywords, 0, 3));
        
        // Google Custom Search API を使用（API キーが設定されている場合）
        $googleApiKey = defined('GOOGLE_API_KEY') ? GOOGLE_API_KEY : '';
        $searchEngineId = defined('GOOGLE_SEARCH_ENGINE_ID') ? GOOGLE_SEARCH_ENGINE_ID : '';
        
        if (!empty($googleApiKey) && !empty($searchEngineId)) {
            $apiUrl = "https://www.googleapis.com/customsearch/v1?" . http_build_query([
                'key' => $googleApiKey,
                'cx' => $searchEngineId,
                'q' => $searchQuery,
                'num' => 10 // 多めに取得してフィルタリング
            ]);
            
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'timeout' => 10
                ]
            ]);
            
            $response = @file_get_contents($apiUrl, false, $context);
            
            if ($response !== false) {
                $data = json_decode($response, true);
                $competitors = [];
                $currentDomain = parse_url($currentUrl, PHP_URL_HOST);
                
                if (isset($data['items'])) {
                    foreach ($data['items'] as $item) {
                        $url = $item['link'];
                        $domain = parse_url($url, PHP_URL_HOST);
                        
                        // 自サイトは除外
                        if ($domain !== $currentDomain && count($competitors) < $count) {
                            $competitors[] = $url;
                        }
                    }
                }
                
                if (count($competitors) > 0) {
                    return $competitors;
                }
            }
        }
        
        // APIが使えない場合は、一般的な競合サイトのサンプルを返す
        // 実際のキーワードに基づいて適切なサンプルを選択
        $sampleCompetitors = [];
        
        // キーワードから推測される業界に基づいてサンプルを選択
        if (strpos(strtolower($searchQuery), 'seo') !== false) {
            $sampleCompetitors = [
                "https://moz.com/beginners-guide-to-seo",
                "https://backlinko.com/seo-this-year",
                "https://ahrefs.com/blog/seo-basics/",
                "https://www.searchenginejournal.com/seo-guide/",
                "https://neilpatel.com/what-is-seo/"
            ];
        } else {
            // デフォルトのサンプル
            $sampleCompetitors = [
                "https://www.example1.com/similar-content",
                "https://www.example2.com/related-page",
                "https://www.example3.com/competitor-article",
                "https://www.example4.com/alternative-resource",
                "https://www.example5.com/competing-content"
            ];
        }
        
        return array_slice($sampleCompetitors, 0, $count);
    }
    
    public function analyzeCompetitors($pageInfo, $competitorUrls) {
        $competitorData = [];
        
        foreach ($competitorUrls as $url) {
            try {
                $html = $this->fetchUrlContent($url);
                $competitorInfo = $this->extractPageInfo($html, $url);
                $competitorData[] = $competitorInfo;
            } catch (Exception $e) {
                // エラーの場合はスキップ
                continue;
            }
        }
        
        return $competitorData;
    }
    
    private function formatCompetitorData($competitorData) {
        if (empty($competitorData)) {
            return "競合データが取得できませんでした。";
        }
        
        $formatted = "";
        foreach ($competitorData as $index => $competitor) {
            $num = $index + 1;
            $formatted .= "
### 競合サイト{$num}
- **URL**: {$competitor['url']}
- **タイトル**: {$competitor['title']}
- **メタディスクリプション**: {$competitor['meta_description']}
- **コンテンツ文字数**: {$competitor['content_length']}文字
- **H1数**: " . count($competitor['h1_tags']) . "
- **H2数**: " . count($competitor['h2_tags']) . "
- **画像数**: {$competitor['total_images']}
- **内部リンク数**: {$competitor['internal_links']}
";
        }
        
        return $formatted;
    }

    public function analyzeWithClaude($pageInfo, $useMockData = false, $competitorData = []) {
        if ($useMockData) {
            return $this->getMockAnalysis($pageInfo);
        }
        
        // cURLが利用可能かチェック
        if (!function_exists('curl_init')) {
            throw new Exception('cURLが利用できません');
        }
        
        // API設定の確認
        if (empty($this->api_key)) {
            throw new Exception('Claude APIキーが設定されていません');
        }
        
        if (!preg_match('/^sk-ant-api/', $this->api_key)) {
            throw new Exception('Claude APIキーの形式が正しくありません');
        }
        
        $analysisPrompt = "
あなたはSEOコンサルタントです。以下のWebページを分析し、改善提案をしてください。

URL: " . $pageInfo['url'] . "
タイトル: " . $pageInfo['title'] . "
メタディスクリプション: " . $pageInfo['meta_description'] . "
H1: " . implode(', ', $pageInfo['h1_tags']) . "
H2: " . implode(', ', array_slice($pageInfo['h2_tags'], 0, 3)) . "
画像: " . $pageInfo['total_images'] . "個（alt属性なし: " . $pageInfo['images_without_alt'] . "個）
内部リンク: " . $pageInfo['internal_links'] . "個
外部リンク: " . $pageInfo['external_links'] . "個
コンテンツ文字数: " . $pageInfo['content_length'] . "文字

以下の構成で分析結果を提示してください：

# 🔧 テクニカルSEO改善案
## 1. メタタグ最適化
- 改善案
- 根拠

## 2. 構造化データ改善
- 改善案
- 根拠

## 3. 画像最適化
- 改善案
- 根拠

## 4. 内部リンク改善
- 改善案
- 根拠

# ✍️ コンテンツSEO改善案
## 1. タイトル・見出し改善
- 改善案
- 根拠

## 2. コンテンツ拡充提案
- 改善案
- 根拠

## 3. ユーザー体験向上
- 改善案
- 根拠

# 🎯 トピック拡張戦略
このページの主要キーワードとコンテンツを基に、トピッククラスター戦略を提案してください。

## ピラーページ（中核記事）
- **推奨タイトル**: （SEOタイトル60文字以内）
- **メタディスクリプション**: （160文字以内）
- **見出し構成**: H1〜H3の構造化された見出し案

## クラスターページ（関連記事群）
以下の5つの関連記事を提案してください：

### 1. [クラスター記事1]
- **タイトル**: 
- **メタディスクリプション**: 
- **見出し構成**: 
- **ピラーページとの関連性**: 

### 2. [クラスター記事2]
- **タイトル**: 
- **メタディスクリプション**: 
- **見出し構成**: 
- **ピラーページとの関連性**: 

### 3. [クラスター記事3]
- **タイトル**: 
- **メタディスクリプション**: 
- **見出し構成**: 
- **ピラーページとの関連性**: 

### 4. [クラスター記事4]
- **タイトル**: 
- **メタディスクリプション**: 
- **見出し構成**: 
- **ピラーページとの関連性**: 

### 5. [クラスター記事5]
- **タイトル**: 
- **メタディスクリプション**: 
- **見出し構成**: 
- **ピラーページとの関連性**: 

## 内部リンク戦略
- ピラーページから各クラスターページへのリンク方法
- クラスターページ間の相互リンク方法
- アンカーテキストの提案

# 🏆 競合分析
このページのキーワードで上位表示されている競合サイトを分析し、改善点を特定してください。

## 競合サイト概要
以下の競合サイト情報を基に分析を行ってください：
" . $this->formatCompetitorData($competitorData) . "

## 競合比較分析
### 1. コンテンツボリューム比較
- **自サイト**: " . $pageInfo['content_length'] . "文字
- **競合平均との差**: 分析結果
- **改善提案**: 

### 2. 見出し構造比較
- **自サイトのH1数**: " . count($pageInfo['h1_tags']) . "
- **自サイトのH2数**: " . count($pageInfo['h2_tags']) . "
- **競合との構造比較**: 
- **改善提案**: 

### 3. 画像・メディア活用比較
- **自サイト画像数**: " . $pageInfo['total_images'] . "
- **競合との比較**: 
- **改善提案**: 

### 4. 内部リンク戦略比較
- **自サイト内部リンク数**: " . $pageInfo['internal_links'] . "
- **競合との比較**: 
- **改善提案**: 

## 競合に勝つための戦略
### 1. 差別化ポイント
- 競合にない独自価値の提案

### 2. 不足コンテンツの特定
- 競合が扱っているが自サイトにない要素

### 3. 上位表示のための具体的アクション
- 優先度の高い改善項目3つ

具体的で実装可能な提案をしてください。";

        $data = [
            // 'model' => 'claude-3-7-sonnet-latest',
            'model' => 'claude-sonnet-4-20250514',
            'max_tokens' => 50000,
            'temperature' => 0.3,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $analysisPrompt
                ]
            ]
        ];
        
        // cURLを使用してAPI呼び出し
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.anthropic.com/v1/messages');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 180);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-cURL/8.3');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-api-key: ' . $this->api_key,
            'anthropic-version: 2023-06-01',
            'Content-Length: ' . strlen(json_encode($data))
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        $curl_info = curl_getinfo($ch);
        curl_close($ch);
        
        // デバッグ情報をログに出力
        error_log('Claude API Debug Info: ' . json_encode([
            'http_code' => $http_code,
            'curl_error' => $curl_error,
            'response_length' => strlen($response),
            'connect_time' => $curl_info['connect_time'],
            'total_time' => $curl_info['total_time']
        ]));
        
        if ($response === false) {
            if (strpos($curl_error, 'timeout') !== false) {
                throw new Exception('Claude API通信タイムアウト: APIレスポンスに時間がかかりすぎています。しばらく待ってから再度お試しください。詳細: ' . $curl_error);
            } else {
                throw new Exception('Claude API通信エラー: ' . $curl_error);
            }
        }
        
        if ($http_code !== 200) {
            $error_msg = 'Claude API HTTPエラー: ' . $http_code;
            if (!empty($response)) {
                $decoded = json_decode($response, true);
                if ($decoded && isset($decoded['error']['message'])) {
                    $error_msg .= ' - ' . $decoded['error']['message'];
                }
            }
            throw new Exception($error_msg);
        }
        
        $result = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('JSON decode error: ' . json_last_error_msg());
            throw new Exception('レスポンス解析エラー');
        }
        
        if (isset($result['error'])) {
            error_log('Claude API error: ' . json_encode($result['error']));
            throw new Exception('Claude APIエラー: ' . $result['error']['message']);
        }
        
        if (!isset($result['content'][0]['text'])) {
            error_log('Unexpected response format: ' . json_encode($result));
            throw new Exception('予期しないレスポンス形式');
        }
        
        return $result['content'][0]['text'];
    }
    
    private function getMockAnalysis($pageInfo) {

    }
    
    public function analyzeUrl($url, $useMockData = false, $includeCompetitorAnalysis = true) {
        echo "URL取得中: " . $url . "\n";
        $html = $this->fetchUrlContent($url);
        
        echo "ページ情報抽出中...\n";
        $pageInfo = $this->extractPageInfo($html, $url);
        
        $competitorData = [];
        if ($includeCompetitorAnalysis && !$useMockData) {
            echo "競合サイト分析中...\n";
            // 主要キーワードを抽出（タイトルとH1から）
            $keywords = array_merge(
                explode(' ', $pageInfo['title']), 
                $pageInfo['h1_tags']
            );
            $competitorUrls = $this->getCompetitorUrls($keywords, $url);
            $competitorData = $this->analyzeCompetitors($pageInfo, $competitorUrls);
        }
        
        if ($useMockData) {
            echo "モックデータでSEO分析中...\n";
        } else {
            echo "Claude APIでSEO分析中...\n";
        }
        $analysis = $this->analyzeWithClaude($pageInfo, $useMockData, $competitorData);
        
        return [
            'page_info' => $pageInfo,
            'seo_analysis' => $analysis,
            'competitor_data' => $competitorData
        ];
    }
}

// 実行部分
if (php_sapi_name() === 'cli') {
    $analyzer = new SEOAnalyzer();
    $url = "https://yamalog.flow-t.net/gear/tiger-bottle/";
    
    try {
        $result = $analyzer->analyzeUrl($url);
        
        echo "\n" . str_repeat("=", 80) . "\n";
        echo "SEO分析結果\n";
        echo str_repeat("=", 80) . "\n";
        echo $result['seo_analysis'] . "\n";
        echo "\n" . str_repeat("=", 80) . "\n";
        
        // 結果をファイルに保存
        $output = "URL: " . $url . "\n";
        $output .= str_repeat("=", 80) . "\n";
        $output .= $result['seo_analysis'] . "\n";
        $output .= "\n" . str_repeat("=", 80) . "\n";
        $output .= "詳細データ: " . json_encode($result['page_info'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        
        file_put_contents('seo_analysis_result.txt', $output);
        echo "分析結果をseo_analysis_result.txtに保存しました\n";
        
    } catch (Exception $e) {
        echo "エラーが発生しました: " . $e->getMessage() . "\n";
    }
}
?>