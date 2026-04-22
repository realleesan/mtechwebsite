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
        
        <?php if (!empty($categories)): ?>
        <!-- Category Filters -->
        <div class="project-filters">
            <button class="active" data-filter="all">All Projects</button>
            <?php foreach ($categories as $category): ?>
            <button data-filter="<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $category))); ?>">
                <?php echo htmlspecialchars($category); ?>
            </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <!-- Projects Grid -->
        <div class="row project_info_two">
            <?php if (!empty($projects)): ?>
                <?php foreach ($projects as $project): 
                    $categoryClass = strtolower(str_replace(' ', '-', $project['category'] ?? ''));
                    $projectUrl = '?page=project-details&slug=' . urlencode($project['slug'] ?? '');
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
                            <h5 class="f_600 f_size_18 title_color"><?php echo htmlspecialchars($project['title'] ?? ''); ?></h5>
                        </a>
                        <p><?php echo htmlspecialchars($project['category'] ?? ''); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- No Projects Message -->
                <div class="col-12">
                    <div class="no-projects">
                        <i class="fa fa-folder-open"></i>
                        <h4>No Projects Found</h4>
                        <p>There are currently no projects to display. Please check back later.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Loading Indicator (for AJAX load more) -->
        <div class="projects-loading" style="display: none;">
            <i class="fa fa-spinner fa-spin"></i>
            <p>Loading more projects...</p>
        </div>
        
    </div>
</section>
