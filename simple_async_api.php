<?php
/**
 * シンプルな疑似非同期API
 * 共有サーバー環境での制限に対応
 */

require_once 'config.php';
require_once 'seo_analyzer.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? '';

$job_dir = __DIR__ . '/jobs';
if (!is_dir($job_dir)) {
    mkdir($job_dir, 0755, true);
}

switch ($action) {
    case 'analyze':
        analyzeUrl();
        break;
    
    default:
        echo json_encode(['error' => '無効なアクションです']);
}

function analyzeUrl() {
    global $job_dir;
    
    // PHP実行時間制限を延長
    set_time_limit(600);
    
    $url = $_POST['url'] ?? '';
    
    if (empty($url)) {
        echo json_encode(['error' => 'URLが必要です']);
        return;
    }
    
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        echo json_encode(['error' => '有効なURLを入力してください']);
        return;
    }
    
    $job_id = uniqid('seo_', true);
    
    try {
        // 進捗更新のための出力バッファリングをクリア
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        $analyzer = new SEOAnalyzer();
        
        // 段階的に処理を実行し、進捗を返す
        echo json_encode([
            'status' => 'processing',
            'progress' => 20,
            'message' => 'URL取得中...',
            'job_id' => $job_id
        ]) . "\n";
        flush();
        
        $html = $analyzer->fetchUrlContent($url);
        
        echo json_encode([
            'status' => 'processing',
            'progress' => 40,
            'message' => 'ページ情報抽出中...',
            'job_id' => $job_id
        ]) . "\n";
        flush();
        
        $pageInfo = $analyzer->extractPageInfo($html, $url);
        
        echo json_encode([
            'status' => 'processing',
            'progress' => 60,
            'message' => 'Claude APIで分析中...',
            'job_id' => $job_id
        ]) . "\n";
        flush();
        
        $analysis = $analyzer->analyzeWithClaude($pageInfo);
        
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
        echo json_encode([
            'status' => 'completed',
            'progress' => 100,
            'message' => '分析完了',
            'job_id' => $job_id,
            'saved_file' => $filename,
            'result' => [
                'page_info' => $pageInfo,
                'seo_analysis' => $analysis
            ]
        ]) . "\n";
        
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'progress' => -1,
            'message' => $e->getMessage(),
            'job_id' => $job_id
        ]) . "\n";
    }
}
?>