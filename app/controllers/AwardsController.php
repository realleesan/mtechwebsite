<?php
/**
 * AwardsController - Xử lý trang giải thưởng
 * Chuyển logic từ index.php case 'awards'
 */

require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../models/AwardsModel.php';

class AwardsController extends BaseController
{
    private $awardsModel;
    
    public function __construct()
    {
        $this->awardsModel = new AwardsModel();
    }
    
    /**
     * Hiển thị danh sách giải thưởng
     */
    public function index()
    {
        // Lấy dữ liệu từ model
        $awards = $this->awardsModel->getAllActive();
        
        // Chuẩn bị data cho view
        $data = [
            'awards' => $awards,
            
            // Layout variables
            'page' => 'awards',
            'title' => 'Giải thưởng & Chứng chỉ - MTECH.JSC',
            'content' => 'app/views/about/awards.php',
            'showPageHeader' => true,
            'showCTA' => false,
            'showBreadcrumb' => true
        ];
        
        // Render view
        $this->view('_layout/master.php', $data);
    }
}