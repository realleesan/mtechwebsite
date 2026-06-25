/**
 * blog-categories.js
 * Handle collapsible hierarchical category filter
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize category toggle buttons
    initializeCategoryToggles();
});

/**
 * Initialize category toggle buttons
 */
function initializeCategoryToggles() {
    const toggleButtons = document.querySelectorAll('.category-toggle');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const catId = this.getAttribute('data-cat-id');
            const childrenWrapper = document.querySelector(`.category-children[data-parent-id="${catId}"]`);
            
            if (childrenWrapper) {
                const isExpanded = this.getAttribute('aria-expanded') === 'true';
                
                // Toggle aria-expanded attribute
                this.setAttribute('aria-expanded', !isExpanded);
                
                // Toggle children visibility with animation
                if (isExpanded) {
                    childrenWrapper.style.display = 'none';
                    childrenWrapper.classList.remove('expanded');
                } else {
                    childrenWrapper.style.display = 'block';
                    childrenWrapper.classList.add('expanded');
                }
            }
        });
    });
    
    // Add category link click handler to prevent toggle when clicking text
    const categoryLinks = document.querySelectorAll('.category-link');
    categoryLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Allow normal link behavior
            // The link will navigate to the filter page
        });
    });
}

/**
 * Expand category and all ancestors (for showing active category path)
 * @param {number} catId Category ID to expand
 */
function expandCategoryPath(catId) {
    const categoryItem = document.querySelector(`[data-cat-id="${catId}"]`);
    
    if (categoryItem) {
        // Expand this category's children
        const toggle = categoryItem.querySelector('.category-toggle');
        if (toggle && toggle.getAttribute('aria-expanded') === 'false') {
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
    const toggleButtons = document.querySelectorAll('.category-toggle[aria-expanded="true"]');
    
    toggleButtons.forEach(button => {
        button.click();
    });
}

/**
 * Expand all categories
 */
function expandAllCategories() {
    const toggleButtons = document.querySelectorAll('.category-toggle[aria-expanded="false"]');
    
    toggleButtons.forEach(button => {
        button.click();
    });
}
