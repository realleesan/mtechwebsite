<?php
/**
 * blog.details.php — Nội dung chính trang chi tiết Blog (col-lg-9)
 * Layout bên ngoài (section > container > row > col-9 + col-3 sidebar)
 * được master.php quản lý khi $showBlogSidebar = true.
 *
 * Biến nhận từ index.php:
 *   $blogDetail, $hiringPositions
 */

$blogDetail      = $blogDetail      ?? null;
$hiringPositions = $hiringPositions ?? [];

if (!$blogDetail) {
    echo '<p class="text-danger">Không tìm thấy bài viết.</p>';
    return;
}

$dateStr = format_date_vietnamese(date('d F, Y', strtotime($blogDetail['created_at'])));
$imgSrc  = !empty($blogDetail['image']) ? $blogDetail['image'] : 'assets/images/blogs/default.jpg';

// Tags
$tagLinks = [];
if (!empty($blogDetail['tags'])) {
    foreach ($blogDetail['tags'] as $t) {
        $tagLinks[] = '<a href="/tin-tuc-the-' . urlencode($t['slug']) . '" class="tag-link" target="_self">'
                      . htmlspecialchars($t['name']) . '</a>';
    }
}
$tagsStr = implode(' , ', $tagLinks);

// Kiểm tra blog tuyển dụng (category_id = 7)
$isHiring      = ($blogDetail['category_id'] == 7);
$hiringOpen    = false;
$daysRemaining = null;
$hiringClosed  = false;

if ($isHiring) {
    $hiringStatus  = $blogDetail['hiring_status'] ?? 1;
    $expiresInDays = $blogDetail['expires_in_days'] ?? null;
    $createdAt     = $blogDetail['created_at'] ?? null;

    $isExpired = false;
    if (!empty($expiresInDays) && !empty($createdAt)) {
        $expiresAt     = strtotime($createdAt . ' + ' . $expiresInDays . ' days');
        $isExpired     = time() > $expiresAt;
        $daysRemaining = max(0, ceil(($expiresAt - time()) / 86400));
    }

    $hiringClosed = $isExpired || empty($hiringStatus) || $hiringStatus != 1;
    $hiringOpen   = !$hiringClosed;
}

// Lấy message từ session
$applicationMessage = $_SESSION['job_application_message'] ?? null;
$applicationSuccess = $_SESSION['job_application_success'] ?? null;
unset($_SESSION['job_application_message'], $_SESSION['job_application_success']);
?>

<div class="blog_left_sidebar">

    <div class="lt_blog_item blog_post_item">

        <!-- Thumbnail + date badge -->
        <a href="#" class="blog_img">
            <img src="<?php echo htmlspecialchars($imgSrc); ?>"
                 alt="<?php echo htmlspecialchars($blogDetail['title']); ?>">
            <span class="blog-date-badge"><?php echo $dateStr; ?></span>
        </a>

        <!-- Post Info: Author + Tags -->
        <div class="post_info">
            <div class="blog_author_area">
                Đăng bởi: <a href="#" title="Posts by <?php echo htmlspecialchars($blogDetail['author']); ?>" rel="author">
                    <?php echo htmlspecialchars($blogDetail['author']); ?>
                </a>
                <?php if ($tagsStr): ?>
                    <span class="sep">/</span>
                    <?php echo $tagsStr; ?>
                <?php endif; ?>
            </div>
        </div>

        <hr class="border_bottom mt-0 mb_20">

        <!-- Title -->
        <div class="blog-text">
            <a href="#">
                <h4 class="f_600 title_color">
                    <?php echo htmlspecialchars($blogDetail['title']); ?>
                </h4>
            </a>
        </div>

        <!-- Full Content -->
        <?php if (!empty($blogDetail['full_content'])): ?>
            <?php echo $blogDetail['full_content']; ?>
        <?php elseif (!empty($blogDetail['content'])): ?>
            <div class="blog-text">
                <p><?php echo $blogDetail['content']; ?></p>
            </div>
        <?php endif; ?>

        <!-- Job Application Form — chỉ hiển thị cho blog tuyển dụng -->
        <?php if ($isHiring): ?>
            <div class="job-application-section mt-5">
                <hr class="border_bottom mb-4">

                <?php if ($applicationMessage): ?>
                    <div class="alert <?php echo $applicationSuccess ? 'alert-success' : 'alert-danger'; ?> mb-4">
                        <?php echo htmlspecialchars($applicationMessage); ?>
                    </div>
                <?php endif; ?>

                <?php if ($hiringOpen): ?>
                    <div class="job-form-wrapper">
                        <h3 class="f_600 title_color mb-3">
                            Ứng Tuyển Vị Trí: <?php echo htmlspecialchars($blogDetail['position'] ?? $blogDetail['title']); ?>
                        </h3>

                        <?php if ($daysRemaining !== null): ?>
                            <p class="text-muted mb-4">
                                <i class="far fa-clock mr-1"></i>
                                Thời hạn ứng tuyển còn: <strong><?php echo $daysRemaining; ?> ngày</strong>
                            </p>
                        <?php endif; ?>

                        <form method="POST" action="?page=job-application-submit"
                              enctype="multipart/form-data" class="job-application-form">
                            <input type="hidden" name="blog_id"     value="<?php echo $blogDetail['id']; ?>">
                            <input type="hidden" name="return_slug" value="<?php echo htmlspecialchars($blogDetail['slug']); ?>">

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="full_name">Họ và Tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="full_name" name="full_name"
                                           required placeholder="Nhập họ và tên" minlength="2">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email"
                                           required placeholder="Nhập email">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="phone">Số Điện Thoại <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                           required placeholder="Nhập số điện thoại">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="position">Vị Trí Ứng Tuyển <span class="text-danger">*</span></label>
                                    <select class="form-control" id="position" name="position" required>
                                        <option value="">-- Chọn vị trí --</option>
                                        <option value="<?php echo htmlspecialchars($blogDetail['position'] ?? ''); ?>" selected>
                                            <?php echo htmlspecialchars($blogDetail['position'] ?? 'Vị trí này'); ?>
                                        </option>
                                        <?php foreach ($hiringPositions as $pos): ?>
                                            <?php if ($pos['id'] != $blogDetail['id']): ?>
                                                <option value="<?php echo htmlspecialchars($pos['position']); ?>">
                                                    <?php echo htmlspecialchars($pos['position']); ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="cv_file">CV Ứng Tuyển (PDF) <span class="text-danger">*</span></label>
                                <div class="cv-file-wrapper" id="cv_file_wrapper">
                                    <input type="file" id="cv_file" name="cv" accept=".pdf" required>
                                    <span class="cv-file-text" id="cv_file_label">Chọn file PDF...</span>
                                    <span class="cv-file-btn">Browse</span>
                                </div>
                                <small class="form-text text-muted">Chỉ chấp nhận file PDF, tối đa 5MB</small>
                                <div class="file-upload-feedback" id="cv_feedback"></div>
                            </div>

                            <div class="form-group">
                                <label for="message">Nội Dung / Lời Giới Thiệu</label>
                                <textarea class="form-control" id="message" name="message" rows="4"
                                          placeholder="Viết lời giới thiệu ngắn về bản thân và lý do ứng tuyển..."></textarea>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn-submit">Gửi CV Ứng Tuyển</button>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="hiring-closed-notice text-center py-4">
                        <div class="alert alert-warning">
                            <h4 class="f_600">Tin Tuyển Dụng Đã Hết Hạn</h4>
                            <p class="mb-0">Vị trí này hiện không còn nhận hồ sơ ứng tuyển.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div><!-- /.lt_blog_item -->

</div><!-- /.blog_left_sidebar -->
