<?php
/**
 * blogs.php — Trang danh sách bài viết Blog
 * Biến nhận từ index.php:
 *   $blogs, $totalBlogs, $currentPage, $perPage
 *   $blogCategories, $recentBlogs, $allTags
 *   $filterCatId, $filterTag, $searchQuery
 */

$blogs          = $blogs          ?? [];
$totalBlogs     = $totalBlogs     ?? 0;
$currentPage    = $currentPage    ?? 1;
$perPage        = $perPage        ?? 6;
$blogCategories = $blogCategories ?? [];
$recentBlogs    = $recentBlogs    ?? [];
$allTags        = $allTags        ?? [];
$filterCatId    = $filterCatId    ?? 0;
$filterTag      = $filterTag      ?? '';
$searchQuery    = $searchQuery    ?? '';

$totalPages = $perPage > 0 ? (int) ceil($totalBlogs / $perPage) : 1;

function blogs_page_url($p, $catId, $tagSlug, $search) {
    $params = ['page' => 'blogs', 'p' => $p];
    if ($catId)   $params['cat']    = $catId;
    if ($tagSlug) $params['tag']    = $tagSlug;
    if ($search)  $params['search'] = urlencode($search);
    return '?' . http_build_query($params);
}
?>

<section class="blog_area">
    <div class="container">
        <div class="row">

            <!-- ================================================
                 LEFT: Danh sách bài viết (col-lg-9)
                 ================================================ -->
            <div class="col-lg-9">
                <div class="blog_left_sidebar">

                    <?php if (empty($blogs)): ?>
                        <p class="text-muted">Không có bài viết nào.</p>
                    <?php else: ?>
                        <?php foreach ($blogs as $blog): ?>
                            <?php
                            $blogUrl = '?page=blog-details&slug=' . urlencode($blog['slug']);
                            $dateStr = date('F d, Y', strtotime($blog['created_at']));
                            $imgSrc  = !empty($blog['image']) ? $blog['image'] : 'assets/images/blogs/default.jpg';
                            $excerpt = !empty($blog['excerpt'])
                                       ? (mb_strlen($blog['excerpt']) > 220
                                           ? mb_substr($blog['excerpt'], 0, 220) . '...'
                                           : $blog['excerpt'])
                                       : '';

                            // Tags dạng text: "industry , manufacturing , treatment"
                            $tagLinks = [];
                            if (!empty($blog['tags'])) {
                                foreach ($blog['tags'] as $t) {
                                    $tagLinks[] = '<a href="?page=blogs&tag=' . urlencode($t['slug']) . '" class="tag-link">'
                                                  . htmlspecialchars($t['name']) . '</a>';
                                }
                            }
                            $tagsStr = implode(' , ', $tagLinks);
                            ?>

                            <div class="lt_blog_item">

                                <!-- Thumbnail + date badge đè lên ảnh -->
                                <a href="<?php echo $blogUrl; ?>" class="blog_img">
                                    <img src="<?php echo htmlspecialchars($imgSrc); ?>"
                                         alt="<?php echo htmlspecialchars($blog['title']); ?>">
                                    <span class="blog-date-badge"><?php echo $dateStr; ?></span>
                                </a>

                                <!-- Meta: By admin / tags -->
                                <div class="post_info">
                                    <div class="blog_author_area">
                                        By : <a href="#"><?php echo htmlspecialchars($blog['author']); ?></a>
                                        <?php if ($tagsStr): ?>
                                            <span class="sep">/</span>
                                            <?php echo $tagsStr; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Title + excerpt + Read More -->
                                <div class="blog-text">
                                    <a href="<?php echo $blogUrl; ?>">
                                        <h4 class="f_600 title_color">
                                            <?php echo htmlspecialchars($blog['title']); ?>
                                        </h4>
                                    </a>

                                    <?php if ($excerpt): ?>
                                        <p><?php echo htmlspecialchars($excerpt); ?></p>
                                    <?php endif; ?>

                                    <a href="<?php echo $blogUrl; ?>" class="read_btn_two">Read More</a>
                                </div>

                            </div><!-- /.lt_blog_item -->
                        <?php endforeach; ?>
                    <?php endif; ?>

                </div><!-- /.blog_left_sidebar -->

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="blog_pagination mt-4">
                        <nav aria-label="Blog pagination">
                            <ul class="pagination">
                                <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="<?php echo blogs_page_url($currentPage - 1, $filterCatId, $filterTag, $searchQuery); ?>">&laquo;</a>
                                </li>
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                                        <a class="page-link" href="<?php echo blogs_page_url($i, $filterCatId, $filterTag, $searchQuery); ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="<?php echo blogs_page_url($currentPage + 1, $filterCatId, $filterTag, $searchQuery); ?>">&raquo;</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>

            </div><!-- /.col-lg-9 -->

            <!-- ================================================
                 RIGHT: Sidebar (col-lg-3)
                 ================================================ -->
            <div class="col-lg-3">
                <div class="blog_sidebar_area">

                    <!-- Search -->
                    <aside class="r_widget search_widget">
                        <form method="get" action="">
                            <input type="hidden" name="page" value="blogs">
                            <?php if ($filterCatId): ?>
                                <input type="hidden" name="cat" value="<?php echo $filterCatId; ?>">
                            <?php endif; ?>
                            <div class="input-group">
                                <input type="text" class="form-control" name="search"
                                       value="<?php echo htmlspecialchars($searchQuery); ?>"
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
                                <!-- All Categories — luôn hiển thị để back về tổng hợp -->
                                <li class="<?php echo $filterCatId === 0 && empty($filterTag) ? 'active' : ''; ?>">
                                    <a href="?page=blogs">All Categories</a>
                                </li>
                                <?php foreach ($blogCategories as $cat): ?>
                                    <li class="<?php echo (int)$filterCatId === (int)$cat['id'] ? 'active' : ''; ?>">
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
                                               class="<?php echo $filterTag === $tag['slug'] ? 'active' : ''; ?>"
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
