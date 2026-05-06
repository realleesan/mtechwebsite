<?php
/**
 * BlogsController - Xử lý trang danh sách blogs/tin tức
 * Chuyển logic từ index.php case 'blogs'
 */

require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../models/BlogsModel.php';

class BlogsController extends BaseController
{
    private $blogsModel;
    
    public function __construct()
    {
        $this->blogsModel = new BlogsModel();
    }
    
    /**
     * Hiển thị danh sách blogs với filter và pagination
     */
    public function index()
    {
        // Xử lý filter category: hỗ trợ cả cat (ID) và cat_slug (slug)
        $filterCatId = 0;
        $categoryName = '';
        $categorySlug = '';
        
        if (isset($_GET['cat'])) {
            $filterCatId = (int) $_GET['cat'];
        } elseif (isset($_GET['cat_slug'])) {
            // Nếu có cat_slug, tìm ID từ slug
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
        $this->view('_layout/master.php', $data);
    }
}