<?php
/**
 * Database Connection
 * 
 * Quản lý kết nối PDO đến MySQL/MariaDB
 * Đọc cấu hình từ .env file
 */

// Ngăn truy cập trực tiếp
if (!defined('APP_INIT') && basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    http_response_code(403);
    exit('Direct access not allowed');
}

/**
 * Đọc file .env và trả về mảng cấu hình
 * @param string $path Đường dẫn đến file .env
 * @return array Mảng cấu hình
 */
function loadEnv($path = __DIR__ . '/../.env') {
    $config = [];
    
    if (!file_exists($path)) {
        return $config;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Bỏ qua comment
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse key=value
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Loại bỏ quote nếu có
            if ((strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) ||
                (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1)) {
                $value = substr($value, 1, -1);
            }
            
            $config[$key] = $value;
        }
    }
    
    return $config;
}

/**
 * Lấy giá trị từ .env
 * @param string $key Key cấu hình
 * @param mixed $default Giá trị mặc định
 * @return mixed Giá trị hoặc default
 */
function env($key, $default = null) {
    static $envConfig = null;
    
    if ($envConfig === null) {
        $envConfig = loadEnv();
    }
    
    return isset($envConfig[$key]) ? $envConfig[$key] : $default;
}

/**
 * Kết nối database và trả về PDO instance
 * @return PDO|null PDO instance hoặc null nếu lỗi
 */
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo !== null) {
        return $pdo;
    }
    
    // Đọc cấu hình từ .env
    $host = env('DB_HOST', 'localhost');
    $database = env('DB_NAME', 'mtech');
    $username = env('DB_USER', 'root');
    $password = env('DB_PASSWORD', '');
    $charset = env('DB_CHARSET', 'utf8mb4');
    $port = env('DB_PORT', '3306');
    
    $dsn = "mysql:host={$host};port={$port};dbname={$database};charset={$charset}";
    
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$charset} COLLATE utf8mb4_unicode_ci"
    ];
    
    try {
        $pdo = new PDO($dsn, $username, $password, $options);
    } catch (PDOException $e) {
        error_log('Database Connection Error: ' . $e->getMessage());
        throw new Exception('Không thể kết nối database. Vui lòng kiểm tra cấu hình.');
    }
    
    return $pdo;
}

/**
 * Thực thi SQL và trả về statement
 * @param string $sql Câu lệnh SQL
 * @param array $params Tham số
 * @return PDOStatement
 */
function dbQuery($sql, $params = []) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Lấy một bản ghi duy nhất
 * @param string $sql Câu lệnh SQL
 * @param array $params Tham số
 * @return array|null
 */
function dbFetchOne($sql, $params = []) {
    return dbQuery($sql, $params)->fetch();
}

/**
 * Lấy nhiều bản ghi
 * @param string $sql Câu lệnh SQL
 * @param array $params Tham số
 * @return array
 */
function dbFetchAll($sql, $params = []) {
    return dbQuery($sql, $params)->fetchAll();
}

/**
 * Lấy ID vừa insert
 * @return string
 */
function dbLastInsertId() {
    return getDBConnection()->lastInsertId();
}

/**
 * Bắt đầu transaction
 */
function dbBeginTransaction() {
    getDBConnection()->beginTransaction();
}

/**
 * Commit transaction
 */
function dbCommit() {
    getDBConnection()->commit();
}

/**
 * Rollback transaction
 */
function dbRollback() {
    getDBConnection()->rollBack();
}
