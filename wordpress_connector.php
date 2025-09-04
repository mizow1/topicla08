<?php
require_once 'config.php';

class WordPressConnector {
    private $site_url;
    private $username;
    private $password;
    private $auth_header;
    
    public function __construct($site_url, $username, $password) {
        $this->site_url = rtrim($site_url, '/');
        $this->username = $username;
        $this->password = $password;
        $this->auth_header = 'Basic ' . base64_encode($username . ':' . $password);
    }
    
    /**
     * WordPressサイトの基本情報を取得
     */
    public function getSiteInfo() {
        $endpoint = '/wp-json';
        
        $response = $this->makeRequest('GET', $endpoint);
        
        if ($response['success']) {
            return [
                'success' => true,
                'info' => $response['data']
            ];
        }
        
        return $response;
    }
    
    /**
     * 投稿一覧を取得
     */
    public function getPosts($per_page = 10, $page = 1, $search = '') {
        $params = [
            'per_page' => $per_page,
            'page' => $page,
            '_embed' => '1'
        ];
        
        if (!empty($search)) {
            $params['search'] = $search;
        }
        
        $endpoint = '/wp-json/wp/v2/posts?' . http_build_query($params);
        
        return $this->makeRequest('GET', $endpoint);
    }
    
    /**
     * 特定の投稿を取得
     */
    public function getPost($post_id) {
        $endpoint = '/wp-json/wp/v2/posts/' . $post_id . '?_embed=1';
        
        return $this->makeRequest('GET', $endpoint);
    }
    
    /**
     * 投稿を更新
     */
    public function updatePost($post_id, $data) {
        $endpoint = '/wp-json/wp/v2/posts/' . $post_id;
        
        return $this->makeRequest('POST', $endpoint, $data);
    }
    
    /**
     * ページ一覧を取得
     */
    public function getPages($per_page = 10, $page = 1, $search = '') {
        $params = [
            'per_page' => $per_page,
            'page' => $page,
            '_embed' => '1'
        ];
        
        if (!empty($search)) {
            $params['search'] = $search;
        }
        
        $endpoint = '/wp-json/wp/v2/pages?' . http_build_query($params);
        
        return $this->makeRequest('GET', $endpoint);
    }
    
    /**
     * 特定のページを取得
     */
    public function getPage($page_id) {
        $endpoint = '/wp-json/wp/v2/pages/' . $page_id . '?_embed=1';
        
        return $this->makeRequest('GET', $endpoint);
    }
    
    /**
     * ページを更新
     */
    public function updatePage($page_id, $data) {
        $endpoint = '/wp-json/wp/v2/pages/' . $page_id;
        
        return $this->makeRequest('POST', $endpoint, $data);
    }
    
    /**
     * URLから投稿/ページを検索
     */
    public function findPostByUrl($target_url) {
        // URLから投稿スラッグを抽出
        $parsed_url = parse_url($target_url);
        $path = trim($parsed_url['path'], '/');
        $segments = explode('/', $path);
        
        // 最後のセグメントをスラッグとして使用
        $slug = end($segments);
        
        if (empty($slug)) {
            return ['success' => false, 'error' => 'URLからスラッグを抽出できません'];
        }
        
        // 投稿を検索
        $posts_result = $this->makeRequest('GET', '/wp-json/wp/v2/posts?slug=' . urlencode($slug));
        if ($posts_result['success'] && !empty($posts_result['data'])) {
            return [
                'success' => true,
                'type' => 'post',
                'data' => $posts_result['data'][0]
            ];
        }
        
        // ページを検索
        $pages_result = $this->makeRequest('GET', '/wp-json/wp/v2/pages?slug=' . urlencode($slug));
        if ($pages_result['success'] && !empty($pages_result['data'])) {
            return [
                'success' => true,
                'type' => 'page',
                'data' => $pages_result['data'][0]
            ];
        }
        
        return ['success' => false, 'error' => '指定されたURLの投稿/ページが見つかりません'];
    }
    
    /**
     * SEO改善を適用
     */
    public function applySeoImprovement($target_url, $improvement_type, $improved_value, $original_value = '') {
        // まずURLから投稿/ページを特定
        $find_result = $this->findPostByUrl($target_url);
        
        if (!$find_result['success']) {
            return $find_result;
        }
        
        $content_type = $find_result['type'];
        $content_data = $find_result['data'];
        $content_id = $content_data['id'];
        
        // 改善タイプに応じてデータを準備
        $update_data = $this->prepareImprovementData($improvement_type, $improved_value, $content_data);
        
        if (!$update_data) {
            return ['success' => false, 'error' => 'サポートされていない改善タイプ: ' . $improvement_type];
        }
        
        // 更新を実行
        if ($content_type === 'post') {
            $result = $this->updatePost($content_id, $update_data);
        } else {
            $result = $this->updatePage($content_id, $update_data);
        }
        
        if ($result['success']) {
            return [
                'success' => true,
                'message' => $improvement_type . ' を更新しました',
                'content_type' => $content_type,
                'content_id' => $content_id,
                'updated_data' => $update_data
            ];
        }
        
        return $result;
    }
    
    /**
     * 改善データを準備
     */
    private function prepareImprovementData($improvement_type, $improved_value, $existing_data) {
        switch ($improvement_type) {
            case 'title':
                return [
                    'title' => $improved_value
                ];
                
            case 'meta_description':
                // Yoast SEOメタデータを更新
                $meta = $existing_data['meta'] ?? [];
                $meta['_yoast_wpseo_metadesc'] = $improved_value;
                return [
                    'meta' => $meta
                ];
                
            case 'content':
                return [
                    'content' => $improved_value
                ];
                
            case 'excerpt':
                return [
                    'excerpt' => $improved_value
                ];
                
            case 'featured_image_alt':
                // アイキャッチ画像のalt属性を更新
                if (isset($existing_data['featured_media']) && $existing_data['featured_media'] > 0) {
                    $media_result = $this->updateMediaAlt($existing_data['featured_media'], $improved_value);
                    return $media_result ? [] : null; // 空の配列を返して他の更新は行わない
                }
                return null;
                
            default:
                return null;
        }
    }
    
    /**
     * メディアのalt属性を更新
     */
    public function updateMediaAlt($media_id, $alt_text) {
        $endpoint = '/wp-json/wp/v2/media/' . $media_id;
        
        $data = [
            'alt_text' => $alt_text
        ];
        
        $result = $this->makeRequest('POST', $endpoint, $data);
        return $result['success'];
    }
    
    /**
     * カテゴリ一覧を取得
     */
    public function getCategories() {
        $endpoint = '/wp-json/wp/v2/categories?per_page=100';
        
        return $this->makeRequest('GET', $endpoint);
    }
    
    /**
     * タグ一覧を取得
     */
    public function getTags() {
        $endpoint = '/wp-json/wp/v2/tags?per_page=100';
        
        return $this->makeRequest('GET', $endpoint);
    }
    
    /**
     * ユーザー情報を取得
     */
    public function getCurrentUser() {
        $endpoint = '/wp-json/wp/v2/users/me';
        
        return $this->makeRequest('GET', $endpoint);
    }
    
    /**
     * プラグイン情報を取得（可能な場合）
     */
    public function getPlugins() {
        // WordPress標準ではプラグイン情報の取得APIはないため、
        // カスタムエンドポイントが必要
        $endpoint = '/wp-json/custom/v1/plugins';
        
        return $this->makeRequest('GET', $endpoint);
    }
    
    /**
     * HTTP リクエストを実行
     */
    private function makeRequest($method, $endpoint, $data = null) {
        $url = $this->site_url . $endpoint;
        
        $headers = [
            'Authorization: ' . $this->auth_header,
            'Content-Type: application/json'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        
        if ($method === 'POST' && $data) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);
        
        if ($response === false) {
            return [
                'success' => false,
                'error' => 'cURL エラー: ' . $curl_error
            ];
        }
        
        $decoded_response = json_decode($response, true);
        
        if ($http_code >= 200 && $http_code < 300) {
            return [
                'success' => true,
                'data' => $decoded_response,
                'http_code' => $http_code
            ];
        } else {
            $error_message = 'HTTP エラー: ' . $http_code;
            
            if ($decoded_response && isset($decoded_response['message'])) {
                $error_message .= ' - ' . $decoded_response['message'];
            } elseif ($decoded_response && isset($decoded_response['code'])) {
                $error_message .= ' - ' . $decoded_response['code'];
            }
            
            return [
                'success' => false,
                'error' => $error_message,
                'http_code' => $http_code,
                'response' => $decoded_response
            ];
        }
    }
    
    /**
     * 接続テスト
     */
    public function testConnection() {
        $result = $this->getCurrentUser();
        
        if ($result['success']) {
            return [
                'success' => true,
                'message' => 'WordPress接続成功',
                'user' => $result['data']['name'] ?? 'Unknown User',
                'user_data' => $result['data']
            ];
        }
        
        return $result;
    }
    
    /**
     * サイトの統計情報を取得
     */
    public function getSiteStats() {
        $stats = [
            'posts_count' => 0,
            'pages_count' => 0,
            'categories_count' => 0,
            'tags_count' => 0
        ];
        
        // 投稿数を取得
        $posts_result = $this->makeRequest('GET', '/wp-json/wp/v2/posts?per_page=1');
        if ($posts_result['success'] && isset($posts_result['http_code'])) {
            $headers = get_headers($this->site_url . '/wp-json/wp/v2/posts?per_page=1', 1);
            if (isset($headers['X-WP-Total'])) {
                $stats['posts_count'] = intval($headers['X-WP-Total']);
            }
        }
        
        // ページ数を取得
        $pages_result = $this->makeRequest('GET', '/wp-json/wp/v2/pages?per_page=1');
        if ($pages_result['success']) {
            // 同様にヘッダーから取得（簡略化）
        }
        
        return [
            'success' => true,
            'stats' => $stats
        ];
    }
}

/**
 * WordPressコネクターのファクトリー関数
 */
function createWordPressConnector($site_id) {
    try {
        // データベースからサイト情報を取得
        $dbFile = __DIR__ . '/wordpress_sites.db';
        $pdo = new PDO('sqlite:' . $dbFile);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->prepare("SELECT url, username, app_password FROM wordpress_sites WHERE id = ?");
        $stmt->execute([$site_id]);
        $site = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$site) {
            throw new Exception('指定されたサイトが見つかりません');
        }
        
        // パスワードを復号化
        $serverInfo = $_SERVER['SERVER_NAME'] ?? 'localhost';
        $serverInfo .= $_SERVER['DOCUMENT_ROOT'] ?? __DIR__;
        $key = hash('sha256', $serverInfo . 'WORDPRESS_SITES_SECRET_KEY');
        
        $password = openssl_decrypt(base64_decode($site['app_password']), 'AES-256-CBC', $key, 0, substr($key, 0, 16));
        
        return new WordPressConnector($site['url'], $site['username'], $password);
        
    } catch (Exception $e) {
        throw new Exception('WordPressコネクターの作成に失敗: ' . $e->getMessage());
    }
}
?>