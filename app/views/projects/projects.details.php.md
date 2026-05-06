<?php
/**
 * Projects Details Page View
 * Trang chi tiết dự án
 * 
 * Biến truyền vào từ ProjectsController:
 * - $project: array chứa thông tin dự án
 * - $relatedProjects: array dự án liên quan
 * - Các biến từ master.php: $title, $breadcrumbs, etc.
 */

// Kiểm tra dữ liệu từ controller
$project = $project ?? null;

// Nếu không tìm thấy project, hiển thị thông báo lỗi
if (!$project) {
    $projectNotFound = true;
} else {
    $projectNotFound = false;
    
    // Parse tags thành array
    $tags = [];
    if (!empty($project['tags'])) {
        $tags = array_map('trim', explode(',', $project['tags']));
    }
    
    // Parse result_items từ JSON
    $resultItems = [];
    if (!empty($project['result_items'])) {
        $decoded = json_decode($project['result_items'], true);
        if (is_array($decoded)) {
            $resultItems = $decoded;
        }
    }
    
    // Format ngày
    $projectDate = '';
    if (!empty($project['project_date'])) {
        $projectDate = date('d F, Y', strtotime($project['project_date']));
    }
    
    // Xác định ảnh sử dụng
    $detailImage = $project['detail_image'] ?? ($project['image'] ?? '');
    $whatWeDidImage = $project['what_we_did_image'] ?? '';
}
?>

<?php if ($projectNotFound): ?>
<!-- Project Not Found State -->
<section class="project_details_area sec_gap">
    <div class="container">
        <div class="project-not-found">
            <i class="fa fa-folder-open-o"></i>
            <h2>Không tìm thấy dự án</h2>
            <p>Dự án bạn đang tìm kiếm không tồn tại hoặc đã bị xóa.</p>
            <a href="/du-an" class="btn-back">
                <i class="fa fa-arrow-left"></i> Quay lại dự án
            </a>
        </div>
    </div>
</section>

<?php else: ?>
<!-- Project Details Area -->
<section class="project_details_area sec_gap">
    <div class="container">
        <div class="row">
            <!-- Project Main Image -->
            <div class="col-lg-8 pr_image">
                <?php if ($detailImage): ?>
                    <img class="img-fluid" src="<?php echo htmlspecialchars($detailImage); ?>" alt="<?php echo htmlspecialchars($project['title'] ?? 'Project'); ?>">
                <?php else: ?>
                    <img class="img-fluid" src="assets/images/placeholder-project.jpg" alt="<?php echo htmlspecialchars($project['title'] ?? 'Project'); ?>">
                <?php endif; ?>
            </div>
            
            <!-- Project Info Box -->
            <div class="col-lg-4">
                <div class="project_info">
                    <ul class="list-unstyled">
                        <?php if (!empty($project['client'])): ?>
                        <li>
                            <span>Khách hàng :</span>
                            <?php echo htmlspecialchars($project['client']); ?>
                        </li>
                        <?php endif; ?>
                        
                        <?php if (!empty($project['category'])): ?>
                        <li>
                            <span>Danh mục :</span>
                            <?php echo htmlspecialchars($project['category'] ?? ''); ?>
                        </li>
                        <?php endif; ?>
                        
                        <?php if (!empty($project['project_date'])): ?>
                        <li>
                            <span>Ngày :</span>
                            <?php echo $projectDate ?? ''; ?>
                        </li>
                        <?php endif; ?>
                        
                        <?php if (!empty($project['status_label'])): ?>
                        <li>
                            <span>Trạng thái :</span>
                            <?php echo htmlspecialchars($project['status_label'] ?? ''); ?>
                        </li>
                        <?php endif; ?>
                        
                        <?php if (!empty($project['live_demo'])): ?>
                        <li>
                            <span>Link dự án :</span>
                            <a href="<?php echo htmlspecialchars($project['live_demo'] ?? ''); ?>" target="_blank" rel="noopener noreferrer">
                                <?php echo htmlspecialchars($project['live_demo'] ?? ''); ?>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if (!empty($tags)): ?>
                        <li>
                            <span>Thẻ :</span>
                            <?php echo htmlspecialchars(implode(', ', $tags ?? [])); ?>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            
            <!-- Project Description -->
            <div class="col-lg-12">
                <div class="project_description">
                    <h2 class="f_size_32 f_600 title_color mb_20">
                        <?php echo htmlspecialchars($project['title'] ?? 'Project'); ?>
                    </h2>
                    
                    <?php if (!empty($project['content'])): ?>
                        <div class="project-content">
                            <?php echo $project['content']; ?>
                        </div>
                    <?php elseif (!empty($project['description'])): ?>
                        <p class="mb_30">
                            <?php echo htmlspecialchars($project['description']); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- What We Did Section -->
<?php if (!empty($project['what_we_did'])): ?>
<section class="project_list_area bg_color_two sec_gap">
    <div class="container">
        <div class="row project_list">
            <div class="col-lg-8">
                <div class="pr_content">
                    <h2 class="f_size_32 f_600 title_color mb_20">
                        <?php echo htmlspecialchars($project['what_we_did_title'] ?? 'Công việc thực hiện'); ?>
                    </h2>
                    
                    <?php echo htmlspecialchars($project['what_we_did'] ?? ''); ?>
                </div>
            </div>
            
            <?php if ($whatWeDidImage): ?>
            <div class="col-lg-4">
                <img class="img-fluid" src="<?php echo htmlspecialchars($whatWeDidImage); ?>" alt="Công việc thực hiện">
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Results Section -->
<?php if (!empty($project['results']) || !empty($resultItems)): ?>
<section class="results_area sec_gap">
    <div class="container">
        <div class="result_content pr_content">
            <h2 class="f_size_32 f_600 title_color mb_20">
                <?php echo !empty($project['results_title']) ? htmlspecialchars($project['results_title']) : 'Kết quả'; ?>
            </h2>
            
            <?php if (!empty($project['results'])): ?>
                <?php echo $project['results']; ?>
            <?php endif; ?>
            
            <?php if (!empty($resultItems)): ?>
            <div class="pr_item_info">
                <?php foreach ($resultItems as $item): ?>
                <div class="pr_item">
                    <span class="dot"></span>
                    <p><?php echo htmlspecialchars($item); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Back to Projects Button -->
<section class="back-to-projects sec_gap" style="padding-top: 0;">
    <div class="container">
        <div class="text-center">
            <a href="/du-an" class="btn-back" style="
                display: inline-block;
                padding: 12px 30px;
                background-color: #1A3FBF;
                color: #fff;
                text-decoration: none;
                font-weight: 600;
                transition: background-color 0.3s ease;
            ">
                <i class="fa fa-arrow-left"></i> Xem tất cả dự án
            </a>
        </div>
    </div>
</section>

<?php endif; ?>
