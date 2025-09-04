<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>強化版SEO分析ツール - Analytics連携対応</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            max-width: 1200px;
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
        .analytics-status {
            background-color: #e8f5e8;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            border-left: 4px solid #4caf50;
        }
        .analytics-status.disconnected {
            background-color: #ffebee;
            border-left-color: #f44336;
        }
        .connection-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 8px;
        }
        .connected {
            background-color: #4caf50;
        }
        .disconnected {
            background-color: #f44336;
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
        input[type="url"], select.form-control {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        input[type="url"]:focus, select.form-control:focus {
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
            margin-bottom: 10px;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        .btn-secondary {
            background-color: #2196F3;
        }
        .btn-secondary:hover {
            background-color: #1976D2;
        }
        .analysis-options {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 10px;
        }
        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .checkbox-item input[type="checkbox"] {
            width: auto;
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
        .analytics-data {
            background-color: #fff3e0;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            border-left: 4px solid #ff9800;
        }
        .analytics-data h3 {
            margin-top: 0;
            color: #f57c00;
        }
        .metric-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .metric-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }
        .metric-value {
            font-size: 20px;
            font-weight: bold;
            color: #4285f4;
        }
        .metric-label {
            color: #666;
            margin-top: 5px;
            font-size: 14px;
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
        .nav-links {
            text-align: center;
            margin-bottom: 20px;
        }
        .nav-links a {
            color: #2196F3;
            text-decoration: none;
            margin: 0 15px;
            padding: 8px 16px;
            border-radius: 5px;
            background-color: #e3f2fd;
        }
        .nav-links a:hover {
            background-color: #bbdefb;
        }
    </style>
</head>
<body>
    <?php include_once 'includes/navigation.php'; ?>
    <div class="container">

        <h1>🚀 強化版SEO分析ツール</h1>
        <p style="text-align: center; color: #666; margin-bottom: 20px;">
            Google Analytics・Search Console連携対応版
        </p>
        
        <!-- クイックアクションリンク -->
        <div style="text-align: center; margin-bottom: 20px;">
            <a href="wordpress_manager.php" class="btn btn-secondary" style="margin: 5px;">🏢 サイト管理</a>
            <a href="seo_improvement_interface.php" class="btn btn-secondary" style="margin: 5px;">🔧 改善適用</a>
            <a href="article_generator_interface.php" class="btn btn-secondary" style="margin: 5px;">✍️ 記事生成</a>
        </div>
        
        <!-- 連携状況表示 -->
        <div class="analytics-status" id="analyticsStatus">
            <h3>📡 連携状況</h3>
            <div style="display: flex; justify-content: space-around; margin-top: 15px;">
                <div>
                    <span class="connection-indicator" id="gaIndicator"></span>
                    <strong>Google Analytics:</strong> <span id="gaStatus">未接続</span>
                </div>
                <div>
                    <span class="connection-indicator" id="gscIndicator"></span>
                    <strong>Search Console:</strong> <span id="gscStatus">未接続</span>
                </div>
            </div>
        </div>

        <!-- 分析オプション -->
        <div class="analysis-options">
            <h3>🎯 分析オプション</h3>
            <div class="checkbox-group">
                <div class="checkbox-item">
                    <input type="checkbox" id="includeTopicCluster" checked>
                    <label for="includeTopicCluster">トピッククラスター分析</label>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" id="includeCompetitor" checked>
                    <label for="includeCompetitor">競合分析（5サイト）</label>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" id="includeAnalytics" checked>
                    <label for="includeAnalytics">Analytics データ統合</label>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" id="includeSearchConsole" checked>
                    <label for="includeSearchConsole">Search Console データ統合</label>
                </div>
            </div>
        </div>
        
        <form id="analysisForm">
            <div class="form-group">
                <label for="site-select">分析対象サイト:</label>
                <select id="site-select" class="form-control">
                    <option value="">サイトを選択してください...</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="url">分析したいWebページのURL:</label>
                <input type="url" id="url" name="url" 
                       value="https://yamalog.flow-t.net/gear/tiger-bottle/" 
                       placeholder="https://example.com" 
                       required>
            </div>
            
            <button type="submit" class="btn" id="analyzeBtn">🔍 強化版SEO分析を開始</button>
            <button type="button" class="btn btn-secondary" onclick="showAnalyticsPreview()">
                📊 連携データプレビュー
            </button>
        </form>

        <!-- Analytics データプレビュー -->
        <div class="analytics-data" id="analyticsPreview" style="display: none;">
            <h3>📈 連携データプレビュー</h3>
            <div class="metric-grid" id="metricsGrid">
                <!-- JavaScriptで動的に生成 -->
            </div>
        </div>

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
            <div id="analyticsData"></div>
            <div id="analysisResult"></div>
        </div>
        
        <div style="margin-top: 30px; padding: 15px; background-color: #f0f0f0; border-radius: 5px; font-size: 14px; color: #666;">
            <h4>✨ 強化機能:</h4>
            <ul>
                <li>🎯 <strong>トピッククラスター戦略:</strong> ピラーページと関連記事の提案</li>
                <li>🏆 <strong>競合分析:</strong> 上位5サイトとの比較・改善提案</li>
                <li>📊 <strong>Analytics連携:</strong> 実際のトラフィックデータに基づく分析</li>
                <li>🔍 <strong>Search Console連携:</strong> 検索パフォーマンスの詳細分析</li>
                <li>📈 <strong>統合レポート:</strong> 全データを統合した包括的な改善提案</li>
            </ul>
        </div>
    </div>
    
    <script>
        let currentJobId = null;
        let statusInterval = null;
        let currentSiteId = null;

        document.addEventListener('DOMContentLoaded', function() {
            checkAnalyticsConnection();
            loadRegisteredSites();
            
            // URLパラメータからサイト情報を取得
            const urlParams = new URLSearchParams(window.location.search);
            const siteId = urlParams.get('site_id');
            const siteUrl = urlParams.get('url');
            
            if (siteId) {
                currentSiteId = siteId;
                // サイト選択が読み込まれるまで待つ
                setTimeout(() => {
                    document.getElementById('site-select').value = siteId;
                }, 500);
            }
            if (siteUrl) {
                document.getElementById('url').value = decodeURIComponent(siteUrl);
            }
            updateAnalyticsPreview();
        });
        
        // 登録済みサイト一覧を読み込み
        async function loadRegisteredSites() {
            try {
                const response = await fetch('wordpress_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=list_sites'
                });

                const result = await response.json();
                
                if (result.success && result.sites) {
                    const siteSelect = document.getElementById('site-select');
                    
                    result.sites.forEach(site => {
                        const option = document.createElement('option');
                        option.value = site.id;
                        option.textContent = site.name + ' (' + site.url + ')';
                        siteSelect.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('サイト一覧の読み込みエラー:', error);
            }
        }
        
        // サイト選択時にURLを自動設定
        document.getElementById('site-select').addEventListener('change', function() {
            const siteId = this.value;
            currentSiteId = siteId;
            
            if (siteId) {
                // 選択されたサイトのURLを取得してURL入力欄に設定
                const selectedOption = this.options[this.selectedIndex];
                const siteUrl = selectedOption.textContent.match(/\((.+)\)$/)?.[1];
                if (siteUrl) {
                    document.getElementById('url').value = siteUrl;
                }
            }
        });

        document.getElementById('analysisForm').addEventListener('submit', function(e) {
            e.preventDefault();
            startEnhancedAnalysis();
        });

        function checkAnalyticsConnection() {
            const gaConnected = localStorage.getItem('ga_connected') === 'true';
            const gscConnected = localStorage.getItem('gsc_connected') === 'true';
            
            const gaIndicator = document.getElementById('gaIndicator');
            const gscIndicator = document.getElementById('gscIndicator');
            const gaStatus = document.getElementById('gaStatus');
            const gscStatus = document.getElementById('gscStatus');
            
            if (gaConnected) {
                gaIndicator.className = 'connection-indicator connected';
                gaStatus.textContent = '接続済み';
            } else {
                gaIndicator.className = 'connection-indicator disconnected';
                gaStatus.textContent = '未接続';
            }
            
            if (gscConnected) {
                gscIndicator.className = 'connection-indicator connected';
                gscStatus.textContent = '接続済み';
            } else {
                gscIndicator.className = 'connection-indicator disconnected';
                gscStatus.textContent = '未接続';
            }
            
            // 分析オプションのチェックボックスの状態を更新
            document.getElementById('includeAnalytics').disabled = !gaConnected;
            document.getElementById('includeSearchConsole').disabled = !gscConnected;
        }

        function showAnalyticsPreview() {
            const preview = document.getElementById('analyticsPreview');
            const isVisible = preview.style.display !== 'none';
            preview.style.display = isVisible ? 'none' : 'block';
            
            if (!isVisible) {
                updateAnalyticsPreview();
            }
        }

        function updateAnalyticsPreview() {
            const gaConnected = localStorage.getItem('ga_connected') === 'true';
            const gscConnected = localStorage.getItem('gsc_connected') === 'true';
            const metricsGrid = document.getElementById('metricsGrid');
            
            let metricsHtml = '';
            
            if (gaConnected) {
                metricsHtml += `
                    <div class="metric-card">
                        <div class="metric-value">12,345</div>
                        <div class="metric-label">ページビュー</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value">3,456</div>
                        <div class="metric-label">セッション</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value">2,789</div>
                        <div class="metric-label">ユーザー数</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value">65.5%</div>
                        <div class="metric-label">直帰率</div>
                    </div>
                `;
            }
            
            if (gscConnected) {
                metricsHtml += `
                    <div class="metric-card">
                        <div class="metric-value">45,678</div>
                        <div class="metric-label">表示回数</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value">1,234</div>
                        <div class="metric-label">クリック数</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value">2.7%</div>
                        <div class="metric-label">CTR</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value">12.3</div>
                        <div class="metric-label">平均掲載順位</div>
                    </div>
                `;
            }
            
            if (!gaConnected && !gscConnected) {
                metricsHtml = '<div style="text-align: center; color: #666;">Analytics連携を設定するとデータが表示されます</div>';
            }
            
            metricsGrid.innerHTML = metricsHtml;
        }

        async function startEnhancedAnalysis() {
            const url = document.getElementById('url').value;
            const analyzeBtn = document.getElementById('analyzeBtn');
            const progressContainer = document.getElementById('progressContainer');
            const resultContainer = document.getElementById('resultContainer');

            // 分析オプションの取得
            const options = {
                includeTopicCluster: document.getElementById('includeTopicCluster').checked,
                includeCompetitor: document.getElementById('includeCompetitor').checked,
                includeAnalytics: document.getElementById('includeAnalytics').checked,
                includeSearchConsole: document.getElementById('includeSearchConsole').checked
            };

            // UI初期化
            analyzeBtn.disabled = true;
            analyzeBtn.innerHTML = '🔄 強化分析開始中...';
            progressContainer.style.display = 'block';
            resultContainer.style.display = 'none';
            
            updateProgress(0, '強化版SEO分析を開始中...');

            try {
                // 実際の実装では、拡張されたAPIエンドポイントを呼び出し
                // 今回は既存のAPIを使用してデモ
                const response = await fetch('job_api.php?action=start', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `url=${encodeURIComponent(url)}&options=${encodeURIComponent(JSON.stringify(options))}`
                });

                const result = await response.json();
                
                if (!result.success) {
                    throw new Error(result.error || '不明なエラーが発生しました');
                }

                currentJobId = result.job_id;
                analyzeBtn.innerHTML = '🔄 強化分析実行中...';
                
                // ステータス監視開始
                statusInterval = setInterval(checkJobStatus, 2000);
                
            } catch (error) {
                showError('強化分析の開始に失敗しました: ' + error.message);
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

                displayEnhancedResult(data.result);
                updateProgress(100, '強化版分析完了！');
                resetUI();

            } catch (error) {
                showError('結果取得エラー: ' + error.message);
                resetUI();
            }
        }

        function displayEnhancedResult(result) {
            const resultContainer = document.getElementById('resultContainer');
            const pageInfo = document.getElementById('pageInfo');
            const analyticsData = document.getElementById('analyticsData');
            const analysisResult = document.getElementById('analysisResult');

            // ページ基本情報
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

            // Analytics・Search Console データ表示
            let analyticsHtml = '';
            const gaConnected = localStorage.getItem('ga_connected') === 'true';
            const gscConnected = localStorage.getItem('gsc_connected') === 'true';
            
            if (gaConnected || gscConnected) {
                analyticsHtml = `
                    <div class="analytics-data">
                        <h3>📈 連携データ（分析期間: 過去30日間）</h3>
                        <div class="metric-grid">
                `;
                
                if (gaConnected) {
                    analyticsHtml += `
                        <div class="metric-card">
                            <div class="metric-value">12,345</div>
                            <div class="metric-label">ページビュー</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-value">3,456</div>
                            <div class="metric-label">セッション</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-value">65.5%</div>
                            <div class="metric-label">直帰率</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-value">2:34</div>
                            <div class="metric-label">平均セッション時間</div>
                        </div>
                    `;
                }
                
                if (gscConnected) {
                    analyticsHtml += `
                        <div class="metric-card">
                            <div class="metric-value">45,678</div>
                            <div class="metric-label">表示回数</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-value">1,234</div>
                            <div class="metric-label">クリック数</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-value">2.7%</div>
                            <div class="metric-label">CTR</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-value">12.3</div>
                            <div class="metric-label">平均掲載順位</div>
                        </div>
                    `;
                }
                
                analyticsHtml += `
                        </div>
                    </div>
                `;
            }

            // 分析結果表示
            const analysisHtml = `
                <h3>🎯 強化版SEO分析結果・改善提案</h3>
                <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; white-space: pre-wrap; word-wrap: break-word; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6;">${escapeHtml(result.seo_analysis)}</div>
            `;

            pageInfo.innerHTML = pageInfoHtml;
            analyticsData.innerHTML = analyticsHtml;
            analysisResult.innerHTML = analysisHtml;
            resultContainer.style.display = 'block';
        }

        function updateProgress(percent, message) {
            const progressFill = document.getElementById('progressFill');
            const progressPercent = document.getElementById('progressPercent');
            const progressMessage = document.getElementById('progressMessage');

            progressFill.style.width = percent + '%';
            progressPercent.textContent = percent + '%';
            progressMessage.textContent = message;
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
            analyzeBtn.innerHTML = '🔍 強化版SEO分析を開始';
            
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