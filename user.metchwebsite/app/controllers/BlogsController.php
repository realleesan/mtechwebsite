<?php
/**
 * BlogsController - Xử lý trang blogs/tin tức, chi tiết blog và tuyển dụng
 * Chuyển logic từ index.php case 'blogs', 'blog-details' và job application
 */

require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../models/BlogsModel.php';
require_once __DIR__ . '/../services/ValidationService.php';

class BlogsController extends BaseController
{
    private $blogsModel;
    
    public function __construct()
    {
        $this->blogsModel = new BlogsModel();
    }
    
    /**
     * Hiển thị trang Tuyển dụng (cat=7)
     */
    public function recruitment()
    {
        // Force cat=7 for recruitment
        $_GET['cat'] = 7;
        // Delegate to index method
        $this->index();
    }
    
    /**
     * Hiển thị danh sách blogs với filter và pagination
     * @param string $cat_slug Slug của category (từ route /tin-tuc-{cat_slug})
     */
    public function index($cat_slug = null)
    {
        // Xử lý filter category: hỗ trợ cả cat (ID) và cat_slug (slug)
        $filterCatId = 0;
        $categoryName = '';
        $categorySlug = '';
        
        if (isset($_GET['cat'])) {
            $filterCatId = (int) $_GET['cat'];
        } elseif ($cat_slug !== null) {
            // Nếu có cat_slug từ URL parameter
            $catSlug = trim($cat_slug);
            $category = $this->blogsModel->getCategoryBySlug($catSlug);
            if ($category) {
                $filterCatId = (int) $category['id'];
                $categoryName = $category['name'];
                $categorySlug = $category['slug'];
            }
        } elseif (isset($_GET['cat_slug'])) {
            // Nếu có cat_slug từ query string
            $catSlug = trim($_GET['cat_slug']);
            $category = $this->blogsModel->getCategoryBySlug($catSlug);
            if ($category) {
                $filterCatId = (int) $category['id'];
                $categoryName = $category['name'];
                $categorySlug = $category['slug'];
            }
        }
        
        $filterTag = isset($_GET['tag']) ? trim($_GET['tag']) : '';
        $searchQuery = isset($_GET['search']) ? trim(urldecode($_GET['search'])) : '';
        $currentPage = isset($_GET['p']) ? max(1, (int) $_GET['p']) : 1;
        $perPage = 5;
        
        // DEBUG: Check if tag parameter is received
        if ($filterTag) {
            error_log("DEBUG: Tag filter = " . $filterTag);
        }
        
        // Lấy dữ liệu từ model
        $blogsResult = $this->blogsModel->getBlogs($currentPage, $perPage, $filterCatId, $filterTag, $searchQuery);
        $blogs = $blogsResult['blogs'];
        $totalBlogs = $blogsResult['total'];
        $blogCategories = $this->blogsModel->getAllBlogCategories();
        $recentBlogs = $this->blogsModel->getRecentBlogs(4);
        $allTags = $this->blogsModel->getAllTags();
        
        // Chuẩn bị data cho view
        $data = [
            'blogs' => $blogs,
            'totalBlogs' => $totalBlogs,
            'blogCategories' => $blogCategories,
            'recentBlogs' => $recentBlogs,
            'allTags' => $allTags,
            'filterCatId' => $filterCatId,
            'categoryName' => $categoryName,
            'categorySlug' => $categorySlug,
            'filterTag' => $filterTag,
            'searchQuery' => $searchQuery,
            'currentPage' => $currentPage,
            'perPage' => $perPage,
            
            // Layout variables
            'page' => 'blogs',
            'title' => 'Blog - MTECHJSC',
            'showPageHeader' => true,
            'showCTA' => false,
            'showBreadcrumb' => true,
            'showBlogSidebar' => true
        ];
        
        // Render view
        $this->view('blogs/blogs.php', $data);
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
        $this->view('blogs/blog.details.php', $data);
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
            $validation = ValidationService::validate($_POST, [
                'full_name' => 'required|min:2|max:100',
                'email' => 'required|email|max:255',
                'phone' => 'required|min:10|max:20',
                'position' => 'required|max:255',
                'message' => 'max:1000'
            ]);
            
            if ($validation->fails()) {
                $this->json([
                    'success' => false,
                    'message' => 'Vui lòng kiểm tra lại thông tin',
                    'errors' => $validation->errors()
                ]);
                return;
            }
            
            // Lấy blogId từ POST data (form gửi blog_id)
            $blogId = isset($_POST['blog_id']) ? (int)$_POST['blog_id'] : 0;
            if (!$blogId) {
                $this->json([
                    'success' => false,
                    'message' => 'Không tìm thấy tin tuyển dụng'
                ]);
                return;
            }
            
            // Kiểm tra blog có tồn tại và là tin tuyển dụng không
            $blog = $this->blogsModel->getBlogById($blogId);
            if (!$blog) {
                $this->json([
                    'success' => false,
                    'message' => 'Tin tuyển dụng không tồn tại'
                ]);
                return;
            }
            
            // Kiểm tra có phải tin tuyển dụng không
            if (!$this->blogsModel->isHiringBlog($blogId)) {
                $this->json([
                    'success' => false,
                    'message' => 'Đây không phải tin tuyển dụng'
                ]);
                return;
            }
            
            // Chuẩn bị data để lưu
            $fullName = trim($_POST['full_name']);
            $email = trim($_POST['email']);
            $phone = trim($_POST['phone']);
            $position = trim($_POST['position']);
            $message = trim($_POST['message'] ?? '');
            
            // Lưu vào database với CV upload
            require_once __DIR__ . '/../models/JobApplicationModel.php';
            $jobModel = new JobApplicationModel();
            
            // Kiểm tra có file CV không
            $cvFile = isset($_FILES['cv']) ? $_FILES['cv'] : null;
            
            $result = $jobModel->createApplication(
                $blogId, 
                $fullName, 
                $email, 
                $phone, 
                $position, 
                $cvFile, 
                $message
            );
            
            if ($result['success']) {
                // Gửi email thông báo
                try {
                    if (file_exists(__DIR__ . '/../services/EmailNotificationService.php')) {
                        require_once __DIR__ . '/../services/EmailNotificationService.php';
                        $emailService = new EmailNotificationService();
                        
                        if ($emailService->isConfigured()) {
                            $emailData = [
                                'application_id' => $result['id'], // Sửa từ application_id thành id
                                'blog_id' => $blogId,
                                'position' => $position,
                                'full_name' => $fullName,
                                'email' => $email,
                                'phone' => $phone,
                                'cv_path' => $result['cv_path'] ?? null
                            ];
                            
                            // Gửi email xác nhận cho ứng viên
                            $emailService->sendThankYouEmail($emailData);
                            // Gửi email thông báo cho admin
                            $emailService->sendJobApplicationNotification($emailData);
                        }
                    }
                } catch (Exception $e) {
                    // Log lỗi nhưng không ảnh hưởng đến kết quả
                    error_log('Job application email sending failed: ' . $e->getMessage());
                }
                
                $this->json([
                    'success' => true,
                    'message' => 'Cảm ơn bạn đã ứng tuyển! Chúng tôi sẽ liên hệ sớm nhất.'
                ]);
            } else {
                $this->json([
                    'success' => false,
                    'message' => $result['error'] ?? 'Có lỗi xảy ra, vui lòng thử lại sau.'
                ]);
            }
            
        } catch (Exception $e) {
            error_log('Job Application Error: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            
            $this->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }
}