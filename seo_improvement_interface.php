<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEO改善案適用 - SEO分析ツール</title>
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
        .improvement-section {
            background: #f9f9f9;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            border-left: 4px solid #4caf50;
        }
        .improvement-item {
            background: white;
            padding: 20px;
            margin: 15px 0;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        .improvement-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .improvement-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        .improvement-type {
            background: #2196F3;
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
        }
        .current-value, .improved-value {
            margin: 10px 0;
            padding: 15px;
            border-radius: 5px;
        }
        .current-value {
            background: #ffebee;
            border-left: 4px solid #f44336;
        }
        .improved-value {
            background: #e8f5e8;
            border-left: 4px solid #4caf50;
        }
        .improvement-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
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
        .btn-danger {
            background-color: #f44336;
        }
        .btn-danger:hover {
            background-color: #da190b;
        }
        .btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-applied {
            background-color: #d4edda;
            color: #155724;
        }
        .status-failed {
            background-color: #f8d7da;
            color: #721c24;
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
        select, textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        select:focus, textarea:focus {
            border-color: #4285f4;
            outline: none;
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
        .success-message, .error-message {
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            display: none;
        }
        .success-message {
            background-color: #e8f5e8;
            color: #2e7d32;
            border: 1px solid #4caf50;
        }
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #f44336;
        }
        .manual-improvement {
            background: #e3f2fd;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            border-left: 4px solid #2196F3;
        }
    </style>
</head>
<body>
    <?php include_once 'includes/navigation.php'; ?>
    <div class="container">

        <h1>🔧 SEO改善案の直接適用</h1>
        
        <!-- ワークフローガイド -->
        <div style="text-align: center; margin-bottom: 20px; padding: 15px; background: #e3f2fd; border-radius: 8px;">
            <p style="margin: 0; color: #1976d2;">
                🔄 <strong>推奨ワークフロー:</strong> 
                <a href="enhanced_seo_interface.php" style="color: #1976d2;">①SEO分析</a> → 
                <a href="seo_improvement_interface.php" style="color: #1976d2;">②改善適用</a> → 
                <a href="analytics_integration.php" style="color: #1976d2;">③効果測定</a>
            </p>
        </div>

        <!-- サイト選択 -->
        <div class="form-group">
            <label for="site-select">対象サイト:</label>
            <select id="site-select">
                <option value="">サイトを選択してください...</option>
            </select>
        </div>

        <!-- 手動改善入力 -->
        <div class="manual-improvement">
            <h3>📝 手動改善入力</h3>
            <div class="form-group">
                <label for="target-url">対象URL:</label>
                <input type="url" id="target-url" placeholder="https://yoursite.com/page" required>
            </div>
            
            <div class="form-group">
                <label for="improvement-type">改善タイプ:</label>
                <select id="improvement-type" required>
                    <option value="">選択してください...</option>
                    <option value="title">タイトル</option>
                    <option value="meta_description">メタディスクリプション</option>
                    <option value="content">コンテンツ</option>
                    <option value="excerpt">抜粋</option>
                    <option value="featured_image_alt">アイキャッチ画像のalt属性</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="improved-value">改善後の値:</label>
                <textarea id="improved-value" rows="4" placeholder="改善後の内容を入力してください" required></textarea>
            </div>
            
            <button class="btn btn-success" onclick="applyManualImprovement()">🚀 改善を適用</button>
            <a href="enhanced_seo_interface.php" class="btn" style="margin-left: 10px;">🔍 SEO分析で改善案を取得</a>
            
            <div class="success-message" id="manual-success"></div>
            <div class="error-message" id="manual-error"></div>
        </div>

        <!-- 改善履歴 -->
        <div class="improvement-section">
            <h3>📈 改善適用履歴</h3>
            <div class="loading" id="history-loading">
                <span class="spinner"></span>履歴を読み込み中...
            </div>
            <div id="improvements-container">
                <!-- JavaScriptで動的に生成 -->
            </div>
        </div>
    </div>

    <script>
        let currentSiteId = null;

        document.addEventListener('DOMContentLoaded', function() {
            loadSites();
            
            // URLパラメータからサイトIDを取得
            const urlParams = new URLSearchParams(window.location.search);
            const siteId = urlParams.get('site_id');
            if (siteId) {
                currentSiteId = siteId;
                setTimeout(() => {
                    document.getElementById('site-select').value = siteId;
                    loadImprovements();
                }, 500);
            }
        });

        // サイト一覧を読み込み
        async function loadSites() {
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

        // サイト選択時の処理
        document.getElementById('site-select').addEventListener('change', function() {
            currentSiteId = this.value;
            if (currentSiteId) {
                loadImprovements();
            } else {
                document.getElementById('improvements-container').innerHTML = '';
            }
        });

        // 手動改善適用
        async function applyManualImprovement() {
            const siteId = currentSiteId;
            const targetUrl = document.getElementById('target-url').value;
            const improvementType = document.getElementById('improvement-type').value;
            const improvedValue = document.getElementById('improved-value').value;
            
            if (!siteId || !targetUrl || !improvementType || !improvedValue) {
                showManualError('すべての項目を入力してください');
                return;
            }

            try {
                const formData = new FormData();
                formData.append('action', 'apply_seo_improvement');
                formData.append('site_id', siteId);
                formData.append('url', targetUrl);
                formData.append('improvement_type', improvementType);
                formData.append('improved_value', improvedValue);
                
                const response = await fetch('wordpress_api.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if (result.success) {
                    showManualSuccess('改善が正常に適用されました！');
                    // フォームをリセット
                    document.getElementById('target-url').value = '';
                    document.getElementById('improvement-type').value = '';
                    document.getElementById('improved-value').value = '';
                    // 履歴を再読み込み
                    loadImprovements();
                } else {
                    showManualError('改善の適用に失敗しました: ' + result.error);
                }
            } catch (error) {
                showManualError('エラーが発生しました: ' + error.message);
            }
        }

        // 改善履歴を読み込み
        async function loadImprovements() {
            if (!currentSiteId) return;
            
            const loading = document.getElementById('history-loading');
            const container = document.getElementById('improvements-container');
            
            loading.style.display = 'block';
            
            try {
                const formData = new FormData();
                formData.append('action', 'get_improvements');
                formData.append('site_id', currentSiteId);
                
                const response = await fetch('wordpress_api.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if (result.success) {
                    displayImprovements(result.improvements);
                } else {
                    container.innerHTML = '<div style="text-align: center; color: #666;">改善履歴の読み込みに失敗しました</div>';
                }
            } catch (error) {
                container.innerHTML = '<div style="text-align: center; color: #666;">改善履歴の読み込み中にエラーが発生しました</div>';
            } finally {
                loading.style.display = 'none';
            }
        }

        // 改善履歴を表示
        function displayImprovements(improvements) {
            const container = document.getElementById('improvements-container');
            
            if (!improvements || improvements.length === 0) {
                container.innerHTML = '<div style="text-align: center; color: #666; padding: 40px;">まだ改善が適用されていません</div>';
                return;
            }

            let html = '';
            improvements.forEach(improvement => {
                const statusClass = getStatusClass(improvement.status);
                const statusText = getStatusText(improvement.status);
                const createdAt = new Date(improvement.created_at).toLocaleString('ja-JP');
                const appliedAt = improvement.applied_at ? new Date(improvement.applied_at).toLocaleString('ja-JP') : '-';
                
                html += `
                    <div class="improvement-item">
                        <div class="improvement-header">
                            <div class="improvement-title">${getImprovementTypeText(improvement.improvement_type)}</div>
                            <div class="improvement-type">${improvement.improvement_type}</div>
                        </div>
                        
                        <div style="margin-bottom: 10px;">
                            <strong>対象URL:</strong> <a href="${escapeHtml(improvement.url)}" target="_blank">${escapeHtml(improvement.url)}</a>
                        </div>
                        
                        ${improvement.original_value ? 
                            `<div class="current-value">
                                <strong>変更前:</strong><br>${escapeHtml(improvement.original_value)}
                            </div>` : ''
                        }
                        
                        <div class="improved-value">
                            <strong>変更後:</strong><br>${escapeHtml(improvement.improved_value)}
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                            <div>
                                <span class="status-badge ${statusClass}">${statusText}</span>
                                <small style="color: #666; margin-left: 15px;">
                                    作成: ${createdAt} | 適用: ${appliedAt}
                                </small>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }

        // ステータスクラスを取得
        function getStatusClass(status) {
            switch (status) {
                case 'applied': return 'status-applied';
                case 'failed': return 'status-failed';
                default: return 'status-pending';
            }
        }

        // ステータステキストを取得
        function getStatusText(status) {
            switch (status) {
                case 'applied': return '適用済み';
                case 'failed': return '失敗';
                default: return '保留中';
            }
        }

        // 改善タイプテキストを取得
        function getImprovementTypeText(type) {
            switch (type) {
                case 'title': return 'タイトル改善';
                case 'meta_description': return 'メタディスクリプション改善';
                case 'content': return 'コンテンツ改善';
                case 'excerpt': return '抜粋改善';
                case 'featured_image_alt': return 'アイキャッチ画像alt属性改善';
                default: return type;
            }
        }

        // 成功メッセージ表示
        function showManualSuccess(message) {
            const successEl = document.getElementById('manual-success');
            successEl.textContent = '✅ ' + message;
            successEl.style.display = 'block';
            
            const errorEl = document.getElementById('manual-error');
            errorEl.style.display = 'none';
            
            setTimeout(() => {
                successEl.style.display = 'none';
            }, 5000);
        }

        // エラーメッセージ表示
        function showManualError(message) {
            const errorEl = document.getElementById('manual-error');
            errorEl.textContent = '❌ ' + message;
            errorEl.style.display = 'block';
            
            const successEl = document.getElementById('manual-success');
            successEl.style.display = 'none';
        }

        // HTMLエスケープ
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