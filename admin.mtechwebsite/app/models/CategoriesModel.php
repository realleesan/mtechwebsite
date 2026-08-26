<?php
/**
 * CategoriesModel.php
 * 
 * Model xử lý dữ liệu bảng `categories`.
 * Chịu trách nhiệm truy vấn và trả về dữ liệu cho View.
 */

class CategoriesModel
{
    /** @var PDO */
    private $db;

    /** @var string Tên bảng */
    private $table = 'categories';

    private static $lastError = null;

    public static function getLastError(): ?string
    {
        return self::$lastError;
    }

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

    private function columnExists(string $column): bool
    {
        try {
            $stmt = $this->db->prepare("SHOW COLUMNS FROM `{$this->table}` LIKE ?");
            $stmt->execute([$column]);
            return (bool)$stmt->fetch();
        } catch (\Exception $e) {
            try {
                $this->db->query("SELECT `{$column}` FROM `{$this->table}` LIMIT 0");
                return true;
            } catch (\Exception $e2) {
                return false;
            }
        }
    }

    public function ensureColumns(): void
    {
        $columns = [
            'parent_id'           => "ALTER TABLE `{$this->table}` ADD COLUMN `parent_id` INT NULL DEFAULT NULL",
            'detail_description'  => "ALTER TABLE `{$this->table}` ADD COLUMN `detail_description` LONGTEXT NULL DEFAULT NULL",
            'image_1'             => "ALTER TABLE `{$this->table}` ADD COLUMN `image_1` VARCHAR(255) NULL DEFAULT NULL",
            'image_2'             => "ALTER TABLE `{$this->table}` ADD COLUMN `image_2` VARCHAR(255) NULL DEFAULT NULL",
            'image_3'             => "ALTER TABLE `{$this->table}` ADD COLUMN `image_3` VARCHAR(255) NULL DEFAULT NULL",
            'benefit_image'       => "ALTER TABLE `{$this->table}` ADD COLUMN `benefit_image` VARCHAR(255) NULL DEFAULT NULL",
            'benefit_title'       => "ALTER TABLE `{$this->table}` ADD COLUMN `benefit_title` VARCHAR(255) NULL DEFAULT NULL",
            'benefit_description' => "ALTER TABLE `{$this->table}` ADD COLUMN `benefit_description` TEXT NULL DEFAULT NULL",
            'benefit_items'       => "ALTER TABLE `{$this->table}` ADD COLUMN `benefit_items` LONGTEXT NULL DEFAULT NULL",
            'feature_image'       => "ALTER TABLE `{$this->table}` ADD COLUMN `feature_image` VARCHAR(255) NULL DEFAULT NULL",
            'feature_1_icon'      => "ALTER TABLE `{$this->table}` ADD COLUMN `feature_1_icon` VARCHAR(255) NULL DEFAULT NULL",
            'feature_1_title'     => "ALTER TABLE `{$this->table}` ADD COLUMN `feature_1_title` VARCHAR(255) NULL DEFAULT NULL",
            'feature_1_text'      => "ALTER TABLE `{$this->table}` ADD COLUMN `feature_1_text` TEXT NULL DEFAULT NULL",
            'feature_2_icon'      => "ALTER TABLE `{$this->table}` ADD COLUMN `feature_2_icon` VARCHAR(255) NULL DEFAULT NULL",
            'feature_2_title'     => "ALTER TABLE `{$this->table}` ADD COLUMN `feature_2_title` VARCHAR(255) NULL DEFAULT NULL",
            'feature_2_text'      => "ALTER TABLE `{$this->table}` ADD COLUMN `feature_2_text` TEXT NULL DEFAULT NULL",
            'faq_items'           => "ALTER TABLE `{$this->table}` ADD COLUMN `faq_items` LONGTEXT NULL DEFAULT NULL",
            'show_in_footer'      => "ALTER TABLE `{$this->table}` ADD COLUMN `show_in_footer` TINYINT DEFAULT 0",
            'featured_project_id' => "ALTER TABLE `{$this->table}` ADD COLUMN `featured_project_id` INT NULL DEFAULT NULL",
            'deleted_at'          => "ALTER TABLE `{$this->table}` ADD COLUMN `deleted_at` DATETIME NULL DEFAULT NULL",
            'updated_at'          => "ALTER TABLE `{$this->table}` ADD COLUMN `updated_at` DATETIME NULL DEFAULT NULL",
        ];

        foreach ($columns as $col => $sql) {
            if (!$this->columnExists($col)) {
                try {
                    $this->db->exec($sql);
                } catch (\Exception $e) {
                    error_log("CategoriesModel::ensureColumns() failed for {$col}: " . $e->getMessage());
                }
            }
        }
    }

    // ----------------------------------------------------------------
    // PUBLIC METHODS
    // ----------------------------------------------------------------

    /**
     * Lấy tất cả categories đang hoạt động (status = 1),
     * sắp xếp theo sort_order tăng dần, sau đó theo id tăng dần.
     *
     * @return array Mảng các category đang active kèm tên dự án được gán
     */
    public function getAllCategories()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT c.id, c.parent_id, c.name, c.slug, c.image, c.description, c.status, 
                        c.sort_order, c.show_in_footer, c.created_at, c.featured_project_id,
                        p.title as featured_project_name
                 FROM `{$this->table}` c
                 LEFT JOIN projects p ON c.featured_project_id = p.id
                 WHERE c.deleted_at IS NULL
                 ORDER BY c.sort_order ASC, c.id ASC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('CategoriesModel::getAllCategories() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy một category theo ID.
     *
     * @param  int        $id
     * @return array|null
     */
    public function getCategoryById($id)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, parent_id, name, slug, image, description, status, sort_order, show_in_footer, created_at,
                        image_1, image_2, image_3, detail_description, benefit_image, benefit_title, benefit_description,
                        benefit_items, feature_image, feature_1_icon, feature_1_title, feature_1_text, feature_2_icon,
                        feature_2_title, feature_2_text, faq_items, featured_project_id
                 FROM `{$this->table}` WHERE id = ? LIMIT 1"
            );
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log('CategoriesModel::getCategoryById() - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy một category theo slug.
     *
     * @param  string     $slug
     * @return array|null
     */
    public function getCategoryBySlug($slug)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM `{$this->table}` WHERE slug = ? AND status = 1 LIMIT 1"
            );
            $stmt->execute([$slug]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log('CategoriesModel::getCategoryBySlug() - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy chi tiết đầy đủ một category theo slug (bao gồm các cột detail).
     * Dùng cho trang categories_details.php
     *
     * @param  string     $slug
     * @return array|null
     */
    public function getCategoryDetailBySlug($slug)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, name, slug, image, image_1, image_2, image_3,
                        description, detail_description,
                        benefit_image, benefit_title, benefit_description, benefit_items,
                        feature_image, feature_1_icon, feature_1_title, feature_1_text,
                        feature_2_icon, feature_2_title, feature_2_text,
                        faq_items, sort_order
                 FROM `{$this->table}`
                 WHERE slug = ? AND status = 1
                 LIMIT 1"
            );
            $stmt->execute([$slug]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                // Decode JSON fields
                $row['benefit_items'] = !empty($row['benefit_items'])
                    ? json_decode($row['benefit_items'], true)
                    : [];
                $row['faq_items'] = !empty($row['faq_items'])
                    ? json_decode($row['faq_items'], true)
                    : [];
                return $row;
            }

            // ── Fallback: dữ liệu tĩnh khi DB chưa có nội dung ──────────
            $fallback = [
                'lap-quy-hoach-xay-dung-va-tu-van-du-an-dau-tu' => [
                    'id'                  => 1,
                    'name'                => 'Lập quy hoạch xây dựng và Tư vấn dự án đầu tư',
                    'slug'                => 'lap-quy-hoach-xay-dung-va-tu-van-du-an-dau-tu',
                    'image'               => '', 'image_1' => '', 'image_2' => '', 'image_3' => '',
                    'description'         => '',
                     'detail_description'  => "MTECH cung cấp lĩnh vực toàn diện trong công tác chuẩn bị đầu tư. Chúng tôi đảm nhận lập quy hoạch xây dựng, lập hồ sơ báo cáo nghiên cứu khả thi, thiết kế cơ sở và lập dự án đầu tư xây dựng công trình.\nVới đội ngũ chuyên gia giàu kinh nghiệm, chúng tôi đảm bảo đánh giá chính xác hiệu quả dự án đầu tư, giúp chủ đầu tư an tâm trong các quyết định chiến lược.",
                    'benefit_image'       => '', 'benefit_title' => '', 'benefit_description' => '', 'benefit_items' => [],
                    'feature_image'       => '',
                    'feature_1_icon'      => '', 'feature_1_title' => 'Quy hoạch & Nghiên cứu khả thi',
                    'feature_1_text'      => 'Thực hiện quy hoạch chi tiết 1/500 và lập báo cáo nghiên cứu khả thi cho các dự án quy mô lớn như nhà máy xi măng, nhiệt điện.',
                    'feature_2_icon'      => '', 'feature_2_title' => 'Thẩm tra dự án',
                    'feature_2_text'      => 'Thẩm tra thiết kế và dự toán xây dựng công trình, đảm bảo tính hợp lý và tuân thủ tiêu chuẩn.',
                    'faq_items'           => [], 'sort_order' => 1,
                ],
                'thiet-ke-xay-dung-chuyen-dung' => [
                    'id'                  => 2,
                    'name'                => 'Thiết kế xây dựng chuyên dụng',
                    'slug'                => 'thiet-ke-xay-dung-chuyen-dung',
                    'image'               => '', 'image_1' => '', 'image_2' => '', 'image_3' => '',
                    'description'         => '',
                    'detail_description'  => "Là đơn vị đạt Chứng chỉ năng lực hoạt động xây dựng Hạng I đối với công trình Nhà công nghiệp và Vật liệu xây dựng, MTECH cung cấp các giải pháp thiết kế tối ưu.\nChúng tôi chuyên thiết kế kiến trúc, thiết kế kết cấu công trình dân dụng và công nghiệp; thiết kế lắp đặt dây chuyền công nghệ silicat; thiết kế công trình khai thác mỏ và hệ thống điện - tự động hóa.",
                    'benefit_image'       => '', 'benefit_title' => '', 'benefit_description' => '', 'benefit_items' => [],
                    'feature_image'       => '',
                    'feature_1_icon'      => '', 'feature_1_title' => 'Chứng chỉ năng lực Hạng I',
                    'feature_1_text'      => 'Đạt chứng chỉ thiết kế, thẩm tra thiết kế xây dựng Hạng I đối với công trình công nghiệp và VLXD.',
                    'feature_2_icon'      => '', 'feature_2_title' => 'Thiết kế đa lĩnh vực',
                    'feature_2_text'      => 'Bao gồm kiến trúc, kết cấu, dây chuyền công nghệ, hạ tầng kỹ thuật và năng lượng.',
                    'faq_items'           => [], 'sort_order' => 2,
                ],
                'quan-ly-du-an-giam-sat-thi-cong-kiem-dinh' => [
                    'id'                  => 3,
                    'name'                => 'Quản lý dự án, Giám sát thi công và Kiểm định',
                    'slug'                => 'quan-ly-du-an-giam-sat-thi-cong-kiem-dinh',
                    'image'               => '', 'image_1' => '', 'image_2' => '', 'image_3' => '',
                    'description'         => '',
                     'detail_description'  => "MTECH đồng hành cùng chủ đầu tư trong suốt quá trình thi công. Chúng tôi cung cấp lĩnh vực quản lý dự án, giám sát thi công xây dựng và hoàn thiện công trình dân dụng, công nghiệp.\nNgoài ra, lĩnh vực kiểm định chất lượng công trình và giám sát lắp đặt thiết bị của MTECH giúp các nhà máy đi vào vận hành an toàn, đúng tiến độ.",
                    'benefit_image'       => '', 'benefit_title' => '', 'benefit_description' => '', 'benefit_items' => [],
                    'feature_image'       => '',
                    'feature_1_icon'      => '', 'feature_1_title' => 'Giám sát thi công chuyên nghiệp',
                    'feature_1_text'      => 'Đảm bảo chất lượng thi công xây dựng, hoàn thiện và lắp đặt thiết bị theo đúng thiết kế.',
                    'feature_2_icon'      => '', 'feature_2_title' => 'Quản lý dự án hiệu quả',
                    'feature_2_text'      => 'Quản lý chặt chẽ tiến độ, chất lượng và an toàn cho các dự án trọng điểm.',
                    'faq_items'           => [], 'sort_order' => 3,
                ],
                'quan-ly-chi-phi-xay-dung-tu-van-dau-thau' => [
                    'id'                  => 4,
                    'name'                => 'Quản lý chi phí xây dựng và Tư vấn đấu thầu',
                    'slug'                => 'quan-ly-chi-phi-xay-dung-tu-van-dau-thau',
                    'image'               => '', 'image_1' => '', 'image_2' => '', 'image_3' => '',
                    'description'         => '',
                     'detail_description'  => "Chúng tôi cung cấp giải pháp tài chính minh bạch và tối ưu cho dự án. MTECH thực hiện đo bóc khối lượng xây dựng, xác định giá gói thầu, lập và thẩm tra tổng mức đầu tư.\nĐồng thời, chúng tôi cung cấp lĩnh vực tư vấn đấu thầu, kiểm soát chi phí, lập hồ sơ thanh toán và quyết toán vốn đầu tư chuyên nghiệp.",
                    'benefit_image'       => '', 'benefit_title' => '', 'benefit_description' => '', 'benefit_items' => [],
                    'feature_image'       => '',
                    'feature_1_icon'      => '', 'feature_1_title' => 'Tư vấn đấu thầu',
                    'feature_1_text'      => 'Hỗ trợ chủ đầu tư lựa chọn nhà thầu uy tín thông qua quy trình đấu thầu minh bạch.',
                    'feature_2_icon'      => '', 'feature_2_title' => 'Kiểm soát chi phí',
                    'feature_2_text'      => 'Xác định suất vốn đầu tư, định mức và lập hồ sơ quyết toán hợp đồng chính xác.',
                    'faq_items'           => [], 'sort_order' => 4,
                ],
                'tu-van-ky-thuat-toi-uu-hoa-nang-luong' => [
                    'id'                  => 5,
                    'name'                => 'Tư vấn kỹ thuật tối ưu hóa năng lượng',
                    'slug'                => 'tu-van-ky-thuat-toi-uu-hoa-nang-luong',
                    'image'               => '', 'image_1' => '', 'image_2' => '', 'image_3' => '',
                    'description'         => '',
                    'detail_description'  => "Hướng tới sự phát triển bền vững, MTECH cung cấp các giải pháp kỹ thuật nhằm tối ưu hóa năng lượng cho các nhà máy công nghiệp nặng.\nChúng tôi đã tư vấn thành công hệ thống phát điện nhiệt dư cho nhiều dự án lớn như NM xi măng Xuân Thành, NM xi măng Đồng Lâm, mang lại hiệu quả kinh tế cao và bảo vệ môi trường.",
                    'benefit_image'       => '', 'benefit_title' => '', 'benefit_description' => '', 'benefit_items' => [],
                    'feature_image'       => '',
                    'feature_1_icon'      => '', 'feature_1_title' => 'Hệ thống phát điện nhiệt dư',
                    'feature_1_text'      => 'Tư vấn thiết kế các trạm phát điện tận dụng nhiệt dư, giúp nhà máy tự chủ một phần điện năng.',
                    'feature_2_icon'      => '', 'feature_2_title' => 'Tối ưu hóa sản xuất',
                    'feature_2_text'      => 'Đưa ra các giải pháp kỹ thuật giúp giảm tiêu hao năng lượng trong quá trình vận hành.',
                    'faq_items'           => [], 'sort_order' => 5,
                ],
                'tong-thau-tu-van-du-an-dau-tu' => [
                    'id'                  => 6,
                    'name'                => 'Tổng thầu tư vấn dự án đầu tư',
                    'slug'                => 'tong-thau-tu-van-du-an-dau-tu',
                    'image'               => '', 'image_1' => '', 'image_2' => '', 'image_3' => '',
                    'description'         => '',
                    'detail_description'  => "MTECH tự hào đảm nhận vai trò Tổng thầu tư vấn cho các dự án quy mô lớn của các tập đoàn hàng đầu như Tập đoàn Xuân Thiện, Long Sơn, Thành Thắng, The Vissai...\nChúng tôi cung cấp gói giải pháp đồng bộ từ giai đoạn khảo sát, thiết kế, giám sát đến khi dự án đi vào vận hành thương mại.",
                    'benefit_image'       => '', 'benefit_title' => '', 'benefit_description' => '', 'benefit_items' => [],
                    'feature_image'       => '',
                    'feature_1_icon'      => '', 'feature_1_title' => 'Kinh nghiệm với các Tập đoàn lớn',
                    'feature_1_text'      => 'Là đối tác tin cậy của SCG, Vissai, Xuân Thiện, Long Sơn trong các dự án công nghiệp quy mô lớn.',
                    'feature_2_icon'      => '', 'feature_2_title' => 'Giải pháp toàn diện',
                    'feature_2_text'      => 'Trách nhiệm xuyên suốt vòng đời dự án, đảm bảo tính đồng bộ và hiệu quả cao nhất.',
                    'faq_items'           => [], 'sort_order' => 6,
                ],
            ];

            return $fallback[$slug] ?? null;

        } catch (PDOException $e) {
            error_log('CategoriesModel::getCategoryDetailBySlug() - ' . $e->getMessage());
            return null;
        }
    }

/**
      * Tạo category mới.
      */
    public function create(array $data): int|false
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO `{$this->table}`
                 (parent_id, name, slug, image, description, detail_description,
                  image_1, image_2, image_3,
                  benefit_image, benefit_title, benefit_description, benefit_items,
                  feature_image,
                  feature_1_icon, feature_1_title, feature_1_text,
                  feature_2_icon, feature_2_title, feature_2_text,
                  faq_items, status, sort_order, show_in_footer, featured_project_id)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
            );
            $ok = $stmt->execute([
                $data['parent_id']           ?? null,
                $data['name'],
                $data['slug'],
                $data['image']               ?? '',
                $data['description']         ?? '',
                $data['detail_description']  ?? '',
                $data['image_1']             ?? '',
                $data['image_2']             ?? '',
                $data['image_3']             ?? '',
                $data['benefit_image']       ?? '',
                $data['benefit_title']       ?? '',
                $data['benefit_description'] ?? '',
                $data['benefit_items']       ?? null,
                $data['feature_image']       ?? '',
                $data['feature_1_icon']      ?? '',
                $data['feature_1_title']     ?? '',
                $data['feature_1_text']      ?? '',
                $data['feature_2_icon']      ?? '',
                $data['feature_2_title']     ?? '',
                $data['feature_2_text']      ?? '',
                $data['faq_items']           ?? null,
                $data['status']              ?? 1,
                $data['sort_order']          ?? 0,
                $data['show_in_footer']      ?? 0,
                $data['featured_project_id'] ?? null,
            ]);
            return $ok ? (int)$this->db->lastInsertId() : false;
        } catch (PDOException $e) {
            self::$lastError = $e->getMessage();
            error_log('CategoriesModel::create() - ' . $e->getMessage());
            return false;
        }
    }

/**
      * Cập nhật category.
      */
    public function update(int $id, array $data): bool
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE `{$this->table}` SET
                 parent_id = ?, name = ?, slug = ?, image = ?, description = ?, detail_description = ?,
                 image_1 = ?, image_2 = ?, image_3 = ?,
                 benefit_image = ?, benefit_title = ?, benefit_description = ?, benefit_items = ?,
                 feature_image = ?,
                 feature_1_icon = ?, feature_1_title = ?, feature_1_text = ?,
                 feature_2_icon = ?, feature_2_title = ?, feature_2_text = ?,
                 faq_items = ?, status = ?, sort_order = ?, show_in_footer = ?, featured_project_id = ?
                 WHERE id = ?"
            );
            return $stmt->execute([
                $data['parent_id']           ?? null,
                $data['name'],
                $data['slug'],
                $data['image']               ?? '',
                $data['description']         ?? '',
                $data['detail_description']  ?? '',
                $data['image_1']             ?? '',
                $data['image_2']             ?? '',
                $data['image_3']             ?? '',
                $data['benefit_image']       ?? '',
                $data['benefit_title']       ?? '',
                $data['benefit_description'] ?? '',
                $data['benefit_items']       ?? null,
                $data['feature_image']       ?? '',
                $data['feature_1_icon']      ?? '',
                $data['feature_1_title']     ?? '',
                $data['feature_1_text']      ?? '',
                $data['feature_2_icon']      ?? '',
                $data['feature_2_title']     ?? '',
                $data['feature_2_text']      ?? '',
                $data['faq_items']           ?? null,
                $data['status']              ?? 1,
                $data['sort_order']          ?? 0,
                $data['show_in_footer']      ?? 0,
                $data['featured_project_id'] ?? null,
                $id,
            ]);
        } catch (PDOException $e) {
            self::$lastError = $e->getMessage();
            error_log('CategoriesModel::update() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa mềm category (chuyển vào thùng rác).
     */
    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE `{$this->table}` SET deleted_at = CURRENT_TIMESTAMP WHERE id = ?"
            );
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('CategoriesModel::delete() - ' . $e->getMessage());
            return false;
        }
    }

    /**
      * Lấy danh sách lĩnh vực trong thùng rác.
     */
    public function getTrashed(int $limit = 20, int $offset = 0): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, name, slug, image, deleted_at
                 FROM `{$this->table}`
                 WHERE deleted_at IS NOT NULL
                 ORDER BY deleted_at DESC
                 LIMIT ? OFFSET ?"
            );
            $stmt->execute([$limit, $offset]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('CategoriesModel::getTrashed() - ' . $e->getMessage());
            return [];
        }
    }

    /**
      * Đếm số lĩnh vực trong thùng rác.
     */
    public function countTrashed(): int
    {
        try {
            $stmt = $this->db->query(
                "SELECT COUNT(*) FROM `{$this->table}` WHERE deleted_at IS NOT NULL"
            );
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('CategoriesModel::countTrashed() - ' . $e->getMessage());
            return 0;
        }
    }

    /**
      * Khôi phục lĩnh vực từ thùng rác.
     */
    public function restore(int $id): bool
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE `{$this->table}` SET deleted_at = NULL WHERE id = ?"
            );
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('CategoriesModel::restore() - ' . $e->getMessage());
            return false;
        }
    }

    /**
      * Xóa vĩnh viễn lĩnh vực.
     */
    public function hardDelete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM `{$this->table}` WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('CategoriesModel::hardDelete() - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Đếm tổng số categories đang hoạt động.
     *
     * @return int
     */
    public function countActiveCategories()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM `{$this->table}` WHERE status = 1"
            );
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('CategoriesModel::countActiveCategories() - ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Lấy services hiển thị trên trang chủ (show_on_home=1).
     * Giới hạn tối đa 6 services.
     *
     * @param int $limit Số lượng tối đa (mặc định 6)
     * @return array Mảng services cho trang chủ
     */
    public function getHomeServices($limit = 6)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, name, slug, image, description, sort_order
                 FROM `{$this->table}`
                 WHERE status = 1 AND show_on_home = 1
                 ORDER BY sort_order ASC, id ASC
                 LIMIT ?"
            );
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('CategoriesModel::getHomeServices() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy services hiển thị trong dropdown menu header (show_in_menu=1).
     *
     * @param int $limit Số lượng tối đa (mặc định 10)
     * @return array Mảng services cho menu dropdown
     */
    public function getMenuServices($limit = 10)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, name, slug
                 FROM `{$this->table}`
                 WHERE status = 1 AND show_in_menu = 1
                 ORDER BY sort_order ASC, id ASC
                 LIMIT ?"
            );
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('CategoriesModel::getMenuServices() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy services hiển thị ở footer (show_in_footer=1).
     * Các mục này sẽ hiển thị trong cột Services của footer.
     *
     * @param int $limit Số lượng tối đa (mặc định 10)
     * @return array Mảng services cho footer
     */
    public function getFooterServices($limit = 10)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, name, slug, sort_order
                 FROM `{$this->table}`
                 WHERE status = 1 AND show_in_footer = 1
                 ORDER BY sort_order ASC, id ASC
                 LIMIT ?"
            );
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('CategoriesModel::getFooterServices() - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Dựng cấu trúc cây phân cấp từ mảng phẳng (độ sâu vô hạn)
     * 
     * @param array $elements Mảng phẳng các đối tượng
     * @param int|null $parentId ID của cha hiện tại
     * @param int $maxDepth Độ sâu tối đa đề phòng vòng lặp vô tận
     * @param int $currentDepth Độ sâu hiện tại
     * @param array $visitedIds Mảng các ID đã duyệt qua
     * @return array Mảng cây lồng nhau
     */
    public function buildTree(array $elements, $parentId = null, $maxDepth = 10, $currentDepth = 0, $visitedIds = []): array
    {
        if ($currentDepth >= $maxDepth) return [];
        $branch = [];
        foreach ($elements as $element) {
            $elementParentId = empty($element['parent_id']) ? null : (int)$element['parent_id'];
            $checkParentId = empty($parentId) ? null : (int)$parentId;
            
            if ($elementParentId === $checkParentId) {
                if (in_array($element['id'], $visitedIds)) {
                    continue;
                }
                $newVisitedIds = array_merge($visitedIds, [$element['id']]);
                $children = $this->buildTree($elements, $element['id'], $maxDepth, $currentDepth + 1, $newVisitedIds);
                $element['children'] = $children ?: [];
                $branch[] = $element;
            }
        }
        return $branch;
    }

    /**
     * Lấy danh sách phẳng được thụt lề theo cấp bậc cha-con để dùng cho thẻ <select>
     * 
     * @param array $elements Mảng cây hoặc mảng phẳng
     * @param int $excludeId ID cần loại trừ (để tránh chọn chính mình làm cha)
     * @return array Danh sách danh mục đã format
     */
    public function getFormattedTreeOptions(array $elements, $excludeId = null): array
    {
        // Nếu truyền vào mảng phẳng, hãy dựng cây trước
        if (isset($elements[0]) && !isset($elements[0]['children'])) {
            $elements = $this->buildTree($elements);
        }
        
        $options = [];
        $helper = function($tree, $prefix = '') use (&$options, &$helper, $excludeId) {
            foreach ($tree as $node) {
                if ($excludeId !== null && (int)$node['id'] === (int)$excludeId) {
                    continue;
                }
                $options[] = [
                    'id' => $node['id'],
                    'name' => $prefix . $node['name']
                ];
                if (!empty($node['children'])) {
                    $helper($node['children'], $prefix . '— ');
                }
            }
        };
        
        $helper($elements);
        return $options;
    }

    /**
      * Lấy dự án được gán cho category (featured_project).
      * @param int $categoryId ID category
      * @return array|null Thông tin dự án
      */
    public function getFeaturedProject(int $categoryId): ?array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT p.id, p.title, p.slug 
                 FROM `{$this->table}` c
                 LEFT JOIN projects p ON c.featured_project_id = p.id
                 WHERE c.id = ? AND p.status = 1
                 LIMIT 1"
            );
            $stmt->execute([$categoryId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log('CategoriesModel::getFeaturedProject() - ' . $e->getMessage());
            return null;
        }
    }
}
