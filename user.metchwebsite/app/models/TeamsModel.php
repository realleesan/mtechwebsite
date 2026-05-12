<?php
/**
 * TeamsModel.php
 * Model xử lý dữ liệu bảng `teams` — đội ngũ chuyên gia.
 */

class TeamsModel
{
    /** @var PDO */
    private $db;
    private $table = 'teams';

    public function __construct($database = null)
    {
        if ($database) {
            $this->db = $database;
        } else {
            require_once __DIR__ . '/../../core/database.php';
            $this->db = getDBConnection();
        }
    }

    /**
     * Lấy tất cả thành viên đang active, sắp xếp theo sort_order.
     * Dùng cho trang hiển thị công khai.
     *
     * @return array
     */
    public function getAllActive()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, name, position, image, bio
                 FROM `{$this->table}`
<<<<<<< HEAD
                 WHERE status = 1
=======
                 WHERE status = 1 AND deleted_at IS NULL
>>>>>>> 70909aa2291eb80ef37d22b71f05807579217647
                 ORDER BY sort_order ASC, id ASC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('TeamsModel::getAllActive() - ' . $e->getMessage());
            return [];
        }
    }
<<<<<<< HEAD
=======

    /**
     * Lấy tối đa 4 thành viên được đánh dấu hiển thị trên trang Giới thiệu.
     * Dùng cho section đội ngũ trong about.php.
     *
     * @return array
     */
    public function getAboutTeams(): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, name, position, image, bio
                 FROM `{$this->table}`
                 WHERE status = 1 AND deleted_at IS NULL AND show_in_about = 1
                 ORDER BY sort_order ASC, id ASC
                 LIMIT 4"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('TeamsModel::getAboutTeams() - ' . $e->getMessage());
            return [];
        }
    }
>>>>>>> 70909aa2291eb80ef37d22b71f05807579217647
}
