/**
 * blog-categories.js — Blog Categories Hierarchical Hover
 * 
 * Logic: Hover vào category cha → hiển thị danh mục con
 *        Rời khỏi → đóng danh mục con
 */

(function() {
    'use strict';

    console.log('[blog-categories] ===== Script Loaded =====');

    /**
     * Show children của category
     */
    function showChildren(categoryItem) {
        if (!categoryItem) return;

        const childrenEl = categoryItem.querySelector('.category-children');
        if (!childrenEl) return;

        const catId = categoryItem.getAttribute('data-cat-id');
        console.log('[blog-categories] SHOW cat', catId);

        childrenEl.style.display = 'block';
        childrenEl.classList.add('show');
    }

    /**
     * Hide children của category
     */
    function hideChildren(categoryItem) {
        if (!categoryItem) return;

        const childrenEl = categoryItem.querySelector('.category-children');
        if (!childrenEl) return;

        const catId = categoryItem.getAttribute('data-cat-id');
        console.log('[blog-categories] HIDE cat', catId);

        childrenEl.classList.remove('show');
        setTimeout(() => {
            childrenEl.style.display = 'none';
        }, 300);
    }

    /**
     * Initialize - gán hover events
     */
    function init() {
        console.log('[blog-categories] init() called');

        const categoriesList = document.querySelector('.blog-categories-hierarchical');
        if (!categoriesList) {
            console.warn('[blog-categories] .blog-categories-hierarchical NOT found');
            return;
        }

        const categoryItems = categoriesList.querySelectorAll('.category-has-children');
        console.log('[blog-categories] Found', categoryItems.length, 'categories with children');

        categoryItems.forEach((categoryItem) => {
            const catId = categoryItem.getAttribute('data-cat-id');

            categoryItem.addEventListener('mouseenter', function() {
                console.log('[blog-categories] mouseenter cat', catId);
                showChildren(this);
            });

            categoryItem.addEventListener('mouseleave', function() {
                console.log('[blog-categories] mouseleave cat', catId);
                hideChildren(this);
            });
        });

        console.log('[blog-categories] init() complete');
    }

    // Try multiple initialization methods to ensure it works
    console.log('[blog-categories] document.readyState =', document.readyState);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Fallback: try again after 1 second
    setTimeout(init, 1000);

    // Fallback: try again after 3 seconds
    setTimeout(init, 3000);

})();
