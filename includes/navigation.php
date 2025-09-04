<?php
// 現在のページを取得
$current_page = basename($_SERVER['PHP_SELF']);

// ナビゲーション項目の定義
$nav_items = [
    [
        'url' => 'index.php',
        'icon' => '🏠',
        'title' => 'ホーム',
        'pages' => ['index.php']
    ],
    [
        'url' => 'wordpress_manager.php',
        'icon' => '🏢',
        'title' => 'サイト管理',
        'pages' => ['wordpress_manager.php']
    ],
    [
        'url' => 'enhanced_seo_interface.php',
        'icon' => '🚀',
        'title' => 'SEO分析',
        'pages' => ['enhanced_seo_interface.php', 'async_web_interface.php']
    ],
    [
        'url' => 'seo_improvement_interface.php',
        'icon' => '🔧',
        'title' => '改善適用',
        'pages' => ['seo_improvement_interface.php']
    ],
    [
        'url' => 'article_generator_interface.php',
        'icon' => '✍️',
        'title' => '記事生成',
        'pages' => ['article_generator_interface.php']
    ],
    [
        'url' => 'analytics_integration.php',
        'icon' => '📊',
        'title' => 'Analytics',
        'pages' => ['analytics_integration.php']
    ],
    [
        'url' => 'settings.php',
        'icon' => '⚙️',
        'title' => '設定',
        'pages' => ['settings.php']
    ]
];

// アクティブページを特定
function isActivePage($nav_item, $current_page) {
    return in_array($current_page, $nav_item['pages']);
}
?>

<style>
.unified-nav {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 15px 0;
    margin-bottom: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.nav-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.nav-brand {
    color: white;
    font-size: 1.5rem;
    font-weight: bold;
    text-decoration: none;
    margin-bottom: 15px;
    display: block;
    text-align: center;
}

.nav-links {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 10px;
}

.nav-links a {
    color: rgba(255,255,255,0.9);
    text-decoration: none;
    padding: 8px 16px;
    border-radius: 25px;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.nav-links a:hover {
    background: rgba(255,255,255,0.2);
    transform: translateY(-2px);
}

.nav-links a.active {
    background: rgba(255,255,255,0.3);
    color: white;
    font-weight: bold;
}

.nav-icon {
    font-size: 16px;
}

@media (max-width: 768px) {
    .nav-links {
        justify-content: center;
    }
    
    .nav-links a {
        flex: 1;
        min-width: 120px;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .nav-links {
        flex-direction: column;
    }
    
    .nav-links a {
        width: 100%;
        text-align: center;
    }
}

/* パンくずナビゲーション */
.breadcrumb {
    background: rgba(255,255,255,0.9);
    padding: 10px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    backdrop-filter: blur(5px);
}

.breadcrumb-items {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: #666;
}

.breadcrumb-items a {
    color: #4285f4;
    text-decoration: none;
}

.breadcrumb-items a:hover {
    text-decoration: underline;
}

.breadcrumb-separator {
    color: #999;
}
</style>

<nav class="unified-nav">
    <div class="nav-container">
        <a href="index.php" class="nav-brand">
            🚀 統合SEO・WordPress管理ツール
        </a>
        
        <div class="nav-links">
            <?php foreach ($nav_items as $item): ?>
                <a href="<?php echo $item['url']; ?>" 
                   class="<?php echo isActivePage($item, $current_page) ? 'active' : ''; ?>">
                    <span class="nav-icon"><?php echo $item['icon']; ?></span>
                    <span><?php echo $item['title']; ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</nav>

<?php
// パンくずナビゲーション生成
function generateBreadcrumb($current_page) {
    $breadcrumbs = [
        'index.php' => [
            ['title' => 'ホーム', 'url' => '']
        ],
        'wordpress_manager.php' => [
            ['title' => 'ホーム', 'url' => 'index.php'],
            ['title' => 'サイト管理', 'url' => '']
        ],
        'enhanced_seo_interface.php' => [
            ['title' => 'ホーム', 'url' => 'index.php'],
            ['title' => 'SEO分析', 'url' => '']
        ],
        'seo_improvement_interface.php' => [
            ['title' => 'ホーム', 'url' => 'index.php'],
            ['title' => '改善適用', 'url' => '']
        ],
        'article_generator_interface.php' => [
            ['title' => 'ホーム', 'url' => 'index.php'],
            ['title' => '記事生成', 'url' => '']
        ],
        'analytics_integration.php' => [
            ['title' => 'ホーム', 'url' => 'index.php'],
            ['title' => 'Analytics連携', 'url' => '']
        ],
        'settings.php' => [
            ['title' => 'ホーム', 'url' => 'index.php'],
            ['title' => '設定', 'url' => '']
        ]
    ];
    
    if (isset($breadcrumbs[$current_page])) {
        echo '<div class="breadcrumb">';
        echo '<div class="breadcrumb-items">';
        
        foreach ($breadcrumbs[$current_page] as $index => $crumb) {
            if ($index > 0) {
                echo '<span class="breadcrumb-separator">›</span>';
            }
            
            if ($crumb['url']) {
                echo '<a href="' . $crumb['url'] . '">' . $crumb['title'] . '</a>';
            } else {
                echo '<strong>' . $crumb['title'] . '</strong>';
            }
        }
        
        echo '</div>';
        echo '</div>';
    }
}

generateBreadcrumb($current_page);
?>

<!-- クイックアクションボタン -->
<?php if ($current_page !== 'index.php'): ?>
<div style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;">
    <div style="display: flex; flex-direction: column; gap: 10px;">
        <a href="index.php" 
           style="background: #4285f4; color: white; padding: 12px; border-radius: 50%; text-decoration: none; box-shadow: 0 4px 20px rgba(0,0,0,0.2); transition: all 0.3s ease;"
           onmouseover="this.style.transform='scale(1.1)'" 
           onmouseout="this.style.transform='scale(1)'">
            🏠
        </a>
        
        <?php if ($current_page !== 'enhanced_seo_interface.php'): ?>
        <a href="enhanced_seo_interface.php" 
           style="background: #4caf50; color: white; padding: 12px; border-radius: 50%; text-decoration: none; box-shadow: 0 4px 20px rgba(0,0,0,0.2); transition: all 0.3s ease;"
           onmouseover="this.style.transform='scale(1.1)'" 
           onmouseout="this.style.transform='scale(1)'">
            🚀
        </a>
        <?php endif; ?>
        
        <?php if ($current_page !== 'wordpress_manager.php'): ?>
        <a href="wordpress_manager.php" 
           style="background: #ff9800; color: white; padding: 12px; border-radius: 50%; text-decoration: none; box-shadow: 0 4px 20px rgba(0,0,0,0.2); transition: all 0.3s ease;"
           onmouseover="this.style.transform='scale(1.1)'" 
           onmouseout="this.style.transform='scale(1)'">
            🏢
        </a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<script>
// ナビゲーション関連の共通JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // アクティブページのハイライト強化
    const activeLink = document.querySelector('.nav-links a.active');
    if (activeLink) {
        activeLink.style.boxShadow = '0 0 20px rgba(255,255,255,0.3)';
    }
    
    // モバイルでの操作性向上
    if (window.innerWidth <= 768) {
        const navLinks = document.querySelectorAll('.nav-links a');
        navLinks.forEach(link => {
            link.addEventListener('touchstart', function() {
                this.style.background = 'rgba(255,255,255,0.3)';
            });
            
            link.addEventListener('touchend', function() {
                setTimeout(() => {
                    if (!this.classList.contains('active')) {
                        this.style.background = 'rgba(255,255,255,0.1)';
                    }
                }, 150);
            });
        });
    }
});
</script>