<?php
/**
 * search.php — Trang kết quả tìm kiếm toàn site
 * Biến nhận từ index.php:
 *   $searchResults, $totalResults, $currentPage, $perPage
 *   $searchQuery, $searchType
 */

$searchResults = $searchResults ?? [];
$totalResults  = $totalResults  ?? 0;
$currentPage   = (int) ($currentPage ?? 1);
$perPage       = $perPage       ?? 10;
$searchQuery   = $searchQuery   ?? '';
$searchType    = $searchType    ?? '';

$totalPages = $perPage > 0 ? (int) ceil($totalResults / $perPage) : 1;

function search_page_url($p, $q, $type) {
    $params = ['page' => 'search', 'q' => $q, 'p' => $p];
    if ($type) $params['type'] = $type;
    return '?' . http_build_query($params);
}

$typeLabels = [
    ''        => 'All',
    'blog'    => 'Blog',
    'service' => 'Service',
    'project' => 'Project',
];
?>

<div class="blog_left_sidebar">

    <?php if (empty($searchQuery)): ?>
        <p class="text-muted">Vui lòng nhập từ khóa để tìm kiếm.</p>

    <?php elseif (empty($searchResults)): ?>
        <p class="text-muted">Không tìm thấy kết quả nào cho "<strong><?php echo htmlspecialchars($searchQuery); ?></strong>".</p>

    <?php else: ?>
        <p class="search-result-count text-muted mb-4">
            Tìm thấy <strong><?php echo $totalResults; ?></strong> kết quả cho "<strong><?php echo htmlspecialchars($searchQuery); ?></strong>"
            <?php if ($searchType && isset($typeLabels[$searchType])): ?>
                trong <strong><?php echo $typeLabels[$searchType]; ?></strong>
            <?php endif; ?>
        </p>

        <?php foreach ($searchResults as $item): ?>
            <?php
            $detailUrl = SearchModel::getDetailUrl($item['type'], $item['slug']);
            $typeLabel = SearchModel::getTypeLabel($item['type']);
            $dateStr   = !empty($item['created_at']) ? format_date_vietnamese(date('d F, Y', strtotime($item['created_at']))) : '';
            $imgSrc    = !empty($item['image']) ? $item['image'] : 'assets/images/blogs/default.jpg';
            $excerpt   = !empty($item['excerpt'])
                         ? (mb_strlen($item['excerpt']) > 220
                             ? mb_substr($item['excerpt'], 0, 220) . '...'
                             : $item['excerpt'])
                         : '';
            ?>

            <div class="lt_blog_item">

                <!-- Thumbnail + date badge -->
                <a href="<?php echo $detailUrl; ?>" class="blog_img" style="position:relative;">
                    <img src="<?php echo htmlspecialchars($imgSrc); ?>"
                         alt="<?php echo htmlspecialchars($item['title']); ?>">
                    <?php if ($dateStr): ?>
                        <span class="blog-date-badge"><?php echo $dateStr; ?></span>
                    <?php endif; ?>
                </a>

                <!-- Meta -->
                <div class="post_info" style="text-align:left;">
                    <div class="blog_author_area">
                        <span class="search-type-label"><?php echo $typeLabel; ?></span>
                    </div>
                </div>

                <!-- Title + excerpt + Read More -->
                <div class="blog-text">
                    <a href="<?php echo $detailUrl; ?>">
                        <h4 class="f_600 title_color">
                            <?php echo htmlspecialchars($item['title']); ?>
                        </h4>
                    </a>

                    <?php if ($excerpt): ?>
                        <p><?php echo htmlspecialchars($excerpt); ?></p>
                    <?php endif; ?>

                    <a href="<?php echo $detailUrl; ?>" class="read_btn_two">Đọc thêm</a>
                </div>

            </div><!-- /.lt_blog_item -->
        <?php endforeach; ?>
    <?php endif; ?>

</div><!-- /.blog_left_sidebar -->

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
    <div class="blog_pagination mt-4">
        <nav aria-label="Search pagination">
            <ul class="pagination">
                <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="<?php echo search_page_url($currentPage - 1, $searchQuery, $searchType); ?>">&laquo;</a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                        <a class="page-link" href="<?php echo search_page_url($i, $searchQuery, $searchType); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="<?php echo search_page_url($currentPage + 1, $searchQuery, $searchType); ?>">&raquo;</a>
                </li>
            </ul>
        </nav>
    </div>
<?php endif; ?>

<style>
.search-type-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    padding: 3px 10px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    color: #fff;
    letter-spacing: 0.5px;
}
.search-type-blog    { background: #5a8dee; }
.search-type-service { background: #f4a22d; }
.search-type-project { background: #28c76f; }
.search-result-count { font-size: 14px; }
</style>
