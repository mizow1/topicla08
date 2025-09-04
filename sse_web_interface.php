<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEO分析ツール（SSE版）</title>
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
        .btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        .progress-container {
            display: none;
            margin-top: 30px;
            padding: 20px;
            background-color: #f0f8ff;
            border-radius: 5px;
            border-left: 4px solid #2196F3;
        }
        .progress-bar {
            width: 100%;
            height: 20px;
            background-color: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }
        .progress-fill {
            height: 100%;
            background-color: #4CAF50;
            transition: width 0.3s ease;
            width: 0%;
        }
        .progress-text {
            text-align: center;
            margin: 10px 0;
            font-weight: bold;
        }
        .result {
            margin-top: 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
            border-left: 4px solid #4CAF50;
            display: none;
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
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 2s linear infinite;
            display: inline-block;
            margin-right: 10px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .log-area {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            font-family: monospace;
            font-size: 14px;
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 SEO分析ツール（SSE版）</h1>
        
        <form id="analysisForm">
            <div class="form-group">
                <label for="url">分析したいWebページのURL:</label>
                <input type="url" id="url" name="url" 
                       value="https://yamalog.flow-t.net/gear/tiger-bottle/" 
                       placeholder="https://example.com" 
                       required>
            </div>
            
            <button type="submit" class="btn" id="analyzeBtn">SEO分析を開始</button>
        </form>

        <div class="progress-container" id="progressContainer">
            <div class="progress-text">
                <div class="spinner"></div>
                <span id="progressMessage">分析中...</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
            <div id="progressPercent" style="text-align: center;">0%</div>
            
            <div class="log-area" id="logArea" style="display: none;">
                <div id="logContent"></div>
            </div>
        </div>

        <div class="result" id="resultContainer">
            <div id="pageInfo"></div>
            <div id="analysisResult"></div>
        </div>
        
        <div style="margin-top: 30px; padding: 15px; background-color: #e8f5e8; border-radius: 5px; font-size: 14px; color: #333;">
            <h4>✅ この版の改善点:</h4>
            <ul>
                <li>Server-Sent Events (SSE) でリアルタイム進捗表示</li>
                <li>共有サーバー環境での制限に対応</li>
                <li>ストリーミング形式で段階的な結果表示</li>
                <li>バックグラウンドプロセス不要で確実な動作</li>
            </ul>
        </div>
    </div>
    
    <script>
        let eventSource = null;

        document.getElementById('analysisForm').addEventListener('submit', function(e) {
            e.preventDefault();
            startAnalysis();
        });

        function startAnalysis() {
            const url = document.getElementById('url').value;
            const analyzeBtn = document.getElementById('analyzeBtn');
            const progressContainer = document.getElementById('progressContainer');
            const resultContainer = document.getElementById('resultContainer');
            const logArea = document.getElementById('logArea');
            const logContent = document.getElementById('logContent');

            // UI初期化
            analyzeBtn.disabled = true;
            analyzeBtn.innerHTML = '🔄 分析中...';
            progressContainer.style.display = 'block';
            resultContainer.style.display = 'none';
            logArea.style.display = 'block';
            logContent.innerHTML = '';
            
            updateProgress(0, '分析を開始しています...');

            // Server-Sent Events用のフォーム送信
            const formData = new FormData();
            formData.append('url', url);

            fetch('sse_processor.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                
                const reader = response.body.getReader();
                const decoder = new TextDecoder();
                
                function readStream() {
                    return reader.read().then(({ done, value }) => {
                        if (done) {
                            resetUI();
                            return;
                        }
                        
                        const chunk = decoder.decode(value);
                        const lines = chunk.split('\n');
                        
                        for (const line of lines) {
                            if (line.trim()) {
                                try {
                                    const data = JSON.parse(line.trim());
                                    handleStreamData(data);
                                } catch (e) {
                                    console.log('Non-JSON line:', line);
                                    logContent.innerHTML += escapeHtml(line) + '<br>';
                                    logArea.scrollTop = logArea.scrollHeight;
                                }
                            }
                        }
                        
                        return readStream();
                    });
                }
                
                return readStream();
            })
            .catch(error => {
                showError('分析エラー: ' + error.message);
                resetUI();
            });
        }

        function handleStreamData(data) {
            const logContent = document.getElementById('logContent');
            
            logContent.innerHTML += `[${new Date().toLocaleTimeString()}] ${data.message}<br>`;
            logContent.parentElement.scrollTop = logContent.parentElement.scrollHeight;

            if (data.status === 'processing') {
                updateProgress(data.progress || 0, data.message);
            } else if (data.status === 'completed') {
                updateProgress(100, '分析完了！');
                if (data.result) {
                    displayResult(data.result);
                }
            } else if (data.status === 'error') {
                showError(data.message);
            }
        }

        function updateProgress(percent, message) {
            const progressFill = document.getElementById('progressFill');
            const progressPercent = document.getElementById('progressPercent');
            const progressMessage = document.getElementById('progressMessage');

            progressFill.style.width = percent + '%';
            progressPercent.textContent = percent + '%';
            progressMessage.textContent = message;
        }

        function displayResult(result) {
            const resultContainer = document.getElementById('resultContainer');
            const pageInfo = document.getElementById('pageInfo');
            const analysisResult = document.getElementById('analysisResult');

            // ページ情報表示
            const pageInfoHtml = `
                <div class="page-info">
                    <h3>📊 ページ基本情報</h3>
                    <div class="info-item"><span class="info-label">URL:</span>${escapeHtml(result.page_info.url)}</div>
                    <div class="info-item"><span class="info-label">タイトル:</span>${escapeHtml(result.page_info.title)}</div>
                    <div class="info-item"><span class="info-label">メタ説明:</span>${escapeHtml(result.page_info.meta_description)}</div>
                    <div class="info-item"><span class="info-label">H1タグ数:</span>${result.page_info.h1_tags.length}</div>
                    <div class="info-item"><span class="info-label">H2タグ数:</span>${result.page_info.h2_tags.length}</div>
                    <div class="info-item"><span class="info-label">画像数:</span>${result.page_info.total_images} (alt属性なし: ${result.page_info.images_without_alt})</div>
                    <div class="info-item"><span class="info-label">内部リンク:</span>${result.page_info.internal_links}</div>
                    <div class="info-item"><span class="info-label">外部リンク:</span>${result.page_info.external_links}</div>
                    <div class="info-item"><span class="info-label">コンテンツ文字数:</span>${result.page_info.content_length.toLocaleString()}文字</div>
                </div>
            `;

            // 分析結果表示
            const analysisHtml = `
                <h3>🎯 SEO分析結果・改善提案</h3>
                <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; white-space: pre-wrap; word-wrap: break-word; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6;">${escapeHtml(result.seo_analysis)}</div>
            `;

            pageInfo.innerHTML = pageInfoHtml;
            analysisResult.innerHTML = analysisHtml;
            resultContainer.style.display = 'block';
        }

        function showError(message) {
            const resultContainer = document.getElementById('resultContainer');
            resultContainer.className = 'result error';
            resultContainer.innerHTML = `<h3>❌ エラーが発生しました</h3><p>${escapeHtml(message)}</p>`;
            resultContainer.style.display = 'block';
        }

        function resetUI() {
            const analyzeBtn = document.getElementById('analyzeBtn');
            analyzeBtn.disabled = false;
            analyzeBtn.innerHTML = 'SEO分析を開始';
            
            setTimeout(() => {
                const progressContainer = document.getElementById('progressContainer');
                const logArea = document.getElementById('logArea');
                logArea.style.display = 'none';
            }, 5000);
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>