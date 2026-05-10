<?php
/**
 * AboutController - Xử lý trang giới thiệu
 * Chuyển logic từ index.php case 'about' và 'company.history'
 */

require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../models/ClientLogosModel.php';
require_once __DIR__ . '/../models/TeamsModel.php';

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

        // Lấy danh sách team (hiển thị tối đa 4 trên trang about)
        $teamsModel = new TeamsModel();
        $teams = $teamsModel->getAllActive();

        // Chuẩn bị data cho view
        $data = [
            'clientLogos' => $clientLogos,
            'teams'       => $teams,

            // Layout variables
            'page'           => 'about',
            'title'          => 'Giới thiệu - MTECHJSC',
            'showPageHeader' => true,
            'showCTA'        => true,
            'showBreadcrumb' => true
        ];

        // Render view
        $this->view('about/about.php', $data);
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
            'showPageHeader' => true,
            'showCTA' => true,
            'showBreadcrumb' => true
        ];
        
        // Render view
        $this->view('about/company.history.php', $data);
    }
}