<?php
/**
 * Projects Page View
 * 
 * Hiển thị danh sách dự án đồng bộ thiết kế với trang Lĩnh vực (Our Services)
 * Bố cục: 2 cột (Dự án col-lg-9 bên trái + Sidebar lọc col-lg-3 bên phải)
 */

$projects = $projects ?? [];
$totalProjects = $totalProjects ?? 0;
$categoryTree = $categoryTree ?? [];
$selectedCatIds = $selectedCatIds ?? [];
$activeCategoryNames = $activeCategoryNames ?? [];
$currentPageNum = $currentPageNum ?? 1;
$totalPages = $totalPages ?? 1;
?>

<section class="projects_area sec_gap">
    <div class="container">
        
        <!-- Section Header nếu cần -->
        <div class="section_title mb_55">
            <h2 class="f_600 f_size_32 title_color">Dự án tiêu biểu</h2>
            <span class="title_br"></span>
            <p class="mt_7">Tổng hợp các dự án tư vấn, thiết kế, giám sát công trình tiêu biểu do MTECH thực hiện.</p>
        </div>

        <div class="row">
            
            <!-- Cột chính bên trái: Danh sách dự án (col-lg-9) -->
            <div class="col-lg-9 order-2 order-lg-1">
                
                <!-- Filter Status Bar -->
                <div class="projects_filter_status_bar">
                    <div class="status_left">
                        <span class="projects_count_text">
                            Hiển thị <strong><?php echo $totalProjects; ?></strong> dự án
                        </span>
                    </div>

                    <div class="status_right" id="active-filter-badges-wrapper">
                        <?php if (!empty($activeCategoryNames)): ?>
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
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Projects Grid Container -->
                <div class="projects_grid_wrapper">
                    <div class="row project_info_two" id="projects-list-container">
                        <?php 
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
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Phân trang -->
                <div class="projects_pagination_area" id="projects-pagination-container">
                    <?php if ($totalPages > 1): ?>
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
