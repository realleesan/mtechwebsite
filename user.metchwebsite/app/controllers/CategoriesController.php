<?php
/**
 * CategoriesController - Xử lý trang danh mục lĩnh vực
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
     * Hiển thị danh sách lĩnh vực/categories
     */
    public function index()
    {
        $allCategories = $this->categoriesModel->getAllCategories();
        $level1Services = array_filter($allCategories, function($cat) {
            return empty($cat['parent_id']) || (int)$cat['parent_id'] === 0;
        });

        // Chuẩn bị data cho view
        $data = [
            'services' => $level1Services,
            // Layout variables
            'page' => 'categories',
            'title' => 'Lĩnh vực - MTECHJSC',
            'showPageHeader' => true,
            'showCTA' => false,
            'showBreadcrumb' => true
        ];
        
        // Render view
        $this->view('categories/categories.php', $data);
    }
    
    /**
     * Hiển thị chi tiết danh mục lĩnh vực
     * @param string $slug Category slug từ URL parameter
     */
    public function details($slug = null)
    {
        // Lấy slug từ parameter hoặc GET
        if (!$slug) {
            $slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
        }
        
        if (empty($slug)) {
            $this->redirect('/linh-vuc');
            return;
        }
        
        // Lấy chi tiết category
        $categoryDetail = $this->categoriesModel->getCategoryDetailBySlug($slug);
        $allCategories = $this->categoriesModel->getAllCategories();
        $level1Categories = array_filter($allCategories, function($cat) {
            return empty($cat['parent_id']) || (int)$cat['parent_id'] === 0;
        });
        
        if (!$categoryDetail) {
            // 404 - Category không tồn tại
            $data = [
                'page' => '404',
                'title' => 'Không tìm thấy - MTECHJSC',
                'showPageHeader' => false,
                'showCTA' => false,
                'showBreadcrumb' => false,
                'hideHeader' => true
            ];
            
            http_response_code(404);
            $this->view('errors/404.php', $data);
            return;
        }
        
        // Chuẩn bị data cho view
        $data = [
            'categoryDetail' => $categoryDetail,
            'allCategories' => $level1Categories,
            
            // Layout variables
            'page' => 'categories-details',
            'title' => htmlspecialchars($categoryDetail['name']) . ' - MTECHJSC',
            'showPageHeader' => true,
            'showCTA' => false,
            'showBreadcrumb' => true
        ];
        
        // Render view
        $this->view('categories/categories_details.php', $data);
    }
}