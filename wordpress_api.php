<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add_site':
        addSite();
        break;
        
    case 'list_sites':
        listSites();
        break;
        
    case 'delete_site':
        deleteSite();
        break;
        
    case 'test_connection':
        testConnection();
        break;
        
    case 'test_site_connection':
        testSiteConnection();
        break;
        
    case 'get_stats':
        getStats();
        break;
        
    case 'apply_seo_improvement':
        applySeoImprovement();
        break;
        
    case 'get_improvements':
        getImprovements();
        break;
        
    default:
        echo json_encode(['error' => 'Invalid action']);
}

/**
 * データベース接続を取得
 */
function getDatabase() {
    $dbFile = __DIR__ . '/wordpress_sites.db';
    
    try {
        $pdo = new PDO('sqlite:' . $dbFile);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // テーブルが存在しない場合は作成
        initializeDatabase($pdo);
        
        return $pdo;
    } catch (PDOException $e) {
        throw new Exception('データベース接続エラー: ' . $e->getMessage());
    }
}

/**
 * データベース初期化
 */
function initializeDatabase($pdo) {
    $sql = file_get_contents(__DIR__ . '/wordpress_sites.sql');
    $pdo->exec($sql);
}

/**
 * サイト追加
 */
function addSite() {
    try {
        $name = $_POST['name'] ?? '';
        $url = $_POST['url'] ?? '';
        $username = $_POST['username'] ?? '';
        $app_password = $_POST['app_password'] ?? '';
        $description = $_POST['description'] ?? '';
        
        if (empty($name) || empty($url) || empty($username) || empty($app_password)) {
            throw new Exception('必須項目が入力されていません');
        }
        
        // URL正規化
        $url = rtrim($url, '/');
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new Exception('有効なURLを入力してください');
        }
        
        // WordPress接続テスト
        $testResult = testWordPressConnection($url, $username, $app_password);
        if (!$testResult['success']) {
            throw new Exception('WordPress接続に失敗しました: ' . $testResult['error']);
        }
        
        $pdo = getDatabase();
        
        // 重複チェック
        $stmt = $pdo->prepare("SELECT id FROM wordpress_sites WHERE url = ?");
        $stmt->execute([$url]);
        if ($stmt->fetch()) {
            throw new Exception('このURLは既に登録されています');
        }
        
        // パスワードを暗号化
        $encrypted_password = encryptValue($app_password);
        
        $stmt = $pdo->prepare("
            INSERT INTO wordpress_sites (name, url, username, app_password, description, last_connected, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, datetime('now'), datetime('now'), datetime('now'))
        ");
        
        $stmt->execute([$name, $url, $username, $encrypted_password, $description]);
        
        echo json_encode([
            'success' => true,
            'message' => 'サイトが正常に登録されました',
            'site_id' => $pdo->lastInsertId()
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

/**
 * サイト一覧取得
 */
function listSites() {
    try {
        $pdo = getDatabase();
        
        $stmt = $pdo->query("
            SELECT id, name, url, username, description, status, last_connected, created_at, updated_at
            FROM wordpress_sites 
            ORDER BY created_at DESC
        ");
        
        $sites = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'sites' => $sites
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

/**
 * サイト削除
 */
function deleteSite() {
    try {
        $site_id = $_POST['site_id'] ?? '';
        
        if (empty($site_id)) {
            throw new Exception('サイトIDが指定されていません');
        }
        
        $pdo = getDatabase();
        
        // サイトの存在確認
        $stmt = $pdo->prepare("SELECT name FROM wordpress_sites WHERE id = ?");
        $stmt->execute([$site_id]);
        $site = $stmt->fetch();
        
        if (!$site) {
            throw new Exception('指定されたサイトが見つかりません');
        }
        
        // カスケード削除（外部キー制約により自動的に関連レコードも削除される）
        $stmt = $pdo->prepare("DELETE FROM wordpress_sites WHERE id = ?");
        $stmt->execute([$site_id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'サイト「' . $site['name'] . '」を削除しました'
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

/**
 * WordPress接続テスト（新規登録時）
 */
function testConnection() {
    try {
        $url = $_POST['url'] ?? '';
        $username = $_POST['username'] ?? '';
        $password = $_POST['app_password'] ?? '';
        
        if (empty($url) || empty($username) || empty($password)) {
            throw new Exception('全ての項目を入力してください');
        }
        
        $result = testWordPressConnection($url, $username, $password);
        
        if ($result['success']) {
            echo json_encode([
                'success' => true,
                'message' => 'WordPress接続成功',
                'user' => $result['user']
            ]);
        } else {
            echo json_encode(['error' => $result['error']]);
        }
        
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

/**
 * 登録済みサイトの接続テスト
 */
function testSiteConnection() {
    try {
        $site_id = $_POST['site_id'] ?? '';
        
        if (empty($site_id)) {
            throw new Exception('サイトIDが指定されていません');
        }
        
        $pdo = getDatabase();
        
        $stmt = $pdo->prepare("SELECT url, username, app_password FROM wordpress_sites WHERE id = ?");
        $stmt->execute([$site_id]);
        $site = $stmt->fetch();
        
        if (!$site) {
            throw new Exception('指定されたサイトが見つかりません');
        }
        
        // パスワードを復号化
        $password = decryptValue($site['app_password']);
        
        $result = testWordPressConnection($site['url'], $site['username'], $password);
        
        if ($result['success']) {
            // 最終接続時刻を更新
            $updateStmt = $pdo->prepare("UPDATE wordpress_sites SET last_connected = datetime('now') WHERE id = ?");
            $updateStmt->execute([$site_id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'WordPress接続成功',
                'user' => $result['user']
            ]);
        } else {
            echo json_encode(['error' => $result['error']]);
        }
        
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

/**
 * 統計情報取得
 */
function getStats() {
    try {
        $pdo = getDatabase();
        
        $stats = [];
        
        // 総サイト数
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM wordpress_sites");
        $stats['total_sites'] = $stmt->fetchColumn();
        
        // 稼働中サイト数
        $stmt = $pdo->query("SELECT COUNT(*) as active FROM wordpress_sites WHERE status = 'active'");
        $stats['active_sites'] = $stmt->fetchColumn();
        
        // 適用済み改善数
        $stmt = $pdo->query("SELECT COUNT(*) as improvements FROM seo_improvements WHERE status = 'applied'");
        $stats['total_improvements'] = $stmt->fetchColumn();
        
        echo json_encode([
            'success' => true,
            'stats' => $stats
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

/**
 * SEO改善適用
 */
function applySeoImprovement() {
    try {
        $site_id = $_POST['site_id'] ?? '';
        $url = $_POST['url'] ?? '';
        $improvement_type = $_POST['improvement_type'] ?? '';
        $improved_value = $_POST['improved_value'] ?? '';
        
        if (empty($site_id) || empty($url) || empty($improvement_type) || empty($improved_value)) {
            throw new Exception('必須パラメータが不足しています');
        }
        
        $pdo = getDatabase();
        
        // サイト情報取得
        $stmt = $pdo->prepare("SELECT url, username, app_password FROM wordpress_sites WHERE id = ?");
        $stmt->execute([$site_id]);
        $site = $stmt->fetch();
        
        if (!$site) {
            throw new Exception('指定されたサイトが見つかりません');
        }
        
        // パスワードを復号化
        $password = decryptValue($site['app_password']);
        
        // WordPressに改善を適用
        $result = applyWordPressImprovement($site['url'], $site['username'], $password, $url, $improvement_type, $improved_value);
        
        if ($result['success']) {
            // 改善履歴に記録
            $stmt = $pdo->prepare("
                INSERT INTO seo_improvements (site_id, url, improvement_type, improved_value, status, applied_at, created_at)
                VALUES (?, ?, ?, ?, 'applied', datetime('now'), datetime('now'))
            ");
            $stmt->execute([$site_id, $url, $improvement_type, $improved_value]);
            
            echo json_encode([
                'success' => true,
                'message' => 'SEO改善を適用しました'
            ]);
        } else {
            // 失敗の場合も履歴に記録
            $stmt = $pdo->prepare("
                INSERT INTO seo_improvements (site_id, url, improvement_type, improved_value, status, created_at)
                VALUES (?, ?, ?, ?, 'failed', datetime('now'))
            ");
            $stmt->execute([$site_id, $url, $improvement_type, $improved_value]);
            
            throw new Exception($result['error']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

/**
 * 改善履歴取得
 */
function getImprovements() {
    try {
        $site_id = $_POST['site_id'] ?? '';
        
        if (empty($site_id)) {
            throw new Exception('サイトIDが指定されていません');
        }
        
        $pdo = getDatabase();
        
        $stmt = $pdo->prepare("
            SELECT * FROM seo_improvements 
            WHERE site_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$site_id]);
        
        $improvements = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'improvements' => $improvements
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

/**
 * WordPress接続テスト共通関数
 */
function testWordPressConnection($url, $username, $password) {
    try {
        $api_url = rtrim($url, '/') . '/wp-json/wp/v2/users/me';
        
        $headers = [
            'Authorization: Basic ' . base64_encode($username . ':' . $password),
            'Content-Type: application/json'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);
        
        if ($response === false) {
            return ['success' => false, 'error' => 'cURL エラー: ' . $curl_error];
        }
        
        if ($http_code === 200) {
            $user = json_decode($response, true);
            return [
                'success' => true,
                'user' => $user['name'] ?? 'Unknown User'
            ];
        } else {
            $error = json_decode($response, true);
            $error_message = isset($error['message']) ? $error['message'] : 'HTTP エラー: ' . $http_code;
            return ['success' => false, 'error' => $error_message];
        }
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * WordPressに改善を適用
 */
function applyWordPressImprovement($site_url, $username, $password, $target_url, $improvement_type, $improved_value) {
    try {
        // URLからページIDまたはスラッグを抽出
        $page_info = extractPageInfo($target_url);
        
        if (!$page_info) {
            return ['success' => false, 'error' => 'ページ情報の抽出に失敗しました'];
        }
        
        // WordPress REST APIエンドポイント
        $api_url = rtrim($site_url, '/') . '/wp-json/wp/v2/' . $page_info['type'] . '/' . $page_info['id'];
        
        // 改善内容をWordPress用のデータに変換
        $update_data = prepareUpdateData($improvement_type, $improved_value);
        
        if (!$update_data) {
            return ['success' => false, 'error' => 'サポートされていない改善タイプです'];
        }
        
        $headers = [
            'Authorization: Basic ' . base64_encode($username . ':' . $password),
            'Content-Type: application/json'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($update_data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code === 200) {
            return ['success' => true, 'message' => '改善が適用されました'];
        } else {
            $error = json_decode($response, true);
            $error_message = isset($error['message']) ? $error['message'] : 'HTTP エラー: ' . $http_code;
            return ['success' => false, 'error' => $error_message];
        }
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * URLからページ情報を抽出
 */
function extractPageInfo($url) {
    // 簡単な実装：投稿IDまたはスラッグを推測
    // 実際の実装では、URLからより詳細な情報を抽出する必要がある
    
    $path = parse_url($url, PHP_URL_PATH);
    $segments = explode('/', trim($path, '/'));
    
    // 簡単なケース：posts/123 や pages/about のような形式
    if (count($segments) >= 2 && is_numeric($segments[1])) {
        return [
            'type' => $segments[0] === 'posts' ? 'posts' : 'pages',
            'id' => $segments[1]
        ];
    }
    
    // デフォルト：投稿として扱う
    return [
        'type' => 'posts',
        'id' => 1  // 実際の実装ではより適切な方法でIDを取得
    ];
}

/**
 * 改善データをWordPress用に準備
 */
function prepareUpdateData($improvement_type, $improved_value) {
    switch ($improvement_type) {
        case 'title':
            return ['title' => $improved_value];
            
        case 'meta_description':
            // Yoast SEOやRankMathプラグインのメタデータを更新
            return ['meta' => ['description' => $improved_value]];
            
        case 'content':
            return ['content' => $improved_value];
            
        default:
            return null;
    }
}

/**
 * 値を暗号化
 */
function encryptValue($value) {
    $key = getEncryptionKey();
    return base64_encode(openssl_encrypt($value, 'AES-256-CBC', $key, 0, substr($key, 0, 16)));
}

/**
 * 値を復号化
 */
function decryptValue($encrypted_value) {
    $key = getEncryptionKey();
    return openssl_decrypt(base64_decode($encrypted_value), 'AES-256-CBC', $key, 0, substr($key, 0, 16));
}

/**
 * 暗号化キーを取得
 */
function getEncryptionKey() {
    $serverInfo = $_SERVER['SERVER_NAME'] ?? 'localhost';
    $serverInfo .= $_SERVER['DOCUMENT_ROOT'] ?? __DIR__;
    return hash('sha256', $serverInfo . 'WORDPRESS_SITES_SECRET_KEY');
}
?>