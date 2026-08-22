<?php
/**
 * awards.php — Trang Chứng chỉ năng lực
 * Biến nhận:
 *   $capacityFields (array) — bảng lĩnh vực hoạt động
 */

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
