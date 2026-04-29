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
}
