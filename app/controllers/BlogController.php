<?php
/**
 * BlogController - Xử lý chi tiết blog và job application
 * Chuyển logic từ index.php case 'blog-details'
 */

require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../models/BlogsModel.php';

class BlogController extends BaseController
{
    private $blogsModel;
    
    public function __construct()
    {
        $this->blogsModel = new BlogsModel();
    }
    
    /**
     * Hiển thị chi tiết blog
     * @param string $slug Blog slug từ URL parameter
     */
    public function details($slug = null)
    {
        // Lấy slug từ parameter hoặc GET
        if (!$slug) {
            $slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
        }
        
        if (empty($slug)) {
            $this->redirect('/tin-tuc');
            return;
        }
        
        // Lấy chi tiết blog
        $blogDetail = $this->blogsModel->getBlogDetailsBySlug($slug);
        if (!$blogDetail) {
            // 404 - Blog không tồn tại
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
        
        // Tăng lượt xem
        $this->blogsModel->incrementViews($blogDetail['id']);
        
        // Lấy dữ liệu sidebar (giống trang blogs)
        $blogCategories = $this->blogsModel->getAllBlogCategories();
        $recentBlogs = $this->blogsModel->getRecentBlogs(4);
        $allTags = $this->blogsModel->getAllTags();
        
        // Lấy danh sách vị trí tuyển dụng đang mở (cho form ứng tuyển)
        $hiringPositions = $this->blogsModel->getAllHiringPositions();
        
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Tin tức', 'url' => '/tin-tuc'],
            ['title' => htmlspecialchars($blogDetail['title']), 'url' => null],
        ];
        
        // Chuẩn bị data cho view
        $data = [
            'blogDetail' => $blogDetail,
            'blogCategories' => $blogCategories,
            'recentBlogs' => $recentBlogs,
            'allTags' => $allTags,
            'hiringPositions' => $hiringPositions,
            'breadcrumbs' => $breadcrumbs,
            
            // Layout variables
            'page' => 'blog-details',
            'title' => htmlspecialchars($blogDetail['title']) . ' - MTECHJSC',
            'showPageHeader' => true,
            'showCTA' => false,
            'showBreadcrumb' => true,
            'showBlogSidebar' => true
        ];
        
        // Render view
        $this->view('_layout/master.php', $data);
    }
    
    /**
     * Xử lý form ứng tuyển việc làm (AJAX)
     * Chuyển logic từ các view có job application form
     */
    public function jobApplicationSubmit()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Method not allowed'], 405);
            return;
        }
        
        try {
            // Validate input
            $validation = $this->validate($_POST, [
                'full_name' => 'required|min:2|max:100',
                'email' => 'required|email|max:255',
                'phone' => 'required|min:10|max:20',
                'position' => 'required|max:255',
                'message' => 'max:1000'
            ]);
            
            if (!$validation['passes']) {
                $this->json([
                    'success' => false,
                    'message' => 'Vui lòng kiểm tra lại thông tin',
                    'errors' => $validation['errors']
                ], 400);
                return;
            }
            
            // Chuẩn bị data để lưu
            $applicationData = [
                'full_name' => trim($_POST['full_name']),
                'email' => trim($_POST['email']),
                'phone' => trim($_POST['phone']),
                'position' => trim($_POST['position']),
                'message' => trim($_POST['message'] ?? ''),
                'ip_address' => $this->getClientIP(),
                'user_agent' => $this->getUserAgent(),
                'submitted_at' => date('Y-m-d H:i:s')
            ];
            
            // Lưu vào database (cần tạo JobApplicationModel)
            require_once __DIR__ . '/../models/JobApplicationModel.php';
            $jobModel = new JobApplicationModel();
            $result = $jobModel->create($applicationData);
            
            if ($result) {
                // Gửi email thông báo (optional)
                // TODO: Implement email notification
                
                $this->json([
                    'success' => true,
                    'message' => 'Cảm ơn bạn đã ứng tuyển! Chúng tôi sẽ liên hệ sớm nhất.'
                ]);
            } else {
                $this->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra, vui lòng thử lại sau.'
                ], 500);
            }
            
        } catch (Exception $e) {
            error_log('Job Application Error: ' . $e->getMessage());
            
            $this->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra, vui lòng thử lại sau.'
            ], 500);
        }
    }
}