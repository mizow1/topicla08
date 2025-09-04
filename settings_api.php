<?php
require_once 'config.php';

header('Content-Type: application/json');

// POSTリクエストのみ受け付け
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'save_settings':
        saveSettings();
        break;
        
    case 'load_settings':
        loadSettings();
        break;
        
    case 'test_claude_api':
        testClaudeAPI();
        break;
        
    case 'test_google_api':
        testGoogleAPI();
        break;
        
    case 'test_wordpress_api':
        testWordPressAPI();
        break;
        
    default:
        echo json_encode(['error' => 'Invalid action']);
}

/**
 * 設定保存
 */
function saveSettings() {
    try {
        $settings = [
            'claude_api_key' => $_POST['claude_api_key'] ?? '',
            'google_api_key' => $_POST['google_api_key'] ?? '',
            'google_search_engine_id' => $_POST['google_search_engine_id'] ?? '',
            'wordpress_url' => $_POST['wordpress_url'] ?? '',
            'wordpress_username' => $_POST['wordpress_username'] ?? '',
            'wordpress_app_password' => $_POST['wordpress_app_password'] ?? '',
        ];
        
        // 設定の検証
        foreach ($settings as $key => $value) {
            if (empty($value) && in_array($key, ['claude_api_key'])) {
                throw new Exception("Claude API キーは必須です");
            }
        }
        
        // 暗号化して保存
        $encryptedSettings = encryptSettings($settings);
        $settingsFile = __DIR__ . '/user_settings.json';
        
        if (file_put_contents($settingsFile, json_encode($encryptedSettings, JSON_PRETTY_PRINT)) === false) {
            throw new Exception("設定ファイルの保存に失敗しました");
        }
        
        echo json_encode([
            'success' => true,
            'message' => '設定を保存しました'
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'error' => $e->getMessage()
        ]);
    }
}

/**
 * 設定読み込み
 */
function loadSettings() {
    try {
        $settingsFile = __DIR__ . '/user_settings.json';
        
        if (!file_exists($settingsFile)) {
            echo json_encode([
                'success' => true,
                'settings' => []
            ]);
            return;
        }
        
        $encryptedSettings = json_decode(file_get_contents($settingsFile), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("設定ファイルの読み込みに失敗しました");
        }
        
        $settings = decryptSettings($encryptedSettings);
        
        // パスワードは表示しない（マスク）
        $safeSettings = $settings;
        if (!empty($safeSettings['claude_api_key'])) {
            $safeSettings['claude_api_key'] = maskApiKey($safeSettings['claude_api_key']);
        }
        if (!empty($safeSettings['google_api_key'])) {
            $safeSettings['google_api_key'] = maskApiKey($safeSettings['google_api_key']);
        }
        if (!empty($safeSettings['wordpress_app_password'])) {
            $safeSettings['wordpress_app_password'] = str_repeat('*', 20);
        }
        
        echo json_encode([
            'success' => true,
            'settings' => $safeSettings
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'error' => $e->getMessage()
        ]);
    }
}

/**
 * Claude API テスト
 */
function testClaudeAPI() {
    $apiKey = $_POST['api_key'] ?? '';
    
    if (empty($apiKey)) {
        echo json_encode(['error' => 'APIキーが入力されていません']);
        return;
    }
    
    try {
        $data = [
            'model' => 'claude-sonnet-4-20250514',
            'max_tokens' => 100,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'Hello, this is a test message.'
                ]
            ]
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.anthropic.com/v1/messages');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-api-key: ' . $apiKey,
            'anthropic-version: 2023-06-01'
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code === 200) {
            echo json_encode([
                'success' => true,
                'message' => 'Claude API接続成功'
            ]);
        } else {
            $result = json_decode($response, true);
            $error_message = isset($result['error']['message']) ? $result['error']['message'] : 'API接続エラー';
            throw new Exception($error_message);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'error' => $e->getMessage()
        ]);
    }
}

/**
 * Google API テスト
 */
function testGoogleAPI() {
    $apiKey = $_POST['api_key'] ?? '';
    $searchEngineId = $_POST['search_engine_id'] ?? '';
    
    if (empty($apiKey) || empty($searchEngineId)) {
        echo json_encode(['error' => 'APIキーと検索エンジンIDが必要です']);
        return;
    }
    
    try {
        $apiUrl = "https://www.googleapis.com/customsearch/v1?" . http_build_query([
            'key' => $apiKey,
            'cx' => $searchEngineId,
            'q' => 'test query',
            'num' => 1
        ]);
        
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 10
            ]
        ]);
        
        $response = file_get_contents($apiUrl, false, $context);
        
        if ($response === false) {
            throw new Exception('Google API接続に失敗しました');
        }
        
        $result = json_decode($response, true);
        
        if (isset($result['error'])) {
            throw new Exception($result['error']['message']);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Google Custom Search API接続成功'
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'error' => $e->getMessage()
        ]);
    }
}

/**
 * WordPress API テスト
 */
function testWordPressAPI() {
    $url = $_POST['url'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($url) || empty($username) || empty($password)) {
        echo json_encode(['error' => '全ての項目が必要です']);
        return;
    }
    
    try {
        $api_url = rtrim($url, '/') . '/wp-json/wp/v2/users/me';
        
        $headers = [
            'Authorization: Basic ' . base64_encode($username . ':' . $password)
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
                'success' => true,
                'message' => 'WordPress接続成功',
                'user' => $user['name'] ?? 'Unknown'
            ]);
        } else {
            throw new Exception('WordPress認証に失敗しました');
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'error' => $e->getMessage()
        ]);
    }
}

/**
 * 設定を暗号化
 */
function encryptSettings($settings) {
    $key = getEncryptionKey();
    $encrypted = [];
    
    foreach ($settings as $k => $v) {
        if (!empty($v)) {
            $encrypted[$k] = base64_encode(openssl_encrypt($v, 'AES-256-CBC', $key, 0, substr($key, 0, 16)));
        } else {
            $encrypted[$k] = '';
        }
    }
    
    return $encrypted;
}

/**
 * 設定を復号化
 */
function decryptSettings($encryptedSettings) {
    $key = getEncryptionKey();
    $decrypted = [];
    
    foreach ($encryptedSettings as $k => $v) {
        if (!empty($v)) {
            $decrypted[$k] = openssl_decrypt(base64_decode($v), 'AES-256-CBC', $key, 0, substr($key, 0, 16));
        } else {
            $decrypted[$k] = '';
        }
    }
    
    return $decrypted;
}

/**
 * 暗号化キーを取得
 */
function getEncryptionKey() {
    // サーバー固有の情報を使用してキーを生成
    $serverInfo = $_SERVER['SERVER_NAME'] ?? 'localhost';
    $serverInfo .= $_SERVER['DOCUMENT_ROOT'] ?? __DIR__;
    
    return hash('sha256', $serverInfo . 'SEO_ANALYZER_SECRET_KEY');
}

/**
 * APIキーをマスクする
 */
function maskApiKey($apiKey) {
    $length = strlen($apiKey);
    if ($length <= 8) {
        return str_repeat('*', $length);
    }
    
    return substr($apiKey, 0, 4) . str_repeat('*', $length - 8) . substr($apiKey, -4);
}

/**
 * 実際の設定値を取得する関数（他のファイルから使用）
 */
function getUserSettings() {
    try {
        $settingsFile = __DIR__ . '/user_settings.json';
        
        if (!file_exists($settingsFile)) {
            return [];
        }
        
        $encryptedSettings = json_decode(file_get_contents($settingsFile), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [];
        }
        
        return decryptSettings($encryptedSettings);
        
    } catch (Exception $e) {
        return [];
    }
}
?>