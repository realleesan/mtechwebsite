<?php
/**
 * AwardsController - Xử lý trang giải thưởng & chứng chỉ năng lực
 */

require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../models/AwardsModel.php';
require_once __DIR__ . '/../models/CapacityFieldsModel.php';

class AwardsController extends BaseController
{
    private $awardsModel;
    private CapacityFieldsModel $capacityModel;

    public function __construct()
    {
        $this->awardsModel   = new AwardsModel();
        $this->capacityModel = new CapacityFieldsModel();
    }

    /**
     * Hiển thị trang chứng chỉ năng lực:
     * - Bảng lĩnh vực hoạt động (capacity_fields + items)
     * - Carousel ảnh giải thưởng / chứng chỉ (awards)
     */
    public function index()
    {
        $awards         = $this->awardsModel->getAllActive();
        $capacityFields = $this->capacityModel->getAllWithItems();

        $this->view('about/awards.php', [
            'awards'         => $awards,
            'capacityFields' => $capacityFields,

            // Layout variables
            'page'           => 'awards',
            'title'          => 'Chứng chỉ năng lực - MTECH.JSC',
            'showPageHeader' => true,
            'showCTA'        => false,
            'showBreadcrumb' => true,
        ]);
    }
}