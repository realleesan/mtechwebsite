<?php
/**
 * JobApplicationModel.php
 *
 * Model xử lý CV ứng tuyển cho các vị trí tuyển dụng.
 */

class JobApplicationModel
{
    /** @var PDO */
    private $db;

    /** @var string Thư mục upload CV */
    private $uploadDir;

    /** @var int Max file size (5MB) */
    private $maxFileSize = 5242880;

    /** @var array Allowed file extensions */
    private $allowedExtensions = ['pdf'];

    public function __construct($database = null)
    {
        if ($database) {
            $this->db = $database;
        } else {
            require_once __DIR__ . '/../../core/database.php';
            $this->db = getDBConnection();
        }

        $this->uploadDir = __DIR__ . '/../../uploads/cvs/';

        // Tạo thư mục upload nếu chưa tồn tại
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    // ----------------------------------------------------------------
    // CREATE - Tạo đơn ứng tuyển mới
    // ----------------------------------------------------------------

    /**
     * Tạo đơn ứng tuyển mới với upload CV.
     *
     * @param int    $blogId      ID của bài tuyển dụng
     * @param string $fullName    Họ và tên
     * @param string $email       Email
     * @param string $phone       Số điện thoại
     * @param string $position    Vị trí ứng tuyển
     * @param array  $cvFile      $_FILES['cv'] array
     * @param string $message     Nội dung (optional)
     * @return array ['success' => bool, 'id' => int|null, 'error' => string|null]
     */
    public function createApplication($blogId, $fullName, $email, $phone, $position, $cvFile, $message = '')
    {
        try {
            // Validate dữ liệu
            $validation = $this->validateApplicationData($fullName, $email, $phone, $position, $cvFile);
            if (!$validation['valid']) {
                return ['success' => false, 'id' => null, 'error' => $validation['error']];
            }

            // Upload CV
            $uploadResult = $this->uploadCV($cvFile);
            if (!$uploadResult['success']) {
                return ['success' => false, 'id' => null, 'error' => $uploadResult['error']];
            }

            $cvPath = $uploadResult['path'];

            // Lưu vào database
            $stmt = $this->db->prepare(
                "INSERT INTO `job_applications` 
                (`blog_id`, `full_name`, `email`, `phone`, `position`, `cv_file`, `message`, `status`, `created_at`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())"
            );
            $stmt->execute([
                $blogId,
                $fullName,
                $email,
                $phone,
                $position,
                $cvPath,
                $message
            ]);

            $applicationId = (int) $this->db->lastInsertId();

            return [
                'success' => true,
                'id' => $applicationId,
                'cv_path' => $cvPath,
                'error' => null
            ];

        } catch (PDOException $e) {
            error_log('JobApplicationModel::createApplication() - ' . $e->getMessage());
            return ['success' => false, 'id' => null, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }

    // ----------------------------------------------------------------
    // VALIDATION
    // ----------------------------------------------------------------

    /**
     * Validate dữ liệu ứng tuyển.
     *
     * @return array ['valid' => bool, 'error' => string|null]
     */
    private function validateApplicationData($fullName, $email, $phone, $position, $cvFile)
    {
        if (empty($fullName) || strlen($fullName) < 2) {
            return ['valid' => false, 'error' => 'Họ và tên phải có ít nhất 2 ký tự'];
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'error' => 'Email không hợp lệ'];
        }

        if (empty($phone) || !preg_match('/^[0-9\s\-\+]{9,15}$/', $phone)) {
            return ['valid' => false, 'error' => 'Số điện thoại không hợp lệ'];
        }

        if (empty($position)) {
            return ['valid' => false, 'error' => 'Vui lòng chọn vị trí ứng tuyển'];
        }

        if (empty($cvFile) || $cvFile['error'] !== UPLOAD_ERR_OK) {
            return ['valid' => false, 'error' => 'Vui lòng tải lên CV (PDF)'];
        }

        return ['valid' => true, 'error' => null];
    }

    // ----------------------------------------------------------------
    // FILE UPLOAD
    // ----------------------------------------------------------------

    /**
     * Upload CV file.
     *
     * @param array $file $_FILES['cv'] array
     * @return array ['success' => bool, 'path' => string|null, 'error' => string|null]
     */
    private function uploadCV($file)
    {
        // Kiểm tra lỗi upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'path' => null, 'error' => 'Lỗi upload file: ' . $this->getUploadError($file['error'])];
        }

        // Kiểm tra kích thước
        if ($file['size'] > $this->maxFileSize) {
            return ['success' => false, 'path' => null, 'error' => 'File quá lớn. Giới hạn 5MB'];
        }

        // Kiểm tra extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $this->allowedExtensions)) {
            return ['success' => false, 'path' => null, 'error' => 'Chỉ chấp nhận file PDF'];
        }

        // Kiểm tra MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowedMimes = ['application/pdf', 'application/x-pdf'];
        if (!in_array($mimeType, $allowedMimes)) {
            return ['success' => false, 'path' => null, 'error' => 'File không phải PDF hợp lệ'];
        }

        // Tạo tên file unique
        $fileName = 'cv_' . uniqid() . '_' . time() . '.pdf';
        $filePath = $this->uploadDir . $fileName;
        $webPath = 'uploads/cvs/' . $fileName;

        // Di chuyển file
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            return ['success' => false, 'path' => null, 'error' => 'Không thể lưu file'];
        }

        return ['success' => true, 'path' => $webPath, 'error' => null];
    }

    /**
     * Lấy message từ upload error code.
     */
    private function getUploadError($code)
    {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'File vượt quá kích thước cho phép của server',
            UPLOAD_ERR_FORM_SIZE => 'File vượt quá kích thước form cho phép',
            UPLOAD_ERR_PARTIAL => 'File upload bị gián đoạn',
            UPLOAD_ERR_NO_FILE => 'Không có file nào được upload',
            UPLOAD_ERR_NO_TMP_DIR => 'Thiếu thư mục tạm',
            UPLOAD_ERR_CANT_WRITE => 'Không thể ghi file',
            UPLOAD_ERR_EXTENSION => 'Upload bị dừng bởi extension',
        ];
        return $errors[$code] ?? 'Lỗi không xác định';
    }

    // ----------------------------------------------------------------
    // READ - Lấy danh sách và chi tiết
    // ----------------------------------------------------------------

    /**
     * Lấy danh sách đơn ứng tuyển theo blog_id.
     *
     * @param int $blogId
     * @param string $status Filter theo status (pending/approved/rejected hoặc 'all')
     * @return array
     */
    public function getApplicationsByBlogId($blogId, $status = 'all')
    {
        try {
            $sql = "SELECT ja.*, b.title as blog_title 
                    FROM `job_applications` ja
                    LEFT JOIN `blogs` b ON ja.blog_id = b.id
                    WHERE ja.blog_id = ?";
            $params = [$blogId];

            if ($status !== 'all' && in_array($status, ['pending', 'approved', 'rejected'])) {
                $sql .= " AND ja.status = ?";
                $params[] = $status;
            }

            $sql .= " ORDER BY ja.created_at DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('JobApplicationModel::getApplicationsByBlogId() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy chi tiết một đơn ứng tuyển.
     *
     * @param int $applicationId
     * @return array|null
     */
    public function getApplicationById($applicationId)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT ja.*, b.title as blog_title, b.position as blog_position
                 FROM `job_applications` ja
                 LEFT JOIN `blogs` b ON ja.blog_id = b.id
                 WHERE ja.id = ?
                 LIMIT 1"
            );
            $stmt->execute([$applicationId]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log('JobApplicationModel::getApplicationById() - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy tất cả đơn ứng tuyển (cho admin).
     *
     * @param string $status Filter theo status
     * @param int $limit
     * @return array
     */
    public function getAllApplications($status = 'all', $limit = 50)
    {
        try {
            $sql = "SELECT ja.*, b.title as blog_title, b.position as blog_position
                    FROM `job_applications` ja
                    LEFT JOIN `blogs` b ON ja.blog_id = b.id
                    WHERE 1=1";
            $params = [];

            if ($status !== 'all' && in_array($status, ['pending', 'approved', 'rejected'])) {
                $sql .= " AND ja.status = ?";
                $params[] = $status;
            }

            $sql .= " ORDER BY ja.created_at DESC LIMIT ?";
            $params[] = $limit;

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('JobApplicationModel::getAllApplications() - ' . $e->getMessage());
            return [];
        }
    }

    // ----------------------------------------------------------------
    // UPDATE - Cập nhật trạng thái
    // ----------------------------------------------------------------

    /**
     * Cập nhật trạng thái đơn ứng tuyển.
     *
     * @param int    $applicationId
     * @param string $status        pending/approved/rejected
     * @return bool
     */
    public function updateStatus($applicationId, $status)
    {
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return false;
        }

        try {
            $stmt = $this->db->prepare(
                "UPDATE `job_applications` SET `status` = ?, `updated_at` = NOW() WHERE `id` = ?"
            );
            return $stmt->execute([$status, $applicationId]);
        } catch (PDOException $e) {
            error_log('JobApplicationModel::updateStatus() - ' . $e->getMessage());
            return false;
        }
    }

    // ----------------------------------------------------------------
    // DELETE - Xóa đơn ứng tuyển
    // ----------------------------------------------------------------

    /**
     * Xóa đơn ứng tuyển (cùng file CV).
     *
     * @param int $applicationId
     * @return bool
     */
    public function deleteApplication($applicationId)
    {
        try {
            // Lấy thông tin file CV trước
            $app = $this->getApplicationById($applicationId);
            if (!$app) {
                return false;
            }

            // Xóa record
            $stmt = $this->db->prepare("DELETE FROM `job_applications` WHERE `id` = ?");
            $result = $stmt->execute([$applicationId]);

            // Xóa file CV nếu tồn tại
            if ($result && !empty($app['cv_file'])) {
                $fullPath = __DIR__ . '/../../' . $app['cv_file'];
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }

            return $result;
        } catch (PDOException $e) {
            error_log('JobApplicationModel::deleteApplication() - ' . $e->getMessage());
            return false;
        }
    }

    // ----------------------------------------------------------------
    // STATISTICS - Thống kê
    // ----------------------------------------------------------------

    /**
     * Lấy thống kê đơn ứng tuyển.
     *
     * @return array
     */
    public function getStatistics()
    {
        try {
            $stmt = $this->db->query(
                "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
                 FROM `job_applications`"
            );
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('JobApplicationModel::getStatistics() - ' . $e->getMessage());
            return ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0];
        }
    }

    /**
     * Đếm số đơn ứng tuyển theo blog_id.
     *
     * @param int $blogId
     * @return int
     */
    public function countApplicationsByBlogId($blogId)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM `job_applications` WHERE `blog_id` = ?"
            );
            $stmt->execute([$blogId]);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('JobApplicationModel::countApplicationsByBlogId() - ' . $e->getMessage());
            return 0;
        }
    }
}
