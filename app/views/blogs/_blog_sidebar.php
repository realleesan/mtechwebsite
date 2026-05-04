<?php
$blogCategories = $blogCategories ?? [];
$recentBlogs    = $recentBlogs    ?? [];
$allTags        = $allTags        ?? [];
$filterCatId    = $filterCatId    ?? 0;
$filterTag      = $filterTag      ?? '';
$searchQuery    = $searchQuery    ?? '';
$activeCatId    = $activeCatId    ?? 0;
$activeTagSlugs = $activeTagSlugs ?? [];
?>

<div class="blog_sidebar_area">

    <!-- ── Search ─────────────────────────────────────────── -->
    <aside class="r_widget search_widget">
        <form method="get" action="">
            <input type="hidden" name="page" value="blogs">
            <?php if ($filterCatId): ?>
                <input type="hidden" name="cat" value="<?php echo (int)$filterCatId; ?>">
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

    <!-- ── Categories ─────────────────────────────────────── -->
    <?php if (!empty($blogCategories)): ?>
        <aside class="r_widget widget_categories">
            <div class="r_widget_title">
                <h3 class="f_600 title_color">Categories</h3>
                <span class="title_br"></span>
            </div>
            <ul>
                <?php
                // "All Categories" active khi không lọc gì cả
                $allActive = ($filterCatId === 0 && empty($filterTag) && $activeCatId === 0);
                ?>
                <li class="<?php echo $allActive ? 'active' : ''; ?>">
                    <a href="?page=blogs">All Categories</a>
                </li>
                <?php foreach ($blogCategories as $cat): ?>
                    <?php
                    $isActive = ((int)$filterCatId === (int)$cat['id'])
                             || ((int)$activeCatId  === (int)$cat['id']);
                    ?>
                    <li class="<?php echo $isActive ? 'active' : ''; ?>">
                        <a href="?page=blogs&cat=<?php echo (int)$cat['id']; ?>">
                            <?php echo htmlspecialchars($cat['name']); ?>
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
                <h3 class="f_600 title_color">Recent News</h3>
                <span class="title_br"></span>
            </div>
            <div class="recent_inner">
                <?php foreach ($recentBlogs as $recent): ?>
                    <?php
                    $rImg  = !empty($recent['image']) ? $recent['image'] : 'assets/images/blogs/default.jpg';
                    $rDate = date('F d, Y', strtotime($recent['created_at']));
                    // URL cho tuyển dụng (cat=7) dùng /tuyen-dung-{slug}
                    $isHiringRecent = ($recent['category_id'] == 7);
                    $recentUrl = $isHiringRecent
                        ? '/chi-tiet-' . urlencode($recent['slug'])
                        : '?page=blog-details&slug=' . urlencode($recent['slug']);
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
                <h3 class="f_600 title_color">Tags</h3>
                <span class="title_br"></span>
            </div>
            <div class="tagcloud">
                <ul class="wp-tag-cloud" role="list">
                    <?php foreach ($allTags as $tag): ?>
                        <?php
                        $tagActive = ($filterTag === $tag['slug'])
                                  || in_array($tag['slug'], $activeTagSlugs);
                        ?>
                        <li>
                            <a href="?page=blogs&tag=<?php echo urlencode($tag['slug']); ?>"
                               class="<?php echo $tagActive ? 'active' : ''; ?>"
                               aria-label="<?php echo htmlspecialchars($tag['name']); ?> (<?php echo (int)$tag['post_count']; ?> items)">
                                <?php echo htmlspecialchars($tag['name']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </aside>
    <?php endif; ?>

</div><!-- /.blog_sidebar_area -->
