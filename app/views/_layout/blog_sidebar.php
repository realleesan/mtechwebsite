<?php
/**
 * blog_sidebar.php — Sidebar dùng chung cho trang blogs và blog-details
 *
 * Biến cần có trong scope (truyền từ index.php qua controller):
 *   $blogCategories  — array  — danh sách categories
 *   $recentBlogs     — array  — bài viết gần đây
 *   $allTags         — array  — tất cả tags
 *   $filterCatId     — int    — category đang lọc (blogs page)
 *   $filterTag       — string — tag slug đang lọc (blogs page)
 *   $searchQuery     — string — từ khóa tìm kiếm (blogs page)
 *   $blogDetail      — array  — bài viết hiện tại (blog-details page, để highlight)
 */

$blogCategories = $blogCategories ?? [];
$recentBlogs    = $recentBlogs    ?? [];
$allTags        = $allTags        ?? [];
$filterCatId    = $filterCatId    ?? 0;
$filterTag      = $filterTag      ?? '';
$searchQuery    = $searchQuery    ?? '';

// Xác định category và tags đang active (hỗ trợ cả blogs list lẫn blog-details)
$activeCatId    = (int) ($filterCatId ?: ($blogDetail['category_id'] ?? 0));
$activeTagSlugs = [];
if (!empty($filterTag)) {
    $activeTagSlugs[] = $filterTag;
}
if (!empty($blogDetail['tags'])) {
    foreach ($blogDetail['tags'] as $t) {
        $activeTagSlugs[] = $t['slug'];
    }
}
$activeTagSlugs = array_unique($activeTagSlugs);
?>

<div class="blog_sidebar_area">

    <!-- ── Search ─────────────────────────────────────────── -->
    <aside class="r_widget search_widget">
        <form method="get" action="/tin-tuc" id="blogSidebarSearchForm">
            <?php if ($filterCatId): ?>
                <input type="hidden" name="cat" value="<?php echo (int) $filterCatId; ?>">
            <?php endif; ?>
            <div class="input-group">
                <input type="text" class="form-control" name="search" id="blogSidebarSearchInput"
                       value="<?php echo htmlspecialchars($searchQuery); ?>"
                       placeholder="Nhập từ khóa">
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

    <!-- ── Categories ─────────────────────────────────────── -->
    <?php if (!empty($blogCategories)): ?>
        <aside class="r_widget widget_categories">
            <div class="r_widget_title">
                <h3 class="f_600 title_color">Danh mục</h3>
                <span class="title_br"></span>
            </div>
            <ul>
                <?php
                // "All Categories" active khi không lọc gì cả
                $allActive = ($activeCatId === 0 && empty($filterTag));
                ?>
                <li class="<?php echo $allActive ? 'active' : ''; ?>">
                    <a href="/tin-tuc">Tất cả danh mục</a>
                </li>
                <?php foreach ($blogCategories as $cat): ?>
                    <li class="<?php echo $activeCatId === (int) $cat['id'] ? 'active' : ''; ?>">
                        <a href="/tin-tuc-<?php echo urlencode($cat['slug']); ?>">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>
    <?php endif; ?>

    <!-- ── Filter By Type (chỉ hiển thị trên trang search) ─── -->
    <?php if ($currentPage === 'search'): ?>
        <aside class="r_widget widget_categories">
            <div class="r_widget_title">
                <h3 class="f_600 title_color">Bộ lọc theo loại</h3>
                <span class="title_br"></span>
            </div>
            <ul>
                <?php
                $typeLabels = $typeLabels ?? [
                    ''        => 'Tất cả',
                    'blog'    => 'Tin tức',
                    'service' => 'Dịch vụ',
                    'project' => 'Dự án',
                ];
                $searchType = $searchType ?? '';
                $searchQuery = $searchQuery ?? '';
                ?>
                <?php foreach ($typeLabels as $typeKey => $typeText): ?>
                    <li class="<?php echo $searchType === $typeKey ? 'active' : ''; ?>">
                        <a href="?page=search&q=<?php echo urlencode($searchQuery); ?><?php echo $typeKey ? '&type=' . $typeKey : ''; ?>">
                            <?php echo $typeText; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>
    <?php endif; ?>

    <!-- ── Recent News ────────────────────────────────────── -->
    <?php if (!empty($recentBlogs)): ?>
        <aside class="r_widget widget_news">
            <div class="r_widget_title">
                <h3 class="f_600 title_color"> Tin gần đây</h3>
                <span class="title_br"></span>
            </div>
            <div class="recent_inner">
                <?php foreach ($recentBlogs as $recent): ?>
                    <?php
                    $rImg  = !empty($recent['image']) ? $recent['image'] : 'assets/images/blogs/default.jpg';
                    $rDate = format_date_vietnamese(date('d F, Y', strtotime($recent['created_at'])));
                    // URL cho tuyển dụng (cat=7) dùng /chi-tiet-{slug}, tin tức thường dùng /chi-tiet-tin-tuc-{slug}
                    $isHiringRecent = ($recent['category_id'] == 7);
                    $recentUrl = $isHiringRecent
                        ? '/chi-tiet-' . urlencode($recent['slug'])
                        : '/chi-tiet-tin-tuc-' . urlencode($recent['slug']);
                    ?>
                    <div class="media recent_item">
                        <img src="<?php echo htmlspecialchars($rImg); ?>"
                             alt="<?php echo htmlspecialchars($recent['title']); ?>">
                        <div class="media-body">
                            <a href="<?php echo htmlspecialchars($recentUrl); ?>">
                                <h4><?php echo htmlspecialchars($recent['title']); ?></h4>
                            </a>
                            <h5><?php echo $rDate; ?></h5>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </aside>
    <?php endif; ?>

    <!-- ── Tags ───────────────────────────────────────────── -->
    <?php if (!empty($allTags)): ?>
        <aside class="r_widget widget_tag_cloud">
            <div class="r_widget_title">
                <h3 class="f_600 title_color">Thẻ</h3>
                <span class="title_br"></span>
            </div>
            <div class="tagcloud">
                <ul class="wp-tag-cloud" role="list">
                    <?php foreach ($allTags as $tag): ?>
                        <li>
                            <a href="/tin-tuc-the-<?php echo urlencode($tag['slug']); ?>"
                               class="<?php echo in_array($tag['slug'], $activeTagSlugs) ? 'active' : ''; ?>"
                               aria-label="<?php echo htmlspecialchars($tag['name']); ?> (<?php echo (int) $tag['post_count']; ?> items)">
                                <?php echo htmlspecialchars($tag['name']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </aside>
    <?php endif; ?>

</div><!-- /.blog_sidebar_area -->

<script>
(function () {
    var form  = document.getElementById('blogSidebarSearchForm');
    var input = document.getElementById('blogSidebarSearchInput');
    if (form && input) {
        form.addEventListener('submit', function (e) {
            if (input.value.trim() === '') {
                e.preventDefault();
                input.focus();
            }
        });
    }
})();
</script>
