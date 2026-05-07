<?php
/**
 * AuthMiddleware - Bảo vệ routes admin
 * Kiểm tra session đăng nhập trước khi vào controller
 */

class AuthMiddleware
{
    /**
     * Kiểm tra admin đã đăng nhập chưa
     * Nếu chưa → redirect về /login
     */
    public static function requireLogin(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['admin_id'])) {
            header('Location: /login');
            exit;
        }
    }

    /**
     * Kiểm tra đã đăng nhập rồi thì không cho vào trang login nữa
     * Nếu đã login → redirect về /dashboard
     */
    public static function redirectIfLoggedIn(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!empty($_SESSION['admin_id'])) {
            header('Location: /dashboard');
            exit;
        }
    }

    /**
     * Lấy thông tin admin đang đăng nhập
     * @return array|null
     */
    public static function getAdmin(): ?array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['admin_id'])) {
            return null;
        }

        return [
            'id'       => $_SESSION['admin_id'],
            'username' => $_SESSION['admin_username'] ?? '',
            'email'    => $_SESSION['admin_email']    ?? '',
            'role'     => $_SESSION['admin_role']     ?? 'admin',
        ];
    }

    /**
     * Kiểm tra có phải superadmin không
     * @return bool
     */
    public static function isSuperAdmin(): bool
    {
        return ($_SESSION['admin_role'] ?? '') === 'superadmin';
    }

    /**
     * Đăng xuất - Xóa session
     */
    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }

        session_destroy();
    }
}
