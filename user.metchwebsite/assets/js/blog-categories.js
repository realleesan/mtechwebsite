/**
 * blog-categories.js
 * Handle collapsible hierarchical category filter for sidebar
 */

(function() {
    'use strict';

    /**
     * Initialize blog category toggle buttons
     */
    function initBlogCategoryToggle() {
        const categoryToggles = document.querySelectorAll('.category-toggle');
        
        categoryToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const catId = this.getAttribute('data-cat-id');
                const childrenContainer = document.querySelector(`.category-children[data-parent-id="${catId}"]`);
                const iconElement = this.querySelector('.toggle-icon');
                
                if (!childrenContainer) return;
                
                const isExpanded = this.classList.contains('expanded');
                
                if (!isExpanded) {
                    // Expand: show children
                    childrenContainer.style.display = 'block';
                    this.classList.add('expanded');
                    // Change icon from + to -
                    if (iconElement) {
                        iconElement.textContent = '-';
                    }
                    // Trigger animation
                    setTimeout(() => {
                        childrenContainer.classList.add('show');
                    }, 0);
                } else {
                    // Collapse: hide children
                    childrenContainer.classList.remove('show');
                    this.classList.remove('expanded');
                    // Change icon from - back to +
                    if (iconElement) {
                        iconElement.textContent = '+';
                    }
                    setTimeout(() => {
                        childrenContainer.style.display = 'none';
                    }, 450);
                    
                    // Recursively collapse all grandchildren
                    collapseDescendants(childrenContainer);
                }
            });
        });
    }
    
    /**
     * Recursively collapse all descendant categories
     */
    function collapseDescendants(container) {
        const childToggles = container.querySelectorAll('.category-toggle.expanded');
        childToggles.forEach(toggle => {
            const iconElement = toggle.querySelector('.toggle-icon');
            const childrenCtnr = document.querySelector(`.category-children[data-parent-id="${toggle.getAttribute('data-cat-id')}"]`);
            
            if (childrenCtnr) {
                childrenCtnr.classList.remove('show');
                childrenCtnr.style.display = 'none';
            }
            
            toggle.classList.remove('expanded');
            if (iconElement) {
                iconElement.textContent = '+';
            }
        });
    }
    
    /**
     * Expand category and all ancestors (for showing active category path)
     */
    function expandCategoryPath(catId) {
        const categoryItem = document.querySelector(`[data-cat-id="${catId}"]`);
        
        if (categoryItem) {
            // Expand this category's children
            const toggle = categoryItem.querySelector('.category-toggle');
            if (toggle && !toggle.classList.contains('expanded')) {
                toggle.click();
            }
            
            // Recursively expand parent categories
            const parentId = categoryItem.getAttribute('data-parent-id');
            if (parentId && parentId !== '0') {
                expandCategoryPath(parseInt(parentId));
            }
        }
    }
    
    /**
     * Collapse all categories
     */
    function collapseAllCategories() {
        const toggleButtons = document.querySelectorAll('.category-toggle.expanded');
        
        toggleButtons.forEach(button => {
            if (button.classList.contains('expanded')) {
                button.click();
            }
        });
    }
    
    /**
     * Expand all categories
     */
    function expandAllCategories() {
        const toggleButtons = document.querySelectorAll('.category-toggle:not(.expanded)');
        
        toggleButtons.forEach(button => {
            if (!button.classList.contains('expanded')) {
                button.click();
            }
        });
    }
    
    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', initBlogCategoryToggle);
    
    // Re-initialize if categories are dynamically loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBlogCategoryToggle);
    } else {
        initBlogCategoryToggle();
    }
    
    // Expose functions globally if needed
    window.expandCategoryPath = expandCategoryPath;
    window.collapseAllCategories = collapseAllCategories;
    window.expandAllCategories = expandAllCategories;

})();
