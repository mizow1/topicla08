<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WordPressサイト管理 - SEO分析ツール</title>
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
        .site-card {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin: 15px 0;
            background: #fafafa;
        }
        .site-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .site-name {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        .site-status {
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-active {
            background-color: #4caf50;
            color: white;
        }
        .status-inactive {
            background-color: #f44336;
            color: white;
        }
        .site-info {
            color: #666;
            margin-bottom: 10px;
        }
        .site-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .btn {
            background-color: #4285f4;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover {
            background-color: #357ae8;
        }
        .btn-success {
            background-color: #4caf50;
        }
        .btn-success:hover {
            background-color: #45a049;
        }
        .btn-warning {
            background-color: #ff9800;
        }
        .btn-warning:hover {
            background-color: #e68900;
        }
        .btn-danger {
            background-color: #f44336;
        }
        .btn-danger:hover {
            background-color: #da190b;
        }
        .add-site-section {
            background: #e8f5e8;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #4caf50;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input[type="text"], input[type="url"], input[type="password"], textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        input:focus {
            border-color: #4285f4;
            outline: none;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .success-message {
            background-color: #e8f5e8;
            color: #2e7d32;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #4caf50;
            margin: 15px 0;
            display: none;
        }
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #f44336;
            margin: 15px 0;
            display: none;
        }
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
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
        .no-sites {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        .stats-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
        }
        .stat-label {
            font-size: 14px;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <?php include_once 'includes/navigation.php'; ?>
    <div class="container">

        <h1>🏢 WordPressサイト管理</h1>
        
        <!-- クイックアクションリンク -->
        <div style="text-align: center; margin-bottom: 20px;">
            <a href="enhanced_seo_interface.php" class="btn" style="margin: 5px;">🚀 SEO分析へ</a>
            <a href="seo_improvement_interface.php" class="btn btn-success" style="margin: 5px;">🔧 改善適用へ</a>
            <a href="settings.php" class="btn btn-warning" style="margin: 5px;">⚙️ API設定へ</a>
        </div>

        <!-- 統計情報 -->
        <div class="stats-section">
            <div class="stat-card">
                <div class="stat-number" id="total-sites">0</div>
                <div class="stat-label">登録サイト数</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="active-sites">0</div>
                <div class="stat-label">稼働中サイト</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="total-improvements">0</div>
                <div class="stat-label">改善適用済み</div>
            </div>
        </div>

        <!-- 新規サイト登録 -->
        <div class="add-site-section">
            <h3>📝 新しいWordPressサイトを登録</h3>
            <form id="add-site-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="site-name">サイト名:</label>
                        <input type="text" id="site-name" name="name" placeholder="例: 私のブログ" required>
                    </div>
                    <div class="form-group">
                        <label for="site-url">サイトURL:</label>
                        <input type="url" id="site-url" name="url" placeholder="https://yoursite.com" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="wp-username">WordPressユーザー名:</label>
                        <input type="text" id="wp-username" name="username" placeholder="admin" required>
                    </div>
                    <div class="form-group">
                        <label for="wp-password">アプリケーションパスワード:</label>
                        <input type="password" id="wp-password" name="app_password" placeholder="xxxx xxxx xxxx xxxx xxxx xxxx" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="site-description">説明（任意）:</label>
                    <textarea id="site-description" name="description" placeholder="サイトの説明やメモを入力してください" rows="3"></textarea>
                </div>
                
                <button type="submit" class="btn btn-success">✅ サイトを登録</button>
                <button type="button" class="btn btn-warning" onclick="testConnection()">🔍 接続テスト</button>
            </form>
            
            <div class="success-message" id="add-success"></div>
            <div class="error-message" id="add-error"></div>
        </div>

        <!-- 登録済みサイト一覧 -->
        <div id="sites-list">
            <h3>📋 登録済みサイト一覧</h3>
            <div class="loading" id="sites-loading">
                <span class="spinner"></span>読み込み中...
            </div>
            <div id="sites-container"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadSites();
            loadStats();
        });

        // サイト一覧を読み込み
        async function loadSites() {
            const loading = document.getElementById('sites-loading');
            const container = document.getElementById('sites-container');
            
            loading.style.display = 'block';
            
            try {
                const response = await fetch('wordpress_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=list_sites'
                });

                const result = await response.json();
                
                if (result.success) {
                    displaySites(result.sites);
                } else {
                    showError('サイト一覧の読み込みに失敗しました: ' + result.error);
                }
            } catch (error) {
                showError('サイト一覧の読み込み中にエラーが発生しました: ' + error.message);
            } finally {
                loading.style.display = 'none';
            }
        }

        // サイト一覧を表示
        function displaySites(sites) {
            const container = document.getElementById('sites-container');
            
            if (!sites || sites.length === 0) {
                container.innerHTML = '<div class="no-sites">登録されたサイトはありません。<br>上記フォームから最初のサイトを登録してください。</div>';
                return;
            }

            let html = '';
            sites.forEach(site => {
                const statusClass = site.status === 'active' ? 'status-active' : 'status-inactive';
                const lastConnected = site.last_connected ? new Date(site.last_connected).toLocaleString('ja-JP') : '未接続';
                
                html += `
                    <div class="site-card">
                        <div class="site-header">
                            <div class="site-name">${escapeHtml(site.name)}</div>
                            <div class="site-status ${statusClass}">${site.status === 'active' ? '稼働中' : '停止中'}</div>
                        </div>
                        <div class="site-info">
                            <strong>URL:</strong> <a href="${escapeHtml(site.url)}" target="_blank">${escapeHtml(site.url)}</a><br>
                            <strong>ユーザー:</strong> ${escapeHtml(site.username)}<br>
                            <strong>最終接続:</strong> ${lastConnected}<br>
                            ${site.description ? '<strong>説明:</strong> ' + escapeHtml(site.description) + '<br>' : ''}
                        </div>
                        <div class="site-actions">
                            <button class="btn" onclick="analyzeSite(${site.id}, '${escapeHtml(site.url)}')">🔍 SEO分析</button>
                            <button class="btn btn-warning" onclick="testSiteConnection(${site.id})">📡 接続確認</button>
                            <button class="btn btn-success" onclick="viewImprovements(${site.id})">📈 改善履歴</button>
                            <button class="btn" onclick="editSite(${site.id})">✏️ 編集</button>
                            <button class="btn btn-danger" onclick="deleteSite(${site.id}, '${escapeHtml(site.name)}')">🗑️ 削除</button>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }

        // 統計情報を読み込み
        async function loadStats() {
            try {
                const response = await fetch('wordpress_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=get_stats'
                });

                const result = await response.json();
                
                if (result.success) {
                    document.getElementById('total-sites').textContent = result.stats.total_sites || 0;
                    document.getElementById('active-sites').textContent = result.stats.active_sites || 0;
                    document.getElementById('total-improvements').textContent = result.stats.total_improvements || 0;
                }
            } catch (error) {
                console.error('統計情報の読み込みエラー:', error);
            }
        }

        // 新規サイト登録
        document.getElementById('add-site-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'add_site');
            
            try {
                const response = await fetch('wordpress_api.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if (result.success) {
                    showSuccess('サイトが正常に登録されました！');
                    this.reset();
                    loadSites();
                    loadStats();
                } else {
                    showError(result.error);
                }
            } catch (error) {
                showError('サイト登録中にエラーが発生しました: ' + error.message);
            }
        });

        // 接続テスト
        async function testConnection() {
            const url = document.getElementById('site-url').value;
            const username = document.getElementById('wp-username').value;
            const password = document.getElementById('wp-password').value;
            
            if (!url || !username || !password) {
                alert('URL、ユーザー名、パスワードを入力してください');
                return;
            }

            try {
                const formData = new FormData();
                formData.append('action', 'test_connection');
                formData.append('url', url);
                formData.append('username', username);
                formData.append('app_password', password);
                
                const response = await fetch('wordpress_api.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if (result.success) {
                    showSuccess('WordPress接続成功！ユーザー: ' + result.user);
                } else {
                    showError('接続失敗: ' + result.error);
                }
            } catch (error) {
                showError('接続テスト中にエラーが発生しました: ' + error.message);
            }
        }

        // サイト別接続テスト
        async function testSiteConnection(siteId) {
            try {
                const formData = new FormData();
                formData.append('action', 'test_site_connection');
                formData.append('site_id', siteId);
                
                const response = await fetch('wordpress_api.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if (result.success) {
                    alert('✅ 接続成功！\\nユーザー: ' + result.user + '\\n最終接続: ' + new Date().toLocaleString('ja-JP'));
                    loadSites(); // 最終接続時刻を更新
                } else {
                    alert('❌ 接続失敗: ' + result.error);
                }
            } catch (error) {
                alert('❌ 接続テスト中にエラーが発生しました: ' + error.message);
            }
        }

        // SEO分析実行
        function analyzeSite(siteId, siteUrl) {
            // SEO分析ページに遷移（サイト情報を渡す）
            window.open(`enhanced_seo_interface.php?site_id=${siteId}&url=${encodeURIComponent(siteUrl)}`, '_blank');
        }

        // 改善履歴表示
        function viewImprovements(siteId) {
            window.open(`improvement_history.php?site_id=${siteId}`, '_blank');
        }

        // サイト編集
        function editSite(siteId) {
            // 編集モーダルまたはページを表示
            alert('編集機能は今後実装予定です');
        }

        // サイト削除
        async function deleteSite(siteId, siteName) {
            if (!confirm(`サイト「${siteName}」を削除しますか？\\n\\n関連する分析履歴と改善履歴もすべて削除されます。\\nこの操作は取り消せません。`)) {
                return;
            }

            try {
                const formData = new FormData();
                formData.append('action', 'delete_site');
                formData.append('site_id', siteId);
                
                const response = await fetch('wordpress_api.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if (result.success) {
                    showSuccess('サイトが削除されました');
                    loadSites();
                    loadStats();
                } else {
                    showError('削除に失敗しました: ' + result.error);
                }
            } catch (error) {
                showError('削除中にエラーが発生しました: ' + error.message);
            }
        }

        // ユーティリティ関数
        function showSuccess(message) {
            const successEl = document.getElementById('add-success');
            successEl.textContent = '✅ ' + message;
            successEl.style.display = 'block';
            
            const errorEl = document.getElementById('add-error');
            errorEl.style.display = 'none';
            
            setTimeout(() => {
                successEl.style.display = 'none';
            }, 5000);
        }

        function showError(message) {
            const errorEl = document.getElementById('add-error');
            errorEl.textContent = '❌ ' + message;
            errorEl.style.display = 'block';
            
            const successEl = document.getElementById('add-success');
            successEl.style.display = 'none';
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
    </script>
</body>
</html>