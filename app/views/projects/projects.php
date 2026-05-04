<?php
/**
 * Projects Page View
 * 
 * Hiển thị danh sách các dự án theo thiết kế template
 */

// Load ProjectsModel
require_once __DIR__ . '/../../models/ProjectsModel.php';

// Khởi tạo model và lấy dữ liệu
$projectsModel = new ProjectsModel();
$projects = $projectsModel->getAll(9, 0, 1);
$categories = $projectsModel->getCategories();
?>

<section class="projects_area sec_gap">
    <div class="container">
        
        <!-- Projects Grid -->
        <div class="row project_info_two">
            <?php if (!empty($projects)): ?>
                <?php foreach ($projects as $project): 
                    $categoryClass = strtolower(str_replace(' ', '-', $project['category'] ?? ''));
                    $projectUrl = '/chi-tiet-du-an-' . urlencode($project['slug'] ?? '');
                    $imageUrl = !empty($project['image']) ? $project['image'] : 'assets/images/projects/placeholder.jpg';
                ?>
                <div class="col-lg-4 col-sm-6">
                    <div class="lt_project_item text-center mb_40" data-category="<?php echo htmlspecialchars($categoryClass); ?>">
                        <div class="lt_project_img">
                            <img class="img-fluid" src="<?php echo htmlspecialchars($imageUrl); ?>" alt="<?php echo htmlspecialchars($project['title'] ?? ''); ?>">
                            <span class="arrow">
                                <a href="<?php echo htmlspecialchars($projectUrl); ?>">
                                    <i class="fa fa-link"></i>
                                </a>
                            </span>
                        </div>
                        <a href="<?php echo htmlspecialchars($projectUrl); ?>">
                            <h5 class="project-title"><?php echo htmlspecialchars($project['title'] ?? ''); ?></h5>
                        </a>
                        <p class="project-category"><?php echo htmlspecialchars($project['category'] ?? ''); ?></p>
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
