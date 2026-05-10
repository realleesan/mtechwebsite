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
        $this->renderSearch($searchQuery);
    }

    /**
     * Hiển thị kết quả tìm kiếm với keyword từ URL path
     * Route: /ket-qua-tim-kiem-{keyword}
     * Router truyền $keyword đã được decode đúng từ REQUEST_URI
     */
    public function indexWithKeyword($keyword = '')
    {
        // Router PHP decode REQUEST_URI và truyền vào đây
        // Đổi dấu - thành dấu cách để search
        $searchQuery = trim(str_replace('-', ' ', urldecode($keyword)));
        $this->renderSearch($searchQuery);
    }

    /**
     * Logic render chung cho cả hai method
     */
    private function renderSearch($searchQuery)
    {
        $searchType  = isset($_GET['type']) ? trim($_GET['type']) : '';
        $currentPage = isset($_GET['p']) ? max(1, (int) $_GET['p']) : 1;
        $perPage     = 10;

        // Chỉ chấp nhận type hợp lệ
        if (!in_array($searchType, ['blog', 'service', 'project', ''])) {
            $searchType = '';
        }

        if (!empty($searchQuery)) {
            $searchResult  = $this->searchModel->search($searchQuery, $currentPage, $perPage, $searchType);
            $searchResults = $searchResult['results'];
            $totalResults  = $searchResult['total'];
        } else {
            $searchResults = [];
            $totalResults  = 0;
        }

        $this->view('search/search.php', [
            'searchQuery'   => $searchQuery,
            'searchType'    => $searchType,
            'searchResults' => $searchResults,
            'totalResults'  => $totalResults,
            'currentPage'   => $currentPage,
            'perPage'       => $perPage,

            'page'           => 'search',
            'title'          => (!empty($searchQuery) ? 'Kết quả: ' . $searchQuery . ' - ' : '') . 'Tìm kiếm - MTECHJSC',
            'showPageHeader' => true,
            'showBlogSidebar'=> true,
            'showCTA'        => false,
            'showBreadcrumb' => true,
        ]);
    }
}