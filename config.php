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

// Claude API設定
define('CLAUDE_API_KEY', $_ENV['CLAUDE_API_KEY'] ?? '');

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