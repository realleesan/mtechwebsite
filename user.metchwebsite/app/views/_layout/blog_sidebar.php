<?php
/**
 * blog_sidebar.php — Sidebar dùng chung cho blogs, blog-details, search
 *
 * Biến nhận từ controller:
 *   $page            — string — tên trang hiện tại ('blogs','blog-details','search')
 *   $blogCategories  — array  — danh sách categories (blogs/blog-details)
 *   $recentBlogs     — array  — bài viết gần đây
 *   $allTags         — array  — tất cả tags
 *   $filterCatId     — int    — category đang lọc
 *   $filterTag       — string — tag slug đang lọc
 *   $searchQuery     — string — từ khóa tìm kiếm
 *   $searchType      — string — loại filter ('blog','service','project','')
 *   $blogDetail      — array  — bài viết hiện tại (blog-details)
 */

/**
 * Render hierarchical category tree recursively
 * @param array $categories Current level categories
 * @param array $allCategories All categories (for finding children)
 * @param int $activeCatId Active category ID
 * @param int $depth Current depth level
 * @return string HTML markup
 */
function renderCategoryTree($categories, $allCategories, $activeCatId, $depth = 0) {
    $html = '';
    
    foreach ($categories as $cat) {
        $catId = (int) $cat['id'];
        $isActive = ($activeCatId === $catId);
        
        // Find children of this category
        $children = array_filter($allCategories, fn($c) => (int) ($c['parent_id'] ?? 0) === $catId);
        $hasChildren = !empty($children);
        
        // Category item class
        $itemClass = 'category-item category-depth-' . $depth;
        if ($hasChildren) {
            $itemClass .= ' category-has-children';
        }
        if ($isActive) {
            $itemClass .= ' active';
        }
        
        $html .= '<li class="' . $itemClass . '" data-cat-id="' . $catId . '" data-parent-id="' . ($cat['parent_id'] ?? 0) . '">';
        
        // Category wrapper with toggle button
        if ($hasChildren) {
            $html .= '<div class="category-header">';
            $html .= '<button class="category-toggle" type="button" aria-expanded="false" data-cat-id="' . $catId . '">';
            $html .= '<span class="toggle-icon">+</span>';
            $html .= '</button>';
            $html .= '<a href="/tin-tuc-' . urlencode($cat['slug']) . '" class="category-link">';
            $html .= '<span class="cat-name">' . htmlspecialchars($cat['name']) . '</span>';
            $html .= '</a>';
            $html .= '</div>';
        } else {
            $html .= '<a href="/tin-tuc-' . urlencode($cat['slug']) . '" class="category-link">';
            $html .= '<span class="cat-name">' . htmlspecialchars($cat['name']) . '</span>';
            $html .= '</a>';
        }
        
        // Render children (hidden by default)
        if ($hasChildren) {
            $html .= '<ul class="category-children category-children--hidden" data-parent-id="' . $catId . '">';
            $html .= renderCategoryTree($children, $allCategories, $activeCatId, $depth + 1);
            $html .= '</ul>';
        }
        
        $html .= '</li>';
    }
    
    return $html;
}

$blogCategories = $blogCategories ?? [];
$recentBlogs    = $recentBlogs    ?? [];
$allTags        = $allTags        ?? [];
$filterCatId    = $filterCatId    ?? 0;
$filterTag      = $filterTag      ?? '';
$searchQuery    = $searchQuery    ?? '';
$searchType     = $searchType     ?? '';
$currentPage    = $page           ?? '';   // dùng $page từ controller

// Xác định category và tags đang active
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

$isSearchPage = ($currentPage === 'search');
?>

<div class="blog_sidebar_area">

    <?php if ($isSearchPage): ?>
    <!-- ── Filter By Type (chỉ hiển thị trên trang search) ─── -->
    <aside class="r_widget widget_categories">
        <div class="r_widget_title">
            <h3 class="f_600 title_color">Bộ lọc theo loại</h3>
            <span class="title_br"></span>
        </div>
        <ul>
            <?php
            // typeKey => [label, url suffix]
            $typeOptions = [
                ''        => ['label' => 'Tất cả',  'suffix' => ''],
                'blog'    => ['label' => 'Tin tức',  'suffix' => '-tin-tuc'],
                'project' => ['label' => 'Dự án',    'suffix' => '-du-an'],
                'service' => ['label' => 'Dịch vụ',  'suffix' => '-dich-vu'],
            ];
            // Slug không dấu của từ khóa tìm kiếm
            $searchSlug = !empty($searchQuery) ? slugify($searchQuery) : '';
            ?>
            <?php foreach ($typeOptions as $typeKey => $typeOpt): ?>
                <li class="<?php echo $searchType === $typeKey ? 'active' : ''; ?>">
                    <?php
                    if ($searchSlug) {
                        $filterUrl = '/ket-qua-tim-kiem-' . $searchSlug . $typeOpt['suffix'];
                    } else {
                        $filterUrl = '/ket-qua-tim-kiem';
                    }
                    ?>
                    <a href="<?php echo htmlspecialchars($filterUrl); ?>">
                        <?php echo $typeOpt['label']; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </aside>

    <?php else: ?>
    <!-- ── Categories (chỉ hiển thị trên trang blogs/blog-details) ─── -->
    <?php if (!empty($blogCategories)): ?>
        <aside class="r_widget widget_categories">
            <div class="r_widget_title">
                <h3 class="f_600 title_color">Danh mục</h3>
                <span class="title_br"></span>
            </div>
            <ul class="blog-categories-hierarchical" id="blog-categories-list">
                <?php $allActive = ($activeCatId === 0 && empty($filterTag)); ?>
                <li class="category-item category-all <?php echo $allActive ? 'active' : ''; ?>">
                    <a href="/tin-tuc" class="category-link">Tất cả danh mục</a>
                </li>
                
                <!-- Render hierarchical categories -->
                <?php 
                // Build root categories (parent_id = null or 0)
                $rootCategories = array_filter($blogCategories, fn($cat) => empty($cat['parent_id']));
                echo renderCategoryTree($rootCategories, $blogCategories, $activeCatId);
                ?>
            </ul>
        </aside>
    <?php endif; ?>
    <?php endif; ?>

    <!-- ── Recent News ────────────────────────────────────── -->
    <?php if (!empty($recentBlogs)): ?>
        <aside class="r_widget widget_news">
            <div class="r_widget_title">
                <h3 class="f_600 title_color">Tin gần đây</h3>
                <span class="title_br"></span>
            </div>
            <div class="recent_inner">
                <?php foreach ($recentBlogs as $recent): ?>
                    <?php
                    $rImg  = !empty($recent['image']) ? $recent['image'] : 'assets/images/blogs/default.jpg';
                    $rDate = format_date_vietnamese(date('d F, Y', strtotime($recent['created_at'])));
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

    <!-- ── Tags (chỉ hiển thị trên trang blogs/blog-details) ─── -->
    <?php if (!$isSearchPage && !empty($allTags)): ?>
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
                               aria-label="<?php echo htmlspecialchars($tag['name']); ?>">
                                <?php echo htmlspecialchars($tag['name']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </aside>
    <?php endif; ?>

</div><!-- /.blog_sidebar_area -->


