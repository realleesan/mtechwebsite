<?php
/**
 * SearchController - Xử lý tìm kiếm toàn site
 * Chuyển logic từ index.php case 'search'
 */

require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../models/SearchModel.php';

class SearchController extends BaseController
{
    private $searchModel;
    
    public function __construct()
    {
        $this->searchModel = new SearchModel();
    }
    
    /**
     * Hiển thị trang tìm kiếm và kết quả
     */
    public function index()
    {
        $searchQuery = isset($_GET['q']) ? trim(urldecode($_GET['q'])) : '';
        $searchType = isset($_GET['type']) ? trim($_GET['type']) : '';
        $currentPage = isset($_GET['p']) ? max(1, (int) $_GET['p']) : 1;
        $perPage = 10;
        
        // Chỉ chấp nhận type hợp lệ
        if (!in_array($searchType, ['blog', 'service', 'project', ''])) {
            $searchType = '';
        }
        
        if (!empty($searchQuery)) {
            $searchResult = $this->searchModel->search($searchQuery, $currentPage, $perPage, $searchType);
            $searchResults = $searchResult['results'];
            $totalResults = $searchResult['total'];
        } else {
            $searchResults = [];
            $totalResults = 0;
        }
        
        // Chuẩn bị data cho view
        $data = [
            'searchQuery' => $searchQuery,
            'searchType' => $searchType,
            'searchResults' => $searchResults,
            'totalResults' => $totalResults,
            'currentPage' => $currentPage,
            'perPage' => $perPage,
            
            // Layout variables
            'page' => 'search',
            'title' => 'Search Results - MTECHJSC',
            'content' => 'app/views/search/search.php',
            'showPageHeader' => true,
            'showBlogSidebar' => true,
            'showCTA' => false,
            'showBreadcrumb' => true
        ];
        
        // Render view
        $this->view('_layout/master.php', $data);
    }
}