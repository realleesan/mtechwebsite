<?php
/**
 * JobApplicationModel.php
 *
 * Model xử lý CV ứng tuyển cho các vị trí tuyển dụng.
 * Hỗ trợ soft delete, phân trang, lọc theo status.
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

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }

        $this->ensureColumns();
    }

    // ----------------------------------------------------------------
    // Schema migration tự động
    // ----------------------------------------------------------------

    private function ensureColumns()
    {
        $columns = [
            'deleted_at'    => "ALTER TABLE `job_applications` ADD COLUMN `deleted_at` DATETIME NULL DEFAULT NULL",
            'updated_at'    => "ALTER TABLE `job_applications` ADD COLUMN `updated_at` DATETIME NULL DEFAULT NULL",
            'admin_note'    => "ALTER TABLE `job_applications` ADD COLUMN `admin_note` TEXT NULL DEFAULT NULL",
            'employer_reply'=> "ALTER TABLE `job_applications` ADD COLUMN `employer_reply` TEXT NULL DEFAULT NULL",
            'cover_letter'  => "ALTER TABLE `job_applications` ADD COLUMN `cover_letter` TEXT NULL DEFAULT NULL",
            'ip_address'    => "ALTER TABLE `job_applications` ADD COLUMN `ip_address` VARCHAR(45) NULL DEFAULT NULL",
        ];

        foreach ($columns as $col => $sql) {
            if (!$this->columnExists($col)) {
                try {
                    $this->db->exec($sql);
                } catch (\Exception $e) {
                    error_log("JobApplicationModel::ensureColumns() [{$col}] - " . $e->getMessage());
                }
            }
        }
    }

    private function columnExists($column)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = DATABASE() 
                 AND TABLE_NAME = 'job_applications' 
                 AND COLUMN_NAME = ?"
            );
            $stmt->execute([$column]);
            return (int)$stmt->fetchColumn() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    // ----------------------------------------------------------------
    // CREATE
    // ----------------------------------------------------------------

    /**
     * Tạo đơn ứng tuyển mới với upload CV.
     */
    public function createApplication($blogId, $fullName, $email, $phone, $position, $cvFile, $message = '')
    {
        try {
            $validation = $this->validateApplicationData($fullName, $email, $phone, $position, $cvFile);
            if (!$validation['valid']) {
                return ['success' => false, 'id' => null, 'error' => $validation['error']];
            }

            $uploadResult = $this->uploadCV($cvFile);
            if (!$uploadResult['success']) {
                return ['success' => false, 'id' => null, 'error' => $uploadResult['error']];
            }

            $stmt = $this->db->prepare(
                "INSERT INTO `job_applications` 
                (`blog_id`, `full_name`, `email`, `phone`, `position`, `cv_file`, `cover_letter`, `status`, `created_at`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())"
            );
            $stmt->execute([
                $blogId, $fullName, $email, $phone,
                $position, $uploadResult['path'], $message
            ]);

            return ['success' => true, 'id' => (int)$this->db->lastInsertId(), 'error' => null];

        } catch (PDOException $e) {
            error_log('JobApplicationModel::createApplication() - ' . $e->getMessage());
            return ['success' => false, 'id' => null, 'error' => 'Lỗi hệ thống'];
        }
    }

    // ----------------------------------------------------------------
    // READ
    // ----------------------------------------------------------------

    /**
     * Lấy tất cả đơn (chưa xóa) với phân trang và lọc status.
     */
    public function getAllApplications($status = 'all', $limit = 15, $offset = 0)
    {
        try {
            $where  = ['ja.deleted_at IS NULL'];
            $params = [];

            $allowed = ['pending', 'reviewing', 'approved', 'rejected'];
            if ($status !== 'all' && in_array($status, $allowed)) {
                $where[]  = 'ja.status = ?';
                $params[] = $status;
            }

            $whereClause = 'WHERE ' . implode(' AND ', $where);

            $sql = "SELECT ja.*, b.title as blog_title
                    FROM `job_applications` ja
                    LEFT JOIN `blogs` b ON ja.blog_id = b.id
                    {$whereClause}
                    ORDER BY ja.created_at DESC
                    LIMIT ? OFFSET ?";

            $params[] = (int)$limit;
            $params[] = (int)$offset;

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('JobApplicationModel::getAllApplications() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy chi tiết một đơn theo ID.
     */
    public function getApplicationById($id)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT ja.*, b.title as blog_title
                 FROM `job_applications` ja
                 LEFT JOIN `blogs` b ON ja.blog_id = b.id
                 WHERE ja.id = ?
                 LIMIT 1"
            );
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log('JobApplicationModel::getApplicationById() - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy đơn theo blog_id.
     */
    public function getApplicationsByBlogId($blogId, $status = 'all')
    {
        try {
            $where  = ['ja.blog_id = ?', 'ja.deleted_at IS NULL'];
            $params = [$blogId];

            $allowed = ['pending', 'reviewing', 'approved', 'rejected'];
            if ($status !== 'all' && in_array($status, $allowed)) {
                $where[]  = 'ja.status = ?';
                $params[] = $status;
            }

            $sql = "SELECT ja.* FROM `job_applications` ja
                    WHERE " . implode(' AND ', $where) . "
                    ORDER BY ja.created_at DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('JobApplicationModel::getApplicationsByBlogId() - ' . $e->getMessage());
            return [];
        }
    }

    // ----------------------------------------------------------------
    // COUNT
    // ----------------------------------------------------------------

    /**
     * Đếm tổng đơn chưa xóa, có thể lọc theo status.
     */
    public function countAll($status = null)
    {
        try {
            $where  = ['deleted_at IS NULL'];
            $params = [];

            $allowed = ['pending', 'reviewing', 'approved', 'rejected'];
            if ($status && in_array($status, $allowed)) {
                $where[]  = 'status = ?';
                $params[] = $status;
            }

            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM `job_applications` WHERE " . implode(' AND ', $where)
            );
            $stmt->execute($params);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('JobApplicationModel::countAll() - ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Đếm theo status cụ thể (chưa xóa).
     */
    public function countByStatus($status)
    {
        return $this->countAll($status);
    }

    /**
     * Đếm đơn trong thùng rác.
     */
    public function countTrashed()
    {
        try {
            $stmt = $this->db->query(
                "SELECT COUNT(*) FROM `job_applications` WHERE deleted_at IS NOT NULL"
            );
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('JobApplicationModel::countTrashed() - ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Đếm đơn theo blog_id.
     */
    public function countApplicationsByBlogId($blogId)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM `job_applications` WHERE `blog_id` = ? AND deleted_at IS NULL"
            );
            $stmt->execute([$blogId]);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    // ----------------------------------------------------------------
    // UPDATE
    // ----------------------------------------------------------------

    /**
     * Cập nhật status, ghi chú admin và phản hồi nhà tuyển dụng.
     */
    public function updateStatusAndNote($id, $status, $adminNote = null, $employerReply = null)
    {
        $allowed = ['pending', 'approved', 'rejected'];
        if (!in_array($status, $allowed)) return false;

        try {
            $stmt = $this->db->prepare(
                "UPDATE `job_applications` 
                 SET `status` = ?, `admin_note` = ?, `employer_reply` = ?, `updated_at` = NOW() 
                 WHERE `id` = ?"
            );
            return $stmt->execute([$status, $adminNote, $employerReply, $id]);
        } catch (PDOException $e) {
            error_log('JobApplicationModel::updateStatusAndNote() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật chỉ status (backward compat).
     */
    public function updateStatus($id, $status)
    {
        return $this->updateStatusAndNote($id, $status);
    }

    // ----------------------------------------------------------------
    // SOFT DELETE / RESTORE / HARD DELETE
    // ----------------------------------------------------------------

    public function softDelete($id)
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE `job_applications` SET `deleted_at` = NOW() WHERE `id` = ?"
            );
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('JobApplicationModel::softDelete() - ' . $e->getMessage());
            return false;
        }
    }

    public function restore($id)
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE `job_applications` SET `deleted_at` = NULL WHERE `id` = ?"
            );
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('JobApplicationModel::restore() - ' . $e->getMessage());
            return false;
        }
    }

    public function hardDelete($id)
    {
        try {
            $app = $this->getApplicationById($id);

            $stmt = $this->db->prepare(
                "DELETE FROM `job_applications` WHERE `id` = ?"
            );
            $result = $stmt->execute([$id]);

            // Xóa file CV nếu tồn tại
            if ($result && !empty($app['cv_file'])) {
                $fullPath = __DIR__ . '/../../' . $app['cv_file'];
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
            }

            return $result;
        } catch (PDOException $e) {
            error_log('JobApplicationModel::hardDelete() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy danh sách đơn trong thùng rác.
     */
    public function getTrashed($limit = 20, $offset = 0)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT ja.*, b.title as blog_title
                 FROM `job_applications` ja
                 LEFT JOIN `blogs` b ON ja.blog_id = b.id
                 WHERE ja.deleted_at IS NOT NULL
                 ORDER BY ja.deleted_at DESC
                 LIMIT ? OFFSET ?"
            );
            $stmt->execute([(int)$limit, (int)$offset]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('JobApplicationModel::getTrashed() - ' . $e->getMessage());
            return [];
        }
    }

    // ----------------------------------------------------------------
    // STATISTICS
    // ----------------------------------------------------------------

    public function getStatistics()
    {
        try {
            $stmt = $this->db->query(
                "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending'   THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'reviewing' THEN 1 ELSE 0 END) as reviewing,
                    SUM(CASE WHEN status = 'approved'  THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN status = 'rejected'  THEN 1 ELSE 0 END) as rejected
                 FROM `job_applications`
                 WHERE deleted_at IS NULL"
            );
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('JobApplicationModel::getStatistics() - ' . $e->getMessage());
            return ['total' => 0, 'pending' => 0, 'reviewing' => 0, 'approved' => 0, 'rejected' => 0];
        }
    }

    // ----------------------------------------------------------------
    // FILE UPLOAD (dùng từ user-side)
    // ----------------------------------------------------------------

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

    private function uploadCV($file)
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'path' => null, 'error' => 'Lỗi upload file'];
        }
        if ($file['size'] > $this->maxFileSize) {
            return ['success' => false, 'path' => null, 'error' => 'File quá lớn. Giới hạn 5MB'];
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $this->allowedExtensions)) {
            return ['success' => false, 'path' => null, 'error' => 'Chỉ chấp nhận file PDF'];
        }

        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, ['application/pdf', 'application/x-pdf'])) {
            return ['success' => false, 'path' => null, 'error' => 'File không phải PDF hợp lệ'];
        }

        $fileName = 'cv_' . uniqid() . '_' . time() . '.pdf';
        $filePath = $this->uploadDir . $fileName;
        $webPath  = 'uploads/cvs/' . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            return ['success' => false, 'path' => null, 'error' => 'Không thể lưu file'];
        }

        return ['success' => true, 'path' => $webPath, 'error' => null];
    }
}
