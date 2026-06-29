<?php
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
                    <i class="bi bi-grid-3x3-gap me-2"></i>Dịch vụ
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
    <!-- === TAB 1: DỊCH VỤ === -->
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
                        <h6 class="card-title mb-0 fw-semibold">Danh sách sắp xếp Dịch vụ</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;"></th>
                                        <th style="width: 80px;">ID</th>
                                        <th>Tên dịch vụ</th>
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

<style>
.draggable-item {
    transition: background-color 0.2s ease, transform 0.2s ease;
}
.draggable-item.dragging {
    opacity: 0.5;
    background-color: #f1f3f9;
    transform: scale(0.98);
}
.draggable-item.drag-disabled .drag-handle {
    cursor: not-allowed;
    opacity: 0.35;
}
.draggable-item.can-drag .drag-handle {
    cursor: move;
    color: #0d6efd !important;
}
.drag-over-top {
    box-shadow: inset 0 2px 0 #0d6efd;
}
.drag-over-bottom {
    box-shadow: inset 0 -2px 0 #0d6efd;
}
.bg-light-info {
    background-color: #e8f4fd !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const containers = document.querySelectorAll('.drag-container');

    containers.forEach(container => {
        let dragRows = [];
        let dragEl = null;

        updateDragState(container);

        container.addEventListener('click', function(e) {
            const toggle = e.target.closest('.filter-tree-toggle');
            if (!toggle) return;

            const row = toggle.closest('.draggable-item');
            const isOpen = row.dataset.expanded === '1';
            row.dataset.expanded = isOpen ? '0' : '1';
            toggle.classList.toggle('is-open', !isOpen);
            toggle.querySelector('i').className = isOpen ? 'bi bi-chevron-right' : 'bi bi-chevron-down';

            if (isOpen) {
                collapseDescendants(container, row.dataset.id);
            }

            refreshVisibility(container);
            updateDragState(container);
        });

        container.addEventListener('dragstart', function(e) {
            const row = e.target.closest('.draggable-item');
            if (!row || row.draggable !== true) {
                e.preventDefault();
                return;
            }

            dragEl = row;
            dragRows = getSubtreeRows(container, row);
            dragRows.forEach(item => item.classList.add('dragging'));
            e.dataTransfer.effectAllowed = 'move';
        });

        container.addEventListener('dragover', function(e) {
            if (!dragEl) return;

            const targetRow = e.target.closest('.draggable-item.can-drag');
            if (!targetRow || targetRow === dragEl || dragRows.includes(targetRow)) return;

            e.preventDefault();
            clearDragMarkers(container);

            const targetBlock = getSubtreeRows(container, targetRow);
            const rect = targetRow.getBoundingClientRect();
            const insertAfter = (e.clientY - rect.top) / Math.max(1, rect.height) > 0.5;
            targetRow.classList.add(insertAfter ? 'drag-over-bottom' : 'drag-over-top');

            dragRows.forEach(item => item.remove());
            const reference = insertAfter ? targetBlock[targetBlock.length - 1].nextSibling : targetRow;
            dragRows.forEach(item => container.insertBefore(item, reference));
        });

        container.addEventListener('dragend', function() {
            dragRows.forEach(item => item.classList.remove('dragging'));
            clearDragMarkers(container);
            dragRows = [];
            dragEl = null;
            refreshVisibility(container);
            updateDragState(container);
        });
    });

    function collapseDescendants(container, parentId) {
        getDescendantRows(container, parentId).forEach(row => {
            row.dataset.expanded = '0';
            const toggle = row.querySelector('.filter-tree-toggle');
            if (toggle) {
                toggle.classList.remove('is-open');
                toggle.querySelector('i').className = 'bi bi-chevron-right';
            }
        });
    }

    function refreshVisibility(container) {
        container.querySelectorAll('.draggable-item').forEach(row => {
            const parentId = row.dataset.parentId;
            if (!parentId) {
                row.classList.remove('d-none');
                return;
            }
            row.classList.toggle('d-none', !areAncestorsOpen(container, row));
        });
    }

    function areAncestorsOpen(container, row) {
        let parentId = row.dataset.parentId;
        while (parentId) {
            const parent = findRow(container, parentId);
            if (!parent || parent.dataset.expanded !== '1') return false;
            parentId = parent.dataset.parentId;
        }
        return true;
    }

    function getExpandedRows(container) {
        return Array.from(container.querySelectorAll('.draggable-item[data-expanded="1"]'));
    }

    function getActiveParentId(container) {
        const expanded = getExpandedRows(container);
        if (expanded.length === 0) return '';

        // Check if multiple chevrons are open at the same level (e.g., a1 and b1 both open)
        const depthCount = {};
        expanded.forEach(row => {
            const depth = row.dataset.depth;
            depthCount[depth] = (depthCount[depth] || 0) + 1;
        });
        for (const depth in depthCount) {
            if (depthCount[depth] > 1) {
                return null; // Multiple chevrons at same level - no drag allowed
            }
        }

        const sorted = expanded.sort((a, b) => Number(a.dataset.depth) - Number(b.dataset.depth));
        for (let i = 1; i < sorted.length; i++) {
            if (!isDescendantOf(container, sorted[i], sorted[i - 1].dataset.id)) {
                return null;
            }
        }
        return sorted[sorted.length - 1].dataset.id;
    }

    function updateDragState(container) {
        const activeParentId = getActiveParentId(container);
        container.querySelectorAll('.draggable-item').forEach(row => {
            // Can drag if:
            // 1. No chevrons open (activeParentId === '') → drag root level items
            // 2. One chevron open (activeParentId is a valid ID) → drag only children of that parent, NOT the parent itself
            // 3. Multiple chevrons open (activeParentId === null) → cannot drag anything
            const isRootLevel = !row.dataset.parentId;
            const isChildOfActive = activeParentId !== null && activeParentId !== '' && row.dataset.parentId === activeParentId;
            const isRootDrag = activeParentId === '';
            const canDrag = (isRootDrag && isRootLevel) || (isChildOfActive && !row.classList.contains('d-none'));
            row.draggable = canDrag;
            row.classList.toggle('can-drag', canDrag);
            row.classList.toggle('drag-disabled', !canDrag);
        });
    }

    function getSubtreeRows(container, row) {
        const rows = Array.from(container.querySelectorAll('.draggable-item'));
        const start = rows.indexOf(row);
        const startDepth = Number(row.dataset.depth);
        const result = [row];

        for (let i = start + 1; i < rows.length; i++) {
            const candidateDepth = Number(rows[i].dataset.depth);
            if (candidateDepth <= startDepth) break;
            result.push(rows[i]);
        }

        return result;
    }

    function getDescendantRows(container, parentId) {
        const parent = findRow(container, parentId);
        return parent ? getSubtreeRows(container, parent).slice(1) : [];
    }

    function findRow(container, id) {
        return container.querySelector(`.draggable-item[data-id="${CSS.escape(String(id))}"]`);
    }

    function isDescendantOf(container, row, ancestorId) {
        let parentId = row.dataset.parentId;
        while (parentId) {
            if (parentId === ancestorId) return true;
            const parent = findRow(container, parentId);
            parentId = parent ? parent.dataset.parentId : '';
        }
        return false;
    }

    function clearDragMarkers(container) {
        container.querySelectorAll('.drag-over-top, .drag-over-bottom').forEach(row => {
            row.classList.remove('drag-over-top', 'drag-over-bottom');
        });
    }

    document.getElementById('btnSaveConfig').addEventListener('click', function() {
        const activeTabPane = document.querySelector('.tab-pane.active');
        const container = activeTabPane.querySelector('.drag-container');
        const criteriaType = container.getAttribute('data-type');
        const siblingCounters = {};
        const items = [];

        container.querySelectorAll('.draggable-item').forEach(row => {
            const parentId = row.dataset.parentId || null;
            const counterKey = parentId || 'root';
            siblingCounters[counterKey] = siblingCounters[counterKey] || 0;

            items.push({
                item_id: row.dataset.id,
                parent_id: parentId,
                sort_order: siblingCounters[counterKey]++,
                is_enabled: row.querySelector('.item-status-switch').checked ? 1 : 0
            });
        });

        const btn = document.getElementById('btnSaveConfig');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Dang luu...';

        fetch('/filter-config/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                criteria_type: criteriaType,
                items: items
            })
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = originalText;

            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Co loi xay ra: ' + data.message);
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            alert('Loi ket noi mang: ' + err.message);
        });
    });
});
</script>
