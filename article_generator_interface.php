<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEO記事生成 & WordPress連携</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            max-width: 1400px;
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
        .wp-status {
            background-color: #e8f5e8;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            border-left: 4px solid #4caf50;
        }
        .wp-status.disconnected {
            background-color: #ffebee;
            border-left-color: #f44336;
        }
        .cluster-proposals {
            margin-bottom: 30px;
        }
        .proposal-card {
            background-color: #f9f9f9;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            position: relative;
        }
        .proposal-card.pillar {
            background-color: #fff3e0;
            border-color: #ff9800;
        }
        .proposal-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }
        .proposal-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            flex: 1;
        }
        .proposal-badge {
            background-color: #2196F3;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            margin-left: 10px;
        }
        .proposal-badge.pillar {
            background-color: #ff9800;
        }
        .proposal-meta {
            color: #666;
            margin: 10px 0;
            font-size: 14px;
        }
        .proposal-headings {
            margin: 15px 0;
            padding: 15px;
            background-color: white;
            border-radius: 5px;
            border: 1px solid #e0e0e0;
        }
        .proposal-headings h4 {
            margin-top: 0;
            color: #1976D2;
        }
        .heading-item {
            margin: 8px 0;
            padding-left: 20px;
            color: #555;
        }
        .proposal-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        .btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .btn-generate {
            background-color: #2196F3;
        }
        .btn-generate:hover {
            background-color: #1976D2;
        }
        .btn-wordpress {
            background-color: #673AB7;
        }
        .btn-wordpress:hover {
            background-color: #5E35B1;
        }
        .btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        .article-preview {
            display: none;
            margin-top: 20px;
            padding: 20px;
            background-color: white;
            border: 2px solid #4CAF50;
            border-radius: 8px;
        }
        .article-preview h3 {
            color: #4CAF50;
            margin-top: 0;
        }
        .article-content {
            max-height: 400px;
            overflow-y: auto;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
            margin: 15px 0;
        }
        .wp-options {
            background-color: #f0f4f8;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
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
        input[type="text"], select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
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
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 2s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .success-message {
            display: none;
            background-color: #e8f5e8;
            color: #2e7d32;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
            border-left: 4px solid #4caf50;
        }
        .error-message {
            display: none;
            background-color: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
            border-left: 4px solid #f44336;
        }
        .wp-config-section {
            background-color: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .config-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 15px;
        }
        @media (max-width: 768px) {
            .config-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include_once 'includes/navigation.php'; ?>
    <div class="container">

        <h1>✍️ SEO記事生成 & WordPress連携</h1>
        
        <!-- ワークフローガイダンス -->
        <div style="text-align: center; margin-bottom: 20px; padding: 15px; background: #e8f5e8; border-radius: 8px;">
            <p style="margin: 0; color: #2e7d32;">
                📝 <strong>コンテンツ作成フロー:</strong> 
                <a href="enhanced_seo_interface.php" style="color: #2e7d32;">①SEO分析</a> → 
                <a href="article_generator_interface.php" style="color: #2e7d32;">②記事生成</a> → 
                <a href="wordpress_manager.php" style="color: #2e7d32;">③WordPress投稿</a>
            </p>
        </div>

        <!-- WordPress接続状態 -->
        <div class="wp-status" id="wpStatus">
            <h3>📡 WordPress連携状況</h3>
            <div id="wpConnectionInfo">
                <p>接続状態を確認中...</p>
            </div>
        </div>

        <!-- WordPress設定セクション -->
        <div class="wp-config-section">
            <h3>⚙️ WordPress連携設定</h3>
            <div class="config-grid">
                <div class="form-group">
                    <label for="wp-url">WordPress サイトURL:</label>
                    <input type="text" id="wp-url" placeholder="https://yoursite.com">
                </div>
                <div class="form-group">
                    <label for="wp-username">ユーザー名:</label>
                    <input type="text" id="wp-username" placeholder="admin">
                </div>
                <div class="form-group">
                    <label for="wp-password">アプリケーションパスワード:</label>
                    <input type="text" id="wp-password" placeholder="xxxx xxxx xxxx xxxx xxxx xxxx">
                </div>
                <div class="form-group">
                    <button class="btn" onclick="saveWPConfig()">設定を保存</button>
                </div>
            </div>
        </div>

        <!-- トピッククラスター提案セクション -->
        <div class="cluster-proposals">
            <h2>🎯 トピッククラスター記事提案</h2>
            
            <!-- ピラーページ -->
            <div class="proposal-card pillar" id="pillar-proposal">
                <div class="proposal-header">
                    <span class="proposal-title">タイガー魔法瓶の水筒完全ガイド：選び方から使い方まで徹底解説</span>
                    <span class="proposal-badge pillar">ピラーページ</span>
                </div>
                <div class="proposal-meta">
                    メタディスクリプション: タイガー魔法瓶の水筒について、選び方、お手入れ方法、おすすめモデルまで完全網羅。あなたに最適な一本を見つけるための決定版ガイド。
                </div>
                <div class="proposal-headings">
                    <h4>見出し構成:</h4>
                    <div class="heading-item">H2: タイガー魔法瓶とは - ブランドの歴史と特徴</div>
                    <div class="heading-item">H2: タイガー水筒の種類と特徴</div>
                    <div class="heading-item">H3: ステンレスボトル</div>
                    <div class="heading-item">H3: スポーツボトル</div>
                    <div class="heading-item">H2: 選び方のポイント</div>
                    <div class="heading-item">H2: お手入れとメンテナンス</div>
                </div>
                <div class="proposal-actions">
                    <button class="btn btn-generate" onclick="generateArticle('pillar', 0)">
                        📝 記事を生成
                    </button>
                    <button class="btn btn-wordpress" onclick="showWordPressOptions('pillar', 0)" disabled id="wp-btn-pillar-0">
                        📤 WordPressに投稿
                    </button>
                </div>
                <div class="article-preview" id="preview-pillar-0"></div>
                <div class="wp-options" id="wp-options-pillar-0" style="display: none;"></div>
            </div>

            <!-- クラスターページ1 -->
            <div class="proposal-card" id="cluster-proposal-1">
                <div class="proposal-header">
                    <span class="proposal-title">タイガー水筒のサイズ選び完全ガイド｜用途別おすすめ容量</span>
                    <span class="proposal-badge">クラスター記事1</span>
                </div>
                <div class="proposal-meta">
                    メタディスクリプション: タイガー水筒のサイズ選びに迷っている方へ。350ml〜1.5Lまで、通勤・通学・スポーツなど用途別に最適な容量を徹底解説。
                </div>
                <div class="proposal-headings">
                    <h4>見出し構成:</h4>
                    <div class="heading-item">H2: タイガー水筒のサイズラインナップ</div>
                    <div class="heading-item">H2: 用途別おすすめサイズ</div>
                    <div class="heading-item">H3: 通勤・通学用（350-500ml）</div>
                    <div class="heading-item">H3: スポーツ・アウトドア用（800ml-1.5L）</div>
                    <div class="heading-item">H2: サイズ選びのチェックポイント</div>
                </div>
                <div class="proposal-actions">
                    <button class="btn btn-generate" onclick="generateArticle('cluster', 1)">
                        📝 記事を生成
                    </button>
                    <button class="btn btn-wordpress" onclick="showWordPressOptions('cluster', 1)" disabled id="wp-btn-cluster-1">
                        📤 WordPressに投稿
                    </button>
                </div>
                <div class="article-preview" id="preview-cluster-1"></div>
                <div class="wp-options" id="wp-options-cluster-1" style="display: none;"></div>
            </div>

            <!-- クラスターページ2 -->
            <div class="proposal-card" id="cluster-proposal-2">
                <div class="proposal-header">
                    <span class="proposal-title">タイガー水筒の洗い方｜清潔に保つお手入れ方法を徹底解説</span>
                    <span class="proposal-badge">クラスター記事2</span>
                </div>
                <div class="proposal-meta">
                    メタディスクリプション: タイガー水筒を清潔に長持ちさせる正しい洗い方を解説。パッキンの外し方、茶渋の落とし方、消臭方法まで完全網羅。
                </div>
                <div class="proposal-headings">
                    <h4>見出し構成:</h4>
                    <div class="heading-item">H2: 日常のお手入れ方法</div>
                    <div class="heading-item">H2: パーツ別の洗い方</div>
                    <div class="heading-item">H3: パッキンの取り外しと洗浄</div>
                    <div class="heading-item">H3: 中栓の分解清掃</div>
                    <div class="heading-item">H2: 頑固な汚れ・臭いの対処法</div>
                </div>
                <div class="proposal-actions">
                    <button class="btn btn-generate" onclick="generateArticle('cluster', 2)">
                        📝 記事を生成
                    </button>
                    <button class="btn btn-wordpress" onclick="showWordPressOptions('cluster', 2)" disabled id="wp-btn-cluster-2">
                        📤 WordPressに投稿
                    </button>
                </div>
                <div class="article-preview" id="preview-cluster-2"></div>
                <div class="wp-options" id="wp-options-cluster-2" style="display: none;"></div>
            </div>

            <!-- 記事生成の進捗表示 -->
            <div class="loading" id="loadingIndicator">
                <div class="spinner"></div>
                <p>記事を生成中...</p>
            </div>

            <div class="success-message" id="successMessage"></div>
            <div class="error-message" id="errorMessage"></div>
        </div>
    </div>

    <script>
        // 記事データの管理
        let generatedArticles = {};
        let wpConnected = false;

        // ページ読み込み時にWordPress接続確認
        document.addEventListener('DOMContentLoaded', function() {
            checkWordPressConnection();
            loadSavedWPConfig();
        });

        // WordPress設定を読み込み
        function loadSavedWPConfig() {
            const wpUrl = localStorage.getItem('wp_url');
            const wpUsername = localStorage.getItem('wp_username');
            
            if (wpUrl) document.getElementById('wp-url').value = wpUrl;
            if (wpUsername) document.getElementById('wp-username').value = wpUsername;
        }

        // WordPress設定を保存
        function saveWPConfig() {
            const wpUrl = document.getElementById('wp-url').value;
            const wpUsername = document.getElementById('wp-username').value;
            const wpPassword = document.getElementById('wp-password').value;
            
            if (!wpUrl || !wpUsername || !wpPassword) {
                showError('すべての項目を入力してください');
                return;
            }
            
            localStorage.setItem('wp_url', wpUrl);
            localStorage.setItem('wp_username', wpUsername);
            localStorage.setItem('wp_password', wpPassword);
            
            showSuccess('WordPress設定を保存しました');
            checkWordPressConnection();
        }

        // WordPress接続確認
        async function checkWordPressConnection() {
            try {
                const response = await fetch('article_generator_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=check_wordpress_connection'
                });
                
                const result = await response.json();
                const wpStatus = document.getElementById('wpStatus');
                const wpInfo = document.getElementById('wpConnectionInfo');
                
                if (result.connected) {
                    wpConnected = true;
                    wpStatus.className = 'wp-status';
                    wpInfo.innerHTML = `
                        <p>✅ <strong>接続状態:</strong> 接続済み</p>
                        <p><strong>サイト:</strong> ${result.site_url}</p>
                        <p><strong>ユーザー:</strong> ${result.user}</p>
                    `;
                    
                    // WordPress投稿ボタンを有効化
                    document.querySelectorAll('[id^="wp-btn-"]').forEach(btn => {
                        if (btn.dataset.articleGenerated === 'true') {
                            btn.disabled = false;
                        }
                    });
                } else {
                    wpConnected = false;
                    wpStatus.className = 'wp-status disconnected';
                    wpInfo.innerHTML = `
                        <p>❌ <strong>接続状態:</strong> 未接続</p>
                        <p>${result.message}</p>
                    `;
                }
            } catch (error) {
                console.error('WordPress接続確認エラー:', error);
            }
        }

        // 記事生成
        async function generateArticle(type, index) {
            const loadingIndicator = document.getElementById('loadingIndicator');
            loadingIndicator.style.display = 'block';
            hideMessages();
            
            // 提案データを取得（実際の実装では動的に取得）
            const proposals = getProposalData();
            const proposal = type === 'pillar' ? proposals.pillar : proposals.clusters[index - 1];
            
            try {
                const formData = new FormData();
                formData.append('action', 'generate_article');
                formData.append('title', proposal.title);
                formData.append('meta_description', proposal.meta_description);
                formData.append('headings', JSON.stringify(proposal.headings));
                formData.append('relation', proposal.relation || '');
                formData.append('main_keyword', 'タイガー水筒');
                formData.append('article_type', type);
                
                const response = await fetch('article_generator_api.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // 記事を保存
                    const articleKey = `${type}-${index}`;
                    generatedArticles[articleKey] = result;
                    
                    // プレビュー表示
                    showArticlePreview(type, index, result);
                    
                    // WordPress投稿ボタンを有効化
                    const wpBtn = document.getElementById(`wp-btn-${type}-${index}`);
                    wpBtn.disabled = !wpConnected;
                    wpBtn.dataset.articleGenerated = 'true';
                    
                    showSuccess('記事の生成が完了しました！');
                } else {
                    showError(result.error || '記事生成に失敗しました');
                }
            } catch (error) {
                showError('記事生成中にエラーが発生しました: ' + error.message);
            } finally {
                loadingIndicator.style.display = 'none';
            }
        }

        // 記事プレビュー表示
        function showArticlePreview(type, index, article) {
            const preview = document.getElementById(`preview-${type}-${index}`);
            preview.innerHTML = `
                <h3>📄 生成された記事プレビュー</h3>
                <div class="article-content">
                    ${article.content}
                </div>
                <p><strong>保存ファイル:</strong> ${article.saved_file}</p>
            `;
            preview.style.display = 'block';
        }

        // WordPress投稿オプション表示
        function showWordPressOptions(type, index) {
            const wpOptions = document.getElementById(`wp-options-${type}-${index}`);
            const articleKey = `${type}-${index}`;
            const article = generatedArticles[articleKey];
            
            if (!article) {
                showError('先に記事を生成してください');
                return;
            }
            
            wpOptions.innerHTML = `
                <h4>WordPress投稿設定</h4>
                <div class="form-group">
                    <label>投稿ステータス:</label>
                    <select id="wp-status-${articleKey}">
                        <option value="draft">下書き</option>
                        <option value="publish">公開</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>カテゴリー（カンマ区切り）:</label>
                    <input type="text" id="wp-categories-${articleKey}" placeholder="例: SEO, コンテンツマーケティング">
                </div>
                <div class="form-group">
                    <label>タグ（カンマ区切り）:</label>
                    <input type="text" id="wp-tags-${articleKey}" placeholder="例: タイガー水筒, 魔法瓶">
                </div>
                <button class="btn btn-wordpress" onclick="publishToWordPress('${type}', ${index})">
                    WordPressに投稿する
                </button>
            `;
            
            wpOptions.style.display = wpOptions.style.display === 'none' ? 'block' : 'none';
        }

        // WordPressに投稿
        async function publishToWordPress(type, index) {
            const articleKey = `${type}-${index}`;
            const article = generatedArticles[articleKey];
            
            if (!article) {
                showError('記事データが見つかりません');
                return;
            }
            
            const status = document.getElementById(`wp-status-${articleKey}`).value;
            const categories = document.getElementById(`wp-categories-${articleKey}`).value.split(',').map(c => c.trim());
            const tags = document.getElementById(`wp-tags-${articleKey}`).value.split(',').map(t => t.trim());
            
            const loadingIndicator = document.getElementById('loadingIndicator');
            loadingIndicator.style.display = 'block';
            hideMessages();
            
            try {
                const formData = new FormData();
                formData.append('action', 'publish_to_wordpress');
                formData.append('title', article.title);
                formData.append('content', article.content);
                formData.append('status', status);
                formData.append('categories', JSON.stringify(categories));
                formData.append('tags', JSON.stringify(tags));
                formData.append('meta_description', article.meta_description);
                
                const response = await fetch('article_generator_api.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showSuccess(`WordPressに投稿しました！<br>投稿URL: <a href="${result.post_url}" target="_blank">${result.post_url}</a>`);
                } else {
                    showError(result.error || 'WordPress投稿に失敗しました');
                }
            } catch (error) {
                showError('WordPress投稿中にエラーが発生しました: ' + error.message);
            } finally {
                loadingIndicator.style.display = 'none';
            }
        }

        // 提案データ取得（実際の実装では動的）
        function getProposalData() {
            return {
                pillar: {
                    title: 'タイガー魔法瓶の水筒完全ガイド：選び方から使い方まで徹底解説',
                    meta_description: 'タイガー魔法瓶の水筒について、選び方、お手入れ方法、おすすめモデルまで完全網羅。あなたに最適な一本を見つけるための決定版ガイド。',
                    headings: [
                        'タイガー魔法瓶とは - ブランドの歴史と特徴',
                        'タイガー水筒の種類と特徴',
                        '選び方のポイント',
                        'お手入れとメンテナンス'
                    ]
                },
                clusters: [
                    {
                        title: 'タイガー水筒のサイズ選び完全ガイド｜用途別おすすめ容量',
                        meta_description: 'タイガー水筒のサイズ選びに迷っている方へ。350ml〜1.5Lまで、通勤・通学・スポーツなど用途別に最適な容量を徹底解説。',
                        headings: [
                            'タイガー水筒のサイズラインナップ',
                            '用途別おすすめサイズ',
                            'サイズ選びのチェックポイント'
                        ],
                        relation: 'ピラーページの「選び方のポイント」セクションを深掘りした専門記事'
                    },
                    {
                        title: 'タイガー水筒の洗い方｜清潔に保つお手入れ方法を徹底解説',
                        meta_description: 'タイガー水筒を清潔に長持ちさせる正しい洗い方を解説。パッキンの外し方、茶渋の落とし方、消臭方法まで完全網羅。',
                        headings: [
                            '日常のお手入れ方法',
                            'パーツ別の洗い方',
                            '頑固な汚れ・臭いの対処法'
                        ],
                        relation: 'ピラーページの「お手入れとメンテナンス」セクションの詳細解説'
                    }
                ]
            };
        }

        // メッセージ表示
        function showSuccess(message) {
            const successMsg = document.getElementById('successMessage');
            successMsg.innerHTML = message;
            successMsg.style.display = 'block';
        }

        function showError(message) {
            const errorMsg = document.getElementById('errorMessage');
            errorMsg.innerHTML = message;
            errorMsg.style.display = 'block';
        }

        function hideMessages() {
            document.getElementById('successMessage').style.display = 'none';
            document.getElementById('errorMessage').style.display = 'none';
        }
    </script>
</body>
</html>