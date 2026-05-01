<?php
/**
 * blog.details.php — Trang chi tiết bài viết Blog
 * Biến nhận từ index.php:
 *   $blogDetail
 *   $blogCategories, $recentBlogs, $allTags
 */

$blogDetail     = $blogDetail     ?? null;
$blogCategories = $blogCategories ?? [];
$recentBlogs    = $recentBlogs    ?? [];
$allTags        = $allTags        ?? [];
$hiringPositions = $hiringPositions ?? [];

if (!$blogDetail) {
    echo '<p class="text-danger">Không tìm thấy bài viết.</p>';
    return;
}

$dateStr = date('F d, Y', strtotime($blogDetail['created_at']));
$imgSrc  = !empty($blogDetail['image']) ? $blogDetail['image'] : 'assets/images/blogs/default.jpg';

// Tags
$tagLinks = [];
if (!empty($blogDetail['tags'])) {
    foreach ($blogDetail['tags'] as $t) {
        $tagLinks[] = '<a href="?page=blogs&tag=' . urlencode($t['slug']) . '" class="tag-link">'
                      . htmlspecialchars($t['name']) . '</a>';
    }
}
$tagsStr = implode(' , ', $tagLinks);

// Kiểm tra blog tuyển dụng (category_id = 7)
$isHiring = ($blogDetail['category_id'] == 7);
$hiringOpen = false;
$daysRemaining = null;
$hiringClosed = false;

if ($isHiring) {
    // Kiểm tra trạng thái tuyển dụng
    $hiringStatus = $blogDetail['hiring_status'] ?? 1;
    $expiresInDays = $blogDetail['expires_in_days'] ?? null;
    $createdAt = $blogDetail['created_at'] ?? null;
    
    $isExpired = false;
    if (!empty($expiresInDays) && !empty($createdAt)) {
        $expiresAt = strtotime($createdAt . ' + ' . $expiresInDays . ' days');
        $isExpired = time() > $expiresAt;
        $daysRemaining = max(0, ceil(($expiresAt - time()) / 86400));
    }
    
    $hiringClosed = $isExpired || empty($hiringStatus) || $hiringStatus != 1;
    $hiringOpen = !$hiringClosed;
}

// Lấy message từ session (nếu submit form thành công/lỗi)
$applicationMessage = $_SESSION['job_application_message'] ?? null;
$applicationSuccess = $_SESSION['job_application_success'] ?? null;
unset($_SESSION['job_application_message'], $_SESSION['job_application_success']);
?>

<section class="blog_details_area sec_gap">
    <div class="container">
        <div class="row main_blog_inner">

            <!-- ================================================
                 LEFT: Nội dung chi tiết bài viết (col-lg-9)
                 ================================================ -->
            <div class="col-lg-9">
                <div class="blog_left_sidebar">

                    <!-- Blog Post Item -->
                    <div class="lt_blog_item blog_post_item">

                        <!-- Thumbnail + date badge đè lên ảnh -->
                        <a href="#" class="blog_img">
                            <img src="<?php echo htmlspecialchars($imgSrc); ?>" 
                                 alt="<?php echo htmlspecialchars($blogDetail['title']); ?>">
                            <span class="blog-date-badge"><?php echo $dateStr; ?></span>
                        </a>

                        <!-- Post Info: Author + Tags (bên phải, dưới ảnh) -->
                        <div class="post_info">
                            <div class="blog_author_area">
                                By : <a href="#" title="Posts by <?php echo htmlspecialchars($blogDetail['author']); ?>" rel="author">
                                    <?php echo htmlspecialchars($blogDetail['author']); ?>
                                </a>
                                <?php if ($tagsStr): ?>
                                    <span class="sep">/</span>
                                    <?php echo $tagsStr; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Đường kẻ ngang phân cách sau meta -->
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

                                <h3 class="f_600 title_color">Key Insights &amp; Industry Perspective</h3>
                                <p>Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est. Temporibus autem quibusdam et aut officiis debitis rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint molestiae non recusandae.</p>
                            </div>
                            <div class="s_main_text">
                                <p>Here is main text quis nostrud exercitation ullamco laboris nisi here is <em>italic text</em> ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla rure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat <a href="#">here is link</a> cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                            </div>
                        <?php endif; ?>

                        <!-- Job Application Form - Chỉ hiển thị cho blog tuyển dụng -->
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
                                        
                                        <form method="POST" action="?page=job-application-submit" enctype="multipart/form-data" class="job-application-form">
                                            <input type="hidden" name="blog_id" value="<?php echo $blogDetail['id']; ?>">
                                            <input type="hidden" name="return_slug" value="<?php echo htmlspecialchars($blogDetail['slug']); ?>">
                                            
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label for="full_name">Họ và Tên <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="full_name" name="full_name" required 
                                                           placeholder="Nhập họ và tên" minlength="2">
                                                </div>
                                                
                                                <div class="col-md-6 form-group">
                                                    <label for="email">Email <span class="text-danger">*</span></label>
                                                    <input type="email" class="form-control" id="email" name="email" required 
                                                           placeholder="Nhập email">
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label for="phone">Số Điện Thoại <span class="text-danger">*</span></label>
                                                    <input type="tel" class="form-control" id="phone" name="phone" required 
                                                           placeholder="Nhập số điện thoại">
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
                                                <button type="submit" class="btn-submit">
                                                    Gửi CV Ứng Tuyển
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <div class="hiring-closed-notice text-center py-4">
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-circle fa-2x mb-2 d-block"></i>
                                            <h4 class="f_600">Tin Tuyển Dụng Đã Hết Hạn</h4>
                                            <p class="mb-0">Vị trí này hiện không còn nhận hồ sơ ứng tuyển.</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                    </div><!-- /.lt_blog_item -->

                </div><!-- /.blog_left_sidebar -->
            </div><!-- /.col-lg-9 -->

            <!-- ================================================
                 RIGHT: Sidebar (col-lg-3) - Giống trang blogs
                 ================================================ -->
            <div class="col-lg-3">
                <div class="blog_sidebar_area">

                    <!-- Search -->
                    <aside class="r_widget search_widget">
                        <form method="get" action="">
                            <input type="hidden" name="page" value="blogs">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search"
                                       placeholder="Enter Search Keywords">
                                <span class="input-group-btn">
                                    <button class="btn" type="submit">
                                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="11" cy="11" r="7"/>
                                            <line x1="16.5" y1="16.5" x2="22" y2="22"/>
                                        </svg>
                                    </button>
                                </span>
                            </div>
                        </form>
                    </aside>

                    <?php
                    // Lấy tag slugs của bài viết hiện tại để highlight trong sidebar
                    $currentTagSlugs = [];
                    if (!empty($blogDetail['tags'])) {
                        foreach ($blogDetail['tags'] as $t) {
                            $currentTagSlugs[] = $t['slug'];
                        }
                    }
                    ?>

                    <!-- Categories -->
                    <?php if (!empty($blogCategories)): ?>
                        <aside class="r_widget widget_categories">
                            <div class="r_widget_title">
                                <h3 class="f_600 title_color">Categories</h3>
                                <span class="title_br"></span>
                            </div>
                            <ul>
                                <li><a href="?page=blogs">All Categories</a></li>
                                <?php foreach ($blogCategories as $cat): ?>
                                    <li class="<?php echo (int)$blogDetail['category_id'] === (int)$cat['id'] ? 'active' : ''; ?>">
                                        <a href="?page=blogs&cat=<?php echo $cat['id']; ?>">
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </aside>
                    <?php endif; ?>

                    <!-- Recent News -->
                    <?php if (!empty($recentBlogs)): ?>
                        <aside class="r_widget widget_news">
                            <div class="r_widget_title">
                                <h3 class="f_600 title_color">Recent News</h3>
                                <span class="title_br"></span>
                            </div>
                            <div class="recent_inner">
                                <?php foreach ($recentBlogs as $recent): ?>
                                    <?php
                                    $rImg  = !empty($recent['image']) ? $recent['image'] : 'assets/images/blogs/default.jpg';
                                    $rDate = date('F d, Y', strtotime($recent['created_at']));
                                    ?>
                                    <div class="media recent_item">
                                        <img src="<?php echo htmlspecialchars($rImg); ?>"
                                             alt="<?php echo htmlspecialchars($recent['title']); ?>">
                                        <div class="media-body">
                                            <a href="?page=blog-details&slug=<?php echo urlencode($recent['slug']); ?>">
                                                <h4><?php echo htmlspecialchars($recent['title']); ?></h4>
                                            </a>
                                            <h5><?php echo $rDate; ?></h5>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </aside>
                    <?php endif; ?>

                    <!-- Tags -->
                    <?php if (!empty($allTags)): ?>
                        <aside class="r_widget widget_tag_cloud">
                            <div class="r_widget_title">
                                <h3 class="f_600 title_color">Tags</h3>
                                <span class="title_br"></span>
                            </div>
                            <div class="tagcloud">
                                <ul class="wp-tag-cloud" role="list">
                                    <?php foreach ($allTags as $tag): ?>
                                        <li>
                                            <a href="?page=blogs&tag=<?php echo urlencode($tag['slug']); ?>"
                                               class="<?php echo in_array($tag['slug'], $currentTagSlugs) ? 'active' : ''; ?>"
                                               aria-label="<?php echo htmlspecialchars($tag['name']); ?> (<?php echo $tag['post_count']; ?> items)">
                                                <?php echo htmlspecialchars($tag['name']); ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </aside>
                    <?php endif; ?>

                </div><!-- /.blog_sidebar_area -->
            </div><!-- /.col-lg-3 -->

        </div><!-- /.row -->
    </div><!-- /.container -->
</section>
