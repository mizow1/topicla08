<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Analytics・Search Console 連携</title>
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
        .integration-section {
            margin-bottom: 40px;
            padding: 20px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
        }
        .integration-section h2 {
            color: #4285f4;
            margin-bottom: 20px;
        }
        .status-indicator {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            margin-left: 10px;
        }
        .status-connected {
            background-color: #4CAF50;
            color: white;
        }
        .status-disconnected {
            background-color: #f44336;
            color: white;
        }
        .btn {
            background-color: #4285f4;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .btn:hover {
            background-color: #357ae8;
        }
        .btn-disconnect {
            background-color: #f44336;
        }
        .btn-disconnect:hover {
            background-color: #d32f2f;
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
        input[type="text"], input[type="url"], textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        textarea {
            height: 120px;
            resize: vertical;
        }
        .info-box {
            background-color: #e3f2fd;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            border-left: 4px solid #2196F3;
        }
        .warning-box {
            background-color: #fff3e0;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            border-left: 4px solid #ff9800;
        }
        .success-box {
            background-color: #e8f5e8;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            border-left: 4px solid #4caf50;
            display: none;
        }
        .error-box {
            background-color: #ffebee;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            border-left: 4px solid #f44336;
            display: none;
        }
        .metrics-preview {
            margin-top: 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
        }
        .metric-card {
            display: inline-block;
            background: white;
            padding: 20px;
            margin: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
            min-width: 150px;
        }
        .metric-value {
            font-size: 24px;
            font-weight: bold;
            color: #4285f4;
        }
        .metric-label {
            color: #666;
            margin-top: 5px;
        }
        .oauth-flow {
            display: none;
            margin-top: 20px;
            padding: 20px;
            background-color: #f0f8ff;
            border-radius: 8px;
            border: 1px solid #2196f3;
        }
        .step {
            margin-bottom: 15px;
        }
        .step-number {
            display: inline-block;
            width: 25px;
            height: 25px;
            background-color: #4285f4;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 25px;
            margin-right: 10px;
        }
        .code-block {
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
            font-family: monospace;
            margin: 10px 0;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <?php include_once 'includes/navigation.php'; ?>
    <div class="container">
        <h1>📊 Google Analytics・Search Console 連携</h1>
        
        <!-- 連携後の活用ガイド -->
        <div style="text-align: center; margin-bottom: 20px; padding: 15px; background: #fff3e0; border-radius: 8px;">
            <p style="margin: 0; color: #f57c00;">
                📈 <strong>データ活用フロー:</strong> 
                <a href="analytics_integration.php" style="color: #f57c00;">①Analytics連携</a> → 
                <a href="enhanced_seo_interface.php" style="color: #f57c00;">②データ統合分析</a> → 
                <a href="seo_improvement_interface.php" style="color: #f57c00;">③改善実施</a>
            </p>
        </div>
        
        <!-- Google Analytics 連携セクション -->
        <div class="integration-section">
            <h2>🎯 Google Analytics 連携
                <span class="status-indicator" id="ga-status">未接続</span>
            </h2>
            
            <div class="info-box">
                <strong>📋 Google Analytics連携の機能：</strong>
                <ul>
                    <li>ページビュー数、セッション数、ユーザー数の取得</li>
                    <li>直帰率、滞在時間、コンバージョン率の分析</li>
                    <li>トラフィック流入元の詳細分析</li>
                    <li>ユーザー行動とSEOパフォーマンスの相関分析</li>
                </ul>
            </div>

            <div class="warning-box">
                <strong>⚠️ 注意：</strong>
                Google Analytics APIを使用するには、Google Cloud ConsoleでAnalytics Reporting APIを有効化し、サービスアカウントキーまたはOAuth認証を設定する必要があります。
            </div>

            <div class="form-group">
                <label for="ga-property-id">Google Analytics プロパティID (GA4):</label>
                <input type="text" id="ga-property-id" placeholder="例: 123456789" value="">
            </div>

            <div class="form-group">
                <label for="ga-service-account">サービスアカウントキー (JSON):</label>
                <textarea id="ga-service-account" placeholder="サービスアカウントのJSONキーをここに貼り付けてください"></textarea>
            </div>

            <button class="btn" id="ga-connect-btn" onclick="connectGoogleAnalytics()">Google Analyticsに接続</button>
            <button class="btn btn-disconnect" id="ga-disconnect-btn" onclick="disconnectGoogleAnalytics()" style="display: none;">切断</button>
            <button class="btn" onclick="showGAOAuthFlow()">OAuth認証フローを表示</button>

            <div class="oauth-flow" id="ga-oauth-flow">
                <h3>🔐 OAuth認証フロー</h3>
                <div class="step">
                    <span class="step-number">1</span>
                    <strong>Google Cloud Consoleでプロジェクトを作成</strong><br>
                    <a href="https://console.cloud.google.com/" target="_blank">https://console.cloud.google.com/</a>
                </div>
                <div class="step">
                    <span class="step-number">2</span>
                    <strong>Google Analytics Reporting APIを有効化</strong>
                </div>
                <div class="step">
                    <span class="step-number">3</span>
                    <strong>認証情報を作成（OAuth 2.0 クライアントID）</strong><br>
                    リダイレクトURL: <div class="code-block"><?php echo (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/oauth/callback'; ?></div>
                </div>
                <div class="step">
                    <span class="step-number">4</span>
                    <strong>クライアントIDとシークレットを下記に入力</strong>
                </div>
                <div class="form-group">
                    <label for="ga-client-id">クライアントID:</label>
                    <input type="text" id="ga-client-id" placeholder="Google CloudコンソールのクライアントID">
                </div>
                <div class="form-group">
                    <label for="ga-client-secret">クライアントシークレット:</label>
                    <input type="text" id="ga-client-secret" placeholder="Google Cloudコンソールのクライアントシークレット">
                </div>
                <button class="btn" onclick="startGAOAuth()">OAuth認証を開始</button>
            </div>

            <div class="success-box" id="ga-success">
                ✅ Google Analytics連携が完了しました！
            </div>
            <div class="error-box" id="ga-error">
                ❌ Google Analytics連携でエラーが発生しました。設定を確認してください。
            </div>
        </div>

        <!-- Google Search Console 連携セクション -->
        <div class="integration-section">
            <h2>🔍 Google Search Console 連携
                <span class="status-indicator" id="gsc-status">未接続</span>
            </h2>

            <div class="info-box">
                <strong>📋 Search Console連携の機能：</strong>
                <ul>
                    <li>検索クエリ、表示回数、クリック数、CTR、平均掲載順位の取得</li>
                    <li>インデックス状況とクロールエラーの監視</li>
                    <li>コアウェブバイタル（Core Web Vitals）のパフォーマンス分析</li>
                    <li>モバイルユーザビリティの問題検出</li>
                </ul>
            </div>

            <div class="warning-box">
                <strong>⚠️ 注意：</strong>
                Search Console APIを使用するには、Google Cloud ConsoleでGoogle Search Console APIを有効化し、対象サイトの所有者権限が必要です。
            </div>

            <div class="form-group">
                <label for="gsc-site-url">Search Consoleプロパティ URL:</label>
                <input type="url" id="gsc-site-url" placeholder="https://example.com/" value="">
            </div>

            <div class="form-group">
                <label for="gsc-service-account">サービスアカウントキー (JSON):</label>
                <textarea id="gsc-service-account" placeholder="サービスアカウントのJSONキーをここに貼り付けてください"></textarea>
            </div>

            <button class="btn" id="gsc-connect-btn" onclick="connectSearchConsole()">Search Consoleに接続</button>
            <button class="btn btn-disconnect" id="gsc-disconnect-btn" onclick="disconnectSearchConsole()" style="display: none;">切断</button>
            <button class="btn" onclick="showGSCOAuthFlow()">OAuth認証フローを表示</button>

            <div class="oauth-flow" id="gsc-oauth-flow">
                <h3>🔐 OAuth認証フロー</h3>
                <div class="step">
                    <span class="step-number">1</span>
                    <strong>Google Cloud Consoleでプロジェクトを作成</strong><br>
                    <a href="https://console.cloud.google.com/" target="_blank">https://console.cloud.google.com/</a>
                </div>
                <div class="step">
                    <span class="step-number">2</span>
                    <strong>Google Search Console APIを有効化</strong>
                </div>
                <div class="step">
                    <span class="step-number">3</span>
                    <strong>Search Consoleでサイトの所有者権限を設定</strong><br>
                    <a href="https://search.google.com/search-console" target="_blank">https://search.google.com/search-console</a>
                </div>
                <div class="step">
                    <span class="step-number">4</span>
                    <strong>認証情報を作成（OAuth 2.0 クライアントID）</strong><br>
                    リダイレクトURL: <div class="code-block"><?php echo (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/oauth/callback'; ?></div>
                </div>
                <div class="form-group">
                    <label for="gsc-client-id">クライアントID:</label>
                    <input type="text" id="gsc-client-id" placeholder="Google CloudコンソールのクライアントID">
                </div>
                <div class="form-group">
                    <label for="gsc-client-secret">クライアントシークレット:</label>
                    <input type="text" id="gsc-client-secret" placeholder="Google Cloudコンソールのクライアントシークレット">
                </div>
                <button class="btn" onclick="startGSCOAuth()">OAuth認証を開始</button>
            </div>

            <div class="success-box" id="gsc-success">
                ✅ Google Search Console連携が完了しました！
            </div>
            <div class="error-box" id="gsc-error">
                ❌ Google Search Console連携でエラーが発生しました。設定を確認してください。
            </div>
        </div>

        <!-- メトリクスプレビューセクション -->
        <div class="metrics-preview">
            <h2>📈 連携データプレビュー</h2>
            <div id="metrics-container">
                <div class="info-box">
                    Google AnalyticsまたはSearch Consoleに接続すると、ここにデータが表示されます。
                </div>
            </div>
        </div>

        <!-- ナビゲーションリンク -->
        <div style="text-align: center; margin-top: 40px;">
            <button class="btn" onclick="goToSEOAnalysis()" style="background-color: #4CAF50; font-size: 18px; padding: 15px 40px;">
                🚀 連携データを使ってSEO分析を開始
            </button>
            <br><br>
            <div style="text-align: center;">
                <a href="enhanced_seo_interface.php" style="margin: 0 15px; color: #2196F3; text-decoration: none;">📊 SEO分析画面へ</a>
                <a href="article_generator_interface.php" style="margin: 0 15px; color: #2196F3; text-decoration: none;">✍️ 記事生成画面へ</a>
                <a href="settings.php" style="margin: 0 15px; color: #2196F3; text-decoration: none;">⚙️ API設定</a>
            </div>
        </div>
    </div>

    <script>
        // 接続状態の管理
        let gaConnected = false;
        let gscConnected = false;

        // 初期化
        document.addEventListener('DOMContentLoaded', function() {
            loadSavedSettings();
            updateConnectionStatus();
        });

        function loadSavedSettings() {
            // ローカルストレージから設定を読み込み
            const gaPropertyId = localStorage.getItem('ga_property_id');
            const gscSiteUrl = localStorage.getItem('gsc_site_url');
            
            if (gaPropertyId) {
                document.getElementById('ga-property-id').value = gaPropertyId;
            }
            if (gscSiteUrl) {
                document.getElementById('gsc-site-url').value = gscSiteUrl;
            }

            // 接続状態をチェック
            gaConnected = localStorage.getItem('ga_connected') === 'true';
            gscConnected = localStorage.getItem('gsc_connected') === 'true';
        }

        function updateConnectionStatus() {
            const gaStatus = document.getElementById('ga-status');
            const gscStatus = document.getElementById('gsc-status');
            const gaConnectBtn = document.getElementById('ga-connect-btn');
            const gaDisconnectBtn = document.getElementById('ga-disconnect-btn');
            const gscConnectBtn = document.getElementById('gsc-connect-btn');
            const gscDisconnectBtn = document.getElementById('gsc-disconnect-btn');

            if (gaConnected) {
                gaStatus.textContent = '接続済み';
                gaStatus.className = 'status-indicator status-connected';
                gaConnectBtn.style.display = 'none';
                gaDisconnectBtn.style.display = 'inline-block';
                loadGAMetrics();
            } else {
                gaStatus.textContent = '未接続';
                gaStatus.className = 'status-indicator status-disconnected';
                gaConnectBtn.style.display = 'inline-block';
                gaDisconnectBtn.style.display = 'none';
            }

            if (gscConnected) {
                gscStatus.textContent = '接続済み';
                gscStatus.className = 'status-indicator status-connected';
                gscConnectBtn.style.display = 'none';
                gscDisconnectBtn.style.display = 'inline-block';
                loadGSCMetrics();
            } else {
                gscStatus.textContent = '未接続';
                gscStatus.className = 'status-indicator status-disconnected';
                gscConnectBtn.style.display = 'inline-block';
                gscDisconnectBtn.style.display = 'none';
            }
        }

        async function connectGoogleAnalytics() {
            const propertyId = document.getElementById('ga-property-id').value;
            const serviceAccount = document.getElementById('ga-service-account').value;

            if (!propertyId) {
                alert('プロパティIDを入力してください');
                return;
            }

            // サービスアカウントまたはOAuth認証をチェック
            if (!serviceAccount && !localStorage.getItem('ga_oauth_token')) {
                alert('サービスアカウントキーを入力するか、OAuth認証を完了してください');
                return;
            }

            try {
                // 実際の実装では、ここでGoogle Analytics APIに接続
                // 今回はモック接続として処理
                
                localStorage.setItem('ga_property_id', propertyId);
                localStorage.setItem('ga_service_account', serviceAccount);
                localStorage.setItem('ga_connected', 'true');
                
                gaConnected = true;
                updateConnectionStatus();
                
                document.getElementById('ga-success').style.display = 'block';
                setTimeout(() => {
                    document.getElementById('ga-success').style.display = 'none';
                }, 5000);
                
            } catch (error) {
                document.getElementById('ga-error').style.display = 'block';
                document.getElementById('ga-error').innerHTML = '❌ 接続エラー: ' + error.message;
            }
        }

        async function connectSearchConsole() {
            const siteUrl = document.getElementById('gsc-site-url').value;
            const serviceAccount = document.getElementById('gsc-service-account').value;

            if (!siteUrl) {
                alert('サイトURLを入力してください');
                return;
            }

            if (!serviceAccount && !localStorage.getItem('gsc_oauth_token')) {
                alert('サービスアカウントキーを入力するか、OAuth認証を完了してください');
                return;
            }

            try {
                // 実際の実装では、ここでSearch Console APIに接続
                // 今回はモック接続として処理
                
                localStorage.setItem('gsc_site_url', siteUrl);
                localStorage.setItem('gsc_service_account', serviceAccount);
                localStorage.setItem('gsc_connected', 'true');
                
                gscConnected = true;
                updateConnectionStatus();
                
                document.getElementById('gsc-success').style.display = 'block';
                setTimeout(() => {
                    document.getElementById('gsc-success').style.display = 'none';
                }, 5000);
                
            } catch (error) {
                document.getElementById('gsc-error').style.display = 'block';
                document.getElementById('gsc-error').innerHTML = '❌ 接続エラー: ' + error.message;
            }
        }

        function disconnectGoogleAnalytics() {
            localStorage.removeItem('ga_connected');
            localStorage.removeItem('ga_property_id');
            localStorage.removeItem('ga_service_account');
            localStorage.removeItem('ga_oauth_token');
            gaConnected = false;
            updateConnectionStatus();
        }

        function disconnectSearchConsole() {
            localStorage.removeItem('gsc_connected');
            localStorage.removeItem('gsc_site_url');
            localStorage.removeItem('gsc_service_account');
            localStorage.removeItem('gsc_oauth_token');
            gscConnected = false;
            updateConnectionStatus();
        }

        function showGAOAuthFlow() {
            const flow = document.getElementById('ga-oauth-flow');
            flow.style.display = flow.style.display === 'none' ? 'block' : 'none';
        }

        function showGSCOAuthFlow() {
            const flow = document.getElementById('gsc-oauth-flow');
            flow.style.display = flow.style.display === 'none' ? 'block' : 'none';
        }

        function startGAOAuth() {
            const clientId = document.getElementById('ga-client-id').value;
            if (!clientId) {
                alert('クライアントIDを入力してください');
                return;
            }
            
            // 実際の実装では、Google OAuth URLにリダイレクト
            alert('OAuth認証は実装中です。現在はサービスアカウント認証をお使いください。');
        }

        function startGSCOAuth() {
            const clientId = document.getElementById('gsc-client-id').value;
            if (!clientId) {
                alert('クライアントIDを入力してください');
                return;
            }
            
            // 実際の実装では、Google OAuth URLにリダイレクト
            alert('OAuth認証は実装中です。現在はサービスアカウント認証をお使いください。');
        }

        function loadGAMetrics() {
            // モックデータを表示（実際の実装ではGoogle Analytics APIからデータを取得）
            const metricsHtml = `
                <h3>Google Analytics データ</h3>
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
            document.getElementById('metrics-container').innerHTML = metricsHtml;
        }

        function loadGSCMetrics() {
            // モックデータを表示（実際の実装ではSearch Console APIからデータを取得）
            let currentHtml = document.getElementById('metrics-container').innerHTML;
            if (!currentHtml.includes('Google Analytics データ')) {
                currentHtml = '';
            }
            
            const gscMetricsHtml = `
                <h3>Search Console データ</h3>
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
            document.getElementById('metrics-container').innerHTML = currentHtml + gscMetricsHtml;
        }

        function goToSEOAnalysis() {
            if (!gaConnected && !gscConnected) {
                alert('Google AnalyticsまたはSearch Consoleのいずれかに接続してください');
                return;
            }
            
            // SEO分析画面に移動
            window.location.href = 'enhanced_seo_interface.php';
        }
    </script>
</body>
</html>