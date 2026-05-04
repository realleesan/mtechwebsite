<?php
/**
 * blogs.php — Nội dung chính trang danh sách Blog (col-lg-9)
 * Layout bên ngoài (section > container > row > col-9 + col-3 sidebar)
 * được master.php quản lý khi $showBlogSidebar = true.
 *
 * Biến nhận từ index.php:
 *   $blogs, $totalBlogs, $currentPage, $perPage
 *   $filterCatId, $filterTag, $searchQuery
 */

$blogs       = $blogs       ?? [];
$totalBlogs  = $totalBlogs  ?? 0;
$currentPage = (int) ($currentPage ?? 1);
$perPage     = $perPage     ?? 6;
$filterCatId = $filterCatId ?? 0;
$filterTag   = $filterTag   ?? '';
$searchQuery = $searchQuery ?? '';

$totalPages = $perPage > 0 ? (int) ceil($totalBlogs / $perPage) : 1;

function blogs_page_url($p, $catId, $tagSlug, $search) {
    $params = ['page' => 'blogs', 'p' => $p];
    if ($catId)   $params['cat']    = $catId;
    if ($tagSlug) $params['tag']    = $tagSlug;
    if ($search)  $params['search'] = urlencode($search);
    return '?' . http_build_query($params);
}
?>

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

            // Tags
            $tagLinks = [];
            if (!empty($blog['tags'])) {
                foreach ($blog['tags'] as $t) {
                    $tagLinks[] = '<a href="?page=blogs&tag=' . urlencode($t['slug']) . '" class="tag-link">'
                                  . htmlspecialchars($t['name']) . '</a>';
                }
            }
            $tagsStr = implode(' , ', $tagLinks);

            // Tính toán hết hạn cho blog tuyển dụng (cat=7)
            $isHiring      = ($blog['category_id'] == 7);
            $daysRemaining = null;
            $hiringClosed  = false;
            if ($isHiring && !empty($blog['expires_in_days'])) {
                $expiresAt     = strtotime($blog['created_at'] . ' + ' . $blog['expires_in_days'] . ' days');
                $isExpired     = time() > $expiresAt;
                $daysRemaining = max(0, (int) ceil(($expiresAt - time()) / 86400));
                $hiringClosed  = $isExpired || empty($blog['hiring_status']) || $blog['hiring_status'] != 1;
            }
            ?>

            <div class="lt_blog_item">

                <!-- Thumbnail + date badge -->
                <a href="<?php echo $blogUrl; ?>" class="blog_img">
                    <img src="<?php echo htmlspecialchars($imgSrc); ?>"
                         alt="<?php echo htmlspecialchars($blog['title']); ?>">
                    <span class="blog-date-badge"><?php echo $dateStr; ?></span>
                    <?php if ($isHiring): ?>
                        <span class="blog-expiry-badge <?php echo $hiringClosed ? 'expired' : 'active'; ?>">
                            <?php echo $hiringClosed ? 'Hết hạn' : 'Hết hạn sau ' . $daysRemaining . ' ngày'; ?>
                        </span>
                    <?php endif; ?>
                </a>

                <!-- Meta: By author / tags -->
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
