<?php
/**
 * TeamsController - Xử lý trang đội ngũ và form câu hỏi
 * Kế thừa BaseController để sử dụng các helper methods
 */

require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/../services/ValidationService.php';
require_once __DIR__ . '/../models/ContactsModel.php';

class TeamsController extends BaseController
{
    /**
     * Hiển thị trang đội ngũ
     * Tách từ index.php:305-312
     */
    public function index()
    {
        // Set các biến cho layout
        $title = 'Đội ngũ - MTECHJSC';
        $content = 'about/teams.php';
        $showPageHeader = true;
        $showCTA = false;
        $showBreadcrumb = true;
        
        // Render view
        $this->view($content, [
            'title' => $title,
            'showPageHeader' => $showPageHeader,
            'showCTA' => $showCTA,
            'showBreadcrumb' => $showBreadcrumb
        ]);
    }
    
    /**
     * Xử lý AJAX form submit câu hỏi
     * Tách từ app/views/about/teams.php:7-105
     */
    public function submitQuestion()
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
                'email' => 'required|email',
                'subject' => 'required',
                'message' => 'required|min:10'
            ]);
            
            if ($validation->fails()) {
                $this->json([
                    'success' => false,
                    'message' => 'Vui lòng kiểm tra lại thông tin',
                    'errors' => $validation->errors()
                ]);
            }
            
            // Chuẩn bị dữ liệu
            $questionData = [
                'name' => trim($_POST['email']), // không có trường name, dùng email làm tên
                'email' => trim($_POST['email']),
                'phone' => null,
                'subject' => trim($_POST['subject']),
                'message' => trim($_POST['message']),
                'ip_address' => $this->getClientIP(),
                'user_agent' => $this->getUserAgent()
            ];
            
            // Lưu vào database (tái sử dụng ContactsModel)
            $contactsModel = new ContactsModel();
            $contactId = $contactsModel->create($questionData);
            
            if (!$contactId) {
                $this->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi lưu thông tin. Vui lòng thử lại.'
                ]);
            }
            
            // Gửi email thông báo
            $emailError = null;
            try {
                if (file_exists(__DIR__ . '/../services/EmailNotificationService.php')) {
                    require_once __DIR__ . '/../services/EmailNotificationService.php';
                    $emailService = new EmailNotificationService();
                    
                    if ($emailService->isConfigured()) {
                        // Gửi email xác nhận cho người dùng
                        $emailService->sendQuestionConfirmation([
                            'email' => $questionData['email'],
                            'subject' => $questionData['subject'],
                            'message' => $questionData['message']
                        ]);
                        // Gửi email thông báo cho admin
                        $emailService->sendNewQuestionNotification([
                            'email' => $questionData['email'],
                            'subject' => $questionData['subject'],
                            'message' => $questionData['message']
                        ]);
                    }
                }
            } catch (Exception $e) {
                // Log lỗi nhưng không ảnh hưởng đến kết quả
                $emailError = $e->getMessage();
                error_log('Question email sending failed: ' . $emailError);
            }
            
            $this->json([
                'success' => true,
                'message' => 'Câu hỏi của bạn đã được gửi! Chúng tôi sẽ phản hồi sớm nhất có thể.'
            ]);
            
        } catch (Exception $e) {
            error_log('Question form error: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            
            $this->json([
                'success' => false,
                'message' => 'Có lỗi hệ thống: ' . $e->getMessage()
            ]);
        }
    }
}