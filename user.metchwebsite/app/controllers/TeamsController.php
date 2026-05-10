<?php
/**
 * TeamsController - Xử lý trang đội ngũ và form câu hỏi
 * Kế thừa BaseController để sử dụng các helper methods
 */

require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../services/ValidationService.php';
require_once __DIR__ . '/../models/ContactsModel.php';
require_once __DIR__ . '/../models/TeamsModel.php';

class TeamsController extends BaseController
{
    /**
     * Hiển thị trang đội ngũ
     */
    public function index()
    {
        // Lấy danh sách thành viên từ database
        $teamsModel = new TeamsModel();
        $teams = $teamsModel->getAllActive();

        // Render view
        $this->view('about/teams.php', [
            'teams'          => $teams,
            'page'           => 'teams',
            'title'          => 'Đội ngũ - MTECHJSC',
            'showPageHeader' => true,
            'showCTA'        => false,
            'showBreadcrumb' => true,
        ]);
    }

    /**
     * Xử lý AJAX form submit câu hỏi
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
                'email'   => 'required|email',
                'subject' => 'required',
                'message' => 'required|min:10'
            ]);

            if ($validation->fails()) {
                $this->json([
                    'success' => false,
                    'message' => 'Vui lòng kiểm tra lại thông tin',
                    'errors'  => $validation->errors()
                ]);
            }

            // Chuẩn bị dữ liệu
            $questionData = [
                'name'       => trim($_POST['email']), // không có trường name, dùng email
                'email'      => trim($_POST['email']),
                'phone'      => null,
                'subject'    => trim($_POST['subject']),
                'message'    => trim($_POST['message']),
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

            // Gửi email thông báo (không bắt buộc)
            try {
                if (file_exists(__DIR__ . '/../services/EmailNotificationService.php')) {
                    require_once __DIR__ . '/../services/EmailNotificationService.php';
                    $emailService = new EmailNotificationService();

                    if ($emailService->isConfigured()) {
                        $emailService->sendQuestionConfirmation([
                            'email'   => $questionData['email'],
                            'subject' => $questionData['subject'],
                            'message' => $questionData['message']
                        ]);
                        $emailService->sendNewQuestionNotification([
                            'email'   => $questionData['email'],
                            'subject' => $questionData['subject'],
                            'message' => $questionData['message']
                        ]);
                    }
                }
            } catch (Exception $e) {
                error_log('Question email sending failed: ' . $e->getMessage());
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
