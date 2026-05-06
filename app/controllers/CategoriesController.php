<?php
/**
 * CategoriesController - Xử lý trang danh mục dịch vụ
 * Chuyển logic từ index.php case 'categories' và 'categories-details'
 */

require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../models/CategoriesModel.php';

class CategoriesController extends BaseController
{
    private $categoriesModel;
    
    public function __construct()
    {
        $this->categoriesModel = new CategoriesModel();
    }
    
    /**
     * Hiển thị danh sách dịch vụ/categories
     */
    public function index()
    {
        // Chuẩn bị data cho view
        $data = [
            // Layout variables
            'page' => 'categories',
            'title' => 'Dịch vụ - MTECHJSC',
            'content' => 'app/views/categories/categories.php',
            'showPageHeader' => true,
            'showCTA' => false,
            'showBreadcrumb' => true
        ];
        
        // Render view
        $this->view('_layout/master.php', $data);
    }
    
    /**
     * Hiển thị chi tiết danh mục dịch vụ
     * @param string $slug Category slug từ URL parameter
     */
    public function details($slug = null)
    {
        // Lấy slug từ parameter hoặc GET
        if (!$slug) {
            $slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
        }
        
        if (empty($slug)) {
            $this->redirect('/dich-vu');
            return;
        }
        
        // Lấy chi tiết category
        $categoryDetail = $this->categoriesModel->getCategoryDetailBySlug($slug);
        $allCategories = $this->categoriesModel->getAllCategories();
        
        if (!$categoryDetail) {
            // 404 - Category không tồn tại
            $data = [
                'page' => '404',
                'title' => 'Không tìm thấy - MTECHJSC',
                'content' => 'errors/404.php',
                'showPageHeader' => false,
                'showCTA' => false,
                'showBreadcrumb' => false,
                'hideHeader' => true
            ];
            
            http_response_code(404);
            $this->view('_layout/master.php', $data);
            return;
        }
        
        // Chuẩn bị data cho view
        $data = [
            'categoryDetail' => $categoryDetail,
            'allCategories' => $allCategories,
            
            // Layout variables
            'page' => 'categories-details',
            'title' => htmlspecialchars($categoryDetail['name']) . ' - MTECHJSC',
            'content' => 'app/views/categories/categories_details.php',
            'showPageHeader' => true,
            'showCTA' => false,
            'showBreadcrumb' => true
        ];
        
        // Render view
        $this->view('_layout/master.php', $data);
    }
}