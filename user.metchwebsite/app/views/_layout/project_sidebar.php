<?php
/**
 * project_sidebar.php — Layout Sidebar bộ lọc cho trang Dự án
 * Đồng bộ với phong cách widget của website (r_widget)
 *
 * Biến nhận từ ProjectsController:
 *   $categoryTree        — array  — Cây danh mục lĩnh vực phân cấp cha-con
 *   $selectedCatIds      — array  — Danh sách ID lĩnh vực đang được chọn để lọc
 *   $totalProjects       — int    — Tổng số lượng dự án tìm thấy
 */

$categoryTree = $categoryTree ?? [];
$selectedCatIds = $selectedCatIds ?? [];
$hasActiveFilter = !empty($selectedCatIds);

/**
 * Kiểm tra xem nhánh cây danh mục có chứa bất kỳ danh mục con nào đang được check hay không
 */
if (!function_exists('hasCheckedDescendant')) {
    function hasCheckedDescendant(array $category, array $selectedCatIds): bool {
        if (empty($category['children'])) {
            return false;
        }
        foreach ($category['children'] as $child) {
            if (in_array((int)$child['id'], $selectedCatIds)) {
                return true;
            }
            if (hasCheckedDescendant($child, $selectedCatIds)) {
                return true;
            }
        }
        return false;
    }
}

/**
 * Render đệ quy cây danh mục lĩnh vực cha - con
 */
if (!function_exists('renderProjectCategoryTree')) {
    function renderProjectCategoryTree(array $categories, array $selectedCatIds = [], int $depth = 0): string {
        if (empty($categories)) {
            return '';
        }

        $html = '';
        foreach ($categories as $cat) {
            $catId = (int)$cat['id'];
            $parentId = empty($cat['parent_id']) ? 0 : (int)$cat['parent_id'];
            $hasChildren = !empty($cat['children']);
            $isChecked = in_array($catId, $selectedCatIds);
            
            // Tự động mở rộng nếu chính nó hoặc con cháu nó được chọn
            $isExpanded = $isChecked || hasCheckedDescendant($cat, $selectedCatIds);

            $itemClasses = ['project-cat-item', 'depth-' . $depth];
            if ($hasChildren) {
                $itemClasses[] = 'has-children';
                if ($isExpanded) {
                    $itemClasses[] = 'is-expanded';
                }
            }
            if ($isChecked) {
                $itemClasses[] = 'is-checked';
            }

            $count = (int)($cat['project_count'] ?? 0);
            $badgeHtml = $count > 0 ? '<span class="cat-count">(' . $count . ')</span>' : '';

            $html .= '<li class="' . implode(' ', $itemClasses) . '" data-id="' . $catId . '" data-parent-id="' . $parentId . '">';
            $html .= '<div class="project-cat-row">';

            // 1. Nút Chevron đóng/mở danh mục con
            if ($hasChildren) {
                $html .= '<button type="button" class="btn-tree-toggle" aria-expanded="' . ($isExpanded ? 'true' : 'false') . '" title="Ẩn/Hiện danh mục con">';
                $html .= '<i class="fa ' . ($isExpanded ? 'fa-chevron-down' : 'fa-chevron-right') . '"></i>';
                $html .= '</button>';
            } else {
                $html .= '<span class="tree-toggle-spacer"></span>';
            }

            // 2. Custom Checkbox ô vuông
            $checkboxId = 'p_cat_' . $catId;
            $html .= '<label class="cat-checkbox-label" for="' . $checkboxId . '">';
            $html .= '<input type="checkbox" id="' . $checkboxId . '" name="categories[]" class="project-filter-checkbox" value="' . $catId . '" data-id="' . $catId . '" data-parent-id="' . $parentId . '" ' . ($isChecked ? 'checked' : '') . '>';
            $html .= '<span class="square-box"></span>';
            $html .= '<span class="cat-label-name">' . htmlspecialchars($cat['name']) . '</span>';
            $html .= '</label>';

            // 3. Số lượng dự án
            $html .= $badgeHtml;

            $html .= '</div>'; // End .project-cat-row

            // 4. Danh mục con đệ quy
            if ($hasChildren) {
                $childrenStyle = $isExpanded ? 'display: block;' : 'display: none;';
                $html .= '<ul class="project-cat-children" style="' . $childrenStyle . '">';
                $html .= renderProjectCategoryTree($cat['children'], $selectedCatIds, $depth + 1);
                $html .= '</ul>';
            }

            $html .= '</li>';
        }

        return $html;
    }
}
?>

<div class="project_sidebar_area">
    <aside class="r_widget widget_project_filter">
        
        <!-- Widget Header theo chuẩn của website -->
        <div class="r_widget_title">
            <h3 class="f_600 title_color">Lĩnh vực</h3>
            <span class="title_br"></span>
        </div>

        <!-- Form lọc -->
        <form id="project-filter-form" method="GET" action="/du-an">
            
            <?php if (!empty($categoryTree)): ?>
                <div class="project_categories_wrapper">
                    <ul class="project-categories-list" id="project-categories-tree">
                        <?php echo renderProjectCategoryTree($categoryTree, $selectedCatIds, 0); ?>
                    </ul>
                </div>
            <?php else: ?>
                <p class="text-muted text-center py-3">Chưa có danh mục lĩnh vực.</p>
            <?php endif; ?>

            <!-- Action Buttons: Chỉ lọc khi bấm nút -->
            <div class="filter_actions_box">
                <button type="submit" class="btn_filter_apply" id="btn-apply-project-filter">
                    <i class="fa fa-filter"></i> Lọc lĩnh vực
                </button>

                <?php if ($hasActiveFilter): ?>
                    <a href="/du-an" class="btn_filter_reset" id="btn-reset-project-filter">
                        <i class="fa fa-refresh"></i> Xóa bộ lọc
                    </a>
                <?php endif; ?>
            </div>

        </form>

    </aside>
</div>
