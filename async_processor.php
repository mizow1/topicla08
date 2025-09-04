<?php
/**
 * 非同期処理用のプロセッサー
 * バックグラウンドでSEO分析を実行し、結果をファイルに保存
 */

require_once 'config.php';
require_once 'seo_analyzer.php';

if (php_sapi_name() !== 'cli') {
    exit('このスクリプトはCLIからのみ実行できます');
}

if ($argc < 3) {
    exit("使用方法: php async_processor.php <URL> <job_id>\n");
}

$url = $argv[1];
$job_id = $argv[2];

// ジョブディレクトリが存在しない場合は作成
$job_dir = __DIR__ . '/jobs';
if (!is_dir($job_dir)) {
    mkdir($job_dir, 0755, true);
}

$job_file = $job_dir . '/' . $job_id . '.json';

try {
    // 開始状態を記録
    file_put_contents($job_file, json_encode([
        'status' => 'processing',
        'url' => $url,
        'start_time' => date('Y-m-d H:i:s'),
        'progress' => 0
    ]));

    $analyzer = new SEOAnalyzer();
    
    // URL取得開始
    file_put_contents($job_file, json_encode([
        'status' => 'processing',
        'url' => $url,
        'start_time' => date('Y-m-d H:i:s'),
        'progress' => 20,
        'message' => 'URL取得中...'
    ]));
    
    $html = $analyzer->fetchUrlContent($url);
    
    // ページ情報抽出
    file_put_contents($job_file, json_encode([
        'status' => 'processing',
        'url' => $url,
        'start_time' => date('Y-m-d H:i:s'),
        'progress' => 40,
        'message' => 'ページ情報抽出中...'
    ]));
    
    $pageInfo = $analyzer->extractPageInfo($html, $url);
    
    // 競合分析
    file_put_contents($job_file, json_encode([
        'status' => 'processing',
        'url' => $url,
        'start_time' => date('Y-m-d H:i:s'),
        'progress' => 50,
        'message' => '競合サイト分析中...'
    ]));
    
    $keywords = array_merge(
        explode(' ', $pageInfo['title']), 
        $pageInfo['h1_tags']
    );
    $competitorUrls = $analyzer->getCompetitorUrls($keywords, $url);
    $competitorData = $analyzer->analyzeCompetitors($pageInfo, $competitorUrls);
    
    // Claude API分析
    file_put_contents($job_file, json_encode([
        'status' => 'processing',
        'url' => $url,
        'start_time' => date('Y-m-d H:i:s'),
        'progress' => 70,
        'message' => 'Claude APIで分析中...'
    ]));
    
    $analysis = $analyzer->analyzeWithClaude($pageInfo, false, $competitorData);
    
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
    file_put_contents($job_file, json_encode([
        'status' => 'completed',
        'url' => $url,
        'start_time' => date('Y-m-d H:i:s'),
        'end_time' => date('Y-m-d H:i:s'),
        'progress' => 100,
        'message' => '分析完了',
        'saved_file' => $filename,
        'result' => [
            'page_info' => $pageInfo,
            'seo_analysis' => $analysis,
            'competitor_data' => $competitorData
        ]
    ]));

} catch (Exception $e) {
    // エラー状態を記録
    file_put_contents($job_file, json_encode([
        'status' => 'error',
        'url' => $url,
        'start_time' => date('Y-m-d H:i:s'),
        'end_time' => date('Y-m-d H:i:s'),
        'progress' => -1,
        'error' => $e->getMessage()
    ]));
}
?>