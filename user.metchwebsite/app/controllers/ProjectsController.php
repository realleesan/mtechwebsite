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
     * Hiển thị danh sách dự án kèm bộ lọc lĩnh vực và chế độ drill-down phân cấp danh mục
     */
    public function index()
    {
        // 1. Lấy tham số bộ lọc & phân trang
        $page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : (isset($_GET['page_num']) ? max(1, (int)$_GET['page_num']) : 1);
        $limit = 9;
        $offset = ($page - 1) * $limit;
        
        // 2. Lấy dữ liệu tất cả categories và tính toán cây phân cấp + đếm số lượng dự án
        $allCategories = $this->categoriesModel->getAllCategories();
        $categoryCounts = $this->projectsModel->getCategoryProjectCounts();
        
        // Map category ID => category info & Slug => category info
        $categoryMap = [];
        $categorySlugMap = [];
        $childrenMap = [];
        foreach ($allCategories as $cat) {
            $catId = (int)$cat['id'];
            $parentId = empty($cat['parent_id']) ? 0 : (int)$cat['parent_id'];
            $categoryMap[$catId] = $cat;
            $categorySlugMap[$cat['slug']] = $cat;
            $childrenMap[$parentId][] = $cat;
        }

        // Gắn số lượng dự án cho từng category và dựng cây phân cấp
        $categoriesWithCounts = [];
        foreach ($allCategories as $cat) {
            $catId = (int)$cat['id'];
            $cat['project_count'] = $categoryCounts[$catId] ?? 0;
            $categoriesWithCounts[] = $cat;
        }
        $categoryTree = $this->categoriesModel->buildTree($categoriesWithCounts);
        $this->computeTreeCounts($categoryTree, $categoryCounts, $allCategories);

        // 3. Parse tham số xem và lọc
        $selectedCatIds = [];
        $singleCategory = null; // Danh mục cụ thể đang được chọn drill-down

        if (!empty($_GET['cat'])) {
            $catParam = trim($_GET['cat']);
            if (is_numeric($catParam) && isset($categoryMap[(int)$catParam])) {
                $singleCategory = $categoryMap[(int)$catParam];
                $selectedCatIds = [(int)$catParam];
            } elseif (isset($categorySlugMap[$catParam])) {
                $singleCategory = $categorySlugMap[$catParam];
                $selectedCatIds = [(int)$singleCategory['id']];
            }
        } elseif (!empty($_GET['category_id'])) {
            $cId = is_array($_GET['category_id']) ? (int)$_GET['category_id'][0] : (int)$_GET['category_id'];
            if (isset($categoryMap[$cId])) {
                $singleCategory = $categoryMap[$cId];
                $selectedCatIds = [$cId];
            }
        } elseif (!empty($_GET['categories'])) {
            if (is_array($_GET['categories'])) {
                $selectedCatIds = array_map('intval', $_GET['categories']);
            } else {
                $selectedCatIds = array_map('intval', explode(',', $_GET['categories']));
            }
            // Nếu chỉ chọn đúng 1 danh mục từ sidebar
            if (count($selectedCatIds) === 1 && isset($categoryMap[$selectedCatIds[0]])) {
                $singleCategory = $categoryMap[$selectedCatIds[0]];
            }
        }

        $selectedCatIds = array_values(array_filter(array_unique($selectedCatIds), function($id) {
            return $id > 0;
        }));

        // 4. Xác định chế độ hiển thị (Mode): 'categories' hay 'projects'
        $mode = 'categories';
        $displayCategories = [];
        $currentParentCategory = null;
        $breadcrumbs = [
            ['title' => 'Tất cả lĩnh vực', 'url' => '/du-an']
        ];

        if (empty($selectedCatIds)) {
            // Mặc định (Root): hiển thị các lĩnh vực cấp 1
            $mode = 'categories';
            $displayCategories = $childrenMap[0] ?? [];
        } elseif (!empty($_GET['categories'])) {
            // Người dùng dùng BỘ LỌC SIDEBAR -> Lọc và hiển thị đúng các Thẻ Lĩnh vực được tick chọn (Hướng 2)
            $mode = 'categories';
            foreach ($selectedCatIds as $id) {
                if (isset($categoryMap[$id])) {
                    $displayCategories[] = $categoryMap[$id];
                }
            }
            $breadcrumbs[] = ['title' => 'Lĩnh vực đã lọc (' . count($displayCategories) . ')', 'url' => null];
        } else {
            // Người dùng click vào một Thẻ Lĩnh vực (Drill-Down)
            if ($singleCategory !== null) {
                $hasChildren = !empty($childrenMap[(int)$singleCategory['id']]);
                if ($hasChildren) {
                    // Lĩnh vực này là LĨNH VỰC CHA và CÓ CON -> Hiển thị danh sách các thẻ lĩnh vực con
                    $mode = 'categories';
                    $currentParentCategory = $singleCategory;
                    $displayCategories = $childrenMap[(int)$singleCategory['id']] ?? [];
                    $breadcrumbs[] = ['title' => $singleCategory['name'], 'url' => null];
                } else {
                    // Lĩnh vực LÁ (không có con hoặc cấp con cuối cùng) -> Hiển thị danh sách các Dự án
                    $mode = 'projects';
                    if (!empty($singleCategory['parent_id']) && isset($categoryMap[(int)$singleCategory['parent_id']])) {
                        $parentCat = $categoryMap[(int)$singleCategory['parent_id']];
                        $breadcrumbs[] = ['title' => $parentCat['name'], 'url' => '/du-an?cat=' . urlencode($parentCat['slug'])];
                    }
                    $breadcrumbs[] = ['title' => $singleCategory['name'], 'url' => null];
                }
            } else {
                $mode = 'categories';
                $displayCategories = $childrenMap[0] ?? [];
            }
        }

        // 5. Nếu ở chế độ 'projects', truy vấn dữ liệu dự án
        $projects = [];
        $totalProjects = 0;
        $totalPages = 1;

        if ($mode === 'projects') {
            $allFilterIds = $this->expandCategoryIdsWithChildren($allCategories, $selectedCatIds);
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
        } else {
            // Ở chế độ categories, đếm tổng số dự án của danh mục đang xem
            if ($currentParentCategory !== null) {
                $parentFilterIds = $this->expandCategoryIdsWithChildren($allCategories, [(int)$currentParentCategory['id']]);
                $totalProjects = $this->projectsModel->countFilteredProjects($parentFilterIds, 1);
            } else {
                $totalProjects = $this->projectsModel->countFilteredProjects([], 1);
            }
        }

        // Danh sách tên các lĩnh vực đang được chọn để hiển thị badges
        $activeCategoryNames = [];
        foreach ($selectedCatIds as $id) {
            if (isset($categoryMap[$id])) {
                $activeCategoryNames[$id] = $categoryMap[$id]['name'];
            }
        }

        // 6. Xử lý AJAX request
        $isAjax = isset($_GET['ajax']) && $_GET['ajax'] == '1' 
               || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

        if ($isAjax) {
            ob_start();
            if ($mode === 'categories') {
                $this->renderCategoriesGrid($displayCategories, $categoryCounts, $allCategories);
            } else {
                $this->renderProjectsGrid($projects);
            }
            $gridHtml = ob_get_clean();

            ob_start();
            if ($mode === 'projects') {
                $this->renderPagination($page, $totalPages, $selectedCatIds);
            }
            $paginationHtml = ob_get_clean();

            ob_start();
            $this->renderActiveFilterBadges($activeCategoryNames);
            $badgesHtml = ob_get_clean();

            ob_start();
            $this->renderBreadcrumbsNav($breadcrumbs);
            $breadcrumbsHtml = ob_get_clean();

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'status' => 'success',
                'mode' => $mode,
                'total' => $totalProjects,
                'page' => $page,
                'totalPages' => $totalPages,
                'html' => $gridHtml,
                'pagination_html' => $paginationHtml,
                'badges_html' => $badgesHtml,
                'breadcrumbs_html' => $breadcrumbsHtml,
                'selected_ids' => $selectedCatIds
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 7. Chuẩn bị data cho view chính
        $data = [
            'mode' => $mode,
            'displayCategories' => $displayCategories,
            'currentParentCategory' => $currentParentCategory,
            'breadcrumbs' => $breadcrumbs,
            'projects' => $projects,
            'totalProjects' => $totalProjects,
            'categoryTree' => $categoryTree,
            'allCategories' => $allCategories,
            'categoryCounts' => $categoryCounts,
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
     * Render grid các lĩnh vực (Chế độ xem phân cấp lĩnh vực)
     */
    public function renderCategoriesGrid(array $categories, array $countsMap = [], array $allCategories = []): void
    {
        if (!empty($categories)):
            // Map children count
            $childrenCountMap = [];
            foreach ($allCategories as $c) {
                $pId = empty($c['parent_id']) ? 0 : (int)$c['parent_id'];
                $childrenCountMap[$pId] = ($childrenCountMap[$pId] ?? 0) + 1;
            }

            foreach ($categories as $cat):
                $catId = (int)$cat['id'];
                $hasChildren = !empty($childrenCountMap[$catId]);
                $catUrl = '/du-an?cat=' . urlencode($cat['slug']);
                $imageUrl = !empty($cat['image']) ? $cat['image'] : 'assets/images/services/service-1.jpg';
                $projectCount = (int)($cat['project_count'] ?? ($countsMap[$catId] ?? 0));
                $actionText = $hasChildren ? 'Xem lĩnh vực con' : 'Xem dự án';
            ?>
            <div class="col-lg-4 col-md-6 project-grid-item category-card-col">
                <div class="service_item project_service_card category_drilldown_card" data-id="<?php echo $catId; ?>" data-has-children="<?php echo $hasChildren ? '1' : '0'; ?>">
                    <div class="service_img">
                        <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="<?php echo htmlspecialchars($cat['name'] ?? ''); ?>" loading="lazy">
                        <div class="hover_content">
                            <a href="<?php echo htmlspecialchars($catUrl); ?>" class="read_more btn-drilldown-cat" data-id="<?php echo $catId; ?>" data-slug="<?php echo htmlspecialchars($cat['slug']); ?>">
                                <?php echo $actionText; ?> <i class="fa <?php echo $hasChildren ? 'fa-folder-open-o' : 'fa-arrow-right'; ?>"></i>
                            </a>
                        </div>
                    </div>
                    <a href="<?php echo htmlspecialchars($catUrl); ?>" class="btn-drilldown-cat-title" data-id="<?php echo $catId; ?>" data-slug="<?php echo htmlspecialchars($cat['slug']); ?>">
                        <h3 class="f_size_20 title_color f_600 project_item_title"><?php echo htmlspecialchars($cat['name'] ?? ''); ?></h3>
                    </a>
                    <span class="bottom_br"></span>
                    <p class="project_cat_subtitle">
                        <?php if ($hasChildren): ?>
                            <span class="badge-cat-type"><i class="fa fa-sitemap"></i> <?php echo $childrenCountMap[$catId]; ?> lĩnh vực con</span>
                        <?php else: ?>
                            <span class="badge-cat-type"><i class="fa fa-briefcase"></i> <?php echo $projectCount; ?> dự án</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="no-projects-found">
                    <div class="no-projects-icon">
                        <i class="fa fa-folder-open-o"></i>
                    </div>
                    <h4>Chưa có lĩnh vực nào trong mục này</h4>
                    <p>Vui lòng quay lại danh sách tất cả lĩnh vực.</p>
                    <a href="/du-an" class="btn_filter_reset_inline">
                        <i class="fa fa-arrow-left"></i> Về trang dự án
                    </a>
                </div>
            </div>
        <?php endif;
    }

    /**
     * Render Breadcrumbs Navigation cho trang Dự án
     */
    public function renderBreadcrumbsNav(array $breadcrumbs): void
    {
        if (count($breadcrumbs) <= 1) {
            return;
        }
        ?>
        <nav class="project-drilldown-breadcrumbs mb-3">
            <ol class="breadcrumb-drilldown-list">
                <?php foreach ($breadcrumbs as $idx => $bc): ?>
                    <?php if (!empty($bc['url'])): ?>
                        <li class="breadcrumb-item"><a href="<?php echo htmlspecialchars($bc['url']); ?>" class="drilldown-bc-link"><i class="fa fa-folder-o me-1"></i><?php echo htmlspecialchars($bc['title']); ?></a></li>
                    <?php else: ?>
                        <li class="breadcrumb-item active"><i class="fa fa-folder-open me-1"></i><?php echo htmlspecialchars($bc['title']); ?></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ol>
        </nav>
        <?php
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