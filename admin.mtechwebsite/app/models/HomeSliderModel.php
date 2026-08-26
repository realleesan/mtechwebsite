<?php
/**
 * HomeSliderModel.php
 * 
 * Model xử lý dữ liệu bảng `home_sliders` - Hero Slider trang chủ.
 */

class HomeSliderModel
{
    /** @var PDO */
    private $db;

    /** @var string Tên bảng */
    private $table = 'home_sliders';

    private $adminBaseUrl = 'https://admin.mtechjsc.com';
    private $uploadDir    = '/assets/uploads/home-sliders/';
    private $maxFileSize  = 5 * 1024 * 1024; // 5MB
    private $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];

    private $imageFields = ['image_1', 'image_2', 'image_3'];

    public function __construct($database = null)
    {
        if (function_exists('env')) {
            $this->adminBaseUrl = env('ADMIN_BASE_URL', 'https://admin.mtechjsc.com');
        }
        if ($database) {
            $this->db = $database;
        } else {
            require_once __DIR__ . '/../../core/database.php';
            $this->db = getDBConnection();
            if (function_exists('env')) {
                $this->adminBaseUrl = env('ADMIN_BASE_URL', 'https://admin.mtechjsc.com');
            }
        }
    }

    public function getAll()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM `{$this->table}`
                 WHERE deleted_at IS NULL
                 ORDER BY sort_order ASC, id ASC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('HomeSliderModel::getAll() - ' . $e->getMessage());
            return [];
        }
    }

    public function getActive()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM `{$this->table}`
                 WHERE status = 1 AND deleted_at IS NULL
                 ORDER BY sort_order ASC, id ASC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('HomeSliderModel::getActive() - ' . $e->getMessage());
            return [];
        }
    }

    public function getActiveSlides()
    {
        return $this->getActive();
    }

    public function getById($id)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM `{$this->table}` WHERE id = ? LIMIT 1"
            );
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            error_log('HomeSliderModel::getById() - ' . $e->getMessage());
            return null;
        }
    }

    public function create(array $data)
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO `{$this->table}`
                 (sort_order, status, image_1, image_2, image_3)
                 VALUES (?,?,?,?,?)"
            );
            $ok = $stmt->execute([
                isset($data['sort_order']) ? (int)$data['sort_order'] : 0,
                isset($data['status']) ? (int)$data['status'] : 1,
                isset($data['image_1']) ? $data['image_1'] : '',
                isset($data['image_2']) ? $data['image_2'] : '',
                isset($data['image_3']) ? $data['image_3'] : '',
            ]);
            if ($ok) {
                return (int)$this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log('HomeSliderModel::create() - ' . $e->getMessage());
            return false;
        }
    }

    public function update($id, array $data)
    {
        try {
            $sets = [];
            $params = [];

            $fields = ['sort_order', 'status', 'image_1', 'image_2', 'image_3'];
            foreach ($fields as $field) {
                if (isset($data[$field])) {
                    $sets[] = "{$field} = ?";
                    $params[] = $data[$field];
                }
            }

            if (empty($sets)) return false;

            $params[] = $id;
            $sql = "UPDATE `{$this->table}` SET " . implode(', ', $sets) . " WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log('HomeSliderModel::update() - ' . $e->getMessage());
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE `{$this->table}` SET deleted_at = CURRENT_TIMESTAMP WHERE id = ?"
            );
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('HomeSliderModel::delete() - ' . $e->getMessage());
            return false;
        }
    }

    public function getTrashed($limit = 20, $offset = 0)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM `{$this->table}`
                 WHERE deleted_at IS NOT NULL
                 ORDER BY deleted_at DESC
                 LIMIT ? OFFSET ?"
            );
            $stmt->execute([$limit, $offset]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('HomeSliderModel::getTrashed() - ' . $e->getMessage());
            return [];
        }
    }

    public function countTrashed()
    {
        try {
            $stmt = $this->db->query(
                "SELECT COUNT(*) FROM `{$this->table}` WHERE deleted_at IS NOT NULL"
            );
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('HomeSliderModel::countTrashed() - ' . $e->getMessage());
            return 0;
        }
    }

    public function restore($id)
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE `{$this->table}` SET deleted_at = NULL WHERE id = ?"
            );
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('HomeSliderModel::restore() - ' . $e->getMessage());
            return false;
        }
    }

    public function hardDelete($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM `{$this->table}` WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('HomeSliderModel::hardDelete() - ' . $e->getMessage());
            return false;
        }
    }

    public function buildImageUrls(array $data)
    {
        $result = [];
        foreach ($this->imageFields as $field) {
            if (!empty($data[$field])) {
                $result[$field] = $this->adminBaseUrl . $this->uploadDir . $data[$field];
            } else {
                $result[$field] = '';
            }
        }
        return $result;
    }
}
