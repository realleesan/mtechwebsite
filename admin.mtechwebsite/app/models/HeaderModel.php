<?php
/**
 * HeaderModel.php
 *
 * Model xử lý dữ liệu cho header dynamic.
 * Bao gồm: logo, phone, iso text, profile pdf
 */

class HeaderModel
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
    // 1. HEADER SETTINGS
    // =================================================================

    /**
     * Lấy toàn bộ settings của header
     * @return array|null
     */
    public function getSettings()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM `header_settings` LIMIT 1"
            );
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log('HeaderModel::getSettings() - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cập nhật settings header
     * @param array $data ['logo_path', 'logo_alt', 'phone', 'phone_href', 'iso_text', 'profile_pdf_path', 'profile_pdf_label']
     * @return bool
     */
    public function updateSettings($data)
    {
        try {
            $fields = ['logo_path', 'logo_alt', 'phone', 'phone_href',
                       'iso_text', 'profile_pdf_path', 'profile_pdf_label'];

            $sets   = [];
            $params = [];

            foreach ($fields as $field) {
                if (array_key_exists($field, $data)) {
                    $sets[]   = "`{$field}` = ?";
                    $params[] = $data[$field];
                }
            }

            if (empty($sets)) return false;

            $stmt = $this->db->prepare(
                "UPDATE `header_settings` SET " . implode(', ', $sets) . " WHERE id = 1"
            );
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log('HeaderModel::updateSettings() - ' . $e->getMessage());
            return false;
        }
    }

    // =================================================================
    // 2. FALLBACK - Trả về giá trị mặc định nếu DB chưa có dữ liệu
    // =================================================================

    /**
     * Lấy settings, nếu DB lỗi hoặc chưa có dữ liệu thì trả về default
     * @return array
     */
    public function getSettingsWithFallback()
    {
        $defaults = [
            'logo_path'         => 'assets/images/logo.png',
            'logo_alt'          => 'Wokrate Industrial',
            'phone'             => '0123 456 789',
            'phone_href'        => '0123456789',
            'iso_text'          => 'ISO 9001 - 2010',
            'profile_pdf_path'  => 'assets/files/ho-so-nang-luc.pdf',
            'profile_pdf_label' => 'Hồ Sơ Năng Lực',
        ];

        $settings = $this->getSettings();
        if (!$settings) return $defaults;

        // Merge: ưu tiên DB, fallback về default nếu field rỗng
        foreach ($defaults as $key => $value) {
            if (empty($settings[$key])) {
                $settings[$key] = $value;
            }
        }

        return $settings;
    }
}
