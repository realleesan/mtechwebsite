<?php
/**
 * FooterModel.php
 *
 * Model xử lý dữ liệu cho footer dynamic.
 * Bao gồm: useful links, newsletter, social icons
 */

class FooterModel
{
    /** @var PDO */
    private $db;

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

    // =================================================================
    // 1. FOOTER SETTINGS - Tiêu đề Useful Links
    // =================================================================

    /**
     * Lấy settings của footer (tiêu đề useful links)
     * @return array|null
     */
    public function getSettings()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM `footer_settings` LIMIT 1"
            );
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log('FooterModel::getSettings() - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cập nhật tiêu đề Useful Links
     * @param string $title
     * @return bool
     */
    public function updateUsefulLinksTitle($title)
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE `footer_settings` SET `useful_links_title` = ?"
            );
            return $stmt->execute([$title]);
        } catch (PDOException $e) {
            error_log('FooterModel::updateUsefulLinksTitle() - ' . $e->getMessage());
            return false;
        }
    }

    // =================================================================
    // 2. FOOTER LINKS - Useful Links items
    // =================================================================

    /**
     * Lấy tất cả footer links đang active
     * @return array
     */
    public function getAllLinks()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, title, url, sort_order, is_active
                 FROM `footer_links`
                 ORDER BY sort_order ASC, id ASC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('FooterModel::getAllLinks() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy chỉ các link đang active (để hiển thị ở footer)
     * @return array
     */
    public function getActiveLinks()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, title, url, sort_order
                 FROM `footer_links`
                 WHERE is_active = 1
                 ORDER BY sort_order ASC, id ASC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('FooterModel::getActiveLinks() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy một link theo ID
     * @param int $id
     * @return array|null
     */
    public function getLinkById($id)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM `footer_links` WHERE id = ? LIMIT 1"
            );
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log('FooterModel::getLinkById() - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Thêm link mới
     * @param array $data ['title', 'url', 'sort_order', 'is_active']
     * @return bool
     */
    public function addLink($data)
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO `footer_links` (title, url, sort_order, is_active)
                 VALUES (?, ?, ?, ?)"
            );
            return $stmt->execute([
                $data['title'],
                $data['url'],
                $data['sort_order'] ?? 0,
                $data['is_active'] ?? 1
            ]);
        } catch (PDOException $e) {
            error_log('FooterModel::addLink() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật link
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateLink($id, $data)
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE `footer_links`
                 SET title = ?, url = ?, sort_order = ?, is_active = ?
                 WHERE id = ?"
            );
            return $stmt->execute([
                $data['title'],
                $data['url'],
                $data['sort_order'] ?? 0,
                $data['is_active'] ?? 1,
                $id
            ]);
        } catch (PDOException $e) {
            error_log('FooterModel::updateLink() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa link
     * @param int $id
     * @return bool
     */
    public function deleteLink($id)
    {
        try {
            $stmt = $this->db->prepare(
                "DELETE FROM `footer_links` WHERE id = ?"
            );
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('FooterModel::deleteLink() - ' . $e->getMessage());
            return false;
        }
    }

    // =================================================================
    // 3. NEWSLETTER - Lưu email đăng ký
    // =================================================================

    /**
     * Lưu email đăng ký newsletter
     * @param string $email
     * @return bool
     */
    public function subscribeNewsletter($email)
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO `footer_newsletter` (email) VALUES (?)"
            );
            return $stmt->execute([$email]);
        } catch (PDOException $e) {
            // Email đã tồn tại
            if ($e->getCode() == 23000) {
                return false;
            }
            error_log('FooterModel::subscribeNewsletter() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Kiểm tra email đã đăng ký chưa
     * @param string $email
     * @return bool
     */
    public function isEmailSubscribed($email)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM `footer_newsletter` WHERE email = ?"
            );
            $stmt->execute([$email]);
            return (int) $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log('FooterModel::isEmailSubscribed() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy tất cả email đã đăng ký
     * @return array
     */
    public function getAllSubscribers()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM `footer_newsletter` ORDER BY created_at DESC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('FooterModel::getAllSubscribers() - ' . $e->getMessage());
            return [];
        }
    }

    // =================================================================
    // 4. SOCIAL ICONS - Facebook, LinkedIn, Twitter, Google
    // =================================================================

    /**
     * Lấy tất cả social links (cho admin)
     * @return array
     */
    public function getAllSocialLinks()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, platform, url, is_visible, sort_order
                 FROM `footer_social`
                 ORDER BY sort_order ASC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('FooterModel::getAllSocialLinks() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy chỉ các social icons đang visible (để hiển thị ở footer)
     * @return array
     */
    public function getVisibleSocialLinks()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT platform, url
                 FROM `footer_social`
                 WHERE is_visible = 1
                 ORDER BY sort_order ASC"
            );
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // Convert to associative array với key là platform
            $social = [];
            foreach ($result as $item) {
                $social[$item['platform']] = $item['url'];
            }
            return $social;
        } catch (PDOException $e) {
            error_log('FooterModel::getVisibleSocialLinks() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Cập nhật social link
     * @param string $platform (facebook, linkedin, twitter, google)
     * @param array $data ['url', 'is_visible']
     * @return bool
     */
    public function updateSocialLink($platform, $data)
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE `footer_social`
                 SET url = ?, is_visible = ?
                 WHERE platform = ?"
            );
            return $stmt->execute([
                $data['url'] ?? '#',
                $data['is_visible'] ?? 1,
                $platform
            ]);
        } catch (PDOException $e) {
            error_log('FooterModel::updateSocialLink() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy một social link theo platform
     * @param string $platform
     * @return array|null
     */
    public function getSocialLinkByPlatform($platform)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM `footer_social` WHERE platform = ? LIMIT 1"
            );
            $stmt->execute([$platform]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log('FooterModel::getSocialLinkByPlatform() - ' . $e->getMessage());
            return null;
        }
    }

    // =================================================================
    // 5. GET ALL FOOTER DATA - Lấy toàn bộ dữ liệu cho footer
    // =================================================================

    /**
     * Lấy tất cả dữ liệu cần thiết để render footer
     * @return array
     */
    public function getFooterData()
    {
        return [
            'settings' => $this->getSettings(),
            'useful_links' => $this->getActiveLinks(),
            'social' => $this->getVisibleSocialLinks()
        ];
    }
}