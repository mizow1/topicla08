<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEO分析ツール</title>
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
        .result h2, .result h3, .result h4 {
            color: #2c3e50;
            margin-top: 25px;
            margin-bottom: 15px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
        }
        .result h2 {
            font-size: 1.4em;
        }
        .result h3 {
            font-size: 1.2em;
        }
        .result ul {
            margin: 15px 0;
            padding-left: 25px;
        }
        .result li {
            margin: 8px 0;
            line-height: 1.6;
        }
        .result strong {
            color: #e74c3c;
            font-weight: 600;
        }
        .result code {
            background-color: #ecf0f1;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
        }
        .result p {
            line-height: 1.7;
            margin: 12px 0;
        }
        .result a {
            color: #3498db;
            text-decoration: none;
        }
        .result a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 SEO分析ツール</h1>
        
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
            require_once 'seo_analyzer.php';
            require_once 'markdown_converter.php';
            
            echo '<div class="loading">🔄 分析中... しばらくお待ちください</div>';
            echo '<script>document.querySelector(".btn").disabled = true;</script>';
            
            try {
                $analyzer = new SEOAnalyzer();
                $converter = new MarkdownConverter();
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
                echo $converter->convertToHtml($result['seo_analysis']);
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
        
        <div style="margin-top: 30px; padding: 15px; background-color: #f0f0f0; border-radius: 5px; font-size: 14px; color: #666;">
            <h4>📝 使用方法:</h4>
            <ol>
                <li>分析したいWebページのURLを入力してください</li>
                <li>「SEO分析を開始」ボタンをクリックします</li>
                <li>Claude AIがページを分析し、SEO改善案を提案します</li>
                <li>分析結果は自動的にテキストファイルとして保存されます</li>
            </ol>
            <p><strong>注意:</strong> 分析には数十秒かかる場合があります。</p>
        </div>
    </div>
    
    <script>
        document.getElementById('analysisForm').addEventListener('submit', function() {
            document.querySelector('.btn').innerHTML = '🔄 分析中...';
        });
    </script>
</body>
</html>