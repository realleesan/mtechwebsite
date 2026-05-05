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
    // PUBLIC METHODS
    // ----------------------------------------------------------------

    /**
     * Lấy tất cả categories đang hoạt động (status = 1),
     * sắp xếp theo sort_order tăng dần, sau đó theo id tăng dần.
     *
     * @return array Mảng các category đang active
     */
    public function getAllCategories()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, name, slug, image, description, sort_order
                 FROM `{$this->table}`
                 WHERE status = 1
                 ORDER BY sort_order ASC, id ASC"
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
                "SELECT * FROM `{$this->table}` WHERE id = ? LIMIT 1"
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
                    'detail_description'  => "MTECH cung cấp dịch vụ toàn diện trong công tác chuẩn bị đầu tư. Chúng tôi đảm nhận lập quy hoạch xây dựng, lập hồ sơ báo cáo nghiên cứu khả thi, thiết kế cơ sở và lập dự án đầu tư xây dựng công trình.\nVới đội ngũ chuyên gia giàu kinh nghiệm, chúng tôi đảm bảo đánh giá chính xác hiệu quả dự án đầu tư, giúp chủ đầu tư an tâm trong các quyết định chiến lược.",
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
                    'detail_description'  => "MTECH đồng hành cùng chủ đầu tư trong suốt quá trình thi công. Chúng tôi cung cấp dịch vụ quản lý dự án, giám sát thi công xây dựng và hoàn thiện công trình dân dụng, công nghiệp.\nNgoài ra, dịch vụ kiểm định chất lượng công trình và giám sát lắp đặt thiết bị của MTECH giúp các nhà máy đi vào vận hành an toàn, đúng tiến độ.",
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
                    'detail_description'  => "Chúng tôi cung cấp giải pháp tài chính minh bạch và tối ưu cho dự án. MTECH thực hiện đo bóc khối lượng xây dựng, xác định giá gói thầu, lập và thẩm tra tổng mức đầu tư.\nĐồng thời, chúng tôi cung cấp dịch vụ tư vấn đấu thầu, kiểm soát chi phí, lập hồ sơ thanh toán và quyết toán vốn đầu tư chuyên nghiệp.",
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
}
