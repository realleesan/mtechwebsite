<?php
/**
 * ProjectsModel.php
 * 
 * Model xử lý dữ liệu dự án (Projects)
 * Cung cấp các phương thức CRUD và truy vấn dữ liệu
 */

class ProjectsModel {
    private $db;
    private $table = 'projects';
    
    /**
     * Constructor - Khởi tạo kết nối database
     */
    public function __construct($database = null) {
        if ($database) {
            $this->db = $database;
        } else {
            // Kết nối database mặc định
            require_once __DIR__ . '/../../core/database.php';
            $this->db = getDBConnection();
        }
    }
    
    /**
     * Lấy tất cả dự án (có phân trang)
     * @param int $limit Số lượng bản ghi mỗi trang
     * @param int $offset Vị trí bắt đầu
     * @param int $status Trạng thái: 0=inactive, 1=active, 2=featured, null=all
     * @return array Danh sách dự án
     */
    public function getAll($limit = 9, $offset = 0, $status = 1) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL";
            $params = [];
            
            if ($status !== null) {
                $sql .= " AND status = ?";
                $params[] = $status;
            }
            
            $sql .= " ORDER BY sort_order ASC, created_at DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ProjectsModel::getAll Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Lấy dự án theo ID
     * @param int $id ID dự án
     * @return array|null Thông tin dự án
     */
    public function getById($id) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE id = ? AND deleted_at IS NULL";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("ProjectsModel::getById Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Lấy dự án theo slug (cho URL thân thiện)
     * @param string $slug Slug dự án
     * @return array|null Thông tin dự án
     */
    public function getBySlug($slug) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE slug = ? AND deleted_at IS NULL AND status = 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$slug]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("ProjectsModel::getBySlug Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Lấy dự án theo danh mục
     * @param string $category Tên danh mục
     * @param int $limit Số lượng
     * @return array Danh sách dự án
     */
    public function getByCategory($category, $limit = 9) {
        try {
            $sql = "SELECT * FROM {$this->table} 
                    WHERE category = ? 
                    AND deleted_at IS NULL 
                    AND status = 1 
                    ORDER BY sort_order ASC, created_at DESC 
                    LIMIT ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$category, $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ProjectsModel::getByCategory Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Lấy dự án nổi bật (featured)
     * @param int $limit Số lượng
     * @return array Danh sách dự án nổi bật
     */
    public function getFeatured($limit = 6) {
        try {
            $sql = "SELECT * FROM {$this->table} 
                    WHERE status = 2 
                    AND deleted_at IS NULL 
                    ORDER BY sort_order ASC, created_at DESC 
                    LIMIT ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ProjectsModel::getFeatured Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Tìm kiếm dự án
     * @param string $keyword Từ khóa
     * @param int $limit Số lượng
     * @return array Kết quả tìm kiếm
     */
    public function search($keyword, $limit = 9) {
        try {
            $sql = "SELECT * FROM {$this->table} 
                    WHERE (title LIKE ? OR description LIKE ? OR category LIKE ?) 
                    AND deleted_at IS NULL 
                    AND status = 1 
                    ORDER BY created_at DESC 
                    LIMIT ?";
            $searchTerm = "%{$keyword}%";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ProjectsModel::search Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Đếm tổng số dự án
     * @param int $status Trạng thái
     * @return int Tổng số dự án
     */
    public function count($status = 1) {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->table} WHERE deleted_at IS NULL";
            $params = [];
            
            if ($status !== null) {
                $sql .= " AND status = ?";
                $params[] = $status;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("ProjectsModel::count Error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Lấy tất cả danh mục (distinct)
     * @return array Danh sách danh mục
     */
    public function getCategories() {
        try {
            $sql = "SELECT DISTINCT category FROM {$this->table} 
                    WHERE deleted_at IS NULL 
                    AND status = 1 
                    ORDER BY category ASC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("ProjectsModel::getCategories Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Tạo dự án mới
     * @param array $data Dữ liệu dự án
     * @return int|bool ID dự án mới hoặc false
     */
    public function create($data) {
        try {
            $fields = ['title', 'slug', 'category', 'description', 'content', 'image', 
                      'gallery', 'client', 'location', 'project_date', 'status', 
                      'sort_order', 'meta_title', 'meta_description'];
            
            $insertData = [];
            $columns = [];
            $values = [];
            
            foreach ($fields as $field) {
                if (isset($data[$field])) {
                    $columns[] = $field;
                    $values[] = '?';
                    $insertData[] = $data[$field];
                }
            }
            
            $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") 
                    VALUES (" . implode(', ', $values) . ")";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($insertData);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("ProjectsModel::create Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cập nhật dự án
     * @param int $id ID dự án
     * @param array $data Dữ liệu cập nhật
     * @return bool Kết quả
     */
    public function update($id, $data) {
        try {
            $fields = ['title', 'slug', 'category', 'description', 'content', 'image', 
                      'gallery', 'client', 'location', 'project_date', 'status', 
                      'sort_order', 'meta_title', 'meta_description'];
            
            $updateData = [];
            $sets = [];
            
            foreach ($fields as $field) {
                if (isset($data[$field])) {
                    $sets[] = "{$field} = ?";
                    $updateData[] = $data[$field];
                }
            }
            
            if (empty($sets)) {
                return false;
            }
            
            $updateData[] = $id;
            $sql = "UPDATE {$this->table} SET " . implode(', ', $sets) . " WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($updateData);
        } catch (PDOException $e) {
            error_log("ProjectsModel::update Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Xóa mềm dự án (soft delete)
     * @param int $id ID dự án
     * @return bool Kết quả
     */
    public function delete($id) {
        try {
            $sql = "UPDATE {$this->table} SET deleted_at = CURRENT_TIMESTAMP WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("ProjectsModel::delete Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Xóa vĩnh viễn dự án (hard delete)
     * @param int $id ID dự án
     * @return bool Kết quả
     */
    public function hardDelete($id) {
        try {
            $sql = "DELETE FROM {$this->table} WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("ProjectsModel::hardDelete Error: " . $e->getMessage());
            return false;
        }
    }
}
