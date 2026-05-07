<?php
/**
 * AuthModel - Xử lý xác thực admin
 * Vì admin dùng hardcoded credentials, model này chủ yếu
 * xử lý reset token (lưu vào DB hoặc file tạm)
 */

class AuthModel
{
    /** @var PDO */
    private $db;

    /** @var string File lưu reset tokens (fallback nếu không có bảng) */
    private $tokenFile;

    public function __construct($database = null)
    {
        if ($database) {
            $this->db = $database;
        } else {
            require_once __DIR__ . '/../../core/database.php';
            try {
                $this->db = getDBConnection();
            } catch (Exception $e) {
                $this->db = null;
            }
        }

        $this->tokenFile = __DIR__ . '/../../logs/reset_tokens.json';
    }

    // ----------------------------------------------------------------
    // Verify admin credentials (hardcoded)
    // ----------------------------------------------------------------

    /**
     * Xác thực admin - hardcoded credentials
     * Email: baominhkpkp@gmail.com | Password: admin123
     */
    public function verifyAdmin($email, $password): ?array
    {
        $validEmail    = 'baominhkpkp@gmail.com';
        $validPassword = 'admin123';

        if ($email === $validEmail && $password === $validPassword) {
            return [
                'id'       => 1,
                'username' => 'Admin',
                'email'    => $validEmail,
                'role'     => 'superadmin',
            ];
        }

        return null;
    }

    /**
     * Tìm admin theo email
     */
    public function findByEmail($email): ?array
    {
        if ($email === 'baominhkpkp@gmail.com') {
            return [
                'id'    => 1,
                'email' => $email,
            ];
        }
        return null;
    }

    // ----------------------------------------------------------------
    // Reset Token - lưu vào file JSON (không cần bảng DB)
    // ----------------------------------------------------------------

    /**
     * Tạo reset token và lưu vào file
     * @return string|false Token hoặc false nếu lỗi
     */
    public function createResetToken($email)
    {
        $token   = bin2hex(random_bytes(32));
        $expires = time() + 3600; // 1 giờ

        $tokens = $this->loadTokens();
        // Xóa token cũ của email này
        $tokens = array_filter($tokens, fn($t) => $t['email'] !== $email);
        $tokens[] = [
            'token'   => $token,
            'email'   => $email,
            'expires' => $expires,
        ];

        if ($this->saveTokens(array_values($tokens))) {
            return $token;
        }

        return false;
    }

    /**
     * Kiểm tra reset token có hợp lệ không
     */
    public function verifyResetToken($token): bool
    {
        $tokens = $this->loadTokens();

        foreach ($tokens as $t) {
            if ($t['token'] === $token && $t['expires'] > time()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Reset password (với hardcoded credentials thì chỉ log lại)
     * Trong thực tế nên lưu vào DB hoặc file config
     */
    public function resetPassword($token, $newPassword): bool
    {
        if (!$this->verifyResetToken($token)) {
            return false;
        }

        // Xóa token đã dùng
        $tokens = $this->loadTokens();
        $tokens = array_filter($tokens, fn($t) => $t['token'] !== $token);
        $this->saveTokens(array_values($tokens));

        // Log mật khẩu mới (admin cần cập nhật thủ công vào code)
        error_log('AuthModel::resetPassword() - New password requested. Update hardcoded password manually.');

        return true;
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
        return array_filter($data, fn($t) => $t['expires'] > time());
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
