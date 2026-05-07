/* ==========================================
   Projects Page JavaScript
   ========================================== */

(function() {
    'use strict';

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initProjectsPage();
    });

    /**
     * Initialize Projects Page functionality
     */
    function initProjectsPage() {
        // Initialize project hover effects
        initProjectHoverEffects();
        
        // Initialize category filters if they exist
        initCategoryFilters();
        
        // Initialize lazy loading for images
        initLazyLoading();
        
        // Initialize animation on scroll
        initScrollAnimations();
    }

    /**
     * Initialize hover effects for project items
     */
    function initProjectHoverEffects() {
        const projectItems = document.querySelectorAll('.lt_project_item');
        
        projectItems.forEach(function(item) {
            item.addEventListener('mouseenter', function() {
                this.classList.add('is-hovered');
            });
            
            item.addEventListener('mouseleave', function() {
                this.classList.remove('is-hovered');
            });
        });
    }

    /**
     * Initialize category filter buttons
     */
    function initCategoryFilters() {
        const filterButtons = document.querySelectorAll('.project-filters button');
        const projectItems = document.querySelectorAll('.lt_project_item');
        
        if (filterButtons.length === 0) return;
        
        filterButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                filterButtons.forEach(function(btn) {
                    btn.classList.remove('active');
                });
                
                // Add active class to clicked button
                this.classList.add('active');
                
                const filterValue = this.getAttribute('data-filter');
                
                // Filter projects
                projectItems.forEach(function(item) {
                    const category = item.getAttribute('data-category');
                    
                    if (filterValue === 'all' || category === filterValue) {
                        item.style.display = 'block';
                        item.style.opacity = '0';
                        setTimeout(function() {
                            item.style.transition = 'opacity 0.4s ease';
                            item.style.opacity = '1';
                        }, 50);
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    }

    /**
     * Initialize lazy loading for images
     */
    function initLazyLoading() {
        const images = document.querySelectorAll('.lt_project_img img');
        
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                        }
                        
                        img.classList.add('loaded');
                        observer.unobserve(img);
                    }
                });
            }, {
                rootMargin: '50px 0px',
                threshold: 0.01
            });
            
            images.forEach(function(img) {
                imageObserver.observe(img);
            });
        } else {
            // Fallback for browsers without IntersectionObserver
            images.forEach(function(img) {
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                }
                img.classList.add('loaded');
            });
        }
    }

    /**
     * Initialize animations on scroll
     */
    function initScrollAnimations() {
        const projectItems = document.querySelectorAll('.lt_project_item');
        
        if ('IntersectionObserver' in window) {
            const animationObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry, index) {
                    if (entry.isIntersecting) {
                        setTimeout(function() {
                            entry.target.classList.add('is-visible');
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0)';
                        }, index * 100); // Stagger effect
                        
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });
            
            projectItems.forEach(function(item) {
                item.style.opacity = '0';
                item.style.transform = 'translateY(30px)';
                item.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                animationObserver.observe(item);
            });
        } else {
            // Fallback
            projectItems.forEach(function(item) {
                item.classList.add('is-visible');
            });
        }
    }

    /**
     * AJAX function to load more projects
     * @param {number} page - Page number to load
     * @param {string} category - Category filter
     */
    window.loadMoreProjects = function(page, category) {
        const container = document.querySelector('.project_info_two');
        const loadingMsg = document.querySelector('.projects-loading');
        
        if (loadingMsg) {
            loadingMsg.style.display = 'block';
        }
        
        // Build AJAX request URL
        const params = new URLSearchParams({
            action: 'load_more',
            page: page,
            category: category || 'all'
        });
        
        fetch('?page=projects&' + params.toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(response) {
            return response.text();
        })
        .then(function(html) {
            if (loadingMsg) {
                loadingMsg.style.display = 'none';
            }
            
            // Append new projects to container
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            const newProjects = tempDiv.querySelectorAll('.lt_project_item');
            
            newProjects.forEach(function(project) {
                container.appendChild(project);
            });
            
            // Reinitialize effects for new items
            initProjectHoverEffects();
            initScrollAnimations();
        })
        .catch(function(error) {
            console.error('Error loading projects:', error);
            if (loadingMsg) {
                loadingMsg.style.display = 'none';
            }
        });
    };

    /**
     * Debounce function for performance
     */
    function debounce(func, wait, immediate) {
        let timeout;
        return function() {
            const context = this, args = arguments;
            const later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            const callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    }

})();
