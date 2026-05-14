<?php
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../models/HeaderModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class HeaderController extends BaseController
{
    private $model;

    public function __construct()
    {
        AuthMiddleware::requireLogin();
        $this->model = new HeaderModel();
    }

    /**
     * Trang chính quản lý Header (Tổng quan)
     */
    public function index()
    {
        $header = $this->model->getSettingsWithFallback();
        $this->view('header/index', [
            'title'  => 'Quản lý Header - Admin MTech',
            'page'   => 'header',
            'header' => $header,
            'admin'  => AuthMiddleware::getAdmin(),
        ]);
    }

    /**
     * Trang cài đặt chung (Logo, Phone, ISO)
     */
    public function settings()
    {
        $header = $this->model->getSettingsWithFallback();
        $this->view('header/settings', [
            'title'  => 'Cài đặt Header - Admin MTech',
            'page'   => 'header',
            'header' => $header,
            'admin'  => AuthMiddleware::getAdmin(),
        ]);
    }

    /**
     * Xử lý cập nhật cài đặt chung
     */
    public function updateSettings()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/header/settings');
            return;
        }

        $data = [
            'logo_alt'   => $_POST['logo_alt'] ?? '',
            'phone'      => $_POST['phone'] ?? '',
            'phone_href' => $_POST['phone_href'] ?? '',
            'iso_text'   => $_POST['iso_text'] ?? '',
        ];

        // Xử lý upload logo
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../assets/images/';
            $fileName = 'logo_' . time() . '_' . $_FILES['logo']['name'];
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadDir . $fileName)) {
                $data['logo_path'] = 'assets/images/' . $fileName;
            }
        }

        if ($this->model->updateSettings($data)) {
            $_SESSION['success'] = 'Cập nhật cài đặt Header thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật cài đặt.';
        }

        $this->redirect('/header/settings');
    }

    /**
     * Trang quản lý Hồ sơ năng lực
     */
    public function profile()
    {
        $header = $this->model->getSettingsWithFallback();
        $this->view('header/profile', [
            'title'  => 'Quản lý Hồ sơ năng lực - Admin MTech',
            'page'   => 'header',
            'header' => $header,
            'admin'  => AuthMiddleware::getAdmin(),
        ]);
    }

    /**
     * Xử lý cập nhật Hồ sơ năng lực
     */
    public function updateProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/header/profile');
            return;
        }

        $data = [
            'profile_pdf_label' => $_POST['profile_pdf_label'] ?? '',
        ];

        // Xử lý upload profile PDF
        if (isset($_FILES['profile_pdf']) && $_FILES['profile_pdf']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../assets/files/';
            
            // Tạo thư mục nếu chưa tồn tại
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    $_SESSION['error'] = 'Không thể tạo thư mục uploads: ' . $uploadDir;
                    $this->redirect('/header/profile');
                    return;
                }
            }
            
            // Kiểm tra quyền ghi
            if (!is_writable($uploadDir)) {
                $_SESSION['error'] = 'Thư mục uploads không có quyền ghi: ' . $uploadDir;
                $this->redirect('/header/profile');
                return;
            }
            
            // Kiểm tra file type - chỉ chấp nhận PDF
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $_FILES['profile_pdf']['tmp_name']);
            finfo_close($finfo);
            
            if ($mimeType !== 'application/pdf') {
                $_SESSION['error'] = 'Chỉ chấp nhận file PDF. File bạn tải lên là: ' . $mimeType;
                $this->redirect('/header/profile');
                return;
            }
            
            // Lấy tên file gốc và chuyển sang .pdf
            $originalBaseName = pathinfo($_FILES['profile_pdf']['name'], PATHINFO_FILENAME);
            $fileName = 'hsnl_' . time() . '_' . $originalBaseName . '.pdf';
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['profile_pdf']['tmp_name'], $targetPath)) {
                $data['profile_pdf_path'] = 'assets/files/' . $fileName;
            } else {
                $_SESSION['error'] = 'Không thể di chuyển file PDF. Kiểm tra lại quyền thư mục.';
                $this->redirect('/header/profile');
                return;
            }
        }

        if ($this->model->updateSettings($data)) {
            $_SESSION['success'] = 'Cập nhật Hồ sơ năng lực thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật Hồ sơ năng lực.';
        }

        $this->redirect('/header/profile');
    }

    /**
     * Xử lý cập nhật nhanh từ trang index (giữ lại để tương thích)
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/header');
            return;
        }

        $data = [
            'logo_alt'          => $_POST['logo_alt'] ?? '',
            'phone'             => $_POST['phone'] ?? '',
            'phone_href'        => $_POST['phone_href'] ?? '',
            'iso_text'          => $_POST['iso_text'] ?? '',
            'profile_pdf_label' => $_POST['profile_pdf_label'] ?? '',
        ];

        // Xử lý upload logo
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../assets/images/';
            $fileName = 'logo_' . time() . '_' . $_FILES['logo']['name'];
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadDir . $fileName)) {
                $data['logo_path'] = 'assets/images/' . $fileName;
            }
        }

        // Xử lý upload profile PDF
        if (isset($_FILES['profile_pdf']) && $_FILES['profile_pdf']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../assets/files/';
            
            // Tạo thư mục nếu chưa tồn tại
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Kiểm tra file type - chỉ chấp nhận PDF
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $_FILES['profile_pdf']['tmp_name']);
            finfo_close($finfo);
            
            if ($mimeType !== 'application/pdf') {
                $_SESSION['error'] = 'Chỉ chấp nhận file PDF. File bạn tải lên là: ' . $mimeType;
                $this->redirect('/header');
                return;
            }
            
            // Lấy tên file gốc và chuyển sang .pdf
            $originalBaseName = pathinfo($_FILES['profile_pdf']['name'], PATHINFO_FILENAME);
            $fileName = 'hsnl_' . time() . '_' . $originalBaseName . '.pdf';
            
            if (move_uploaded_file($_FILES['profile_pdf']['tmp_name'], $uploadDir . $fileName)) {
                $data['profile_pdf_path'] = 'assets/files/' . $fileName;
            }
        }

        if ($this->model->updateSettings($data)) {
            $_SESSION['success'] = 'Cập nhật Header thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật Header.';
        }

        $this->redirect('/header');
    }
}
