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
                "INSERT INTO `{$this->table}` (name, email, phone, message, ip_address, user_agent, status, created_at) 
                 VALUES (:name, :email, :phone, :message, :ip_address, :user_agent, 0, NOW())"
            );

            $stmt->execute([
                ':name' => $data['name'],
                ':email' => $data['email'],
                ':phone' => $data['phone'] ?? null,
                ':message' => $data['message'],
                ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);

            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log('ContactsModel::create() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy tất cả contacts, sắp xếp theo mới nhất
     *
     * @param int $limit Số lượng cần lấy
     * @param int $offset Vị trí bắt đầu
     * @return array Mảng các contact
     */
    public function getAll($limit = 50, $offset = 0)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, name, email, phone, message, status, created_at 
                 FROM `{$this->table}`
                 ORDER BY created_at DESC 
                 LIMIT :limit OFFSET :offset"
            );
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('ContactsModel::getAll() - ' . $e->getMessage());
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
                "UPDATE `{$this->table}` SET status = ? WHERE id = ?"
            );
            return $stmt->execute([$status, $id]);
        } catch (PDOException $e) {
            error_log('ContactsModel::updateStatus() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa một contact
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare(
                "DELETE FROM `{$this->table}` WHERE id = ?"
            );
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('ContactsModel::delete() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Đếm tổng số contacts
     *
     * @param int|null $status Lọc theo trạng thái (optional)
     * @return int
     */
    public function count($status = null)
    {
        try {
            $sql = "SELECT COUNT(*) FROM `{$this->table}`";
            $params = [];
            
            if ($status !== null) {
                $sql .= " WHERE status = ?";
                $params[] = $status;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('ContactsModel::count() - ' . $e->getMessage());
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
