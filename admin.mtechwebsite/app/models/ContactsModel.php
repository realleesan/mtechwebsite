<?php
/**
 * ContactsModel.php
 * 
 * Model xử lý dữ liệu bảng `contacts`.
 * Chịu trách nhiệm lưu trữ và truy vấn thông tin liên hệ.
 */

class ContactsModel
{
    /** @var PDO */
    private $db;

    /** @var string Tên bảng */
    private $table = 'contacts';

    /**
     * Constructor - Khởi tạo kết nối database
     * @param PDO|null $database Inject PDO từ ngoài (optional)
     */
    public function __construct($database = null)
    {
        if ($database) {
            $this->db = $database;
        } else {
            require_once __DIR__ . '/../../core/database.php';
            $this->db = getDBConnection();
        }
        $this->ensureColumns();
    }

    /**
     * Kiểm tra cột deleted_at có tồn tại không
     * Cache kết quả để tránh query nhiều lần
     */
    private $hasDeletedAt = null;
    private $hasAdminReply = null;

    private function columnExists($column)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = DATABASE() 
                 AND TABLE_NAME = ? 
                 AND COLUMN_NAME = ?"
            );
            $stmt->execute([$this->table, $column]);
            return (int)$stmt->fetchColumn() > 0;
        } catch (\Exception $e) {
            // Fallback: thử query trực tiếp
            try {
                $this->db->query("SELECT `{$column}` FROM `{$this->table}` LIMIT 0");
                return true;
            } catch (\Exception $e2) {
                return false;
            }
        }
    }

    /**
     * Tự động thêm các cột mới nếu chưa tồn tại trong bảng
     * Giải quyết vấn đề migration chưa chạy
     */
    private function ensureColumns()
    {
        try {
            $this->hasDeletedAt  = $this->columnExists('deleted_at');
            $this->hasAdminReply = $this->columnExists('admin_reply');

            if (!$this->hasDeletedAt) {
                $this->db->exec("ALTER TABLE `{$this->table}` ADD COLUMN `deleted_at` DATETIME NULL DEFAULT NULL");
                try {
                    $this->db->exec("ALTER TABLE `{$this->table}` ADD INDEX `idx_deleted_at` (`deleted_at`)");
                } catch (\Exception $e) { /* index có thể đã tồn tại */ }
                $this->hasDeletedAt = true;
            }
            if (!$this->hasAdminReply) {
                $this->db->exec("ALTER TABLE `{$this->table}` ADD COLUMN `admin_reply` TEXT NULL DEFAULT NULL AFTER `message`");
                $this->hasAdminReply = true;
            }
            // updated_at
            if (!$this->columnExists('updated_at')) {
                $this->db->exec("ALTER TABLE `{$this->table}` ADD COLUMN `updated_at` DATETIME NULL DEFAULT NULL");
            }
        } catch (\Exception $e) {
            error_log('ContactsModel::ensureColumns() - ' . $e->getMessage());
            // Nếu ALTER TABLE thất bại (không có quyền), đặt lại flag
            $this->hasDeletedAt  = $this->columnExists('deleted_at');
            $this->hasAdminReply = $this->columnExists('admin_reply');
        }
    }

    // ----------------------------------------------------------------
    // PUBLIC METHODS - CRUD
    // ----------------------------------------------------------------

    /**
     * Tạo một contact mới từ form liên hệ
     *
     * @param array $data Dữ liệu form: name, email, phone, message
     * @return int|false ID của contact vừa tạo hoặc false nếu thất bại
     */
    public function create($data)
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO `{$this->table}` (name, email, phone, subject, message, ip_address, user_agent, status, created_at) 
                 VALUES (:name, :email, :phone, :subject, :message, :ip_address, :user_agent, 0, NOW())"
            );

            $stmt->execute([
                ':name'       => $data['name'],
                ':email'      => $data['email'],
                ':phone'      => $data['phone']   ?? null,
                ':subject'    => $data['subject'] ?? null,
                ':message'    => $data['message'],
                ':ip_address' => $data['ip_address'] ?? null,
                ':user_agent' => $data['user_agent'] ?? null,
            ]);

            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log('ContactsModel::create() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy tất cả contacts (chưa xóa), sắp xếp theo mới nhất
     *
     * @param int $limit Số lượng cần lấy
     * @param int $offset Vị trí bắt đầu
     * @param string|null $search Từ khóa tìm kiếm
     * @param int|null $statusFilter Lọc theo trạng thái
     * @return array Mảng các contact
     */
    public function getAll($limit = 50, $offset = 0, $search = null, $statusFilter = null)
    {
        // Thử query với deleted_at trước, nếu fail thì query không có deleted_at
        $filters = [];
        $params  = [];

        if ($statusFilter !== null && $statusFilter !== '') {
            $filters[] = 'status = :status_filter';
            $params[':status_filter'] = (int)$statusFilter;
        }

        // Thử với deleted_at IS NULL
        try {
            $where = array_merge(['deleted_at IS NULL'], $filters);
            $whereClause = 'WHERE ' . implode(' AND ', $where);

            $stmt = $this->db->prepare(
                "SELECT id, name, email, phone, subject, message, status, created_at 
                 FROM `{$this->table}`
                 {$whereClause}
                 ORDER BY created_at DESC 
                 LIMIT :limit OFFSET :offset"
            );
            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $this->hasDeletedAt = true;
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // deleted_at chưa tồn tại → query không có điều kiện đó
            $this->hasDeletedAt = false;
        }

        // Fallback: không dùng deleted_at
        try {
            $whereClause = !empty($filters) ? 'WHERE ' . implode(' AND ', $filters) : '';

            $stmt = $this->db->prepare(
                "SELECT id, name, email, phone, subject, message, status, created_at 
                 FROM `{$this->table}`
                 {$whereClause}
                 ORDER BY created_at DESC 
                 LIMIT :limit OFFSET :offset"
            );
            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('ContactsModel::getAll() fallback - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy một contact theo ID
     *
     * @param int $id
     * @return array|null
     */
    public function getById($id)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM `{$this->table}` WHERE id = ? LIMIT 1"
            );
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log('ContactsModel::getById() - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cập nhật trạng thái contact
     *
     * @param int $id
     * @param int $status 0=chưa đọc, 1=đã đọc, 2=đã phản hồi
     * @return bool
     */
    public function updateStatus($id, $status)
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE `{$this->table}` SET status = ?, updated_at = NOW() WHERE id = ?"
            );
            return $stmt->execute([$status, $id]);
        } catch (PDOException $e) {
            error_log('ContactsModel::updateStatus() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật status và admin_reply
     *
     * @param int $id
     * @param int $status
     * @param string|null $adminReply
     * @return bool
     */
    public function update($id, $status, $adminReply = null)
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE `{$this->table}` SET status = ?, admin_reply = ?, updated_at = NOW() WHERE id = ?"
            );
            return $stmt->execute([$status, $adminReply, $id]);
        } catch (PDOException $e) {
            error_log('ContactsModel::update() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Soft delete (chuyển vào thùng rác)
     *
     * @param int $id
     * @return bool
     */
    public function softDelete($id)
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE `{$this->table}` SET deleted_at = NOW() WHERE id = ?"
            );
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('ContactsModel::softDelete() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Khôi phục từ thùng rác
     *
     * @param int $id
     * @return bool
     */
    public function restore($id)
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE `{$this->table}` SET deleted_at = NULL WHERE id = ?"
            );
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('ContactsModel::restore() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa vĩnh viễn
     *
     * @param int $id
     * @return bool
     */
    public function hardDelete($id)
    {
        try {
            $stmt = $this->db->prepare(
                "DELETE FROM `{$this->table}` WHERE id = ?"
            );
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('ContactsModel::hardDelete() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy danh sách đã xóa (thùng rác)
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getTrashed($limit = 50, $offset = 0)
    {
        if (!$this->hasDeletedAt) return [];
        try {
            $stmt = $this->db->prepare(
                "SELECT id, name, email, phone, subject, status, created_at, deleted_at
                 FROM `{$this->table}`
                 WHERE deleted_at IS NOT NULL
                 ORDER BY deleted_at DESC
                 LIMIT :limit OFFSET :offset"
            );
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('ContactsModel::getTrashed() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Đếm số bản ghi trong thùng rác
     *
     * @return int
     */
    public function countTrashed()
    {
        if (!$this->hasDeletedAt) return 0;
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM `{$this->table}` WHERE deleted_at IS NOT NULL"
            );
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('ContactsModel::countTrashed() - ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Đếm tổng số contacts (chưa xóa)
     *
     * @param int|null $status Lọc theo trạng thái (optional)
     * @param string|null $search Từ khóa tìm kiếm
     * @return int
     */
    public function count($status = null, $search = null)
    {
        $filters = [];
        $params  = [];

        if ($status !== null) {
            $filters[] = 'status = ?';
            $params[]  = $status;
        }

        // Thử với deleted_at IS NULL
        try {
            $where = array_merge(['deleted_at IS NULL'], $filters);
            $whereClause = 'WHERE ' . implode(' AND ', $where);
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM `{$this->table}` {$whereClause}");
            $stmt->execute($params);
            $this->hasDeletedAt = true;
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            $this->hasDeletedAt = false;
        }

        // Fallback: không dùng deleted_at
        try {
            $whereClause = !empty($filters) ? 'WHERE ' . implode(' AND ', $filters) : '';
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM `{$this->table}` {$whereClause}");
            $stmt->execute($params);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('ContactsModel::count() fallback - ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Đếm số contact chưa đọc
     *
     * @return int
     */
    public function countUnread()
    {
        return $this->count(0);
    }
}
