<?php
/**
 * clients.php — Trang danh sách khách hàng
 * Grid 3×3 với phân trang JS
 */

$clientLogos    = $clientLogos ?? [];
$perPage        = 9;
$totalClients   = count($clientLogos);
$totalPages     = $perPage > 0 ? (int) ceil($totalClients / $perPage) : 1;
?>

<section class="clients_page_area">
    <div class="container">

        <!-- Section Title -->
        <div class="section_title mb_55">
            <h2 class="f_600 f_size_30 title_color">Danh sách khách hàng</h2>
            <span class="title_br"></span>
            <p class="mt_7">Những đối tác và khách hàng đã tin tưởng đồng hành cùng MTECH.JSC trên hành trình phát triển.</p>
        </div>

        <?php if (empty($clientLogos)): ?>
            <p class="text-muted" style="text-align:center; padding: 60px 0;">Chưa có dữ liệu khách hàng.</p>
        <?php else: ?>

            <!-- Đếm tổng số -->
            <p class="cp_total_count">
                Hiển thị <strong><?php echo min($perPage, $totalClients); ?></strong> / <strong><?php echo $totalClients; ?></strong> khách hàng
            </p>

            <!-- Grid 3×3 -->
            <div class="clients_page_grid" id="clientsPageGrid">
                <?php foreach ($clientLogos as $i => $client): ?>
                    <div class="cp_item" data-index="<?php echo $i; ?>">
                        <a href="<?php echo htmlspecialchars($client['url'] ?? '#'); ?>"
                           <?php echo (!empty($client['url']) && $client['url'] !== '#') ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                            <div class="cp_img_wrap">
                                <img src="<?php echo htmlspecialchars($client['logo']); ?>"
                                     alt="<?php echo htmlspecialchars($client['name']); ?>"
                                     class="cp_img"
                                     loading="lazy">
                            </div>
                            <span class="cp_name"><?php echo htmlspecialchars($client['name']); ?></span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div><!-- /.clients_page_grid -->

            <!-- Pagination — luôn hiển thị nếu có data -->
            <div class="cp_pagination" id="cpPagination">
                <button class="cp_pag_btn cp_prev" id="cpPrev" aria-label="Trang trước" disabled>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"/>
                    </svg>
                </button>

                <div class="cp_pag_pages" id="cpPages">
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <button class="cp_pag_page <?php echo $p === 1 ? 'active' : ''; ?>"
                                data-page="<?php echo $p; ?>"><?php echo $p; ?></button>
                    <?php endfor; ?>
                </div>

                <button class="cp_pag_btn cp_next" id="cpNext" aria-label="Trang sau"
                        <?php echo $totalPages <= 1 ? 'disabled' : ''; ?>>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </button>
            </div>

        <?php endif; ?>

    </div>
</section>

<script>
(function () {
    var perPage     = <?php echo $perPage; ?>;
    var totalPages  = <?php echo $totalPages; ?>;
    var currentPage = 1;

    var grid     = document.getElementById('clientsPageGrid');
    var prevBtn  = document.getElementById('cpPrev');
    var nextBtn  = document.getElementById('cpNext');
    var pageBtns = document.querySelectorAll('.cp_pag_page');
    var countEl  = document.querySelector('.cp_total_count');

    if (!grid) return;

    var items = grid.querySelectorAll('.cp_item');

    function showPage(page) {
        currentPage = page;
        var start = (page - 1) * perPage;
        var end   = Math.min(start + perPage, items.length);

        items.forEach(function (item, i) {
            item.style.display = (i >= start && i < end) ? '' : 'none';
        });

        if (prevBtn) prevBtn.disabled = (page <= 1);
        if (nextBtn) nextBtn.disabled = (page >= totalPages);

        pageBtns.forEach(function (btn) {
            btn.classList.toggle('active', parseInt(btn.dataset.page) === page);
        });

        // Cập nhật số đếm
        if (countEl) {
            var showing = end - start;
            countEl.innerHTML = 'Hiển thị <strong>' + showing + '</strong> / <strong>' + items.length + '</strong> khách hàng';
        }
    }

    // Khởi tạo trang 1
    showPage(1);

    if (prevBtn) prevBtn.addEventListener('click', function () {
        if (currentPage > 1) showPage(currentPage - 1);
    });

    if (nextBtn) nextBtn.addEventListener('click', function () {
        if (currentPage < totalPages) showPage(currentPage + 1);
    });

    pageBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            showPage(parseInt(this.dataset.page));
            // Scroll về đầu section
            var section = document.querySelector('.clients_page_area');
            if (section) section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
})();
</script>
