<?php
/**
 * NewsletterController - Xử lý đăng ký newsletter từ footer
 * Kế thừa BaseController để sử dụng các helper methods
 */

require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../services/ValidationService.php';
require_once __DIR__ . '/../models/FooterModel.php';

class NewsletterController extends BaseController
{
    /**
     * Xử lý AJAX form newsletter subscription
     */
    public function subscribe()
    {
        // Chỉ xử lý POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json([
                'success' => false,
                'message' => 'Method not allowed'
            ], 405);
        }
        
        try {
            // Validate input
            $validation = ValidationService::validate($_POST, [
                'email' => 'required|email'
            ]);
            
            if ($validation->fails()) {
                $this->json([
                    'success' => false,
                    'message' => 'Email không hợp lệ',
                    'errors' => $validation->errors()
                ]);
            }
            
            $email = trim($_POST['email']);
            
            // Kiểm tra email đã đăng ký chưa
            $footerModel = new FooterModel();
            if ($footerModel->isEmailSubscribed($email)) {
                $this->json([
                    'success' => false,
                    'message' => 'Email này đã được đăng ký trước đó.'
                ]);
            }
            
            // Lưu vào database
            $result = $footerModel->subscribeNewsletter($email);
            
            if (!$result) {
                $this->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi đăng ký. Vui lòng thử lại.'
                ]);
            }
            
            // Gửi email xác nhận (optional)
            try {
                if (file_exists(__DIR__ . '/../services/EmailNotificationService.php')) {
                    require_once __DIR__ . '/../services/EmailNotificationService.php';
                    $emailService = new EmailNotificationService();
                    
                    if ($emailService->isConfigured()) {
                        // Gửi email xác nhận đăng ký
                        $emailService->sendNewsletterConfirmation($email);
                        // Thông báo admin có subscriber mới
                        $emailService->sendNewSubscriberNotification($email);
                    }
                }
            } catch (Exception $e) {
                // Log lỗi nhưng không ảnh hưởng đến kết quả
                error_log('Newsletter email sending failed: ' . $e->getMessage());
            }
            
            $this->json([
                'success' => true,
                'message' => 'Cảm ơn bạn đã đăng ký! Chúng tôi sẽ gửi thông tin mới nhất đến email của bạn.'
            ]);
            
        } catch (Exception $e) {
            error_log('Newsletter subscription error: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            
            $this->json([
                'success' => false,
                'message' => 'Có lỗi hệ thống. Vui lòng thử lại sau.'
            ]);
        }
    }
}