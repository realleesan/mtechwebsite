<?php
/**
 * Projects Page View
 * 
 * Hiển thị danh mục phân cấp theo Drill-Down (Lĩnh vực cha -> con -> danh sách dự án)
 * Bố cục: 2 cột (Khu vực chính col-lg-9 bên trái + Sidebar lọc col-lg-3 bên phải)
 */

$mode = $mode ?? 'categories'; // 'categories' hoặc 'projects'
$displayCategories = $displayCategories ?? [];
$currentParentCategory = $currentParentCategory ?? null;
$breadcrumbs = $breadcrumbs ?? [];
$projects = $projects ?? [];
$totalProjects = $totalProjects ?? 0;
$categoryTree = $categoryTree ?? [];
$categoryCounts = $categoryCounts ?? [];
$allCategories = $allCategories ?? [];
$selectedCatIds = $selectedCatIds ?? [];
$activeCategoryNames = $activeCategoryNames ?? [];
$currentPageNum = $currentPageNum ?? 1;
$totalPages = $totalPages ?? 1;

// Tiêu đề trang
$pageHeading = 'Dự án MTECH';
$pageDescription = 'Khám phá các lĩnh vực tư vấn, thiết kế, giám sát công trình tiêu biểu do MTECH thực hiện.';

if ($mode === 'categories') {
    if ($currentParentCategory !== null) {
        $pageHeading = htmlspecialchars($currentParentCategory['name']);
        $pageDescription = 'Chọn lĩnh vực chi tiết bên dưới để xem danh sách các dự án tiêu biểu.';
    } else {
        $pageHeading = 'Lĩnh vực dự án';
        $pageDescription = 'Chọn lĩnh vực bên dưới để khám phá các dự án tương ứng.';
    }
} elseif (!empty($activeCategoryNames)) {
    $pageHeading = 'Dự án: ' . htmlspecialchars(implode(', ', $activeCategoryNames));
    $pageDescription = 'Danh sách các dự án thuộc lĩnh vực đã chọn.';
}
?>

<section class="projects_area sec_gap">
    <div class="container">
        
        <!-- Section Header -->
        <div class="section_title mb_55">
            <h2 class="f_600 f_size_32 title_color" id="projects-page-heading"><?php echo $pageHeading; ?></h2>
            <span class="title_br"></span>
            <p class="mt_7" id="projects-page-description"><?php echo $pageDescription; ?></p>
        </div>

        <div class="row">
            
            <!-- Cột chính bên trái: Danh mục phân cấp / Danh sách dự án (col-lg-9) -->
            <div class="col-lg-9 order-2 order-lg-1">
                
                <!-- Breadcrumbs Drilldown Navigation -->
                <div id="project-breadcrumbs-wrapper">
                    <?php if (count($breadcrumbs) > 1): ?>
                    <nav class="project-drilldown-breadcrumbs mb-3">
                        <ol class="breadcrumb-drilldown-list">
                            <?php foreach ($breadcrumbs as $idx => $bc): ?>
                                <?php if (!empty($bc['url'])): ?>
                                    <li class="breadcrumb-item">
                                        <a href="<?php echo htmlspecialchars($bc['url']); ?>" class="drilldown-bc-link">
                                            <i class="fa fa-folder-o me-1"></i><?php echo htmlspecialchars($bc['title']); ?>
                                        </a>
                                    </li>
                                <?php else: ?>
                                    <li class="breadcrumb-item active">
                                        <i class="fa fa-folder-open me-1"></i><?php echo htmlspecialchars($bc['title']); ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ol>
                    </nav>
                    <?php endif; ?>
                </div>

                <!-- Filter Status Bar -->
                <div class="projects_filter_status_bar">
                    <div class="status_left">
                        <span class="projects_count_text">
                            <?php if ($mode === 'categories'): ?>
                                Hiển thị <strong><?php echo count($displayCategories); ?></strong> lĩnh vực (Tổng <strong><?php echo $totalProjects; ?></strong> dự án)
                            <?php else: ?>
                                Hiển thị <strong><?php echo $totalProjects; ?></strong> dự án
                            <?php endif; ?>
                        </span>
                    </div>

                    <div class="status_right" id="active-filter-badges-wrapper">
                        <?php if (!empty($activeCategoryNames)): ?>
                        <div class="active-filter-tags">
                            <span class="active-filter-label"><i class="fa fa-filter"></i> Đang chọn:</span>
                            <?php foreach ($activeCategoryNames as $catId => $name): ?>
                                <span class="filter-tag-badge" data-id="<?php echo (int)$catId; ?>">
                                    <?php echo htmlspecialchars($name); ?>
                                    <button type="button" class="btn-remove-tag" data-id="<?php echo (int)$catId; ?>" title="Bỏ chọn">&times;</button>
                                </span>
                            <?php endforeach; ?>
                            <button type="button" class="btn-clear-all-tags" id="btn-clear-all-filters">Về tất cả lĩnh vực</button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Main Grid Container: Categories or Projects -->
                <div class="projects_grid_wrapper">
                    <div class="row project_info_two" id="projects-list-container">
                        <?php 
                        if ($mode === 'categories') {
                            if (!empty($displayCategories)) {
                                // Map children count
                                $childrenCountMap = [];
                                foreach ($allCategories as $c) {
                                    $pId = empty($c['parent_id']) ? 0 : (int)$c['parent_id'];
                                    $childrenCountMap[$pId] = ($childrenCountMap[$pId] ?? 0) + 1;
                                }

                                foreach ($displayCategories as $cat):
                                    $catId = (int)$cat['id'];
                                    $hasChildren = !empty($childrenCountMap[$catId]);
                                    $catUrl = '/du-an?cat=' . urlencode($cat['slug']);
                                    $imageUrl = !empty($cat['image']) ? $cat['image'] : 'assets/images/services/service-1.jpg';
                                    $projectCount = (int)($cat['project_count'] ?? ($categoryCounts[$catId] ?? 0));
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
                                <?php 
                                endforeach;
                            } else {
                                ?>
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
                                <?php
                            }
                        } else {
                            // Mode === 'projects'
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
                            <?php 
                                endforeach;
                            else: 
                            ?>
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
                        ?>
                    </div>
                </div>

                <!-- Phân trang (chỉ hiển thị khi ở chế độ Projects) -->
                <div class="projects_pagination_area" id="projects-pagination-container">
                    <?php if ($mode === 'projects' && $totalPages > 1): ?>
                    <nav aria-label="Projects pagination" class="projects-pagination-nav">
                        <ul class="pagination">
                            <li class="page-item <?php echo $currentPageNum <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="#" data-page="<?php echo max(1, $currentPageNum - 1); ?>" aria-label="Previous">
                                    <i class="fa fa-angle-left"></i>
                                </a>
                            </li>
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo $i === $currentPageNum ? 'active' : ''; ?>">
                                    <a class="page-link" href="#" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?php echo $currentPageNum >= $totalPages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="#" data-page="<?php echo min($totalPages, $currentPageNum + 1); ?>" aria-label="Next">
                                    <i class="fa fa-angle-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>

            </div><!-- /.col-lg-9 -->

            <!-- Cột phụ bên phải: Layout Sidebar Bộ lọc (col-lg-3) -->
            <div class="col-lg-3 order-1 order-lg-2">
                <?php include __DIR__ . '/../_layout/project_sidebar.php'; ?>
            </div><!-- /.col-lg-3 -->

        </div><!-- /.row -->

    </div><!-- /.container -->
</section>
