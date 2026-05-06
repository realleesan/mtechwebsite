<?php
/**
 * AboutController - Xử lý trang giới thiệu
 * Chuyển logic từ index.php case 'about' và 'company.history'
 */

require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../models/ClientLogosModel.php';

class AboutController extends BaseController
{
    /**
     * Hiển thị trang giới thiệu
     */
    public function index()
    {
        // Lấy dữ liệu client logos
        $clientLogosModel = new ClientLogosModel();
        $clientLogos = $clientLogosModel->getAllActive();
        
        // Chuẩn bị data cho view
        $data = [
            'clientLogos' => $clientLogos,
            
            // Layout variables
            'page' => 'about',
            'title' => 'Giới thiệu - MTECHJSC',
            'content' => 'app/views/about/about.php',
            'showPageHeader' => true,
            'showCTA' => true,
            'showBreadcrumb' => true
        ];
        
        // Render view
        $this->view('_layout/master.php', $data);
    }
    
    /**
     * Hiển thị trang lịch sử công ty
     */
    public function companyHistory()
    {
        // Chuẩn bị data cho view
        $data = [
            // Layout variables
            'page' => 'company.history',
            'title' => 'Lịch sử công ty - MTECHJSC',
            'content' => 'app/views/about/company.history.php',
            'showPageHeader' => true,
            'showCTA' => true,
            'showBreadcrumb' => true
        ];
        
        // Render view
        $this->view('_layout/master.php', $data);
    }
}