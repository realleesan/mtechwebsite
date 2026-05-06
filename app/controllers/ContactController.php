<?php
/**
 * ContactController - Xử lý trang liên hệ và form submission
 * Kế thừa BaseController để sử dụng các helper methods
 */

require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/../services/ValidationService.php';
require_once __DIR__ . '/../models/ContactsModel.php';

class ContactController extends BaseController
{
    /**
     * Hiển thị trang liên hệ
     * Tách từ index.php:610-617
     */
    public function index()
    {
        // Set các biến cho layout
        $title = 'Liên hệ - MTECHJSC';
        $content = 'contact/contact.php';
        $showPageHeader = true;
        $showCTA = false;
        $showBreadcrumb = true;
        
        // Thông tin liên hệ MTECH chuẩn từ hồ sơ năng lực
        $contactInfo = [
            'address' => 'Tòa nhà 227 phố Nguyễn Ngọc Nại, phường Khương Mai, Quận Thanh Xuân, TP. Hà Nội',
            'headquarters' => 'Số 8, ngõ 151, phố Định Công, Phường Định Công, Quận Hoàng Mai, TP. Hà Nội',
            'phone_primary' => '0243.6231691',
            'phone_secondary' => '0913.034.656',
            'email' => 'mtechjsc2011.info@gmail.com',
            'email_alt' => 'mtechjsc.info@gmail.com',
            'website' => 'www.mtechjsc.com',
            'description' => 'Công ty Cổ phần Tư vấn Kỹ thuật và Thương mại MTECH (MTECH.JSC) là đơn vị chuyên nghiệp được thành lập từ năm 2011, chuyên cung cấp các dịch vụ tư vấn kỹ thuật chuyên sâu cho các dự án đầu tư xây dựng quy mô lớn.',
            'map_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1770.29856830816!2d105.82591253855631!3d20.9965573951612!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ac8841f5e629%3A0x9e2493836c1f1359!2zMjI3IFAuIE5ndXnhu4VuIE5n4buNYyBO4bqhaSwgUGjGsMahbmcgTGnhu4d0LCBIw6AgTuG7mWkgMTAwMDAwLCBWaeG7h3QgTmFt!5e1!3m2!1svi!2s!4v1777798035503!5m2!1svi!2s',
            'map_label' => 'Tòa nhà 227 phố Nguyễn Ngọc Nại, Khương Mai, Thanh Xuân, Hà Nội'
        ];
        
        // Render view
        $this->view($content, [
            'title' => $title,
            'showPageHeader' => $showPageHeader,
            'showCTA' => $showCTA,
            'showBreadcrumb' => $showBreadcrumb,
            'contactInfo' => $contactInfo
        ]);
    }
    
    /**
     * Xử lý AJAX form submission
     * Tách từ app/views/contact/contact.php:7-145
     */
    public function submit()
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
                'name' => 'required',
                'email' => 'required|email',
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
            $contactData = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'phone' => isset($_POST['phone']) ? trim($_POST['phone']) : '',
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
                        $emailService->sendContactConfirmation($contactData);
                        // Gửi email thông báo cho admin
                        $emailService->sendNewContactNotification($contactData);
                    }
                }
            } catch (Exception $e) {
                // Log lỗi nhưng không ảnh hưởng đến kết quả
                $emailError = $e->getMessage();
                error_log('Email sending failed: ' . $emailError);
            }
            
            $this->json([
                'success' => true,
                'message' => 'Cảm ơn bạn đã liên hệ! Hệ thống đã nhận được thông tin và sẽ phản hồi sớm nhất.'
            ]);
            
        } catch (Exception $e) {
            error_log('Contact form error: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            
            $this->json([
                'success' => false,
                'message' => 'Có lỗi hệ thống: ' . $e->getMessage()
            ]);
        }
    }
}