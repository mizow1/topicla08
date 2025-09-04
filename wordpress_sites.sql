-- WordPressサイト管理用データベーススキーマ
-- SQLiteでの実装

CREATE TABLE IF NOT EXISTS wordpress_sites (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,                    -- サイト名（管理用）
    url TEXT NOT NULL UNIQUE,              -- WordPressサイトURL
    username TEXT NOT NULL,                -- WordPressユーザー名
    app_password TEXT NOT NULL,            -- アプリケーションパスワード（暗号化保存）
    description TEXT,                      -- サイト説明
    status TEXT DEFAULT 'active',          -- active, inactive
    last_connected DATETIME,               -- 最後の接続日時
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- SEO改善履歴テーブル
CREATE TABLE IF NOT EXISTS seo_improvements (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    site_id INTEGER NOT NULL,
    url TEXT NOT NULL,                     -- 分析対象URL
    improvement_type TEXT NOT NULL,        -- title, meta_description, content, images等
    original_value TEXT,                   -- 元の値
    improved_value TEXT,                   -- 改善後の値
    status TEXT DEFAULT 'pending',         -- pending, applied, failed
    applied_at DATETIME,                   -- 適用日時
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (site_id) REFERENCES wordpress_sites (id) ON DELETE CASCADE
);

-- 分析結果履歴テーブル
CREATE TABLE IF NOT EXISTS analysis_history (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    site_id INTEGER NOT NULL,
    url TEXT NOT NULL,                     -- 分析対象URL
    analysis_data TEXT,                    -- JSON形式の分析結果
    seo_analysis TEXT,                     -- SEO分析結果
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (site_id) REFERENCES wordpress_sites (id) ON DELETE CASCADE
);

-- インデックス作成
CREATE INDEX IF NOT EXISTS idx_wordpress_sites_url ON wordpress_sites (url);
CREATE INDEX IF NOT EXISTS idx_seo_improvements_site_id ON seo_improvements (site_id);
CREATE INDEX IF NOT EXISTS idx_seo_improvements_status ON seo_improvements (status);
CREATE INDEX IF NOT EXISTS idx_analysis_history_site_id ON analysis_history (site_id);
CREATE INDEX IF NOT EXISTS idx_analysis_history_url ON analysis_history (url);