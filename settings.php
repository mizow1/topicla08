<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API設定管理 - SEO分析ツール</title>
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
        .nav-links a.active {
            background-color: #2196F3;
            color: white;
        }
        .settings-section {
            margin-bottom: 40px;
            padding: 20px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
        }
        .settings-section h2 {
            color: #333;
            margin-bottom: 20px;
            border-bottom: 2px solid #4285f4;
            padding-bottom: 10px;
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
        input[type="text"], input[type="password"], input[type="url"], textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        input:focus {
            border-color: #4285f4;
            outline: none;
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
        .btn-test {
            background-color: #f39c12;
        }
        .btn-test:hover {
            background-color: #e67e22;
        }
        .btn-save {
            background-color: #27ae60;
        }
        .btn-save:hover {
            background-color: #229954;
        }
        .btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        .info-box {
            background-color: #e3f2fd;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #2196F3;
        }
        .warning-box {
            background-color: #fff3e0;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #ff9800;
        }
        .success-box {
            background-color: #e8f5e8;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #4caf50;
            display: none;
        }
        .error-box {
            background-color: #ffebee;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #f44336;
            display: none;
        }
        .test-result {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
            display: none;
        }
        .test-result.success {
            background-color: #e8f5e8;
            color: #2e7d32;
            border: 1px solid #4caf50;
        }
        .test-result.error {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #f44336;
        }
        .step-guide {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
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
            font-size: 14px;
        }
        .loading {
            display: none;
            text-align: center;
        }
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #4285f4;
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
    <?php include_once 'includes/navigation.php'; ?>
    <div class="container">

        <h1>⚙️ API設定管理</h1>
        
        <!-- 設定完了後のガイダンス -->
        <div style="text-align: center; margin-bottom: 20px; padding: 15px; background: #fff3e0; border-radius: 8px;">
            <p style="margin: 0; color: #f57c00;">
                ⚙️ <strong>設定完了後:</strong> 
                <a href="wordpress_manager.php" style="color: #f57c00;">サイト登録</a> → 
                <a href="enhanced_seo_interface.php" style="color: #f57c00;">SEO分析</a>ですぐに始められます！
            </p>
        </div>

        <div class="warning-box">
            <strong>🔐 セキュリティについて</strong><br>
            入力された設定情報は暗号化されてサーバーに保存されます。APIキーなどの機密情報は適切に保護されます。
        </div>

        <!-- Claude API設定 -->
        <div class="settings-section">
            <h2>🤖 Claude API設定</h2>
            
            <div class="info-box">
                <strong>Claude APIについて：</strong><br>
                SEO分析とコンテンツ生成に使用されます。Anthropic社のAPIキーが必要です。<br>
                取得方法: <a href="https://console.anthropic.com/" target="_blank">Anthropic Console</a>
            </div>

            <div class="step-guide">
                <h3>📋 Claude APIキー取得手順</h3>
                <div class="step">
                    <span class="step-number">1</span>
                    <a href="https://console.anthropic.com/" target="_blank">Anthropic Console</a>にアクセスしてアカウント作成
                </div>
                <div class="step">
                    <span class="step-number">2</span>
                    「API Keys」セクションでAPIキーを生成
                </div>
                <div class="step">
                    <span class="step-number">3</span>
                    生成されたAPIキー（sk-ant-api...）をコピーして下記に入力
                </div>
            </div>

            <div class="form-group">
                <label for="claude-api-key">Claude APIキー:</label>
                <input type="password" id="claude-api-key" placeholder="sk-ant-api...">
            </div>

            <button class="btn btn-test" onclick="testClaudeAPI()">接続テスト</button>
            <div class="loading" id="claude-loading">
                <span class="spinner"></span>テスト中...
            </div>
            <div class="test-result" id="claude-result"></div>
        </div>

        <!-- Google API設定 -->
        <div class="settings-section">
            <h2>🔍 Google API設定（競合分析用）</h2>
            
            <div class="info-box">
                <strong>Google Custom Search APIについて：</strong><br>
                競合サイトの自動検索に使用されます。Google Cloud ConsoleでAPIキーとカスタム検索エンジンIDが必要です。
            </div>

            <div class="step-guide">
                <h3>📋 Google API設定手順</h3>
                <div class="step">
                    <span class="step-number">1</span>
                    <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a>でプロジェクト作成
                </div>
                <div class="step">
                    <span class="step-number">2</span>
                    「Custom Search API」を有効化
                </div>
                <div class="step">
                    <span class="step-number">3</span>
                    認証情報でAPIキーを作成
                </div>
                <div class="step">
                    <span class="step-number">4</span>
                    <a href="https://cse.google.com/" target="_blank">Custom Search Engine</a>でカスタム検索エンジンを作成
                </div>
                <div class="step">
                    <span class="step-number">5</span>
                    検索エンジンID（cx=...）をコピー
                </div>
            </div>

            <div class="form-group">
                <label for="google-api-key">Google APIキー:</label>
                <input type="password" id="google-api-key" placeholder="AIza...">
            </div>

            <div class="form-group">
                <label for="google-search-engine-id">カスタム検索エンジンID:</label>
                <input type="text" id="google-search-engine-id" placeholder="012345678901234567890:abcdefghijk">
            </div>

            <button class="btn btn-test" onclick="testGoogleAPI()">接続テスト</button>
            <div class="loading" id="google-loading">
                <span class="spinner"></span>テスト中...
            </div>
            <div class="test-result" id="google-result"></div>
        </div>

        <!-- WordPress API設定 -->
        <div class="settings-section">
            <h2>📝 WordPress連携設定</h2>
            
            <div class="info-box">
                <strong>WordPress REST APIについて：</strong><br>
                生成された記事をWordPressに自動投稿するために使用されます。WordPressのアプリケーションパスワードが必要です。
            </div>

            <div class="step-guide">
                <h3>📋 WordPress設定手順</h3>
                <div class="step">
                    <span class="step-number">1</span>
                    WordPressダッシュボード → ユーザー → プロフィール
                </div>
                <div class="step">
                    <span class="step-number">2</span>
                    「アプリケーションパスワード」セクション
                </div>
                <div class="step">
                    <span class="step-number">3</span>
                    新しいアプリケーション名を入力（例: SEOツール）
                </div>
                <div class="step">
                    <span class="step-number">4</span>
                    生成されたパスワード（xxxx xxxx xxxx...）をコピー
                </div>
            </div>

            <div class="form-group">
                <label for="wordpress-url">WordPress サイトURL:</label>
                <input type="url" id="wordpress-url" placeholder="https://yoursite.com">
            </div>

            <div class="form-group">
                <label for="wordpress-username">WordPressユーザー名:</label>
                <input type="text" id="wordpress-username" placeholder="admin">
            </div>

            <div class="form-group">
                <label for="wordpress-app-password">アプリケーションパスワード:</label>
                <input type="password" id="wordpress-app-password" placeholder="xxxx xxxx xxxx xxxx xxxx xxxx">
            </div>

            <button class="btn btn-test" onclick="testWordPressAPI()">接続テスト</button>
            <div class="loading" id="wordpress-loading">
                <span class="spinner"></span>テスト中...
            </div>
            <div class="test-result" id="wordpress-result"></div>
        </div>

        <!-- 保存ボタン -->
        <div style="text-align: center; margin-top: 40px;">
            <button class="btn btn-save" onclick="saveAllSettings()">
                💾 すべての設定を保存
            </button>
        </div>

        <div class="success-box" id="save-success">
            ✅ 設定を保存しました！
        </div>
        <div class="error-box" id="save-error">
        </div>
    </div>

    <script>
        // ページ読み込み時に既存設定を読み込み
        document.addEventListener('DOMContentLoaded', function() {
            loadSettings();
        });

        // 設定読み込み
        async function loadSettings() {
            try {
                const response = await fetch('settings_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=load_settings'
                });

                const result = await response.json();

                if (result.success && result.settings) {
                    const settings = result.settings;
                    
                    // フィールドに値を設定（マスクされた値）
                    if (settings.claude_api_key) {
                        document.getElementById('claude-api-key').placeholder = settings.claude_api_key;
                    }
                    if (settings.google_api_key) {
                        document.getElementById('google-api-key').placeholder = settings.google_api_key;
                    }
                    if (settings.google_search_engine_id) {
                        document.getElementById('google-search-engine-id').value = settings.google_search_engine_id;
                    }
                    if (settings.wordpress_url) {
                        document.getElementById('wordpress-url').value = settings.wordpress_url;
                    }
                    if (settings.wordpress_username) {
                        document.getElementById('wordpress-username').value = settings.wordpress_username;
                    }
                    if (settings.wordpress_app_password) {
                        document.getElementById('wordpress-app-password').placeholder = settings.wordpress_app_password;
                    }
                }
            } catch (error) {
                console.error('設定読み込みエラー:', error);
            }
        }

        // すべての設定を保存
        async function saveAllSettings() {
            const settings = {
                claude_api_key: document.getElementById('claude-api-key').value,
                google_api_key: document.getElementById('google-api-key').value,
                google_search_engine_id: document.getElementById('google-search-engine-id').value,
                wordpress_url: document.getElementById('wordpress-url').value,
                wordpress_username: document.getElementById('wordpress-username').value,
                wordpress_app_password: document.getElementById('wordpress-app-password').value
            };

            // 空の場合は既存値を保持するため、プレースホルダーから値を取得
            Object.keys(settings).forEach(key => {
                const field = document.getElementById(key.replace('_', '-'));
                if (!settings[key] && field.placeholder && field.placeholder.includes('*')) {
                    // マスクされた値の場合は更新しない（空文字列を送信）
                    settings[key] = '';
                }
            });

            try {
                const formData = new FormData();
                formData.append('action', 'save_settings');
                
                Object.keys(settings).forEach(key => {
                    if (settings[key]) {  // 空でない場合のみ送信
                        formData.append(key, settings[key]);
                    }
                });

                const response = await fetch('settings_api.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showSaveSuccess(result.message);
                    // 設定を再読み込み
                    setTimeout(loadSettings, 1000);
                } else {
                    showSaveError(result.error);
                }
            } catch (error) {
                showSaveError('設定保存中にエラーが発生しました: ' + error.message);
            }
        }

        // Claude API テスト
        async function testClaudeAPI() {
            const apiKey = document.getElementById('claude-api-key').value;
            
            if (!apiKey) {
                alert('Claude APIキーを入力してください');
                return;
            }

            const loading = document.getElementById('claude-loading');
            const result = document.getElementById('claude-result');
            
            loading.style.display = 'block';
            result.style.display = 'none';

            try {
                const formData = new FormData();
                formData.append('action', 'test_claude_api');
                formData.append('api_key', apiKey);

                const response = await fetch('settings_api.php', {
                    method: 'POST',
                    body: formData
                });

                const testResult = await response.json();

                if (testResult.success) {
                    showTestResult('claude-result', 'success', testResult.message);
                } else {
                    showTestResult('claude-result', 'error', testResult.error);
                }
            } catch (error) {
                showTestResult('claude-result', 'error', 'テスト中にエラーが発生しました: ' + error.message);
            } finally {
                loading.style.display = 'none';
            }
        }

        // Google API テスト
        async function testGoogleAPI() {
            const apiKey = document.getElementById('google-api-key').value;
            const searchEngineId = document.getElementById('google-search-engine-id').value;
            
            if (!apiKey || !searchEngineId) {
                alert('Google APIキーと検索エンジンIDを入力してください');
                return;
            }

            const loading = document.getElementById('google-loading');
            const result = document.getElementById('google-result');
            
            loading.style.display = 'block';
            result.style.display = 'none';

            try {
                const formData = new FormData();
                formData.append('action', 'test_google_api');
                formData.append('api_key', apiKey);
                formData.append('search_engine_id', searchEngineId);

                const response = await fetch('settings_api.php', {
                    method: 'POST',
                    body: formData
                });

                const testResult = await response.json();

                if (testResult.success) {
                    showTestResult('google-result', 'success', testResult.message);
                } else {
                    showTestResult('google-result', 'error', testResult.error);
                }
            } catch (error) {
                showTestResult('google-result', 'error', 'テスト中にエラーが発生しました: ' + error.message);
            } finally {
                loading.style.display = 'none';
            }
        }

        // WordPress API テスト
        async function testWordPressAPI() {
            const url = document.getElementById('wordpress-url').value;
            const username = document.getElementById('wordpress-username').value;
            const password = document.getElementById('wordpress-app-password').value;
            
            if (!url || !username || !password) {
                alert('全ての WordPress 設定項目を入力してください');
                return;
            }

            const loading = document.getElementById('wordpress-loading');
            const result = document.getElementById('wordpress-result');
            
            loading.style.display = 'block';
            result.style.display = 'none';

            try {
                const formData = new FormData();
                formData.append('action', 'test_wordpress_api');
                formData.append('url', url);
                formData.append('username', username);
                formData.append('password', password);

                const response = await fetch('settings_api.php', {
                    method: 'POST',
                    body: formData
                });

                const testResult = await response.json();

                if (testResult.success) {
                    showTestResult('wordpress-result', 'success', testResult.message + 
                        (testResult.user ? ' (ユーザー: ' + testResult.user + ')' : ''));
                } else {
                    showTestResult('wordpress-result', 'error', testResult.error);
                }
            } catch (error) {
                showTestResult('wordpress-result', 'error', 'テスト中にエラーが発生しました: ' + error.message);
            } finally {
                loading.style.display = 'none';
            }
        }

        // テスト結果表示
        function showTestResult(elementId, type, message) {
            const result = document.getElementById(elementId);
            result.className = 'test-result ' + type;
            result.textContent = message;
            result.style.display = 'block';
        }

        // 保存成功メッセージ表示
        function showSaveSuccess(message) {
            const successBox = document.getElementById('save-success');
            successBox.textContent = '✅ ' + message;
            successBox.style.display = 'block';
            
            const errorBox = document.getElementById('save-error');
            errorBox.style.display = 'none';
            
            setTimeout(() => {
                successBox.style.display = 'none';
            }, 5000);
        }

        // 保存エラーメッセージ表示
        function showSaveError(message) {
            const errorBox = document.getElementById('save-error');
            errorBox.textContent = '❌ ' + message;
            errorBox.style.display = 'block';
            
            const successBox = document.getElementById('save-success');
            successBox.style.display = 'none';
        }
    </script>
</body>
</html>