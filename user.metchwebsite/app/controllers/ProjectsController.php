<?php
/**
 * ProjectsController - Xử lý trang dự án
 * Chuyển logic từ index.php case 'projects' và 'project-details'
 */

require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../models/ProjectsModel.php';

class ProjectsController extends BaseController
{
    private $projectsModel;
    
    public function __construct()
    {
        $this->projectsModel = new ProjectsModel();
    }
    
    /**
     * Hiển thị danh sách dự án
     */
    public function index()
    {
        // Lấy dữ liệu từ model
        $projects = $this->projectsModel->getAll(9, 0, 1); // 9 projects, offset 0, status active
        $categories = $this->projectsModel->getCategories();
        
        // Chuẩn bị data cho view
        $data = [
            'projects' => $projects,
            'categories' => $categories,
            
            // Layout variables
            'page' => 'projects',
            'title' => 'Dự án - MTECHJSC',
            'showPageHeader' => true,
            'showCTA' => false,
            'showBreadcrumb' => true
        ];
        
        // Render view
        $this->view('projects/projects.php', $data);
    }
    
    /**
     * Hiển thị chi tiết dự án
     * @param string $slug Project slug từ URL parameter
     */
    public function details($slug = null)
    {
        // Lấy slug từ parameter hoặc GET
        if (!$slug) {
            $slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
        }
        
        if (empty($slug)) {
            $this->redirect('/du-an');
            return;
        }
        
        // Lấy chi tiết dự án
        $projectDetail = $this->projectsModel->getBySlug($slug);
        if (!$projectDetail) {
            // 404 - Project không tồn tại
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
        
        // Lấy dự án liên quan (cùng category)
        $relatedProjects = $this->projectsModel->getRelated($projectDetail['category_id'], $projectDetail['id'], 3);
        
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Dự án', 'url' => '/du-an'],
            ['title' => htmlspecialchars($projectDetail['title']), 'url' => null],
        ];
        
        // Chuẩn bị data cho view
        $data = [
            'projectDetail' => $projectDetail,
            'relatedProjects' => $relatedProjects,
            'breadcrumbs' => $breadcrumbs,
            
            // Layout variables
            'page' => 'project-details',
            'title' => htmlspecialchars($projectDetail['title']) . ' - MTECHJSC',
            'showPageHeader' => true,
            'showCTA' => false,
            'showBreadcrumb' => true
        ];
        
        // Render view
        $this->view('projects/projects.details.php', $data);
    }
}