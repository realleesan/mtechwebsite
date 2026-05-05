<?php
/**
 * Teams Page View
 * Template: docs/template/about/code/teams.html
 */

// Xử lý form submission cho Question form
if (isset($_GET['action']) && $_GET['action'] === 'submit_question' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!ob_get_level()) {
        ob_start();
    }

    error_reporting(E_ALL);
    ini_set('display_errors', 0);

    set_exception_handler(function($e) {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
        exit;
    });

    set_error_handler(function($errno, $errstr, $_errfile, $_errline) {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => "Error [$errno]: $errstr"]);
        exit;
    });

    try {
        $errors = [];
        $email   = isset($_POST['email'])   ? trim($_POST['email'])   : '';
        $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
        $message = isset($_POST['message']) ? trim($_POST['message']) : '';

        if (empty($email)) {
            $errors['email'] = 'Vui lòng nhập email';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ';
        }

        if (empty($subject)) {
            $errors['subject'] = 'Vui lòng nhập tiêu đề câu hỏi';
        }

        if (empty($message)) {
            $errors['message'] = 'Vui lòng nhập nội dung câu hỏi';
        } elseif (strlen($message) < 10) {
            $errors['message'] = 'Nội dung phải có ít nhất 10 ký tự';
        }

        if (!empty($errors)) {
            if (ob_get_level()) ob_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Vui lòng kiểm tra lại thông tin', 'errors' => $errors]);
            exit;
        }

        // Lưu vào bảng contacts (tái sử dụng model, subject đã có cột)
        require_once __DIR__ . '/../../models/ContactsModel.php';
        $contactsModel = new ContactsModel();

        $questionData = [
            'name'    => $email, // không có trường name, dùng email làm tên
            'email'   => $email,
            'phone'   => null,
            'subject' => $subject,
            'message' => $message,
        ];

        $contactId = $contactsModel->create($questionData);

        if ($contactId) {
            // Gửi email thông báo
            try {
                require_once __DIR__ . '/../../services/EmailNotificationService.php';
                $emailService = new EmailNotificationService();

                if ($emailService->isConfigured()) {
                    $emailService->sendQuestionConfirmation(['email' => $email, 'subject' => $subject, 'message' => $message]);
                    $emailService->sendNewQuestionNotification(['email' => $email, 'subject' => $subject, 'message' => $message]);
                }
            } catch (Exception $e) {
                error_log('Question email sending failed: ' . $e->getMessage());
            }

            if (ob_get_level()) ob_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Câu hỏi của bạn đã được gửi! Chúng tôi sẽ phản hồi sớm nhất có thể.']);
            exit;
        } else {
            if (ob_get_level()) ob_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi lưu thông tin. Vui lòng thử lại.']);
            exit;
        }

    } catch (Throwable $e) {
        error_log('Question form error: ' . $e->getMessage());
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Có lỗi hệ thống: ' . $e->getMessage()]);
        exit;
    }
}
?>

<!-- Team Area -->
<section class="team_area sec_gap">
    <div class="container">
        <div class="section_title mb_55">
            <h2 class="f_600 f_size_32 title_color">Đội Ngũ Chuyên Gia</h2>
            <span class="title_br"></span>
            <p class="mt_7">Sức mạnh cốt lõi của MTECH nằm ở đội ngũ gồm 25 Thạc sĩ, Kỹ sư và Chuyên gia am hiểu sâu sắc trong các lĩnh vực vật liệu, xây dựng, kiến trúc, cơ điện và kinh tế. Chúng tôi luôn tận tâm mang đến các giải pháp thiết kế và quản lý dự án tối ưu nhất.</p>
        </div>
        <div class="row team_inner mb-30">
            <div class="col-lg-3 col-sm-6">
                <div class="team_member text-center">
                    <div class="team_img">
                        <img src="https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/team2.jpg" alt="team">
                        <div class="overlay"></div>
                        <ul class="nav social_icon">
                            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                            <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                            <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                            <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                        </ul>
                    </div>
                    <h5 class="f_600 f_size_20 title_color mb-0">Nguyễn Tùng Giang</h5>
                    <p>Giám đốc</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="team_member text-center">
                    <div class="team_img">
                        <img src="https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/team1.jpg" alt="team">
                        <div class="overlay"></div>
                        <ul class="nav social_icon">
                            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                            <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                            <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                            <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                        </ul>
                    </div>
                    <h5 class="f_600 f_size_20 title_color mb-0">Đỗ Bá Dương</h5>
                    <p>Chủ nhiệm Khảo sát & Thiết kế</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="team_member text-center">
                    <div class="team_img">
                        <img src="https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/team3.jpg" alt="team">
                        <div class="overlay"></div>
                        <ul class="nav social_icon">
                            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                            <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                            <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                            <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                        </ul>
                    </div>
                    <h5 class="f_600 f_size_20 title_color mb-0">Trần Văn Bình</h5>
                    <p>Chủ trì thiết kế Điện</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="team_member text-center">
                    <div class="team_img">
                        <img src="https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/team4.jpg" alt="team">
                        <div class="overlay"></div>
                        <ul class="nav social_icon">
                            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                            <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                            <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                            <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                        </ul>
                    </div>
                    <h5 class="f_600 f_size_20 title_color mb-0">Nguyễn Ngọc Trường</h5>
                    <p>Chủ trì TK Cấp thoát nước</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Question Area -->
<section class="question_area sec_gap">
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <div class="section_title mb_30">
                    <h2 class="f_600 f_size_32 title_color">Bạn chưa tìm thấy câu trả lời? Hãy gửi câu hỏi cho MTECH</h2>
                    <span class="title_br dark"></span>
                </div>
                <div class="question_form">
                    <form action="/doi-ngu?action=submit_question" method="post" id="questionForm" novalidate>
                        <div class="form-group">
                            <input type="email" name="email" value="" class="form-control" id="q_email" aria-required="true" placeholder="Địa chỉ Email*" required />
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <input type="text" name="subject" value="" class="form-control" id="q_subject" aria-required="true" placeholder="Tiêu đề*" required />
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <textarea name="message" cols="40" rows="10" class="form-control" id="q_message" aria-required="true" placeholder="Nội dung câu hỏi của bạn" required></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <input type="submit" value="Gửi câu hỏi" class="btn submit_btn" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>