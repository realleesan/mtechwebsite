<?php
/**
 * EmailNotificationService.php
 * 
 * Service xử lý gửi email thông báo
 * Sử dụng PHPMailer để gửi email qua SMTP
 */

// Load cấu hình email từ core/email.php
require_once __DIR__ . '/../../core/email.php';

// Load PHPMailer manually (not using composer)
$phpmailerBasePath = __DIR__ . '/../../assets/vendor/phpmailer/src/';

if (file_exists($phpmailerBasePath . 'PHPMailer.php')) {
    require_once $phpmailerBasePath . 'PHPMailer.php';
    require_once $phpmailerBasePath . 'SMTP.php';
    require_once $phpmailerBasePath . 'Exception.php';
} else {
    error_log('EmailNotificationService: PHPMailer not found in ' . $phpmailerBasePath);
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailNotificationService
{
    /** @var PHPMailer */
    private $mailer;

    /** @var array Cấu hình email */
    private $config;

    /**
     * Constructor - Khởi tạo PHPMailer với cấu hình từ .env
     */
    public function __construct()
    {
        $this->config = getEmailConfig();
        
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            throw new Exception('PHPMailer is not installed. Please run: composer require phpmailer/phpmailer');
        }

        $this->mailer = new PHPMailer(true);
        $this->setupSMTP();
    }

    /**
     * Thiết lập cấu hình SMTP
     */
    private function setupSMTP()
    {
        // Server settings
        $this->mailer->isSMTP();
        $this->mailer->Host = $this->config['smtp_host'];
        $this->mailer->SMTPAuth = $this->config['smtp_auth'];
        $this->mailer->Username = $this->config['smtp_username'];
        $this->mailer->Password = $this->config['smtp_password'];
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // tls
        $this->mailer->Port = $this->config['smtp_port'];
        $this->mailer->CharSet = $this->config['charset'];
        
        // Debug mode
        if ($this->config['debug_mode']) {
            $this->mailer->SMTPDebug = SMTP::DEBUG_SERVER;
        }

        // Default sender
        $this->mailer->setFrom(
            $this->config['from_email'], 
            $this->config['from_name']
        );
    }

    /**
     * Gửi email thông báo đã nhận được contact form
     * 
     * @param array $contactData Dữ liệu từ form: name, email, phone, message
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendContactConfirmation($contactData)
    {
        try {
            // Reset recipients
            $this->mailer->clearAddresses();
            
            // Người nhận là email người gửi form
            $this->mailer->addAddress($contactData['email'], $contactData['name']);
            
            // CC cho admin/support
            $this->mailer->addCC($this->config['support_email'], 'Support Team');
            
            // Nội dung email
            $this->mailer->isHTML(true);
            $this->mailer->Subject = '=?UTF-8?B?' . base64_encode('Cảm ơn bạn đã liên hệ với MTech') . '?=';
            
            // Nội dung email
            $body = $this->getContactConfirmationTemplate($contactData);
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $body));
            
            // Gửi email
            $result = $this->mailer->send();
            
            // Log nếu cần
            if ($this->config['log_emails']) {
                error_log('EmailNotificationService: Contact confirmation sent to ' . $contactData['email']);
            }
            
            return [
                'success' => true,
                'message' => 'Email đã được gửi thành công'
            ];
            
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            error_log('EmailNotificationService::sendContactConfirmation() - PHPMailer: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Lỗi gửi email: ' . $e->getMessage()
            ];
        } catch (\Exception $e) {
            error_log('EmailNotificationService::sendContactConfirmation() - General: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Gửi email thông báo cho admin khi có contact mới
     * 
     * @param array $contactData Dữ liệu từ form
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendNewContactNotification($contactData)
    {
        try {
            // Reset recipients
            $this->mailer->clearAddresses();
            
            // Người nhận là admin
            $this->mailer->addAddress($this->config['support_email'], 'Admin');
            
            // Nội dung email
            $this->mailer->isHTML(true);
            $this->mailer->Subject = '=?UTF-8?B?' . base64_encode('[MTech] Có liên hệ mới từ ' . $contactData['name']) . '?=';
            
            // Nội dung email
            $body = $this->getNewContactNotificationTemplate($contactData);
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $body));
            
            // Gửi email
            $result = $this->mailer->send();
            
            // Log nếu cần
            if ($this->config['log_emails']) {
                error_log('EmailNotificationService: New contact notification sent to admin');
            }
            
            return [
                'success' => true,
                'message' => 'Email thông báo đã được gửi'
            ];
            
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            error_log('EmailNotificationService::sendNewContactNotification() - PHPMailer: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Lỗi gửi email: ' . $e->getMessage()
            ];
        } catch (\Exception $e) {
            error_log('EmailNotificationService::sendNewContactNotification() - General: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Template email xác nhận cho người dùng
     */
    private function getContactConfirmationTemplate($data)
    {
        $name = htmlspecialchars($data['name']);
        $email = htmlspecialchars($data['email']);
        $phone = htmlspecialchars($data['phone'] ?? 'Không cung cấp');
        $message = nl2br(htmlspecialchars($data['message']));
        $date = date('d/m/Y H:i:s');
        
        return <<<HTML
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #ffc107; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .header h1 { margin: 0; color: #333; font-size: 24px; }
        .content { background: #f9f9f9; padding: 30px; border: 1px solid #ddd; }
        .footer { background: #333; color: #fff; padding: 20px; text-align: center; border-radius: 0 0 5px 5px; }
        .info-box { background: #fff; padding: 15px; border-left: 4px solid #ffc107; margin: 15px 0; }
        .message-box { background: #fff; padding: 15px; border: 1px solid #ddd; margin: 15px 0; }
        h2 { color: #333; margin-top: 0; }
        p { margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>MTech - Cảm ơn bạn đã liên hệ!</h1>
        </div>
        <div class="content">
            <h2>Xin chào {$name},</h2>
            <p>Chúng tôi đã nhận được thông tin liên hệ của bạn. Dưới đây là chi tiết:</p>
            
            <div class="info-box">
                <p><strong>Họ tên:</strong> {$name}</p>
                <p><strong>Email:</strong> {$email}</p>
                <p><strong>Số điện thoại:</strong> {$phone}</p>
                <p><strong>Thời gian gửi:</strong> {$date}</p>
            </div>
            
            <p><strong>Nội dung tin nhắn:</strong></p>
            <div class="message-box">
                {$message}
            </div>
            
            <p>Đội ngũ của chúng tôi sẽ xem xét và phản hồi sớm nhất có thể.</p>
            <p>Nếu có bất kỳ câu hỏi khẩn cấp nào, vui lòng gọi hotline: <strong>1800 456 7890</strong></p>
            
            <p>Trân trọng,<br><strong>Đội ngũ MTech</strong></p>
        </div>
        <div class="footer">
            <p>© 2026 MTech. Tất cả các quyền được bảo lưu.</p>
            <p>Email: {$this->config['support_email']} | Hotline: 1800 456 7890</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Template email thông báo cho admin
     */
    private function getNewContactNotificationTemplate($data)
    {
        $name = htmlspecialchars($data['name']);
        $email = htmlspecialchars($data['email']);
        $phone = htmlspecialchars($data['phone'] ?? 'Không cung cấp');
        $message = nl2br(htmlspecialchars($data['message']));
        $date = date('d/m/Y H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        
        return <<<HTML
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #dc3545; color: #fff; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .header h1 { margin: 0; font-size: 22px; }
        .content { background: #f9f9f9; padding: 30px; border: 1px solid #ddd; }
        .footer { background: #333; color: #fff; padding: 15px; text-align: center; font-size: 12px; border-radius: 0 0 5px 5px; }
        .alert { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 15px 0; }
        .info-table { width: 100%; background: #fff; border-collapse: collapse; margin: 15px 0; }
        .info-table td { padding: 10px; border: 1px solid #ddd; }
        .info-table td:first-child { font-weight: bold; background: #f5f5f5; width: 30%; }
        .message-box { background: #fff; padding: 15px; border: 1px solid #ddd; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔔 Thông Báo: Có Liên Hệ Mới</h1>
        </div>
        <div class="content">
            <div class="alert">
                <strong>Cảnh báo:</strong> Có người dùng mới gửi form liên hệ vào lúc {$date}
            </div>
            
            <h2>Thông tin người gửi:</h2>
            <table class="info-table">
                <tr><td>Họ tên</td><td>{$name}</td></tr>
                <tr><td>Email</td><td><a href="mailto:{$email}">{$email}</a></td></tr>
                <tr><td>Số điện thoại</td><td>{$phone}</td></tr>
                <tr><td>IP Address</td><td>{$ip}</td></tr>
                <tr><td>Thời gian</td><td>{$date}</td></tr>
            </table>
            
            <h2>Nội dung tin nhắn:</h2>
            <div class="message-box">
                {$message}
            </div>
            
            <p style="text-align: center; margin-top: 30px;">
                <a href="#" style="background: #007bff; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">Xem trong Admin Panel</a>
            </p>
        </div>
        <div class="footer">
            <p>Email thông báo tự động từ hệ thống MTech</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Gửi email xác nhận cho người dùng khi gửi câu hỏi (Question form - Teams page)
     *
     * @param array $data Dữ liệu form: email, subject, message
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendQuestionConfirmation($data)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($data['email']);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = '=?UTF-8?B?' . base64_encode('Chúng tôi đã nhận được câu hỏi của bạn - MTech') . '?=';

            $body = $this->getQuestionConfirmationTemplate($data);
            $this->mailer->Body    = $body;
            $this->mailer->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $body));

            $this->mailer->send();

            if ($this->config['log_emails']) {
                error_log('EmailNotificationService: Question confirmation sent to ' . $data['email']);
            }

            return ['success' => true, 'message' => 'Email xác nhận câu hỏi đã được gửi'];

        } catch (\PHPMailer\PHPMailer\Exception $e) {
            error_log('EmailNotificationService::sendQuestionConfirmation() - PHPMailer: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi gửi email: ' . $e->getMessage()];
        } catch (\Exception $e) {
            error_log('EmailNotificationService::sendQuestionConfirmation() - General: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()];
        }
    }

    /**
     * Gửi email thông báo cho admin khi có câu hỏi mới
     *
     * @param array $data Dữ liệu form: email, subject, message
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendNewQuestionNotification($data)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($this->config['support_email'], 'Admin');

            $this->mailer->isHTML(true);
            $this->mailer->Subject = '=?UTF-8?B?' . base64_encode('[MTech] Câu hỏi mới từ ' . $data['email']) . '?=';

            $body = $this->getNewQuestionNotificationTemplate($data);
            $this->mailer->Body    = $body;
            $this->mailer->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $body));

            $this->mailer->send();

            if ($this->config['log_emails']) {
                error_log('EmailNotificationService: New question notification sent to admin');
            }

            return ['success' => true, 'message' => 'Email thông báo câu hỏi đã được gửi'];

        } catch (\PHPMailer\PHPMailer\Exception $e) {
            error_log('EmailNotificationService::sendNewQuestionNotification() - PHPMailer: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi gửi email: ' . $e->getMessage()];
        } catch (\Exception $e) {
            error_log('EmailNotificationService::sendNewQuestionNotification() - General: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()];
        }
    }

    /**
     * Template email xác nhận câu hỏi cho người dùng
     */
    private function getQuestionConfirmationTemplate($data)
    {
        $email   = htmlspecialchars($data['email']);
        $subject = htmlspecialchars($data['subject']);
        $message = nl2br(htmlspecialchars($data['message']));
        $date    = date('d/m/Y H:i:s');

        return <<<HTML
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #ffc107; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .header h1 { margin: 0; color: #333; font-size: 22px; }
        .content { background: #f9f9f9; padding: 30px; border: 1px solid #ddd; }
        .footer { background: #333; color: #fff; padding: 15px; text-align: center; font-size: 12px; border-radius: 0 0 5px 5px; }
        .info-box { background: #fff; padding: 15px; border-left: 4px solid #ffc107; margin: 15px 0; }
        .message-box { background: #fff; padding: 15px; border: 1px solid #ddd; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>MTech - Chúng tôi đã nhận được câu hỏi của bạn!</h1>
        </div>
        <div class="content">
            <p>Xin chào <strong>{$email}</strong>,</p>
            <p>Cảm ơn bạn đã gửi câu hỏi. Dưới đây là thông tin bạn đã gửi:</p>

            <div class="info-box">
                <p><strong>Email:</strong> {$email}</p>
                <p><strong>Tiêu đề:</strong> {$subject}</p>
                <p><strong>Thời gian gửi:</strong> {$date}</p>
            </div>

            <p><strong>Nội dung câu hỏi:</strong></p>
            <div class="message-box">{$message}</div>

            <p>Đội ngũ của chúng tôi sẽ xem xét và phản hồi sớm nhất có thể.</p>
            <p>Nếu có câu hỏi khẩn cấp, vui lòng gọi hotline: <strong>1800 456 7890</strong></p>

            <p>Trân trọng,<br><strong>Đội ngũ MTech</strong></p>
        </div>
        <div class="footer">
            <p>© 2026 MTech. Tất cả các quyền được bảo lưu.</p>
            <p>Email: {$this->config['support_email']} | Hotline: 1800 456 7890</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Template email thông báo câu hỏi mới cho admin
     */
    private function getNewQuestionNotificationTemplate($data)
    {
        $email   = htmlspecialchars($data['email']);
        $subject = htmlspecialchars($data['subject']);
        $message = nl2br(htmlspecialchars($data['message']));
        $date    = date('d/m/Y H:i:s');
        $ip      = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

        return <<<HTML
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #17a2b8; color: #fff; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .header h1 { margin: 0; font-size: 22px; }
        .content { background: #f9f9f9; padding: 30px; border: 1px solid #ddd; }
        .footer { background: #333; color: #fff; padding: 15px; text-align: center; font-size: 12px; border-radius: 0 0 5px 5px; }
        .alert { background: #d1ecf1; border-left: 4px solid #17a2b8; padding: 15px; margin: 15px 0; }
        .info-table { width: 100%; background: #fff; border-collapse: collapse; margin: 15px 0; }
        .info-table td { padding: 10px; border: 1px solid #ddd; }
        .info-table td:first-child { font-weight: bold; background: #f5f5f5; width: 30%; }
        .message-box { background: #fff; padding: 15px; border: 1px solid #ddd; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>❓ Thông Báo: Có Câu Hỏi Mới</h1>
        </div>
        <div class="content">
            <div class="alert">
                <strong>Thông báo:</strong> Có câu hỏi mới từ người dùng vào lúc {$date}
            </div>

            <h2>Thông tin người gửi:</h2>
            <table class="info-table">
                <tr><td>Email</td><td><a href="mailto:{$email}">{$email}</a></td></tr>
                <tr><td>Tiêu đề</td><td>{$subject}</td></tr>
                <tr><td>IP Address</td><td>{$ip}</td></tr>
                <tr><td>Thời gian</td><td>{$date}</td></tr>
            </table>

            <h2>Nội dung câu hỏi:</h2>
            <div class="message-box">{$message}</div>
        </div>
        <div class="footer">
            <p>Email thông báo tự động từ hệ thống MTech</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Kiểm tra cấu hình email có hoạt động không
     * @return bool
     */
    public function isConfigured()
    {
        return isEmailConfigValid();
    }

    // ----------------------------------------------------------------
    // JOB APPLICATION EMAILS - Email cho ứng tuyển
    // ----------------------------------------------------------------

    /**
     * Gửi email thông báo có CV ứng tuyển mới cho admin.
     *
     * @param array $data Dữ liệu: application_id, blog_id, position, full_name, email, phone, cv_path
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendJobApplicationNotification($data)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($this->config['support_email'], 'Admin');

            $this->mailer->isHTML(true);
            $this->mailer->Subject = '=?UTF-8?B?' . base64_encode('[MTech Tuyển Dụng] CV mới: ' . $data['position']) . '?=';

            $body = $this->getJobApplicationNotificationTemplate($data);
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $body));

            // Attach CV file if exists
            $cvPath = __DIR__ . '/../../' . $data['cv_path'];
            if (file_exists($cvPath)) {
                $this->mailer->addAttachment($cvPath, basename($cvPath));
            }

            $result = $this->mailer->send();

            if ($this->config['log_emails']) {
                error_log('EmailNotificationService: Job application notification sent for ' . $data['position']);
            }

            return ['success' => true, 'message' => 'Email đã được gửi thành công'];
        } catch (\Exception $e) {
            error_log('EmailNotificationService::sendJobApplicationNotification() - ' . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi gửi email: ' . $e->getMessage()];
        }
    }

    /**
     * Gửi email cảm ơn cho ứng viên sau khi nộp CV.
     *
     * @param array $data Dữ liệu: full_name, email, position
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendThankYouEmail($data)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($data['email'], $data['full_name']);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = '=?UTF-8?B?' . base64_encode('Cảm ơn bạn đã ứng tuyển vị trí ' . $data['position'] . ' tại MTech') . '?=';

            $body = $this->getThankYouEmailTemplate($data);
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $body));

            $result = $this->mailer->send();

            return ['success' => true, 'message' => 'Email đã được gửi thành công'];
        } catch (\Exception $e) {
            error_log('EmailNotificationService::sendThankYouEmail() - ' . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi gửi email: ' . $e->getMessage()];
        }
    }

    /**
     * Gửi email thông báo CV được duyệt (qua vòng loại).
     *
     * @param array $data Dữ liệu: full_name, email, position
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendApplicationApprovedEmail($data)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($data['email'], $data['full_name']);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = '=?UTF-8?B?' . base64_encode('Chúc mừng! Bạn đã qua vòng loại CV - ' . $data['position']) . '?=';

            $body = $this->getApplicationApprovedTemplate($data);
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $body));

            $result = $this->mailer->send();

            return ['success' => true, 'message' => 'Email đã được gửi thành công'];
        } catch (\Exception $e) {
            error_log('EmailNotificationService::sendApplicationApprovedEmail() - ' . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi gửi email: ' . $e->getMessage()];
        }
    }

    /**
     * Gửi email thông báo CV bị từ chối.
     *
     * @param array $data Dữ liệu: full_name, email, position
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendApplicationRejectedEmail($data)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($data['email'], $data['full_name']);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = '=?UTF-8?B?' . base64_encode('Thông báo kết quả ứng tuyển - ' . $data['position']) . '?=';

            $body = $this->getApplicationRejectedTemplate($data);
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $body));

            $result = $this->mailer->send();

            return ['success' => true, 'message' => 'Email đã được gửi thành công'];
        } catch (\Exception $e) {
            error_log('EmailNotificationService::sendApplicationRejectedEmail() - ' . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi gửi email: ' . $e->getMessage()];
        }
    }

    // ----------------------------------------------------------------
    // EMAIL TEMPLATES - Job Application
    // ----------------------------------------------------------------

    private function getJobApplicationNotificationTemplate($data)
    {
        $date = date('d/m/Y H:i:s');
        $appId = $data['application_id'];
        $position = htmlspecialchars($data['position']);
        $fullName = htmlspecialchars($data['full_name']);
        $email = htmlspecialchars($data['email']);
        $phone = htmlspecialchars($data['phone']);

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: #1A3FBF; color: #fff; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; }
        .alert { background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin-bottom: 20px; }
        .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .info-table td { padding: 12px; border-bottom: 1px solid #eee; }
        .info-table td:first-child { font-weight: bold; width: 30%; color: #555; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎯 CV Ứng Tuyển Mới</h1>
        </div>
        <div class="content">
            <div class="alert">
                <strong>Thông báo:</strong> Có ứng viên mới nộp CV vào lúc {$date}
            </div>
            
            <h2>Thông tin ứng viên:</h2>
            <table class="info-table">
                <tr><td>ID Đơn</td><td>#{$appId}</td></tr>
                <tr><td>Vị trí</td><td>{$position}</td></tr>
                <tr><td>Họ tên</td><td>{$fullName}</td></tr>
                <tr><td>Email</td><td><a href="mailto:{$email}">{$email}</a></td></tr>
                <tr><td>Số điện thoại</td><td>{$phone}</td></tr>
            </table>
            
            <p style="text-align: center; margin-top: 30px;">
                <a href="mailto:{$email}" style="background: #1A3FBF; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">Liên hệ ứng viên</a>
            </p>
        </div>
        <div class="footer">
            <p>Email thông báo tự động từ hệ thống MTech Tuyển Dụng</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function getThankYouEmailTemplate($data)
    {
        $fullName = htmlspecialchars($data['full_name']);
        $position = htmlspecialchars($data['position']);

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: #1A3FBF; color: #fff; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; line-height: 1.6; }
        .content p { margin: 15px 0; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 12px; }
        .highlight { background: #e7f3ff; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🙏 Cảm ơn bạn đã ứng tuyển!</h1>
        </div>
        <div class="content">
            <p>Xin chào <strong>{$fullName}</strong>,</p>
            
            <p>Cảm ơn bạn đã gửi CV ứng tuyển vị trí <strong>{$position}</strong> tại MTech.</p>
            
            <div class="highlight">
                <p>Chúng tôi đã nhận được CV của bạn và sẽ xem xét trong thời gian sớm nhất. Nếu hồ sơ của bạn phù hợp, chúng tôi sẽ liên hệ để sắp xếp buổi phỏng vấn tiếp theo.</p>
            </div>
            
            <p>Nếu có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi qua email hoặc số điện thoại trên website.</p>
            
            <p>Trân trọng,<br><strong>MTech HR Team</strong></p>
        </div>
        <div class="footer">
            <p>Email tự động từ hệ thống MTech</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function getApplicationApprovedTemplate($data)
    {
        $fullName = htmlspecialchars($data['full_name']);
        $position = htmlspecialchars($data['position']);

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: #28a745; color: #fff; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; line-height: 1.6; }
        .content p { margin: 15px 0; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 12px; }
        .highlight { background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #28a745; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Chúc mừng! Bạn đã qua vòng loại</h1>
        </div>
        <div class="content">
            <p>Xin chào <strong>{$fullName}</strong>,</p>
            
            <div class="highlight">
                <p><strong>Chúc mừng bạn!</strong> CV của bạn đã vượt qua vòng sơ tuyển cho vị trí <strong>{$position}</strong>.</p>
            </div>
            
            <p>Chúng tôi rất ấn tượng với hồ sơ của bạn và muốn mời bạn tham gia buổi phỏng vấn trực tiếp.</p>
            
            <p>Chúng tôi sẽ liên hệ với bạn trong vòng 3-5 ngày làm việc để sắp xếp lịch phỏng vấn phù hợp.</p>
            
            <p>Hãy chuẩn bị tinh thần và chúc bạn may mắn!</p>
            
            <p>Trân trọng,<br><strong>MTech HR Team</strong></p>
        </div>
        <div class="footer">
            <p>Email tự động từ hệ thống MTech</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function getApplicationRejectedTemplate($data)
    {
        $fullName = htmlspecialchars($data['full_name']);
        $position = htmlspecialchars($data['position']);

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: #6c757d; color: #fff; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; line-height: 1.6; }
        .content p { margin: 15px 0; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 12px; }
        .highlight { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #6c757d; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Thông báo kết quả ứng tuyển</h1>
        </div>
        <div class="content">
            <p>Xin chào <strong>{$fullName}</strong>,</p>
            
            <p>Cảm ơn bạn đã quan tâm và gửi CV ứng tuyển vị trí <strong>{$position}</strong> tại MTech.</p>
            
            <div class="highlight">
                <p>Sau khi xem xét kỹ lưỡng, chúng tôi rất tiếc phải thông báo rằng <strong>CV của bạn chưa đạt yêu cầu</strong> cho vị trí này tại thời điểm hiện tại.</p>
            </div>
            
            <p>Đây không phải là sự đánh giá về năng lực của bạn, mà chỉ đơn thuần là chúng tôi đang tìm kiếm những kỹ năng và kinh nghiệm phù hợp cụ thể với nhu cầu hiện tại của dự án.</p>
            
            <p>Chúng tôi sẽ lưu hồ sơ của bạn và có thể liên hệ lại khi có vị trí phù hợp hơn trong tương lai.</p>
            
            <p>Chúc bạn sớm tìm được cơ hội phù hợp!</p>
            
            <p>Trân trọng,<br><strong>MTech HR Team</strong></p>
        </div>
        <div class="footer">
            <p>Email tự động từ hệ thống MTech</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    // ----------------------------------------------------------------
    // NEWSLETTER METHODS
    // ----------------------------------------------------------------

    /**
     * Gửi email xác nhận đăng ký newsletter
     * @param string $email Email người đăng ký
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendNewsletterConfirmation($email)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();

            // Người nhận
            $this->mailer->addAddress($email);

            // Nội dung email
            $this->mailer->Subject = 'Xác nhận đăng ký nhận tin - MTECH.JSC';
            $this->mailer->Body = $this->getNewsletterConfirmationTemplate($email);
            $this->mailer->AltBody = strip_tags($this->mailer->Body);

            $this->mailer->send();

            return [
                'success' => true,
                'message' => 'Email xác nhận đã được gửi'
            ];

        } catch (Exception $e) {
            error_log('Newsletter confirmation email failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Không thể gửi email xác nhận: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Gửi thông báo cho admin về subscriber mới
     * @param string $email Email người đăng ký
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendNewSubscriberNotification($email)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();

            // Gửi đến admin
            $adminEmail = $this->config['admin_email'] ?? 'mtechjsc2011.info@gmail.com';
            $this->mailer->addAddress($adminEmail);

            // Nội dung email
            $this->mailer->Subject = 'Subscriber mới đăng ký newsletter - MTECH.JSC';
            $this->mailer->Body = $this->getNewSubscriberNotificationTemplate($email);
            $this->mailer->AltBody = strip_tags($this->mailer->Body);

            $this->mailer->send();

            return [
                'success' => true,
                'message' => 'Thông báo admin đã được gửi'
            ];

        } catch (Exception $e) {
            error_log('New subscriber notification failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Không thể gửi thông báo: ' . $e->getMessage()
            ];
        }
    }

    // ----------------------------------------------------------------
    // EMAIL TEMPLATES - Newsletter
    // ----------------------------------------------------------------

    private function getNewsletterConfirmationTemplate($email)
    {
        $date = date('d/m/Y H:i:s');
        $safeEmail = htmlspecialchars($email);

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: #1A3FBF; color: #fff; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; }
        .success { background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin-bottom: 20px; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 12px; }
        .btn { background: #1A3FBF; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📧 Xác nhận đăng ký Newsletter</h1>
        </div>
        <div class="content">
            <div class="success">
                <strong>Chúc mừng!</strong> Bạn đã đăng ký nhận tin thành công.
            </div>
            
            <p>Xin chào,</p>
            <p>Cảm ơn bạn đã đăng ký nhận tin từ <strong>MTECH.JSC</strong>!</p>
            
            <p>Email <strong>{$safeEmail}</strong> của bạn đã được thêm vào danh sách nhận tin của chúng tôi vào lúc {$date}.</p>
            
            <p>Bạn sẽ nhận được:</p>
            <ul>
                <li>Thông tin về các dự án mới</li>
                <li>Tin tức công nghệ xây dựng</li>
                <li>Cơ hội việc làm tại MTECH</li>
                <li>Các ưu đãi đặc biệt</li>
            </ul>
            
            <p style="text-align: center;">
                <a href="https://mtechjsc.com" class="btn">Khám phá website</a>
            </p>
            
            <p><em>Nếu bạn không đăng ký nhận tin này, vui lòng bỏ qua email này.</em></p>
        </div>
        <div class="footer">
            <p>Email tự động từ hệ thống MTECH.JSC<br>
            Địa chỉ: 227 Nguyễn Ngọc Nại, Khương Mai, Thanh Xuân, Hà Nội</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function getNewSubscriberNotificationTemplate($email)
    {
        $date = date('d/m/Y H:i:s');
        $safeEmail = htmlspecialchars($email);

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: #1A3FBF; color: #fff; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; }
        .alert { background: #d1ecf1; border-left: 4px solid #17a2b8; padding: 15px; margin-bottom: 20px; }
        .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .info-table td { padding: 12px; border-bottom: 1px solid #eee; }
        .info-table td:first-child { font-weight: bold; width: 30%; color: #555; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📬 Subscriber Mới</h1>
        </div>
        <div class="content">
            <div class="alert">
                <strong>Thông báo:</strong> Có người đăng ký newsletter mới vào lúc {$date}
            </div>
            
            <h2>Thông tin subscriber:</h2>
            <table class="info-table">
                <tr><td>Email</td><td><a href="mailto:{$safeEmail}">{$safeEmail}</a></td></tr>
                <tr><td>Thời gian</td><td>{$date}</td></tr>
                <tr><td>Nguồn</td><td>Footer Newsletter Form</td></tr>
            </table>
            
            <p>Tổng số subscriber hiện tại có thể được xem trong admin panel.</p>
        </div>
        <div class="footer">
            <p>Email thông báo tự động từ hệ thống MTECH.JSC</p>
        </div>
    </div>
</body>
</html>
HTML;
    }
}