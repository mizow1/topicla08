<?php
/**
 * ジョブ管理API
 * 非同期SEO分析のジョブを開始・監視・結果取得
 */

require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? '';
$job_id = $_GET['job_id'] ?? '';

$job_dir = __DIR__ . '/jobs';
if (!is_dir($job_dir)) {
    mkdir($job_dir, 0755, true);
}

switch ($action) {
    case 'start':
        startJob();
        break;
    
    case 'status':
        if (empty($job_id)) {
            echo json_encode(['error' => 'job_idが必要です']);
            break;
        }
        getJobStatus($job_id);
        break;
    
    case 'result':
        if (empty($job_id)) {
            echo json_encode(['error' => 'job_idが必要です']);
            break;
        }
        getJobResult($job_id);
        break;
    
    default:
        echo json_encode(['error' => '無効なアクションです']);
}

function startJob() {
    global $job_dir;
    
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
    $job_file = $job_dir . '/' . $job_id . '.json';
    
    // 初期ジョブファイルを作成
    $initial_data = [
        'status' => 'starting',
        'url' => $url,
        'start_time' => date('Y-m-d H:i:s'),
        'progress' => 0,
        'message' => 'ジョブを開始しています...'
    ];
    
    $write_result = file_put_contents($job_file, json_encode($initial_data));
    if ($write_result === false) {
        echo json_encode(['error' => 'ジョブファイルの作成に失敗しました: ' . $job_file]);
        return;
    }
    
    // PHPの実行可能パスを取得
    $php_path = PHP_BINARY;
    
    // バックグラウンドでプロセスを開始
    $processor_path = __DIR__ . '/async_processor.php';
    
    // Linuxの場合
    $command = sprintf(
        '%s %s %s %s > /dev/null 2>&1 &',
        escapeshellarg($php_path),
        escapeshellarg($processor_path),
        escapeshellarg($url),
        escapeshellarg($job_id)
    );
    
    // Windows環境の場合
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $command = sprintf(
            'start /b %s %s %s %s',
            escapeshellarg($php_path),
            escapeshellarg($processor_path),
            escapeshellarg($url),
            escapeshellarg($job_id)
        );
    }
    
    // エラーログに記録
    error_log("Starting background job: " . $command);
    
    $output = [];
    $return_var = 0;
    exec($command, $output, $return_var);
    
    // デバッグ情報
    error_log("Job start result: return_var={$return_var}, job_file={$job_file}");
    
    echo json_encode([
        'success' => true,
        'job_id' => $job_id,
        'message' => 'ジョブを開始しました',
        'debug' => [
            'job_file' => $job_file,
            'command' => $command,
            'return_var' => $return_var,
            'php_path' => $php_path,
            'file_exists' => file_exists($job_file)
        ]
    ]);
}

function getJobStatus($job_id) {
    global $job_dir;
    $job_file = $job_dir . '/' . $job_id . '.json';
    
    if (!file_exists($job_file)) {
        echo json_encode(['error' => 'ジョブが見つかりません']);
        return;
    }
    
    $job_data = json_decode(file_get_contents($job_file), true);
    if (!$job_data) {
        echo json_encode(['error' => 'ジョブデータの読み込みに失敗しました']);
        return;
    }
    
    // 結果データは除外（軽量化）
    unset($job_data['result']);
    
    echo json_encode($job_data);
}

function getJobResult($job_id) {
    global $job_dir;
    $job_file = $job_dir . '/' . $job_id . '.json';
    
    if (!file_exists($job_file)) {
        echo json_encode(['error' => 'ジョブが見つかりません']);
        return;
    }
    
    $job_data = json_decode(file_get_contents($job_file), true);
    if (!$job_data) {
        echo json_encode(['error' => 'ジョブデータの読み込みに失敗しました']);
        return;
    }
    
    if ($job_data['status'] !== 'completed') {
        echo json_encode(['error' => 'ジョブがまだ完了していません', 'status' => $job_data['status']]);
        return;
    }
    
    echo json_encode([
        'success' => true,
        'result' => $job_data['result']
    ]);
}
?>