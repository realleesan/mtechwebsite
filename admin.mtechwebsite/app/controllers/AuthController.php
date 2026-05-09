<?php
/**
 * AuthController - Xử lý đăng nhập, đăng xuất, quên mật khẩu
 */

require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../models/AuthModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class AuthController extends BaseController
{
    private $authModel;

    public function __construct()
    {
        $this->authModel = new AuthModel();
    }

    // ----------------------------------------
    // Login
    // ----------------------------------------

    public function showLogin()
    {
        // Nếu đã login rồi thì redirect về dashboard
        AuthMiddleware::redirectIfLoggedIn();

        $this->renderAuthView('auth/login', [
            'title' => 'Đăng nhập - Admin MTech',
        ]);
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
            return;
        }

        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        // Validate
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin';
            $this->redirect('/login');
            return;
        }

        // Xác thực qua AuthModel (đọc từ .env, verify bcrypt hash)
        $admin = $this->authModel->verifyAdmin($email, $password);

        if (!$admin) {
            $_SESSION['error'] = 'Email hoặc mật khẩu không đúng';
            $this->redirect('/login');
            return;
        }

        // Gửi email thông báo đăng nhập
        $this->sendLoginNotification($email);

        // Lưu session
        $_SESSION['admin_id']       = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_email']    = $admin['email'];
        $_SESSION['admin_role']     = $admin['role'];

        // Redirect về dashboard
        $_SESSION['success'] = 'Đăng nhập thành công!';
        $this->redirect('/dashboard');
    }

    /**
     * Gửi email thông báo đăng nhập
     */
    private function sendLoginNotification($email)
    {
        try {
            require_once __DIR__ . '/../services/EmailNotificationService.php';
            
            $emailService = new EmailNotificationService();
            $emailService->sendAdminLoginNotification([
                'email' => $email,
                'ip'    => $this->getClientIP(),
                'time'  => date('d/m/Y H:i:s'),
            ]);
        } catch (Exception $e) {
            // Log lỗi nhưng không block login
            error_log('Failed to send login notification: ' . $e->getMessage());
        }
    }

    protected function getClientIP()
    {
        foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = trim(explode(',', $_SERVER[$key])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        return 'Unknown';
    }

    // ----------------------------------------
    // Logout
    // ----------------------------------------

    public function logout()
    {
        AuthMiddleware::logout();
        $_SESSION['success'] = 'Đã đăng xuất thành công';
        $this->redirect('/login');
    }

    // ----------------------------------------
    // Forgot Password
    // ----------------------------------------

    public function showForgot()
    {
        AuthMiddleware::redirectIfLoggedIn();

        $this->renderAuthView('auth/forgot', [
            'title' => 'Quên mật khẩu - Admin MTech',
        ]);
    }

    public function sendResetLink()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/forgot-password');
            return;
        }

        $email = trim($_POST['email'] ?? '');

        if (empty($email)) {
            $_SESSION['error'] = 'Vui lòng nhập email';
            $this->redirect('/forgot-password');
            return;
        }

        // Kiểm tra email có tồn tại không
        $admin = $this->authModel->findByEmail($email);

        if (!$admin) {
            // Không tiết lộ email có tồn tại hay không (bảo mật)
            $_SESSION['success'] = 'Nếu email tồn tại trong hệ thống, link đặt lại mật khẩu sẽ được gửi đến bạn';
            $this->redirect('/forgot-password');
            return;
        }

        // Tạo reset token
        $token = $this->authModel->createResetToken($email);

        if (!$token) {
            $_SESSION['error'] = 'Không thể tạo link reset. Vui lòng thử lại';
            $this->redirect('/forgot-password');
            return;
        }

        // Gửi email chứa link reset
        $sent = $this->sendPasswordResetEmail($email, $token);

        if (!$sent) {
            $_SESSION['error'] = 'Không thể gửi email. Vui lòng thử lại sau';
            $this->redirect('/forgot-password');
            return;
        }

        $_SESSION['success'] = 'Link đặt lại mật khẩu đã được gửi đến email của bạn. Vui lòng kiểm tra hộp thư (kể cả thư mục spam)';
        $this->redirect('/forgot-password');
    }

    /**
     * Gửi email chứa link reset mật khẩu
     */
    private function sendPasswordResetEmail(string $email, string $token): bool
    {
        try {
            require_once __DIR__ . '/../services/EmailNotificationService.php';

            $protocol  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host      = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $resetLink = $protocol . '://' . $host . '/reset-password?token=' . urlencode($token);

            $emailService = new EmailNotificationService();
            $result = $emailService->sendPasswordResetLink([
                'email'      => $email,
                'reset_link' => $resetLink,
                'expires_in' => '1 giờ',
            ]);

            return $result['success'] ?? false;

        } catch (Exception $e) {
            error_log('AuthController::sendPasswordResetEmail() - ' . $e->getMessage());
            return false;
        }
    }

    // ----------------------------------------
    // Reset Password
    // ----------------------------------------

    public function showReset()
    {
        AuthMiddleware::redirectIfLoggedIn();

        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            $_SESSION['error'] = 'Token không hợp lệ';
            $this->redirect('/login');
            return;
        }

        // Kiểm tra token có hợp lệ không
        if (!$this->authModel->verifyResetToken($token)) {
            $_SESSION['error'] = 'Token không hợp lệ hoặc đã hết hạn';
            $this->redirect('/login');
            return;
        }

        $this->renderAuthView('auth/reset', [
            'title' => 'Đặt lại mật khẩu - Admin MTech',
            'token' => $token,
        ]);
    }

    public function resetPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
            return;
        }

        $token    = $_POST['token']    ?? '';
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        // Validate
        if (empty($token) || empty($password) || empty($confirm)) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin';
            $this->redirect('/reset-password?token=' . urlencode($token));
            return;
        }

        if ($password !== $confirm) {
            $_SESSION['error'] = 'Mật khẩu xác nhận không khớp';
            $this->redirect('/reset-password?token=' . urlencode($token));
            return;
        }

        if (strlen($password) < 6) {
            $_SESSION['error'] = 'Mật khẩu phải có ít nhất 6 ký tự';
            $this->redirect('/reset-password?token=' . urlencode($token));
            return;
        }

        // Reset password
        $result = $this->authModel->resetPassword($token, $password);

        if (!$result) {
            $_SESSION['error'] = 'Không thể đặt lại mật khẩu. Token có thể đã hết hạn';
            $this->redirect('/reset-password?token=' . urlencode($token));
            return;
        }

        $_SESSION['success'] = 'Đặt lại mật khẩu thành công. Vui lòng đăng nhập';
        $this->redirect('/login');
    }

    // ----------------------------------------
    // Helper: Render auth view (không dùng master layout)
    // ----------------------------------------

    private function renderAuthView($viewPath, $data = [])
    {
        extract($data);
        $viewFile = __DIR__ . '/../views/' . $viewPath . '.php';

        if (!file_exists($viewFile)) {
            throw new Exception("View not found: {$viewPath}");
        }

        include $viewFile;
    }
}
