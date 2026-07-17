<?php
/**
 * ClientsController - Trang danh sách khách hàng
 */

require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../models/ClientLogosModel.php';

class ClientsController extends BaseController
{
    public function index()
    {
        $clientLogosModel = new ClientLogosModel();
        $clientLogos = $clientLogosModel->getAllActive();

        $this->view('about/clients.php', [
            'clientLogos'    => $clientLogos,
            'page'           => 'clients',
            'title'          => 'Danh sách khách hàng - MTECHJSC',
            'showPageHeader' => true,
            'showCTA'        => true,
            'showBreadcrumb' => true,
        ]);
    }
}
