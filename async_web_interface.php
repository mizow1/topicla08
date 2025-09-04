<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEO分析ツール（非同期版）</title>
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
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 SEO分析ツール（非同期版）</h1>
        
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
        </div>

        <div class="result" id="resultContainer">
            <div id="pageInfo"></div>
            <div id="analysisResult"></div>
        </div>
        
        <!-- ナビゲーションリンク -->
        <div style="text-align: center; margin-top: 20px; margin-bottom: 20px;">
            <a href="enhanced_seo_interface.php" style="margin: 0 15px; color: #2196F3; text-decoration: none; background-color: #e3f2fd; padding: 8px 16px; border-radius: 5px;">🚀 強化版SEO分析</a>
            <a href="article_generator_interface.php" style="margin: 0 15px; color: #2196F3; text-decoration: none; background-color: #e3f2fd; padding: 8px 16px; border-radius: 5px;">✍️ 記事生成</a>
            <a href="settings.php" style="margin: 0 15px; color: #2196F3; text-decoration: none; background-color: #e3f2fd; padding: 8px 16px; border-radius: 5px;">⚙️ API設定</a>
        </div>
        
        <div style="margin-top: 30px; padding: 15px; background-color: #f0f0f0; border-radius: 5px; font-size: 14px; color: #666;">
            <h4>📝 改善点:</h4>
            <ul>
                <li>✅ 502エラーを防ぐため非同期処理に変更</li>
                <li>✅ リアルタイムで進捗状況を表示</li>
                <li>✅ 長時間の分析でもブラウザがタイムアウトしない</li>
                <li>✅ プロセスが途中で止まっても状況を確認可能</li>
            </ul>
        </div>
    </div>
    
    <script>
        let currentJobId = null;
        let statusInterval = null;

        document.getElementById('analysisForm').addEventListener('submit', function(e) {
            e.preventDefault();
            startAnalysis();
        });

        async function startAnalysis() {
            const url = document.getElementById('url').value;
            const analyzeBtn = document.getElementById('analyzeBtn');
            const progressContainer = document.getElementById('progressContainer');
            const resultContainer = document.getElementById('resultContainer');

            // UI初期化
            analyzeBtn.disabled = true;
            analyzeBtn.innerHTML = '🔄 ジョブ開始中...';
            progressContainer.style.display = 'block';
            resultContainer.style.display = 'none';
            
            updateProgress(0, '分析ジョブを開始中...');

            try {
                // ジョブ開始
                const response = await fetch('job_api.php?action=start', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `url=${encodeURIComponent(url)}`
                });

                const result = await response.json();
                
                if (!result.success) {
                    throw new Error(result.error || '不明なエラーが発生しました');
                }

                currentJobId = result.job_id;
                analyzeBtn.innerHTML = '🔄 分析中...';
                
                // ステータス監視開始
                statusInterval = setInterval(checkJobStatus, 2000);
                
            } catch (error) {
                showError('ジョブの開始に失敗しました: ' + error.message);
                resetUI();
            }
        }

        async function checkJobStatus() {
            if (!currentJobId) return;

            try {
                const response = await fetch(`job_api.php?action=status&job_id=${currentJobId}`);
                const status = await response.json();

                if (status.error) {
                    throw new Error(status.error);
                }

                updateProgress(status.progress || 0, status.message || '処理中...');

                if (status.status === 'completed') {
                    clearInterval(statusInterval);
                    await getJobResult();
                } else if (status.status === 'error') {
                    clearInterval(statusInterval);
                    throw new Error(status.error || '分析中にエラーが発生しました');
                }

            } catch (error) {
                clearInterval(statusInterval);
                showError('ステータス確認エラー: ' + error.message);
                resetUI();
            }
        }

        async function getJobResult() {
            try {
                const response = await fetch(`job_api.php?action=result&job_id=${currentJobId}`);
                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.error || '結果の取得に失敗しました');
                }

                displayResult(data.result);
                updateProgress(100, '分析完了！');
                resetUI();

            } catch (error) {
                showError('結果取得エラー: ' + error.message);
                resetUI();
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

            // 分析結果表示（マークダウン形式で保存、シンプルな表示）
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
            
            const progressContainer = document.getElementById('progressContainer');
            setTimeout(() => {
                progressContainer.style.display = 'none';
            }, 3000);
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>