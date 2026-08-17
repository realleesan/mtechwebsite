<?php
/**
 * awards.php — Trang Giải thưởng & Chứng chỉ năng lực
 * Biến nhận:
 *   $awards         (array) — ảnh giải thưởng carousel
 *   $capacityFields (array) — bảng lĩnh vực hoạt động
 */

$awards         = $awards         ?? [];
$capacityFields = $capacityFields ?? [];

// Helper: số thứ tự → La Mã
function toRomanNumeral(int $n): string {
    $map = [1=>'I',2=>'II',3=>'III',4=>'IV',5=>'V',6=>'VI',7=>'VII',8=>'VIII',9=>'IX',10=>'X'];
    return $map[$n] ?? (string)$n;
}
?>

<!-- ============================================================
     SECTION 1: BẢNG CHỨNG CHỈ NĂNG LỰC HOẠT ĐỘNG XÂY DỰNG
     ============================================================ -->
<?php if (!empty($capacityFields)): ?>
<section class="capacity_table_area sec_gap">
    <div class="container">

        <div class="section_title mb_55">
            <h2 class="f_600 f_size_32 title_color">Chứng chỉ năng lực hoạt động xây dựng</h2>
            <span class="title_br"></span>
            <p class="mt_7">Danh mục lĩnh vực hoạt động và hạng chứng chỉ được cấp phép bởi cơ quan có thẩm quyền.</p>
        </div>

        <div class="capacity_table_wrap">
            <table class="capacity_table">
                <thead>
                    <tr>
                        <th class="col_tt">TT</th>
                        <th class="col_name">Lĩnh vực hoạt động</th>
                        <th class="col_rank">Chứng chỉ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($capacityFields as $field): ?>

                    <!-- Hàng lĩnh vực cha -->
                    <tr class="capacity_row_parent">
                        <td class="col_tt">
                            <strong><?= toRomanNumeral((int)$field['sort_order']) ?></strong>
                        </td>
                        <td class="col_name" colspan="2">
                            <strong><?= htmlspecialchars($field['name']) ?></strong>
                        </td>
                    </tr>

                    <!-- Hàng lĩnh vực con -->
                    <?php if (!empty($field['items'])): ?>
                        <?php foreach ($field['items'] as $item): ?>
                        <tr class="capacity_row_child">
                            <td class="col_tt"></td>
                            <td class="col_name">
                                <span class="capacity_dash">–</span>
                                <?= htmlspecialchars($item['name']) ?>
                            </td>
                            <td class="col_rank">
                                <?php if (!empty($item['rank'])): ?>
                                    <?= htmlspecialchars($item['rank']) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</section>
<?php endif; ?>


<!-- ============================================================
     SECTION 2: CAROUSEL ẢNH GIẢI THƯỞNG / CHỨNG CHỈ
     ============================================================ -->
<?php if (!empty($awards)):
    $duplicated = array_merge($awards, $awards, $awards);
?>

<!-- Modal Lightbox cho Awards -->
<div class="awards_lightbox_overlay" id="awardsLightbox">
    <div class="awards_lightbox_box">
        <button class="awards_lightbox_close" id="awardsLightboxClose" aria-label="Close">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
        <div class="awards_lightbox_content">
            <img id="awardsLightboxImg" src="" alt="Award" class="awards_lightbox_img">
            <div class="awards_lightbox_info">
                <h3 id="awardsLightboxName" class="awards_lightbox_name"></h3>
                <p id="awardsLightboxCert" class="awards_lightbox_cert"></p>
            </div>
        </div>
    </div>
</div>

<section class="awards_area sec_gap">
    <div class="container">
        <div class="section_title mb_55">
            <h2 class="f_600 f_size_32 title_color">Giải thưởng & Chứng chỉ</h2>
            <span class="title_br"></span>
            <p class="mt_7">
                Những giải thưởng và chứng chỉ ghi nhận chất lượng, uy tín và năng lực của MTECH.JSC
                trong lĩnh vực tư vấn, công nghiệp và xây dựng.
            </p>
        </div>
    </div>

    <!-- Carousel full-width -->
    <div class="awards_carousel_wrapper">
        <div class="awards_carousel_track">
            <?php foreach ($duplicated as $award): ?>
                <div class="awards_slide">
                    <div class="awards_img_wrap awards_clickable"
                         data-image="<?= htmlspecialchars($award['image'] ?? '') ?>"
                         data-name="<?= htmlspecialchars($award['name']) ?>"
                         data-cert="<?= htmlspecialchars($award['certificate'] ?? '') ?>">
                        <?php if (!empty($award['image'])): ?>
                            <img src="<?= htmlspecialchars($award['image']) ?>"
                                 alt="<?= htmlspecialchars($award['name']) ?>"
                                 class="awards_img">
                        <?php else: ?>
                            <div class="awards_img_placeholder">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48"
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="8" r="3"/>
                                    <path d="M2 20c0-4 4-7 10-7s10 3 10 7"/>
                                    <path d="M17 3l1.5 1.5L21 2"/>
                                    <path d="M17 7l1.5-1.5L21 8"/>
                                </svg>
                                <span>Award Photo</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="awards_info">
                        <h4 class="awards_name"><?= htmlspecialchars($award['name']) ?></h4>
                        <?php if (!empty($award['certificate'])): ?>
                            <p class="awards_cert"><?= htmlspecialchars($award['certificate']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php endif; ?>
