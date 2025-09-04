<?php
/**
 * Server-Sent Events (SSE) プロセッサー
 * リアルタイムでSEO分析の進捗を送信
 */

require_once 'config.php';
require_once 'seo_analyzer.php';

// PHP実行時間制限を延長
set_time_limit(600);

// SSEヘッダーを設定
header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

// 出力バッファリングを無効化
if (ob_get_level()) {
    ob_end_clean();
}

$url = $_POST['url'] ?? '';

if (empty($url)) {
    sendEvent(['status' => 'error', 'message' => 'URLが必要です']);
    exit;
}

if (!filter_var($url, FILTER_VALIDATE_URL)) {
    sendEvent(['status' => 'error', 'message' => '有効なURLを入力してください']);
    exit;
}

$job_id = uniqid('seo_', true);

try {
    $analyzer = new SEOAnalyzer();
    
    // 段階1: URL取得
    sendEvent([
        'status' => 'processing',
        'progress' => 10,
        'message' => 'URL取得を開始します...',
        'job_id' => $job_id
    ]);
    
    $html = $analyzer->fetchUrlContent($url);
    
    sendEvent([
        'status' => 'processing',
        'progress' => 30,
        'message' => 'URL取得完了。ページ情報を抽出しています...',
        'job_id' => $job_id
    ]);
    
    // 段階2: ページ情報抽出
    $pageInfo = $analyzer->extractPageInfo($html, $url);
    
    sendEvent([
        'status' => 'processing',
        'progress' => 50,
        'message' => 'ページ情報抽出完了。Claude APIで分析を開始します...',
        'job_id' => $job_id
    ]);
    
    // 段階3: Claude API分析
    $analysis = $analyzer->analyzeWithClaude($pageInfo);
    
    sendEvent([
        'status' => 'processing',
        'progress' => 90,
        'message' => 'Claude API分析完了。結果を保存しています...',
        'job_id' => $job_id
    ]);
    
    // 結果をファイルに保存
    $timestamp = date('Y-m-d_H-i-s');
    $filename = 'seo_analysis_' . $timestamp . '.txt';
    $output = "URL: " . $url . "\n";
    $output .= "分析日時: " . date('Y-m-d H:i:s') . "\n";
    $output .= str_repeat("=", 80) . "\n";
    $output .= $analysis . "\n";
    $output .= "\n" . str_repeat("=", 80) . "\n";
    $output .= "詳細データ: " . json_encode($pageInfo, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
    file_put_contents($filename, $output);
    
    // 完了
    sendEvent([
        'status' => 'completed',
        'progress' => 100,
        'message' => '分析完了！結果を ' . $filename . ' に保存しました',
        'job_id' => $job_id,
        'saved_file' => $filename,
        'result' => [
            'page_info' => $pageInfo,
            'seo_analysis' => $analysis
        ]
    ]);
    
} catch (Exception $e) {
    sendEvent([
        'status' => 'error',
        'progress' => -1,
        'message' => 'エラーが発生しました: ' . $e->getMessage(),
        'job_id' => $job_id
    ]);
}

function sendEvent($data) {
    echo json_encode($data) . "\n";
    flush();
    
    // 小さな遅延を追加（フロントエンドでの処理を確実にするため）
    usleep(100000); // 0.1秒
}
?>