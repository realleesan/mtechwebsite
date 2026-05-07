<?php
/**
 * ErrorController - Xử lý các trang lỗi (404, 500)
 * Kế thừa BaseController để sử dụng các helper methods
 */

require_once __DIR__ . '/../../core/BaseController.php';

class ErrorController extends BaseController
{
    /**
     * Hiển thị trang 404 - Không tìm thấy
     */
    public function notFound()
    {
        // Set HTTP status 404
        http_response_code(404);
        
        // Set các biến cho layout
        $title = '404 - Trang không tìm thấy - MTECH';
        $showPageHeader = false;
        $showCTA = false;
        $showBreadcrumb = false;
        
        // Render 404 page
        $this->view('errors/404.php', [
            'title' => $title,
            'showPageHeader' => $showPageHeader,
            'showCTA' => $showCTA,
            'showBreadcrumb' => $showBreadcrumb
        ]);
    }
    
    /**
     * Hiển thị trang 500 - Lỗi máy chủ
     */
    public function serverError()
    {
        // Set HTTP status 500
        http_response_code(500);
        
        // Set các biến cho layout
        $title = '500 - Lỗi máy chủ - MTECH';
        $showPageHeader = false;
        $showCTA = false;
        $showBreadcrumb = false;
        
        // Render 500 page
        $this->view('errors/500.php', [
            'title' => $title,
            'showPageHeader' => $showPageHeader,
            'showCTA' => $showCTA,
            'showBreadcrumb' => $showBreadcrumb
        ]);
    }
}
