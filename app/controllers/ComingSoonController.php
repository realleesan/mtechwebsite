<?php
/**
 * ComingSoonController - Xử lý trang coming soon và form subscribe
 * Kế thừa BaseController để sử dụng các helper methods
 */

require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/../models/ComingsoonModel.php';

class ComingSoonController extends BaseController
{
    /**
     * Hiển thị trang coming soon
     * Tách từ index.php:766-771 và logic comingsoon check
     */
    public function index()
    {
        // Lấy cấu hình từ model
        $comingsoonModel = new ComingsoonModel();
        $settings = $comingsoonModel->getSettings();
        
        // Mặc định nếu không lấy được settings
        $title = $settings['title'] ?? "Website đang bảo trì...";
        $description = $settings['description'] ?? 'Website đang được xây dựng. Chúng tôi sẽ sớm ra mắt với phiên bản mới.';
        $subscribeText = $settings['subscribe_text'] ?? 'Đăng ký để nhận thông báo.';
        $emailPlaceholder = $settings['email_placeholder'] ?? 'Nhập địa chỉ email của bạn';
        $buttonText = $settings['button_text'] ?? 'Đăng ký ngay';
        
        // Render view trực tiếp (không dùng master layout)
        $this->view('comingsoon/comingsoon.php', [
            'title' => $title,
            'description' => $description,
            'subscribeText' => $subscribeText,
            'emailPlaceholder' => $emailPlaceholder,
            'buttonText' => $buttonText,
            'settings' => $settings
        ]);
    }
    
    /**
     * Xử lý AJAX form subscribe
     * Tách từ app/views/comingsoon/comingsoon.php:17-23
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
            $email = $_POST['email'] ?? '';
            
            // Validate email
            if (empty($email)) {
                $this->json([
                    'success' => false,
                    'message' => 'Vui lòng nhập email'
                ]);
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->json([
                    'success' => false,
                    'message' => 'Email không hợp lệ'
                ]);
            }
            
            // Lưu vào database
            $comingsoonModel = new ComingsoonModel();
            $result = $comingsoonModel->saveSubscriber($email);
            
            $this->json($result);
            
        } catch (Exception $e) {
            error_log('ComingSoon subscribe error: ' . $e->getMessage());
            
            $this->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra, vui lòng thử lại sau.'
            ]);
        }
    }
}