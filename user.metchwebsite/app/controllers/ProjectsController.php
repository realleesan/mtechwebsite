<?php
/**
 * ProjectsController - Xử lý trang dự án
 * Hỗ trợ bộ lọc lĩnh vực cha-con, phân trang và AJAX response
 */

require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../models/ProjectsModel.php';
require_once __DIR__ . '/../models/CategoriesModel.php';

class ProjectsController extends BaseController
{
    private $projectsModel;
    private $categoriesModel;
    
    public function __construct()
    {
        $this->projectsModel = new ProjectsModel();
        $this->categoriesModel = new CategoriesModel();
    }
    
    /**
     * Hiển thị danh sách dự án kèm bộ lọc lĩnh vực
     */
    public function index()
    {
        // 1. Lấy tham số bộ lọc & phân trang
        $page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : (isset($_GET['page_num']) ? max(1, (int)$_GET['page_num']) : 1);
        $limit = 9;
        $offset = ($page - 1) * $limit;
        
        // Parse selected category IDs từ mọi định dạng query parameters
        $selectedCatIds = [];

        // Hỗ trợ $_GET['categories'] dạng array hoặc string '1,2,3'
        if (!empty($_GET['categories'])) {
            if (is_array($_GET['categories'])) {
                $selectedCatIds = array_merge($selectedCatIds, array_map('intval', $_GET['categories']));
            } else {
                $selectedCatIds = array_merge($selectedCatIds, array_map('intval', explode(',', $_GET['categories'])));
            }
        }

        // Hỗ trợ $_GET['category'] dạng array hoặc string
        if (!empty($_GET['category'])) {
            if (is_array($_GET['category'])) {
                $selectedCatIds = array_merge($selectedCatIds, array_map('intval', $_GET['category']));
            } else {
                $selectedCatIds = array_merge($selectedCatIds, array_map('intval', explode(',', $_GET['category'])));
            }
        }

        // Hỗ trợ $_GET['category_id']
        if (!empty($_GET['category_id'])) {
            if (is_array($_GET['category_id'])) {
                $selectedCatIds = array_merge($selectedCatIds, array_map('intval', $_GET['category_id']));
            } else {
                $selectedCatIds = array_merge($selectedCatIds, array_map('intval', explode(',', $_GET['category_id'])));
            }
        }

        // Hỗ trợ $_GET['cat'] dạng ID hoặc Slug
        if (!empty($_GET['cat'])) {
            $catParam = trim($_GET['cat']);
            if (is_numeric($catParam)) {
                $selectedCatIds[] = (int)$catParam;
            } else {
                $catObj = $this->categoriesModel->getCategoryBySlug($catParam);
                if ($catObj) {
                    $selectedCatIds[] = (int)$catObj['id'];
                }
            }
        }

        // Fallback đọc trực tiếp từ $_SERVER['QUERY_STRING'] nếu $_GET bị server rewrite can thiệp
        if (empty($selectedCatIds) && !empty($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $parsedQs);
            if (!empty($parsedQs['categories'])) {
                if (is_array($parsedQs['categories'])) {
                    $selectedCatIds = array_merge($selectedCatIds, array_map('intval', $parsedQs['categories']));
                } else {
                    $selectedCatIds = array_merge($selectedCatIds, array_map('intval', explode(',', $parsedQs['categories'])));
                }
            }
            if (!empty($parsedQs['category'])) {
                if (is_array($parsedQs['category'])) {
                    $selectedCatIds = array_merge($selectedCatIds, array_map('intval', $parsedQs['category']));
                } else {
                    $selectedCatIds = array_merge($selectedCatIds, array_map('intval', explode(',', $parsedQs['category'])));
                }
            }
            if (!empty($parsedQs['cat'])) {
                $catParam = trim($parsedQs['cat']);
                if (is_numeric($catParam)) {
                    $selectedCatIds[] = (int)$catParam;
                } else {
                    $catObj = $this->categoriesModel->getCategoryBySlug($catParam);
                    if ($catObj) {
                        $selectedCatIds[] = (int)$catObj['id'];
                    }
                }
            }
        }

        $selectedCatIds = array_values(array_filter(array_unique($selectedCatIds), function($id) {
            return $id > 0;
        }));

        // 2. Lấy dữ liệu tất cả categories và tính toán cây phân cấp + đếm số lượng dự án
        $allCategories = $this->categoriesModel->getAllCategories();
        $categoryCounts = $this->projectsModel->getCategoryProjectCounts();
        
        // Map category ID => category info
        $categoryMap = [];
        foreach ($allCategories as $cat) {
            $categoryMap[(int)$cat['id']] = $cat;
        }

        // Tạo mảng allSelectedIds bao gồm cả ID cha và tất cả ID con của các cha đã chọn
        $allFilterIds = $this->expandCategoryIdsWithChildren($allCategories, $selectedCatIds);

        // Gắn số lượng dự án cho từng category và dựng cây phân cấp
        $categoriesWithCounts = [];
        foreach ($allCategories as $cat) {
            $catId = (int)$cat['id'];
            $cat['project_count'] = $categoryCounts[$catId] ?? 0;
            $categoriesWithCounts[] = $cat;
        }
        
        // Dựng cây danh mục cha - con
        $categoryTree = $this->categoriesModel->buildTree($categoriesWithCounts);
        
        // Tính tổng số lượng đệ quy cho các danh mục cha (bao gồm số dự án của danh mục con)
        $this->computeTreeCounts($categoryTree, $categoryCounts, $allCategories);

        // 3. Lấy dữ liệu dự án đã lọc
        $totalProjects = $this->projectsModel->countFilteredProjects($allFilterIds, 1);
        $totalPages = $limit > 0 ? (int)ceil($totalProjects / $limit) : 1;
        if ($page > $totalPages && $totalPages > 0) {
            $page = $totalPages;
            $offset = ($page - 1) * $limit;
        }

        $projects = $this->projectsModel->getFilteredProjects($allFilterIds, $limit, $offset, 1);

        // Gắn services vào từng project
        $projectIds = array_column($projects, 'id');
        $projectsServices = $this->projectsModel->getProjectsServicesList($projectIds);
        foreach ($projects as &$project) {
            $project['services'] = $projectsServices[$project['id']] ?? [];
        }
        unset($project);

        // Danh sách tên các lĩnh vực đang được chọn để hiển thị badges
        $activeCategoryNames = [];
        foreach ($selectedCatIds as $id) {
            if (isset($categoryMap[$id])) {
                $activeCategoryNames[$id] = $categoryMap[$id]['name'];
            }
        }

        // 4. Xử lý AJAX request
        $isAjax = isset($_GET['ajax']) && $_GET['ajax'] == '1' 
               || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

        if ($isAjax) {
            // Render partial views for AJAX response
            ob_start();
            $this->renderProjectsGrid($projects);
            $gridHtml = ob_get_clean();

            ob_start();
            $this->renderPagination($page, $totalPages, $selectedCatIds);
            $paginationHtml = ob_get_clean();

            ob_start();
            $this->renderActiveFilterBadges($activeCategoryNames);
            $badgesHtml = ob_get_clean();

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'status' => 'success',
                'total' => $totalProjects,
                'page' => $page,
                'totalPages' => $totalPages,
                'html' => $gridHtml,
                'pagination_html' => $paginationHtml,
                'badges_html' => $badgesHtml,
                'selected_ids' => $selectedCatIds
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 5. Chuẩn bị data cho view chính
        $data = [
            'projects' => $projects,
            'totalProjects' => $totalProjects,
            'categoryTree' => $categoryTree,
            'allCategories' => $allCategories,
            'selectedCatIds' => $selectedCatIds,
            'activeCategoryNames' => $activeCategoryNames,
            'currentPageNum' => $page,
            'totalPages' => $totalPages,
            'perPage' => $limit,
            
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
     * Mở rộng danh sách category IDs bao gồm cả các con/cháu trực thuộc
     */
    private function expandCategoryIdsWithChildren(array $allCategories, array $selectedIds): array
    {
        if (empty($selectedIds)) {
            return [];
        }

        // Xây dựng map parent_id => [child_ids]
        $childrenMap = [];
        foreach ($allCategories as $cat) {
            $parentId = empty($cat['parent_id']) ? 0 : (int)$cat['parent_id'];
            $childrenMap[$parentId][] = (int)$cat['id'];
        }

        $allIds = $selectedIds;
        $queue = $selectedIds;

        while (!empty($queue)) {
            $currentId = array_shift($queue);
            if (isset($childrenMap[$currentId])) {
                foreach ($childrenMap[$currentId] as $childId) {
                    if (!in_array($childId, $allIds)) {
                        $allIds[] = $childId;
                        $queue[] = $childId;
                    }
                }
            }
        }

        return array_unique($allIds);
    }

    /**
     * Tính tổng số dự án của từng node trong cây phân cấp
     */
    private function computeTreeCounts(array &$tree, array $countsMap, array $allCategories): void
    {
        foreach ($tree as &$node) {
            $nodeId = (int)$node['id'];
            $directCount = $countsMap[$nodeId] ?? 0;
            
            if (!empty($node['children'])) {
                $this->computeTreeCounts($node['children'], $countsMap, $allCategories);
                $descendantIds = $this->expandCategoryIdsWithChildren($allCategories, [$nodeId]);
                $sum = 0;
                foreach ($descendantIds as $dId) {
                    $sum += ($countsMap[$dId] ?? 0);
                }
                $node['project_count'] = max($directCount, $sum);
            } else {
                $node['project_count'] = $directCount;
            }
        }
        unset($node);
    }

    /**
     * Render grid các items dự án (Dùng chung cho cả AJAX và View)
     */
    public function renderProjectsGrid(array $projects): void
    {
        if (!empty($projects)):
            foreach ($projects as $project): 
                $projectUrl = '/chi-tiet-du-an-' . urlencode($project['slug'] ?? '');
                $imageUrl = !empty($project['image']) ? $project['image'] : 'assets/images/projects/placeholder.jpg';
                
                $serviceNames = [];
                if (!empty($project['services'])) {
                    foreach ($project['services'] as $service) {
                        $serviceNames[] = htmlspecialchars($service['name']);
                    }
                }
                $servicesDisplay = !empty($serviceNames) ? implode(', ', $serviceNames) : 'Chưa phân loại';
            ?>
            <div class="col-lg-4 col-md-6 project-grid-item">
                <div class="service_item project_service_card">
                    <div class="service_img">
                        <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="<?php echo htmlspecialchars($project['title'] ?? ''); ?>" loading="lazy">
                        <div class="hover_content">
                            <a href="<?php echo htmlspecialchars($projectUrl); ?>" class="read_more">Xem thêm</a>
                        </div>
                    </div>
                    <a href="<?php echo htmlspecialchars($projectUrl); ?>">
                        <h3 class="f_size_20 title_color f_600 project_item_title"><?php echo htmlspecialchars($project['title'] ?? ''); ?></h3>
                    </a>
                    <span class="bottom_br"></span>
                    <p class="project_cat_subtitle"><?php echo $servicesDisplay; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="no-projects-found">
                    <div class="no-projects-icon">
                        <i class="fa fa-folder-open-o"></i>
                    </div>
                    <h4>Không tìm thấy dự án nào</h4>
                    <p>Không có dự án nào phù hợp với lĩnh vực bạn đã chọn. Vui lòng chọn tiêu chí khác hoặc xóa bộ lọc.</p>
                    <a href="/du-an" class="btn_filter_reset_inline">
                        <i class="fa fa-refresh"></i> Xóa bộ lọc
                    </a>
                </div>
            </div>
        <?php endif;
    }

    /**
     * Render HTML phân trang
     */
    public function renderPagination(int $currentPage, int $totalPages, array $selectedCatIds = []): void
    {
        if ($totalPages <= 1) {
            return;
        }

        $catsParam = !empty($selectedCatIds) ? implode(',', $selectedCatIds) : '';
        ?>
        <nav aria-label="Projects pagination" class="projects-pagination-nav">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="#" data-page="<?php echo max(1, $currentPage - 1); ?>" aria-label="Previous">
                        <i class="fa fa-angle-left"></i>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                        <a class="page-link" href="#" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="#" data-page="<?php echo min($totalPages, $currentPage + 1); ?>" aria-label="Next">
                        <i class="fa fa-angle-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <?php
    }

    /**
     * Render các badge bộ lọc đang hoạt động
     */
    public function renderActiveFilterBadges(array $activeCategoryNames): void
    {
        if (empty($activeCategoryNames)) {
            return;
        }
        ?>
        <div class="active-filter-tags">
            <span class="active-filter-label"><i class="fa fa-filter"></i> Đang lọc:</span>
            <?php foreach ($activeCategoryNames as $catId => $name): ?>
                <span class="filter-tag-badge" data-id="<?php echo (int)$catId; ?>">
                    <?php echo htmlspecialchars($name); ?>
                    <button type="button" class="btn-remove-tag" data-id="<?php echo (int)$catId; ?>" title="Bỏ chọn">&times;</button>
                </span>
            <?php endforeach; ?>
            <button type="button" class="btn-clear-all-tags" id="btn-clear-all-filters">Xóa tất cả</button>
        </div>
        <?php
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
        
        // Lấy services của project
        $projectServices = $this->projectsModel->getProjectServices($projectDetail['id']);
        
        // Lấy dự án liên quan (cùng services)
        $relatedProjects = $this->projectsModel->getRelatedByServices($projectDetail['id'], 3);
        
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Dự án', 'url' => '/du-an'],
            ['title' => htmlspecialchars($projectDetail['title']), 'url' => null],
        ];
        
        // Chuẩn bị data cho view
        $data = [
            'projectDetail' => $projectDetail,
            'projectServices' => $projectServices,
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