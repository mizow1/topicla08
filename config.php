<?php
// .envファイルを読み込む
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($name, $value) = explode('=', $line, 2);
            $_ENV[trim($name)] = trim($value);
        }
    }
}

// ユーザー設定を読み込む関数を定義
function loadUserSettings() {
    static $settings = null;
    
    if ($settings === null) {
        // まず .env から読み込み
        $settings = [
            'CLAUDE_API_KEY' => $_ENV['CLAUDE_API_KEY'] ?? '',
            'GOOGLE_API_KEY' => $_ENV['GOOGLE_API_KEY'] ?? '',
            'GOOGLE_SEARCH_ENGINE_ID' => $_ENV['GOOGLE_SEARCH_ENGINE_ID'] ?? '',
            'WORDPRESS_URL' => $_ENV['WORDPRESS_URL'] ?? '',
            'WORDPRESS_USERNAME' => $_ENV['WORDPRESS_USERNAME'] ?? '',
            'WORDPRESS_APP_PASSWORD' => $_ENV['WORDPRESS_APP_PASSWORD'] ?? '',
        ];
        
        // ユーザー設定ファイルから読み込み（設定管理画面で設定された値を優先）
        $userSettingsFile = __DIR__ . '/user_settings.json';
        if (file_exists($userSettingsFile)) {
            try {
                $userSettings = getUserSettingsDecrypted();
                
                // ユーザー設定で上書き（空でない場合のみ）
                if (!empty($userSettings['claude_api_key'])) {
                    $settings['CLAUDE_API_KEY'] = $userSettings['claude_api_key'];
                }
                if (!empty($userSettings['google_api_key'])) {
                    $settings['GOOGLE_API_KEY'] = $userSettings['google_api_key'];
                }
                if (!empty($userSettings['google_search_engine_id'])) {
                    $settings['GOOGLE_SEARCH_ENGINE_ID'] = $userSettings['google_search_engine_id'];
                }
                if (!empty($userSettings['wordpress_url'])) {
                    $settings['WORDPRESS_URL'] = $userSettings['wordpress_url'];
                }
                if (!empty($userSettings['wordpress_username'])) {
                    $settings['WORDPRESS_USERNAME'] = $userSettings['wordpress_username'];
                }
                if (!empty($userSettings['wordpress_app_password'])) {
                    $settings['WORDPRESS_APP_PASSWORD'] = $userSettings['wordpress_app_password'];
                }
            } catch (Exception $e) {
                // エラーが発生した場合は .env の値を使用
                error_log('ユーザー設定読み込みエラー: ' . $e->getMessage());
            }
        }
    }
    
    return $settings;
}

// ユーザー設定を復号化して取得する関数
function getUserSettingsDecrypted() {
    $settingsFile = __DIR__ . '/user_settings.json';
    
    if (!file_exists($settingsFile)) {
        return [];
    }
    
    $encryptedSettings = json_decode(file_get_contents($settingsFile), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('設定ファイルの読み込みに失敗しました');
    }
    
    // 暗号化キーを取得
    $serverInfo = $_SERVER['SERVER_NAME'] ?? 'localhost';
    $serverInfo .= $_SERVER['DOCUMENT_ROOT'] ?? __DIR__;
    $key = hash('sha256', $serverInfo . 'SEO_ANALYZER_SECRET_KEY');
    
    // 復号化
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

// 設定値を取得する関数
function getConfigValue($key) {
    $settings = loadUserSettings();
    return $settings[$key] ?? '';
}

// 後方互換性のため定数として定義
$userSettings = loadUserSettings();
define('CLAUDE_API_KEY', $userSettings['CLAUDE_API_KEY']);
define('GOOGLE_API_KEY', $userSettings['GOOGLE_API_KEY']);
define('GOOGLE_SEARCH_ENGINE_ID', $userSettings['GOOGLE_SEARCH_ENGINE_ID']);
define('WORDPRESS_URL', $userSettings['WORDPRESS_URL']);
define('WORDPRESS_USERNAME', $userSettings['WORDPRESS_USERNAME']);
define('WORDPRESS_APP_PASSWORD', $userSettings['WORDPRESS_APP_PASSWORD']);

// エラー設定（XServer環境での動作を考慮）
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

// メモリ制限を増やす（大きなWebページ解析用）
ini_set('memory_limit', '256M');

// SSL証明書検証を無効化（必要に応じて）
// ini_set('openssl.cafile', '');
?>