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
    
    public function analyzeWithClaude($pageInfo, $useMockData = false) {
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
あなたはSEOコンサルタントです。以下の対象ページURLを分析してください。
分析結果を「テクニカルSEO」と「コンテンツSEO」の2つのカテゴリに分けて提案してください。

=== ページ情報 ===
URL: " . $pageInfo['url'] . "
タイトル: " . $pageInfo['title'] . "
メタディスクリプション: " . $pageInfo['meta_description'] . "
H1タグ: " . implode(', ', $pageInfo['h1_tags']) . "
H2タグ: " . implode(', ', array_slice($pageInfo['h2_tags'], 0, 5)) . "
H3タグ: " . implode(', ', array_slice($pageInfo['h3_tags'], 0, 5)) . "
画像数: " . $pageInfo['total_images'] . "（alt属性なし: " . $pageInfo['images_without_alt'] . "）
内部リンク数: " . $pageInfo['internal_links'] . "
外部リンク数: " . $pageInfo['external_links'] . "
カノニカルURL: " . $pageInfo['canonical_url'] . "
メタロボッツ: " . $pageInfo['meta_robots'] . "
OGタグ数: " . count($pageInfo['og_tags']) . "
Twitterカード数: " . count($pageInfo['twitter_tags']) . "
コンテンツ文字数: " . $pageInfo['content_length'] . "

=== 出力フォーマット ===
以下の構成で回答してください：

# 🔧 テクニカルSEO改善案

## 1. メタタグ最適化

### 結論
- 具体的な改善案を提示（コピペできる形式で）

### 根拠
- SEO技術的な観点からの根拠

### 補足説明
- 実装方法や注意点

## 2. 構造化データ・タグ改善

### 結論
[同様の構成]

### 根拠
[同様の構成]

### 補足説明
[同様の構成]

## 3. 画像・メディア最適化

### 結論
[同様の構成]

### 根拠
[同様の構成]

### 補足説明
[同様の構成]

## 4. 内部リンク構造最適化

### 結論
[同様の構成]

### 根拠
[同様の構成]

### 補足説明
[同様の構成]

## 5. パフォーマンス・技術改善

### 結論
[同様の構成]

### 根拠
[同様の構成]

### 補足説明
[同様の構成]

---

# ✍️ コンテンツSEO改善案

## 1. タイトル・見出し魅力度向上

### 結論
- ユーザーにより魅力的で興味を引くタイトル案を提示
- クリック率向上を狙った改善案

### 根拠
- ユーザー行動とエンゲージメント向上の観点

### 補足説明
- 実装時の注意点とA/Bテスト提案

## 2. コンテンツの充実・拡張提案

### 結論
- 不足しているコンテンツ要素の特定
- ユーザーのニーズに応える追加コンテンツ案

### 根拠
- 検索意図とユーザー満足度の観点

### 補足説明
- 具体的なコンテンツ制作方針

## 3. ユーザーエクスペリエンス向上

### 結論
- 読みやすさ・使いやすさの改善案
- エンゲージメント向上施策

### 根拠
- ユーザビリティとSEO効果の関係

### 補足説明
- 具体的な改善手順

## 4. 関連キーワード・トピック拡張

### 結論
- 狙うべき関連キーワードの提案
- コンテンツ拡張の方向性

### 根拠
- 検索ボリュームとユーザーニーズ分析

### 補足説明
- キーワード戦略の実装方法

## 5. エンゲージメント・シェアラビリティ向上

### 結論
- SNSシェアやユーザー参加を促す要素の追加提案
- より魅力的なコンテンツにするための施策

### 根拠
- ソーシャルシグナルとSEO効果

### 補足説明
- 具体的な実装アイデア

上記の構成で、テクニカルSEOは技術的な改善点を、コンテンツSEOはユーザーにとってより魅力的で興味を引く内容にする提案を中心に回答してください。";

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
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
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
            throw new Exception('Claude API通信エラー: ' . $curl_error);
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
    
    public function analyzeUrl($url, $useMockData = false) {
        echo "URL取得中: " . $url . "\n";
        $html = $this->fetchUrlContent($url);
        
        echo "ページ情報抽出中...\n";
        $pageInfo = $this->extractPageInfo($html, $url);
        
        if ($useMockData) {
            echo "モックデータでSEO分析中...\n";
        } else {
            echo "Claude APIでSEO分析中...\n";
        }
        $analysis = $this->analyzeWithClaude($pageInfo, $useMockData);
        
        return [
            'page_info' => $pageInfo,
            'seo_analysis' => $analysis
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