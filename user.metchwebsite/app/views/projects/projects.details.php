<?php
/**
 * Projects Details Page View
 * Trang chi tiết dự án - Bố cục 2 phần: Trái (Slider ảnh trực quan) & Phải (10 mục thông số)
 * 
 * Biến truyền vào từ ProjectsController:
 * - $projectDetail: array thông tin dự án
 * - $projectServices: array các lĩnh vực của dự án
 * - $relatedProjects: array dự án liên quan
 */

// Kiểm tra dữ liệu từ controller
$projectDetail = $projectDetail ?? null;

if (!$projectDetail) {
    $projectNotFound = true;
} else {
    $projectNotFound = false;

    // Thu thập danh sách ảnh cho Slider
    $slideImages = [];
    if (!empty($projectDetail['gallery'])) {
        $decodedGallery = json_decode($projectDetail['gallery'], true);
        if (is_array($decodedGallery) && !empty($decodedGallery)) {
            foreach ($decodedGallery as $gImg) {
                if (!empty($gImg) && !in_array($gImg, $slideImages)) {
                    $slideImages[] = $gImg;
                }
            }
        }
    }
    
    // Nếu gallery trống, fallback sang ảnh đại diện chính
    if (empty($slideImages) && !empty($projectDetail['image'])) {
        $slideImages[] = $projectDetail['image'];
    }
    
    // Nếu vẫn trống, dùng ảnh placeholder mặc định
    if (empty($slideImages)) {
        $slideImages[] = 'assets/images/placeholder-project.jpg';
    }

    // 10 Mục thông số kỹ thuật chuẩn hóa
    $specsList = [
        1 => ['label' => 'Tên dự án', 'value' => $projectDetail['title'] ?? ''],
        2 => ['label' => 'Công suất', 'value' => $projectDetail['capacity'] ?? ''],
        3 => ['label' => 'Địa điểm xây dựng', 'value' => $projectDetail['location'] ?? ''],
        4 => ['label' => 'Chủ đầu tư dự án', 'value' => $projectDetail['client'] ?? ''],
        5 => ['label' => 'Tổng mức đầu tư', 'value' => $projectDetail['total_investment'] ?? ''],
        6 => ['label' => 'Năm xây dựng / hoàn thành', 'value' => $projectDetail['construction_year'] ?? ''],
        7 => ['label' => 'Hình thức gói thầu (EP/EPC)', 'value' => $projectDetail['bidding_form'] ?? ''],
        8 => ['label' => 'Nhà thầu cung cấp thiết bị', 'value' => $projectDetail['equipment_contractor'] ?? ''],
        9 => ['label' => 'Đơn vị tư vấn thiết kế xây dựng', 'value' => $projectDetail['design_consultant'] ?? ''],
        10 => ['label' => 'Đơn vị tư vấn giám sát', 'value' => $projectDetail['supervision_consultant'] ?? ''],
    ];
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
                <i class="fa fa-arrow-left"></i> Quay lại trang dự án
            </a>
        </div>
    </div>
</section>

<?php else: ?>

<!-- Project Details Area - 2 Columns Layout -->
<section class="project_details_area sec_gap">
    <div class="container">
        
        <!-- Header & Breadcrumb Top Bar -->
        <div class="project-details-topbar mb-4">
            <a href="/du-an" class="btn-back-projects">
                <i class="fa fa-arrow-left me-1"></i> Quay lại danh mục dự án
            </a>
        </div>

        <div class="row align-items-start g-4">
            
            <!-- CỘT BÊN TRÁI (col-lg-7): Slider Ảnh Trực Quan Của Dự Án -->
            <div class="col-lg-7">
                <div class="project-slider-wrapper">
                    
                    <!-- Main Slider Stage -->
                    <div class="project-main-slider-container" id="projectMainSlider">
                        <div class="project-slides-track">
                            <?php foreach ($slideImages as $index => $imgUrl): ?>
                                <div class="project-slide-item <?php echo $index === 0 ? 'active' : ''; ?>" data-slide-index="<?php echo $index; ?>">
                                    <img src="<?php echo htmlspecialchars($imgUrl); ?>" alt="<?php echo htmlspecialchars($projectDetail['title']); ?>" loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Prev / Next Navigation Controls -->
                        <?php if (count($slideImages) > 1): ?>
                            <button type="button" class="slider-arrow-btn prev-btn" id="sliderPrevBtn" aria-label="Ảnh trước">
                                <i class="fa fa-angle-left"></i>
                            </button>
                            <button type="button" class="slider-arrow-btn next-btn" id="sliderNextBtn" aria-label="Ảnh tiếp">
                                <i class="fa fa-angle-right"></i>
                            </button>
                        <?php endif; ?>

                        <!-- Bullet Dots Indicator -->
                        <?php if (count($slideImages) > 1): ?>
                            <div class="slider-dots-container" id="sliderDots">
                                <?php foreach ($slideImages as $index => $imgUrl): ?>
                                    <button type="button" class="slider-dot <?php echo $index === 0 ? 'active' : ''; ?>" data-slide-to="<?php echo $index; ?>" aria-label="Đến ảnh <?php echo $index + 1; ?>"></button>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Thumbnails Navigation (Nếu có từ 2 ảnh trở lên) -->
                    <?php if (count($slideImages) > 1): ?>
                        <div class="project-slider-thumbnails mt-3">
                            <?php foreach ($slideImages as $index => $imgUrl): ?>
                                <div class="slider-thumb-item <?php echo $index === 0 ? 'active' : ''; ?>" data-thumb-index="<?php echo $index; ?>">
                                    <img src="<?php echo htmlspecialchars($imgUrl); ?>" alt="Thumbnail <?php echo $index + 1; ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div><!-- /.col-lg-7 -->

            <!-- CỘT BÊN PHẢI (col-lg-5): 10 Mục Thông Số Dự Án -->
            <div class="col-lg-5">
                <div class="project-specs-card">
                    <div class="specs-card-header">
                        <h3 class="specs-card-title">Thông tin dự án</h3>
                        <span class="specs-card-br"></span>
                    </div>

                    <div class="specs-card-body">
                        <table class="table project-specs-table mb-0">
                            <tbody>
                                <?php foreach ($specsList as $num => $spec): ?>
                                    <tr class="spec-row">
                                        <th class="spec-label" scope="row">
                                            <span class="spec-num"><?php echo $num; ?>.</span> <?php echo htmlspecialchars($spec['label']); ?>:
                                        </th>
                                        <td class="spec-value">
                                            <?php 
                                            $val = trim($spec['value']);
                                            if (!empty($val)) {
                                                echo htmlspecialchars($val);
                                            } else {
                                                echo '<span class="text-muted">—</span>';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (!empty($projectDetail['description'])): ?>
                        <div class="specs-card-description mt-3 pt-3 border-top">
                            <h5 class="f_size_16 f_600 title_color mb-2">Mô tả tóm tắt:</h5>
                            <p class="text-secondary small mb-0"><?php echo nl2br(htmlspecialchars($projectDetail['description'])); ?></p>
                        </div>
                    <?php endif; ?>

                </div>
            </div><!-- /.col-lg-5 -->

        </div><!-- /.row -->

    </div><!-- /.container -->
</section>

<?php endif; ?>
