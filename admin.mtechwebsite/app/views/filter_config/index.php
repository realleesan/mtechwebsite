<?php
/**
 * Filter Config Helper Functions
 * Thực hiện prepare & render dữ liệu cho drag-drop interface
 */

function prepareFilterItems(array $items, array $config): array
{
    $configMap = [];
    foreach ($config as $cfg) {
        $configMap[(int)$cfg['item_id']] = $cfg;
    }

    foreach ($items as $index => &$item) {
        $itemId = (int)$item['id'];
        $item['_filter_sort_order'] = isset($configMap[$itemId]) ? (int)$configMap[$itemId]['sort_order'] : (int)($item['sort_order'] ?? $index);
        $item['is_enabled'] = isset($configMap[$itemId]) ? (int)$configMap[$itemId]['is_enabled'] : 1;
        $item['children'] = [];
    }
    unset($item);

    usort($items, function ($a, $b) {
        if ($a['_filter_sort_order'] === $b['_filter_sort_order']) {
            return (int)$a['id'] <=> (int)$b['id'];
        }
        return $a['_filter_sort_order'] <=> $b['_filter_sort_order'];
    });

    $byParent = [];
    foreach ($items as $item) {
        $parentKey = empty($item['parent_id']) ? 0 : (int)$item['parent_id'];
        $byParent[$parentKey][] = $item;
    }

    $buildTree = function ($parentId, $depth = 1) use (&$buildTree, &$byParent) {
        $branch = [];
        foreach ($byParent[(int)$parentId] ?? [] as $item) {
            $item['_filter_depth'] = $depth;
            $item['children'] = $buildTree((int)$item['id'], $depth + 1);
            $branch[] = $item;
        }
        return $branch;
    };

    return $buildTree(0);
}

function renderFilterRows(array $items): void
{
    foreach ($items as $item):
        $hasChildren = !empty($item['children']);
        $depth = (int)($item['_filter_depth'] ?? 1);
        $parentId = empty($item['parent_id']) ? '' : (int)$item['parent_id'];
?>
    <tr class="draggable-item <?= $depth > 1 ? 'is-child d-none' : '' ?>"
        draggable="false"
        data-id="<?= (int)$item['id'] ?>"
        data-parent-id="<?= $parentId ?>"
        data-depth="<?= $depth ?>"
        data-has-children="<?= $hasChildren ? 1 : 0 ?>"
        data-expanded="0">
        <td class="text-muted drag-handle">
            <i class="bi bi-grip-vertical fs-5"></i>
        </td>
        <td><strong>#<?= (int)$item['id'] ?></strong></td>
        <td>
            <div class="d-flex align-items-center" style="padding-left: <?= max(0, $depth - 1) * 24 ?>px;">
                <?php if ($hasChildren): ?>
                    <button type="button" class="btn btn-sm btn-link text-secondary p-0 me-2 filter-tree-toggle" aria-label="Toggle children">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                <?php else: ?>
                    <span class="d-inline-block me-2" style="width: 18px;"></span>
                <?php endif; ?>
                <span class="item-name"><?= htmlspecialchars($item['name']) ?></span>
            </div>
        </td>
        <td><code class="text-secondary"><?= htmlspecialchars($item['slug']) ?></code></td>
        <td class="text-center">
            <div class="form-check form-switch d-inline-block">
                <input class="form-check-input item-status-switch" type="checkbox" role="switch" <?= ($item['is_enabled'] ?? 1) == 1 ? 'checked' : '' ?>>
            </div>
        </td>
    </tr>
<?php
        if ($hasChildren) {
            renderFilterRows($item['children']);
        }
    endforeach;
}
?>

<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <h4><i class="bi bi-sliders me-2 text-primary"></i>Cau hinh bo loc & Mega Menu</h4>
    <button type="button" class="btn btn-primary" id="btnSaveConfig">
        <i class="bi bi-save me-2"></i>Lưu cấu hình
    </button>
</div>

<!-- Tab selection for Categories, Projects, Blog -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-0">
        <ul class="nav nav-pills nav-fill bg-light p-2 rounded" id="filterTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active py-3" id="services-tab" data-bs-toggle="tab" data-bs-target="#tab-services" type="button" role="tab">
                    <i class="bi bi-grid-3x3-gap me-2"></i>Lĩnh vực
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link py-3" id="blogs-tab" data-bs-toggle="tab" data-bs-target="#tab-blogs" type="button" role="tab">
                    <i class="bi bi-journal-text me-2"></i>Danh mục Tin tức
                </button>
            </li>
        </ul>
    </div>
</div>

<div class="tab-content" id="filterTabContent">
    <!-- === TAB 1: LĨNH VỰC === -->
    <div class="tab-pane fade show active" id="tab-services" role="tabpanel">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info border-0 bg-light-info text-info d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-info-circle-fill me-3 fs-4"></i>
                    <div>
                        <strong>Huong dan:</strong> Dung chevron de mo dung mot nhanh can sap xep. Neu khong mo nhanh nao, ban chi keo tha cac muc cap goc.
                    </div>
                </div>
            </div>
            
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 py-3">
                        <h6 class="card-title mb-0 fw-semibold">Danh sách sắp xếp Lĩnh vực</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;"></th>
                                        <th style="width: 80px;">ID</th>
                                        <th>Tên lĩnh vực</th>
                                        <th>Đường dẫn (Slug)</th>
                                        <th style="width: 150px;" class="text-center">Hiện Menu</th>
                                    </tr>
                                </thead>
                                <tbody class="drag-container" data-type="services">
                                    <?php
                                    renderFilterRows(prepareFilterItems($services, $servicesConfig));
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- === TAB 3: DANH MỤC TIN TỨC === -->
    <div class="tab-pane fade" id="tab-blogs" role="tabpanel">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info border-0 bg-light-info text-info d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-info-circle-fill me-3 fs-4"></i>
                    <div>
                        <strong>Huong dan:</strong> Dung chevron de mo dung mot nhanh can sap xep. Neu khong mo nhanh nao, ban chi keo tha cac muc cap goc.
                    </div>
                </div>
            </div>
            
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 py-3">
                        <h6 class="card-title mb-0 fw-semibold">Danh sách sắp xếp Danh mục Tin tức</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;"></th>
                                        <th style="width: 80px;">ID</th>
                                        <th>Tên danh mục</th>
                                        <th>Đường dẫn (Slug)</th>
                                        <th style="width: 150px;" class="text-center">Hiện Menu</th>
                                    </tr>
                                </thead>
                                <tbody class="drag-container" data-type="blog_categories">
                                    <?php
                                    renderFilterRows(prepareFilterItems($blogCategories, $blogCategoriesConfig));
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
