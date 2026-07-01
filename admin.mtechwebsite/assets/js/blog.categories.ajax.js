/**
 * Category Management AJAX Module
 * Handles CRUD operations without page reload
 * Maintains expand/collapse state via localStorage
 */

// ============================================
// INITIALIZATION
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('tr[data-category-id]')) {
        // Initialize localStorage for expand/collapse state
        restoreCategoryState();
        initCategoryEventListeners();
    }
});

// ============================================
// STATE MANAGEMENT (localStorage)
// ============================================

const STORAGE_KEY = 'blog_categories_state';

/**
 * Save expand/collapse state to localStorage
 */
function saveCategoryState() {
    const expandedIds = [];
    document.querySelectorAll('.chevron-toggle.expanded').forEach(btn => {
        const categoryId = btn.closest('tr').getAttribute('data-category-id');
        if (categoryId) expandedIds.push(parseInt(categoryId));
    });
    localStorage.setItem(STORAGE_KEY, JSON.stringify(expandedIds));
}

/**
 * Restore expand/collapse state from localStorage
 */
function restoreCategoryState() {
    const expandedIds = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
    expandedIds.forEach(id => {
        setTimeout(() => {
            toggleBlogCategoryChildren(id, false); // false = silent, don't re-save
        }, 10);
    });
}

// ============================================
// EVENT LISTENERS SETUP
// ============================================

function initCategoryEventListeners() {
    // Delete buttons with AJAX
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-delete-ajax')) {
            e.preventDefault();
            const categoryId = e.target.closest('form').getAttribute('data-category-id');
            deleteCategoryAjax(categoryId);
        }
    });
}

// ============================================
// DELETE OPERATION (AJAX)
// ============================================

/**
 * Delete category via AJAX
 */
function deleteCategoryAjax(categoryId) {
    if (!confirm('Xóa danh mục này? Hành động này không thể hoàn tác.')) {
        return;
    }

    const btn = document.querySelector(`tr[data-category-id="${categoryId}"] .btn-delete-ajax`);
    const originalHtml = btn?.innerHTML;

    // Show loading state
    if (btn) {
        btn.disabled = true;
        btn.classList.add('loading');
    }

    fetch(`/api/blogs/categories/delete/${categoryId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Get parent row to check if it has other children
            const row = document.querySelector(`tr[data-category-id="${categoryId}"]`);
            const parentId = row?.getAttribute('data-parent-id');
            
            // Remove row from table
            if (row) {
                row.classList.add('category-row-remove');
                setTimeout(() => row.remove(), 300);
            }
            
            // If parent had children and now might not, update parent's UI
            if (parentId && parentId !== 'null') {
                setTimeout(() => {
                    const parentRow = document.querySelector(`tr[data-category-id="${parentId}"]`);
                    if (parentRow) {
                        const remainingChildren = document.querySelectorAll(`tr[data-parent-id="${parentId}"]`).length;
                        
                        // If no more children, remove chevron toggle from parent
                        if (remainingChildren === 0) {
                            updateParentChevron(parentId);
                            updateParentDeleteButton(parentId);
                        }
                    }
                }, 300);
            }
            
            // Show success message
            showToast('Xóa danh mục thành công', 'success');
            
            // Update total count
            updateCategoryCount();
        } else {
            showToast(data.error || 'Lỗi khi xóa danh mục', 'danger');
            // Restore button
            if (btn) {
                btn.disabled = false;
                btn.classList.remove('loading');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Lỗi khi xóa danh mục', 'danger');
        // Restore button
        if (btn) {
            btn.disabled = false;
            btn.classList.remove('loading');
        }
    });
}

/**
 * Update parent's chevron toggle after child deletion
 * If parent has no more children, remove the toggle button
 */
function updateParentChevron(parentId) {
    const parentRow = document.querySelector(`tr[data-category-id="${parentId}"]`);
    if (!parentRow) return;
    
    // Find the toggle button
    const toggleBtn = parentRow.querySelector('.chevron-toggle');
    if (!toggleBtn) return;
    
    // Replace toggle button with empty spacer
    const spacer = document.createElement('span');
    spacer.className = 'd-inline-block me-2';
    spacer.style.width = '24px';
    
    toggleBtn.replaceWith(spacer);
}

/**
 * Update parent's delete button after child deletion
 * If parent had children and now has none, show delete button
 */
function updateParentDeleteButton(parentId) {
    const parentRow = document.querySelector(`tr[data-category-id="${parentId}"]`);
    if (!parentRow) return;
    
    // Find the action cell (last td)
    const actionCell = parentRow.querySelector('td:last-child');
    if (!actionCell) return;
    
    // Check if there's a lock button (disabled delete)
    const lockBtn = actionCell.querySelector('.btn-outline-secondary[disabled]');
    if (lockBtn) {
        // Create delete form
        const deleteForm = document.createElement('form');
        deleteForm.method = 'POST';
        deleteForm.action = `/api/blogs/categories/delete/${parentId}`;
        deleteForm.className = 'd-inline';
        deleteForm.setAttribute('data-category-id', parentId);
        
        const deleteBtn = document.createElement('button');
        deleteBtn.type = 'submit';
        deleteBtn.className = 'btn btn-sm btn-outline-danger btn-delete-ajax';
        deleteBtn.title = 'Xóa';
        deleteBtn.innerHTML = '<i class="bi bi-trash"></i>';
        
        deleteForm.appendChild(deleteBtn);
        
        // Replace lock button with delete form
        lockBtn.replaceWith(deleteForm);
        
        // Re-initialize event listeners for new button
        initCategoryEventListeners();
    }
}

// ============================================
// UI UPDATE HELPERS
// ============================================

/**
 * Update total category count in header
 */
function updateCategoryCount() {
    const count = document.querySelectorAll('tr[data-category-id]').length;
    const countDisplay = document.querySelector('.admin-table .d-flex span strong');
    if (countDisplay) {
        countDisplay.textContent = count;
    }
}

/**
 * Show toast notification
 */
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed toast-notification`;
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
            <span>${message}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentElement) {
            toast.classList.add('toast-remove');
            setTimeout(() => toast.remove(), 300);
        }
    }, 3000);
}

// ============================================
// TOGGLE CATEGORIES WITH STATE SAVE
// ============================================

/**
 * Override toggleBlogCategoryChildren to save state
 */
const originalToggle = window.toggleBlogCategoryChildren;

window.toggleBlogCategoryChildren = function(categoryId, shouldSave = true) {
    // Call original toggle function
    if (originalToggle) {
        originalToggle(categoryId);
    }
    
    // Save state to localStorage
    if (shouldSave) {
        saveCategoryState();
    }
};

// Override expandAllBlogCategories to save state
const originalExpandAll = window.expandAllBlogCategories;

window.expandAllBlogCategories = function() {
    if (originalExpandAll) {
        originalExpandAll();
    }
    saveCategoryState();
};

// Override collapseAllBlogCategories to save state
const originalCollapseAll = window.collapseAllBlogCategories;

window.collapseAllBlogCategories = function() {
    if (originalCollapseAll) {
        originalCollapseAll();
    }
    saveCategoryState();
};
