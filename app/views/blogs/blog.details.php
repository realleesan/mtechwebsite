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
                                <?php echo $blogDetail['content']; ?>
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
