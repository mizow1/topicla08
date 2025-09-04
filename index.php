<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>統合SEO分析・WordPress管理ツール</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .hero-section {
            text-align: center;
            padding: 60px 20px;
            color: white;
        }
        
        .hero-section h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .hero-section p {
            font-size: 1.2rem;
            margin-bottom: 40px;
            opacity: 0.9;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }
        
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #4caf50, #2196F3, #ff9800, #e91e63);
        }
        
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            display: block;
        }
        
        .feature-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
        }
        
        .feature-description {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        .feature-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #4caf50, #45a049);
            color: white;
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, #45a049, #4caf50);
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #f5f5f5;
            color: #333;
            border: 2px solid #ddd;
        }
        
        .btn-secondary:hover {
            background: #e0e0e0;
            border-color: #ccc;
        }
        
        .status-indicators {
            display: flex;
            gap: 15px;
            margin: 20px 0;
            flex-wrap: wrap;
        }
        
        .status-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #f9f9f9;
            border-radius: 20px;
            font-size: 14px;
        }
        
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }
        
        .status-ready {
            background-color: #4caf50;
        }
        
        .status-setup {
            background-color: #ff9800;
        }
        
        .workflow-section {
            background: rgba(255,255,255,0.95);
            border-radius: 15px;
            padding: 40px;
            margin: 40px 0;
            backdrop-filter: blur(10px);
        }
        
        .workflow-steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }
        
        .workflow-step {
            text-align: center;
            position: relative;
        }
        
        .workflow-step:not(:last-child)::after {
            content: '→';
            position: absolute;
            right: -15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 2rem;
            color: #4caf50;
        }
        
        .step-number {
            width: 50px;
            height: 50px;
            background: linear-gradient(45deg, #4caf50, #45a049);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .step-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        
        .step-description {
            color: #666;
            font-size: 14px;
        }
        
        .quick-actions {
            background: rgba(255,255,255,0.95);
            border-radius: 15px;
            padding: 30px;
            margin: 30px 0;
            backdrop-filter: blur(10px);
        }
        
        .quick-actions h3 {
            margin-bottom: 20px;
            color: #333;
        }
        
        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .footer {
            text-align: center;
            padding: 40px;
            color: rgba(255,255,255,0.8);
        }
        
        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 2rem;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
            }
            
            .workflow-step:not(:last-child)::after {
                content: '↓';
                right: auto;
                top: auto;
                bottom: -15px;
                left: 50%;
                transform: translateX(-50%);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Hero Section -->
        <div class="hero-section">
            <h1>🚀 統合SEO分析・WordPress管理ツール</h1>
            <p>WordPress サイトの SEO を分析し、改善案を直接適用できる総合プラットフォーム</p>
        </div>

        <!-- Main Features -->
        <div class="features-grid">
            <!-- WordPress Site Management -->
            <div class="feature-card">
                <span class="feature-icon">🏢</span>
                <h3 class="feature-title">WordPress サイト管理</h3>
                <p class="feature-description">
                    複数のWordPressサイトを一元管理。安全な認証情報の暗号化保存と接続テスト機能で、
                    すべてのサイトを効率的に管理できます。
                </p>
                <div class="status-indicators">
                    <div class="status-item">
                        <span class="status-dot status-ready"></span>
                        <span>準備完了</span>
                    </div>
                </div>
                <div class="feature-actions">
                    <a href="wordpress_manager.php" class="btn btn-primary">📝 サイトを管理</a>
                    <a href="settings.php" class="btn btn-secondary">⚙️ 初期設定</a>
                </div>
            </div>

            <!-- SEO Analysis -->
            <div class="feature-card">
                <span class="feature-icon">🔍</span>
                <h3 class="feature-title">SEO 分析エンジン</h3>
                <p class="feature-description">
                    Claude AI を活用した高度なSEO分析。トピッククラスター戦略、競合分析、
                    Google Analytics連携でデータドリブンな改善提案を生成します。
                </p>
                <div class="status-indicators">
                    <div class="status-item">
                        <span class="status-dot status-ready"></span>
                        <span>分析準備完了</span>
                    </div>
                    <div class="status-item">
                        <span class="status-dot status-setup"></span>
                        <span>API設定推奨</span>
                    </div>
                </div>
                <div class="feature-actions">
                    <a href="enhanced_seo_interface.php" class="btn btn-primary">🚀 分析開始</a>
                    <a href="async_web_interface.php" class="btn btn-secondary">🔍 基本分析</a>
                </div>
            </div>

            <!-- SEO Improvement Application -->
            <div class="feature-card">
                <span class="feature-icon">🔧</span>
                <h3 class="feature-title">改善案の直接適用</h3>
                <p class="feature-description">
                    SEO分析結果をWordPressに直接適用。タイトル、メタディスクリプション、
                    コンテンツの最適化を自動化し、改善履歴を詳細に管理できます。
                </p>
                <div class="status-indicators">
                    <div class="status-item">
                        <span class="status-dot status-ready"></span>
                        <span>適用機能有効</span>
                    </div>
                </div>
                <div class="feature-actions">
                    <a href="seo_improvement_interface.php" class="btn btn-primary">🔧 改善を適用</a>
                    <a href="wordpress_manager.php" class="btn btn-secondary">📊 履歴確認</a>
                </div>
            </div>

            <!-- Content Generation -->
            <div class="feature-card">
                <span class="feature-icon">✍️</span>
                <h3 class="feature-title">AI記事生成</h3>
                <p class="feature-description">
                    SEO最適化された記事を自動生成。キーワード戦略に基づいたコンテンツ作成で、
                    検索エンジンでの上位表示を目指します。
                </p>
                <div class="status-indicators">
                    <div class="status-item">
                        <span class="status-dot status-setup"></span>
                        <span>API設定必要</span>
                    </div>
                </div>
                <div class="feature-actions">
                    <a href="article_generator_interface.php" class="btn btn-primary">✍️ 記事作成</a>
                    <a href="settings.php" class="btn btn-secondary">⚙️ API設定</a>
                </div>
            </div>

            <!-- Analytics Integration -->
            <div class="feature-card">
                <span class="feature-icon">📊</span>
                <h3 class="feature-title">Analytics 連携</h3>
                <p class="feature-description">
                    Google Analytics と Search Console のデータを統合。
                    実際のパフォーマンスデータに基づいた詳細な分析とレポートを提供します。
                </p>
                <div class="status-indicators">
                    <div class="status-item">
                        <span class="status-dot status-setup"></span>
                        <span>連携設定推奨</span>
                    </div>
                </div>
                <div class="feature-actions">
                    <a href="analytics_integration.php" class="btn btn-primary">📈 連携設定</a>
                    <a href="enhanced_seo_interface.php" class="btn btn-secondary">📊 分析実行</a>
                </div>
            </div>

            <!-- Settings & Configuration -->
            <div class="feature-card">
                <span class="feature-icon">⚙️</span>
                <h3 class="feature-title">システム設定</h3>
                <p class="feature-description">
                    Claude API、Google API、WordPress認証情報などを安全に管理。
                    暗号化された設定保存で、セキュリティを確保しながら効率的に運用できます。
                </p>
                <div class="status-indicators">
                    <div class="status-item">
                        <span class="status-dot status-setup"></span>
                        <span>初期設定推奨</span>
                    </div>
                </div>
                <div class="feature-actions">
                    <a href="settings.php" class="btn btn-primary">⚙️ 設定管理</a>
                    <a href="#" onclick="runSystemCheck()" class="btn btn-secondary">🔍 接続確認</a>
                </div>
            </div>
        </div>

        <!-- Workflow Section -->
        <div class="workflow-section">
            <h2 style="text-align: center; margin-bottom: 20px; color: #333;">📋 推奨ワークフロー</h2>
            <p style="text-align: center; color: #666; margin-bottom: 30px;">
                効果的なSEO改善のための推奨手順です
            </p>
            
            <div class="workflow-steps">
                <div class="workflow-step">
                    <div class="step-number">1</div>
                    <div class="step-title">初期設定</div>
                    <div class="step-description">
                        API設定とWordPressサイトの登録を完了
                    </div>
                </div>
                
                <div class="workflow-step">
                    <div class="step-number">2</div>
                    <div class="step-title">SEO分析</div>
                    <div class="step-description">
                        対象サイト・ページのSEO状況を詳細分析
                    </div>
                </div>
                
                <div class="workflow-step">
                    <div class="step-number">3</div>
                    <div class="step-title">改善適用</div>
                    <div class="step-description">
                        分析結果を基にWordPressに直接改善を適用
                    </div>
                </div>
                
                <div class="workflow-step">
                    <div class="step-number">4</div>
                    <div class="step-title">効果測定</div>
                    <div class="step-description">
                        Analytics連携で改善効果を継続的に監視
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <h3>🚀 クイックアクション</h3>
            <div class="action-buttons">
                <a href="enhanced_seo_interface.php" class="btn btn-primary">今すぐSEO分析</a>
                <a href="wordpress_manager.php" class="btn btn-primary">サイト登録</a>
                <a href="settings.php" class="btn btn-secondary">API設定</a>
                <a href="analytics_integration.php" class="btn btn-secondary">Analytics連携</a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>🤖 Powered by Claude AI | 🔒 セキュアな認証情報管理 | 📈 データドリブンなSEO改善</p>
        </div>
    </div>

    <script>
        // システムチェック機能
        async function runSystemCheck() {
            if (confirm('システムの接続状況をチェックしますか？\nClaude API、WordPress接続などの状態を確認します。')) {
                alert('システムチェック機能は準備中です。\n各設定画面で個別に接続テストを実行してください。');
                // 実装予定：各APIの接続状況を一括チェック
            }
        }

        // ページ読み込み時の処理
        document.addEventListener('DOMContentLoaded', function() {
            // 設定状況の確認（簡易版）
            checkConfigStatus();
        });

        // 設定状況を確認
        async function checkConfigStatus() {
            try {
                // 設定ファイルの存在確認（簡易版）
                const response = await fetch('settings_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=load_settings'
                });

                const result = await response.json();
                
                if (result.success && result.settings) {
                    // Claude APIの設定状況を反映
                    updateStatusIndicator('claude-api', result.settings.claude_api_key ? 'ready' : 'setup');
                    updateStatusIndicator('google-api', result.settings.google_api_key ? 'ready' : 'setup');
                }
            } catch (error) {
                console.log('設定状況の確認をスキップしました');
            }
        }

        // ステータスインジケーターを更新
        function updateStatusIndicator(apiType, status) {
            // 今後の拡張用：動的なステータス表示更新
        }

        // スムーススクロール
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>