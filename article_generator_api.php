<?php
require_once 'config.php';
require_once 'seo_analyzer.php';

header('Content-Type: application/json');

// POSTリクエストのみ受け付け
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'generate_article':
        generateArticle();
        break;
        
    case 'publish_to_wordpress':
        publishToWordPress();
        break;
        
    case 'check_wordpress_connection':
        checkWordPressConnection();
        break;
        
    default:
        echo json_encode(['error' => 'Invalid action']);
}

/**
 * 記事生成機能
 */
function generateArticle() {
    $title = $_POST['title'] ?? '';
    $meta_description = $_POST['meta_description'] ?? '';
    $headings = json_decode($_POST['headings'] ?? '[]', true);
    $relation = $_POST['relation'] ?? '';
    $main_keyword = $_POST['main_keyword'] ?? '';
    $article_type = $_POST['article_type'] ?? 'cluster'; // 'pillar' or 'cluster'
    
    if (empty($title)) {
        echo json_encode(['error' => 'タイトルが必要です']);
        return;
    }
    
    try {
        $analyzer = new SEOAnalyzer();
        
        // 記事生成用のプロンプト
        $prompt = "
あなたはSEOに精通したコンテンツライターです。以下の指示に従って、高品質な記事を作成してください。

【記事タイプ】: " . ($article_type === 'pillar' ? 'ピラーページ（包括的な中核記事）' : 'クラスターページ（特化型記事）') . "
【メインキーワード】: {$main_keyword}
【タイトル】: {$title}
【メタディスクリプション】: {$meta_description}
【見出し構成】: " . json_encode($headings, JSON_UNESCAPED_UNICODE) . "
【ピラーページとの関連性】: {$relation}

以下の要件を満たす記事を作成してください：

1. **文字数**: " . ($article_type === 'pillar' ? '3000-5000文字' : '1500-2500文字') . "
2. **SEO最適化**:
   - メインキーワードを自然に配置（キーワード密度: 1-2%）
   - 関連キーワードとLSIキーワードを適切に使用
   - 見出しタグ（H2, H3）を効果的に活用

3. **コンテンツ構成**:
   - 導入部: 読者の課題や疑問を明確化（200-300文字）
   - 本文: 見出し構成に沿って詳細に解説
   - まとめ: 要点を簡潔にまとめ、行動喚起を含める

4. **ユーザー価値**:
   - 実用的で具体的な情報を提供
   - 図表やリストを活用して読みやすくする
   - 専門用語は分かりやすく説明

5. **内部リンク戦略**:
   - " . ($article_type === 'pillar' ? 'クラスターページへの誘導リンクを含める' : 'ピラーページへの参照リンクを含める') . "
   - 関連記事への自然なリンクを配置

HTML形式で記事を作成し、WordPressエディタで使用可能な形式にしてください。
";
        
        // Claude APIで記事生成
        $data = [
            'model' => 'claude-sonnet-4-20250514',
            'max_tokens' => 8000,
            'temperature' => 0.7,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ]
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.anthropic.com/v1/messages');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 180);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-api-key: ' . CLAUDE_API_KEY,
            'anthropic-version: 2023-06-01'
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($response === false || $http_code !== 200) {
            throw new Exception('記事生成に失敗しました');
        }
        
        $result = json_decode($response, true);
        
        if (!isset($result['content'][0]['text'])) {
            throw new Exception('予期しないレスポンス形式');
        }
        
        $article_content = $result['content'][0]['text'];
        
        // 記事を保存
        $timestamp = date('Y-m-d_H-i-s');
        $filename = "generated_article_{$timestamp}.html";
        file_put_contents($filename, $article_content);
        
        echo json_encode([
            'success' => true,
            'content' => $article_content,
            'saved_file' => $filename,
            'title' => $title,
            'meta_description' => $meta_description
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'error' => $e->getMessage()
        ]);
    }
}

/**
 * WordPress投稿機能
 */
function publishToWordPress() {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $status = $_POST['status'] ?? 'draft'; // 'draft' or 'publish'
    $categories = json_decode($_POST['categories'] ?? '[]', true);
    $tags = json_decode($_POST['tags'] ?? '[]', true);
    $meta_description = $_POST['meta_description'] ?? '';
    
    if (empty($title) || empty($content)) {
        echo json_encode(['error' => 'タイトルとコンテンツが必要です']);
        return;
    }
    
    // WordPress REST API設定確認（動的に読み込み）
    $wpUrl = getConfigValue('WORDPRESS_URL');
    $wpUsername = getConfigValue('WORDPRESS_USERNAME');
    $wpPassword = getConfigValue('WORDPRESS_APP_PASSWORD');
    
    if (empty($wpUrl) || empty($wpUsername) || empty($wpPassword)) {
        echo json_encode(['error' => 'WordPress連携設定が必要です。設定画面で設定してください。']);
        return;
    }
    
    try {
        // WordPress REST APIエンドポイント
        $api_url = rtrim($wpUrl, '/') . '/wp-json/wp/v2/posts';
        
        // 投稿データ
        $post_data = [
            'title' => $title,
            'content' => $content,
            'status' => $status,
            'categories' => $categories,
            'tags' => $tags,
            'meta' => []
        ];
        
        // Yoast SEO対応（メタディスクリプション）
        if (!empty($meta_description)) {
            $post_data['meta']['_yoast_wpseo_metadesc'] = $meta_description;
        }
        
        // Basic認証用のヘッダー
        $headers = [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($wpUsername . ':' . $wpPassword)
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);
        
        if ($response === false) {
            throw new Exception('WordPress APIへの接続に失敗: ' . $curl_error);
        }
        
        $result = json_decode($response, true);
        
        if ($http_code === 201) {
            // 投稿成功
            echo json_encode([
                'success' => true,
                'post_id' => $result['id'],
                'post_url' => $result['link'],
                'message' => 'WordPressに投稿しました'
            ]);
        } else {
            // エラー
            $error_message = isset($result['message']) ? $result['message'] : 'WordPress投稿エラー';
            throw new Exception($error_message);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'error' => $e->getMessage()
        ]);
    }
}

/**
 * WordPress接続チェック
 */
function checkWordPressConnection() {
    // 設定を動的に読み込み
    $wpUrl = getConfigValue('WORDPRESS_URL');
    $wpUsername = getConfigValue('WORDPRESS_USERNAME');
    $wpPassword = getConfigValue('WORDPRESS_APP_PASSWORD');
    
    if (empty($wpUrl) || empty($wpUsername) || empty($wpPassword)) {
        echo json_encode([
            'connected' => false,
            'message' => 'WordPress連携設定が必要です。設定画面で設定してください。'
        ]);
        return;
    }
    
    try {
        $api_url = rtrim($wpUrl, '/') . '/wp-json/wp/v2/users/me';
        
        $headers = [
            'Authorization: Basic ' . base64_encode($wpUsername . ':' . $wpPassword)
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code === 200) {
            $user = json_decode($response, true);
            echo json_encode([
                'connected' => true,
                'message' => 'WordPress接続成功',
                'user' => $user['name'] ?? 'Unknown',
                'site_url' => $wpUrl
            ]);
        } else {
            echo json_encode([
                'connected' => false,
                'message' => 'WordPress認証に失敗しました'
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'connected' => false,
            'message' => $e->getMessage()
        ]);
    }
}
?>