<?php
/**
 * Projects Page View
 * 
 * Hiển thị danh sách các dự án theo thiết kế template
 * 
 * Biến truyền vào từ ProjectsController:
 * - $projects: array chứa danh sách dự án với services
 * - $categories: array chứa danh mục (để tương thích)
 * - Các biến từ master.php: $title, $breadcrumbs, etc.
 */
?>

<section class="projects_area sec_gap">
    <div class="container">
        
        <!-- Projects Grid -->
        <div class="row project_info_two">
            <?php if (!empty($projects)): ?>
                <?php foreach ($projects as $project): 
                    $projectUrl = '/chi-tiet-du-an-' . urlencode($project['slug'] ?? '');
                    $imageUrl = !empty($project['image']) ? $project['image'] : 'assets/images/projects/placeholder.jpg';
                    
                    // Get service names for display
                    $serviceNames = [];
                    if (!empty($project['services'])) {
                        foreach ($project['services'] as $service) {
                            $serviceNames[] = htmlspecialchars($service['name']);
                        }
                    }
                    $servicesDisplay = !empty($serviceNames) ? implode(', ', $serviceNames) : 'Chưa phân loại';
                ?>
                <div class="col-lg-4 col-sm-6">
                    <div class="lt_project_item text-center mb_40">
                        <div class="lt_project_img">
                            <img class="img-fluid" src="<?php echo htmlspecialchars($imageUrl); ?>" alt="<?php echo htmlspecialchars($project['title'] ?? ''); ?>">
                            <span class="arrow">
                                <a href="<?php echo htmlspecialchars($projectUrl); ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                </a>
                            </span>
                        </div>
                        <div class="lt_project_text">
                            <a href="<?php echo htmlspecialchars($projectUrl); ?>">
                                <h5 class="project-title"><?php echo htmlspecialchars($project['title'] ?? ''); ?></h5>
                            </a>
                            <p class="project-category"><?php echo $servicesDisplay; ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- No Projects Message -->
                <div class="col-12">
                    <div class="no-projects">
                        <i class="fa fa-folder-open"></i>
                        <h4>Không tìm thấy dự án</h4>
                        <p>Hiện tại chưa có dự án nào để hiển thị. Vui lòng quay lại sau.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Loading Indicator (for AJAX load more) -->
        <div class="projects-loading" style="display: none;">
            <i class="fa fa-spinner fa-spin"></i>
            <p>Đang tải thêm dự án...</p>
        </div>
        
    </div>
</section>
