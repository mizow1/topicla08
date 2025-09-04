<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEO分析ツール（シンプル版）</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input[type="url"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        input[type="url"]:focus {
            border-color: #4CAF50;
            outline: none;
        }
        .btn {
            background-color: #4CAF50;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        .loading .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 2s linear infinite;
            margin: 0 auto 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .result {
            margin-top: 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
            border-left: 4px solid #4CAF50;
        }
        .error {
            background-color: #ffebee;
            border-left-color: #f44336;
            color: #c62828;
        }
        .page-info {
            background-color: #e3f2fd;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            border-left: 4px solid #2196F3;
        }
        .page-info h3 {
            margin-top: 0;
            color: #1976D2;
        }
        .info-item {
            margin: 5px 0;
            padding: 5px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .info-label {
            font-weight: bold;
            color: #555;
            display: inline-block;
            width: 150px;
        }
        pre {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .result h1 {
            color: #2c3e50;
            margin-top: 30px;
            margin-bottom: 20px;
            border-bottom: 3px solid #e74c3c;
            padding-bottom: 8px;
            font-size: 1.6em;
        }
        .result h2, .result h3, .result h4 {
            color: #2c3e50;
            margin-top: 25px;
            margin-bottom: 15px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
        }
        .tips {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
        .tips h4 {
            color: #856404;
            margin-top: 0;
        }
        .tips ul {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 SEO分析ツール（シンプル版）</h1>
        
        <form method="POST" id="analysisForm">
            <div class="form-group">
                <label for="url">分析したいWebページのURL:</label>
                <input type="url" id="url" name="url" 
                       value="<?php echo isset($_POST['url']) ? htmlspecialchars($_POST['url']) : 'https://yamalog.flow-t.net/gear/tiger-bottle/'; ?>" 
                       placeholder="https://example.com" 
                       required>
            </div>
            
            <button type="submit" class="btn">SEO分析を開始</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['url'])) {
            // PHP実行時間制限を延長
            set_time_limit(600); // 10分
            
            require_once 'seo_analyzer.php';
            
            echo '<div class="loading">';
            echo '<div class="spinner"></div>';
            echo '<h3>🔄 分析中... しばらくお待ちください</h3>';
            echo '<p>この処理には数分かかる場合があります。<br>';
            echo '502エラー対策のため、最大10分まで処理を継続します。</p>';
            echo '</div>';
            echo '<script>document.querySelector(".btn").disabled = true;</script>';
            
            // 出力をフラッシュして即座に表示
            if (ob_get_level()) ob_end_flush();
            flush();
            
            try {
                $analyzer = new SEOAnalyzer();
                $result = $analyzer->analyzeUrl($_POST['url']);
                
                echo '<div class="page-info">';
                echo '<h3>📊 ページ基本情報</h3>';
                echo '<div class="info-item"><span class="info-label">URL:</span>' . htmlspecialchars($result['page_info']['url']) . '</div>';
                echo '<div class="info-item"><span class="info-label">タイトル:</span>' . htmlspecialchars($result['page_info']['title']) . '</div>';
                echo '<div class="info-item"><span class="info-label">メタ説明:</span>' . htmlspecialchars($result['page_info']['meta_description']) . '</div>';
                echo '<div class="info-item"><span class="info-label">H1タグ数:</span>' . count($result['page_info']['h1_tags']) . '</div>';
                echo '<div class="info-item"><span class="info-label">H2タグ数:</span>' . count($result['page_info']['h2_tags']) . '</div>';
                echo '<div class="info-item"><span class="info-label">画像数:</span>' . $result['page_info']['total_images'] . ' (alt属性なし: ' . $result['page_info']['images_without_alt'] . ')</div>';
                echo '<div class="info-item"><span class="info-label">内部リンク:</span>' . $result['page_info']['internal_links'] . '</div>';
                echo '<div class="info-item"><span class="info-label">外部リンク:</span>' . $result['page_info']['external_links'] . '</div>';
                echo '<div class="info-item"><span class="info-label">コンテンツ文字数:</span>' . number_format($result['page_info']['content_length']) . '文字</div>';
                echo '</div>';
                
                echo '<div class="result">';
                echo '<h3>🎯 SEO分析結果・改善提案</h3>';
                echo '<pre>' . htmlspecialchars($result['seo_analysis']) . '</pre>';
                echo '</div>';
                
                // 分析結果をファイルに保存
                $timestamp = date('Y-m-d_H-i-s');
                $filename = 'seo_analysis_' . $timestamp . '.txt';
                $output = "URL: " . $_POST['url'] . "\n";
                $output .= "分析日時: " . date('Y-m-d H:i:s') . "\n";
                $output .= str_repeat("=", 80) . "\n";
                $output .= $result['seo_analysis'] . "\n";
                $output .= "\n" . str_repeat("=", 80) . "\n";
                $output .= "詳細データ: " . json_encode($result['page_info'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                
                file_put_contents($filename, $output);
                
                echo '<div style="margin-top: 20px; padding: 10px; background-color: #e8f5e8; border-radius: 5px;">';
                echo '<p>✅ 分析結果を <strong>' . $filename . '</strong> に保存しました</p>';
                echo '</div>';
                
            } catch (Exception $e) {
                echo '<div class="result error">';
                echo '<h3>❌ エラーが発生しました</h3>';
                echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '</div>';
            }
            
            echo '<script>document.querySelector(".btn").disabled = false;</script>';
        }
        ?>
        
        <div class="tips">
            <h4>📝 この版について:</h4>
            <ul>
                <li>✅ 最もシンプルで確実に動作する従来型の処理方式</li>
                <li>✅ 502エラー対策として実行時間を10分に延長</li>
                <li>✅ 処理中にローディング表示</li>
                <li>✅ 共有サーバー環境で最も安定した動作</li>
                <li>⚠️ 処理中はページを閉じないでください</li>
            </ul>
            <p><strong>他の版:</strong></p>
            <ul>
                <li><a href="sse_web_interface.php">SSE版</a> - リアルタイム進捗表示</li>
                <li><a href="async_web_interface.php">非同期版</a> - バックグラウンド処理</li>
            </ul>
        </div>
    </div>
    
    <script>
        document.getElementById('analysisForm').addEventListener('submit', function() {
            document.querySelector('.btn').innerHTML = '🔄 分析中...';
        });
    </script>
</body>
</html>