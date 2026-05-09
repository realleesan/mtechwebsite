<?php
/**
 * AuthModel - Xử lý xác thực admin
 *
 * Nguồn dữ liệu: bảng `admins` trong database
 * - Đăng nhập: tìm admin theo email, verify bcrypt hash
 * - Reset password: hash mật khẩu mới, UPDATE vào DB
 * - Reset token: lưu vào file JSON (không cần bảng riêng)
 */

class AuthModel
{
    /** @var PDO|null */
    private $db;

    /** @var string File lưu reset tokens */
    private $tokenFile;

    public function __construct()
    {
        $this->tokenFile = __DIR__ . '/../../logs/reset_tokens.json';

        require_once __DIR__ . '/../../core/database.php';
        try {
            $this->db = getDBConnection();
        } catch (Exception $e) {
            $this->db = null;
            error_log('AuthModel: DB connection failed - ' . $e->getMessage());
        }
    }

    // ----------------------------------------------------------------
    // Verify admin credentials
    // ----------------------------------------------------------------

    /**
     * Xác thực admin — tìm theo email trong DB, verify bcrypt hash
     *
     * @return array|null Thông tin admin hoặc null nếu sai
     */
    public function verifyAdmin(string $email, string $password): ?array
    {
        $admin = $this->findByEmail($email);

        if (!$admin) {
            return null;
        }

        if (!password_verify($password, $admin['password'])) {
            return null;
        }

        // Cập nhật last_login
        $this->updateLastLogin($admin['id']);

        return [
            'id'       => $admin['id'],
            'username' => $admin['username'] ?? 'Admin',
            'email'    => $admin['email'],
            'role'     => 'superadmin',
        ];
    }

    /**
     * Tìm admin theo email
     *
     * @return array|null
     */
    public function findByEmail(string $email): ?array
    {
        if (!$this->db) {
            return null;
        }

        try {
            $stmt = $this->db->prepare(
                'SELECT id, username, email, password, full_name, status FROM admins WHERE email = ? LIMIT 1'
            );
            $stmt->execute([$email]);
            $admin = $stmt->fetch();

            if (!$admin || (int)$admin['status'] !== 1) {
                return null;
            }

            return $admin;
        } catch (Exception $e) {
            error_log('AuthModel::findByEmail() - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cập nhật thời gian đăng nhập cuối
     */
    private function updateLastLogin(int $adminId): void
    {
        if (!$this->db) return;

        try {
            $stmt = $this->db->prepare(
                'UPDATE admins SET last_login = NOW() WHERE id = ?'
            );
            $stmt->execute([$adminId]);
        } catch (Exception $e) {
            error_log('AuthModel::updateLastLogin() - ' . $e->getMessage());
        }
    }

    // ----------------------------------------------------------------
    // Reset Token — lưu vào file JSON
    // ----------------------------------------------------------------

    /**
     * Tạo reset token và lưu vào file
     *
     * @return string|false Token hoặc false nếu lỗi
     */
    public function createResetToken(string $email)
    {
        $token   = bin2hex(random_bytes(32));
        $expires = time() + 3600; // 1 giờ

        $tokens = $this->loadTokens();
        // Xóa token cũ của email này
        $tokens = array_values(array_filter($tokens, fn($t) => $t['email'] !== $email));
        $tokens[] = [
            'token'   => $token,
            'email'   => $email,
            'expires' => $expires,
        ];

        return $this->saveTokens($tokens) ? $token : false;
    }

    /**
     * Kiểm tra reset token có hợp lệ không
     */
    public function verifyResetToken(string $token): bool
    {
        foreach ($this->loadTokens() as $t) {
            if ($t['token'] === $token && $t['expires'] > time()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Reset password — hash bcrypt và UPDATE vào bảng admins
     */
    public function resetPassword(string $token, string $newPassword): bool
    {
        // Tìm token hợp lệ để lấy email
        $email = null;
        foreach ($this->loadTokens() as $t) {
            if ($t['token'] === $token && $t['expires'] > time()) {
                $email = $t['email'];
                break;
            }
        }

        if (!$email) {
            return false;
        }

        if (!$this->db) {
            error_log('AuthModel::resetPassword() - No DB connection');
            return false;
        }

        try {
            // Hash mật khẩu mới bằng bcrypt
            $newHash = password_hash($newPassword, PASSWORD_BCRYPT);

            $stmt = $this->db->prepare(
                'UPDATE admins SET password = ?, updated_at = NOW() WHERE email = ?'
            );
            $result = $stmt->execute([$newHash, $email]);

            if (!$result || $stmt->rowCount() === 0) {
                error_log('AuthModel::resetPassword() - No rows updated for email: ' . $email);
                return false;
            }

            // Xóa token đã dùng (one-time use)
            $tokens = array_values(array_filter($this->loadTokens(), fn($t) => $t['token'] !== $token));
            $this->saveTokens($tokens);

            return true;

        } catch (Exception $e) {
            error_log('AuthModel::resetPassword() - ' . $e->getMessage());
            return false;
        }
    }

    // ----------------------------------------------------------------
    // Token file helpers
    // ----------------------------------------------------------------

    private function loadTokens(): array
    {
        if (!file_exists($this->tokenFile)) {
            return [];
        }

        $content = file_get_contents($this->tokenFile);
        $data    = json_decode($content, true);

        if (!is_array($data)) {
            return [];
        }

        // Lọc bỏ token hết hạn
        return array_values(array_filter($data, fn($t) => $t['expires'] > time()));
    }

    private function saveTokens(array $tokens): bool
    {
        $dir = dirname($this->tokenFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return file_put_contents(
            $this->tokenFile,
            json_encode(array_values($tokens), JSON_PRETTY_PRINT)
        ) !== false;
    }
}
