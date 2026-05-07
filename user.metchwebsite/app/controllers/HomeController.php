<?php
/**
 * HomeController - Xử lý trang chủ và form "Drop a Message"
 * Kế thừa BaseController để sử dụng các helper methods
 */

require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../services/ValidationService.php';
require_once __DIR__ . '/../models/ContactsModel.php';
require_once __DIR__ . '/../models/CategoriesModel.php';
require_once __DIR__ . '/../models/ProjectsModel.php';
require_once __DIR__ . '/../models/ClientLogosModel.php';
require_once __DIR__ . '/../models/BlogsModel.php';

class HomeController extends BaseController
{
    /**
     * Hiển thị trang chủ
     * Tách từ index.php:261-284
     */
    public function index()
    {
        // Load models và lấy dữ liệu cho trang chủ
        $categoriesModel    = new CategoriesModel();
        $projectsModel      = new ProjectsModel();
        $clientLogosModel   = new ClientLogosModel();
        $blogsModel         = new BlogsModel();

        $homeServices       = $categoriesModel->getHomeServices(6);
        $homeProjects       = $projectsModel->getHomeProjects(5);
        $clientLogos        = $clientLogosModel->getAllActive();
        $homeBlogs          = $blogsModel->getHomeBlogs(3);

        // Set các biến cho layout
        $title          = 'Trang chủ - MTECH';
        $content        = 'home/home.php';
        $showPageHeader = false;
        $showCTA        = true;
        $showBreadcrumb = false;
        
        // Render view
        $this->view($content, [
            'title' => $title,
            'showPageHeader' => $showPageHeader,
            'showCTA' => $showCTA,
            'showBreadcrumb' => $showBreadcrumb,
            'homeServices' => $homeServices,
            'homeProjects' => $homeProjects,
            'clientLogos' => $clientLogos,
            'homeBlogs' => $homeBlogs
        ]);
    }
    
    /**
     * Xử lý AJAX form "Drop a Message" từ trang chủ
     * Tách từ app/views/home/home.php:19-108
     */
    public function contactSubmit()
    {
        // Chỉ xử lý POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json([
                'success' => false,
                'message' => 'Method not allowed'
            ], 405);
        }
        
        try {
            // Validate input (form dùng tên field: name, email, tphone, subject, message)
            $validation = ValidationService::validate($_POST, [
                'name' => 'required',
                'email' => 'required|email',
                'tphone' => 'phone',
                'subject' => 'required',
                'message' => 'required|min:10'
            ]);
            
            if ($validation->fails()) {
                $errors = $validation->errors();
                $errorMessages = [];
                
                foreach ($errors as $field => $message) {
                    $fieldLabels = [
                        'name' => 'Họ tên',
                        'email' => 'Email', 
                        'tphone' => 'Số điện thoại',
                        'subject' => 'Tiêu đề',
                        'message' => 'Nội dung tin nhắn'
                    ];
                    $label = isset($fieldLabels[$field]) ? $fieldLabels[$field] : $field;
                    $errorMessages[] = $label . ': ' . $message;
                }
                
                $this->json([
                    'success' => false,
                    'message' => implode('. ', $errorMessages),
                    'errors' => $validation->errors()
                ]);
            }
            
            // Chuẩn bị dữ liệu (form dùng field names: name, email, tphone, subject, message)
            $contactData = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'phone' => isset($_POST['tphone']) ? trim($_POST['tphone']) : '',
                'subject' => isset($_POST['subject']) ? trim($_POST['subject']) : 'Tin nhắn từ trang chủ',
                'message' => trim($_POST['message']),
                'ip_address' => $this->getClientIP(),
                'user_agent' => $this->getUserAgent()
            ];
            
            // Lưu vào database
            $contactsModel = new ContactsModel();
            $contactId = $contactsModel->create($contactData);
            
            if (!$contactId) {
                $this->json([
                    'success' => false,
                    'message' => 'Có lỗi khi lưu thông tin. Vui lòng thử lại.'
                ]);
            }
            
            // Gửi email thông báo
            $emailError = null;
            try {
                if (file_exists(__DIR__ . '/../services/EmailNotificationService.php')) {
                    require_once __DIR__ . '/../services/EmailNotificationService.php';
                    $emailService = new EmailNotificationService();
                    
                    if ($emailService->isConfigured()) {
                        // Thông báo đến contact@mtech.com
                        $emailService->sendNewContactNotification($contactData);
                        // Xác nhận cho người gửi
                        $emailService->sendContactConfirmation($contactData);
                    }
                }
            } catch (Exception $e) {
                // Log lỗi nhưng không ảnh hưởng đến kết quả
                $emailError = $e->getMessage();
                error_log('[HomeContact] Lỗi gửi email: ' . $emailError);
            }
            
            $this->json([
                'success' => true,
                'message' => 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi trong vòng 48 giờ.'
            ]);
            
        } catch (Exception $e) {
            error_log('[HomeContact] Exception: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            
            $this->json([
                'success' => false,
                'message' => 'Có lỗi hệ thống. Vui lòng thử lại sau.'
            ]);
        }
    }
}