/**
 * admin.filter.config.js
 * 
 * Script cho trang cấu hình Filter Config & Mega Menu
 * Xử lý drag-drop, tree collapse/expand, save config qua AJAX
 */

document.addEventListener('DOMContentLoaded', function() {
    const containers = document.querySelectorAll('.drag-container');

    containers.forEach(container => {
        let dragRows = [];
        let dragEl = null;

        updateDragState(container);

        // Handle item status toggle (enable/disable) - ✅ AUTO-DISABLE CHILDREN
        container.addEventListener('change', function(e) {
            const statusSwitch = e.target.closest('.item-status-switch');
            if (!statusSwitch) return;

            const row = statusSwitch.closest('.draggable-item');
            const isChecked = statusSwitch.checked;
            const itemId = row.dataset.id;
            
            // Nếu unchecked (disable) → tự động disable tất cả children
            if (!isChecked) {
                disableAllDescendants(container, itemId);
            }
        });

        // Handle tree toggle (expand/collapse)
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

        // Drag start
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

        // Drag over
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

        // Drag end
        container.addEventListener('dragend', function() {
            dragRows.forEach(item => item.classList.remove('dragging'));
            clearDragMarkers(container);
            dragRows = [];
            dragEl = null;
            refreshVisibility(container);
            updateDragState(container);
        });
    });

    /**
     * Recursively disable all descendants
     * Khi parent disable → tất cả children cũng disable
     * ✅ FIX: Xử lý tất cả descendants kể cả những rows đang bị collapse (d-none)
     */
    function disableAllDescendants(container, parentId) {
        // Lấy tất cả descendants (kể cả hidden/collapsed)
        const parentRow = findRow(container, parentId);
        if (!parentRow) return;

        const descendants = getSubtreeRows(container, parentRow).slice(1);
        descendants.forEach(row => {
            const checkbox = row.querySelector('.item-status-switch');
            if (checkbox && checkbox.checked) {
                checkbox.checked = false;
                // Recursively disable children của row này
                disableAllDescendants(container, row.dataset.id);
            }
        });
    }

    /**
     * Collapse tất cả descendant rows của một parent
     */
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

    /**
     * Cập nhật visibility của rows dựa vào expand/collapse state
     */
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

    /**
     * Kiểm tra tất cả ancestors của row đều open không
     */
    function areAncestorsOpen(container, row) {
        let parentId = row.dataset.parentId;
        while (parentId) {
            const parent = findRow(container, parentId);
            if (!parent || parent.dataset.expanded !== '1') return false;
            parentId = parent.dataset.parentId;
        }
        return true;
    }

    /**
     * Lấy tất cả rows đã expand
     */
    function getExpandedRows(container) {
        return Array.from(container.querySelectorAll('.draggable-item[data-expanded="1"]'));
    }

    /**
     * Xác định active parent ID để kiểm soát drag behavior
     * - Nếu không mở chevron nào → drag root items
     * - Nếu mở 1 chevron → drag children của chevron đó
     * - Nếu mở nhiều chevron ở cùng level → không cho drag
     */
    function getActiveParentId(container) {
        const expanded = getExpandedRows(container);
        if (expanded.length === 0) return '';

        // Kiểm tra nếu có multiple chevrons open ở cùng depth level
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

        // Kiểm tra nếu expanded rows tạo thành chain (parent → child → grandchild)
        const sorted = expanded.sort((a, b) => Number(a.dataset.depth) - Number(b.dataset.depth));
        for (let i = 1; i < sorted.length; i++) {
            if (!isDescendantOf(container, sorted[i], sorted[i - 1].dataset.id)) {
                return null;
            }
        }
        return sorted[sorted.length - 1].dataset.id;
    }

    /**
     * Cập nhật drag state của tất cả items
     */
    function updateDragState(container) {
        const activeParentId = getActiveParentId(container);
        container.querySelectorAll('.draggable-item').forEach(row => {
            const isRootLevel = !row.dataset.parentId;
            const isChildOfActive = activeParentId !== null && activeParentId !== '' && row.dataset.parentId === activeParentId;
            const isRootDrag = activeParentId === '';
            const canDrag = (isRootDrag && isRootLevel) || (isChildOfActive && !row.classList.contains('d-none'));
            
            row.draggable = canDrag;
            row.classList.toggle('can-drag', canDrag);
            row.classList.toggle('drag-disabled', !canDrag);
        });
    }

    /**
     * Lấy subtree rows (item + tất cả children)
     */
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

    /**
     * Lấy tất cả descendant rows của 1 parent
     */
    function getDescendantRows(container, parentId) {
        const parent = findRow(container, parentId);
        return parent ? getSubtreeRows(container, parent).slice(1) : [];
    }

    /**
     * Tìm row bằng ID
     */
    function findRow(container, id) {
        return container.querySelector(`.draggable-item[data-id="${CSS.escape(String(id))}"]`);
    }

    /**
     * Kiểm tra row có phải descendant của ancestor không
     */
    function isDescendantOf(container, row, ancestorId) {
        let parentId = row.dataset.parentId;
        while (parentId) {
            if (parentId === ancestorId) return true;
            const parent = findRow(container, parentId);
            parentId = parent ? parent.dataset.parentId : '';
        }
        return false;
    }

    /**
     * Clear drag markers (visual feedback)
     */
    function clearDragMarkers(container) {
        container.querySelectorAll('.drag-over-top, .drag-over-bottom').forEach(row => {
            row.classList.remove('drag-over-top', 'drag-over-bottom');
        });
    }

    /**
     * Save config via AJAX
     */
    document.getElementById('btnSaveConfig').addEventListener('click', function() {
        const activeTabPane = document.querySelector('.tab-pane.active');
        const container = activeTabPane.querySelector('.drag-container');
        const criteriaType = container.getAttribute('data-type');
        const siblingCounters = {};
        const items = [];

        // Build items array with sort_order based on sibling position
        container.querySelectorAll('.draggable-item').forEach(row => {
            // ✅ FIX: Convert empty string to null for root items
            const parentIdRaw = row.dataset.parentId;
            const parentId = (parentIdRaw === '' || !parentIdRaw) ? null : parseInt(parentIdRaw, 10);
            const counterKey = parentId || 'root';
            siblingCounters[counterKey] = siblingCounters[counterKey] || 0;

            items.push({
                item_id: parseInt(row.dataset.id, 10),
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
