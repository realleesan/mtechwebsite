/**
 * Header JavaScript - MTech Website
 * Xử lý hamburger menu, sticky header, dropdown
 */

(function() {
    'use strict';
    
    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        
        // ========================================
        // Hamburger Menu Toggle
        // ========================================
        const navbarToggler = document.querySelector('.navbar-toggler');
        const navbarCollapse = document.querySelector('.navbar-collapse');
        
        if (navbarToggler && navbarCollapse) {
            navbarToggler.addEventListener('click', function() {
                // Toggle collapsed class
                this.classList.toggle('collapsed');
                
                // Toggle show class on navbar-collapse
                navbarCollapse.classList.toggle('show');
                
                // Prevent body scroll when menu is open
                if (navbarCollapse.classList.contains('show')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            });
            
            // Close menu when clicking outside
            navbarCollapse.addEventListener('click', function(e) {
                if (e.target === this) {
                    navbarToggler.click();
                }
            });
        }
        
        // ========================================
        // Dropdown Toggle for Mobile
        // ========================================
        const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
        
        dropdownToggles.forEach(function(toggle) {
            toggle.addEventListener('click', function(e) {
                // Only prevent default on mobile
                if (window.innerWidth < 992) {
                    e.preventDefault();
                    
                    const parentItem = this.closest('.nav-item');
                    const dropdownMenu = parentItem.querySelector('.dropdown-menu');
                    
                    if (dropdownMenu) {
                        // Toggle show class
                        parentItem.classList.toggle('show');
                        
                        // Close other dropdowns
                        const allDropdowns = document.querySelectorAll('.nav-item.dropdown');
                        allDropdowns.forEach(function(item) {
                            if (item !== parentItem) {
                                item.classList.remove('show');
                            }
                        });
                    }
                }
            });
        });
        
        // ========================================
        // Sticky Header on Scroll
        // ========================================
        const header = document.querySelector('.menu_absolute');
        let lastScrollTop = 0;
        
        if (header) {
            window.addEventListener('scroll', function() {
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                
                // Add sticky class when scrolled down
                if (scrollTop > 100) {
                    header.classList.add('sticky');
                } else {
                    header.classList.remove('sticky');
                }
                
                lastScrollTop = scrollTop;
            });
        }
        
        // ========================================
        // Close Mobile Menu on Link Click
        // ========================================
        const navLinks = document.querySelectorAll('.navbar-collapse .nav-link');
        
        navLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                // Only close on mobile for non-dropdown links
                if (window.innerWidth < 992 && !this.classList.contains('dropdown-toggle')) {
                    if (navbarToggler && navbarCollapse) {
                        navbarToggler.classList.add('collapsed');
                        navbarCollapse.classList.remove('show');
                        document.body.style.overflow = '';
                    }
                }
            });
        });
        
        // ========================================
        // Handle Window Resize
        // ========================================
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                // Close mobile menu on resize to desktop
                if (window.innerWidth >= 992) {
                    if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                        navbarCollapse.classList.remove('show');
                        if (navbarToggler) {
                            navbarToggler.classList.add('collapsed');
                        }
                        document.body.style.overflow = '';
                    }
                    
                    // Remove show class from all dropdowns
                    const allDropdowns = document.querySelectorAll('.nav-item.dropdown');
                    allDropdowns.forEach(function(item) {
                        item.classList.remove('show');
                    });
                }
            }, 250);
        });
        
        // ========================================
        // Smooth Scroll for Anchor Links (optional)
        // ========================================
        const anchorLinks = document.querySelectorAll('a[href^="#"]');
        
        anchorLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                
                // Skip if href is just "#"
                if (href === '#' || href === '#!') {
                    return;
                }
                
                const target = document.querySelector(href);
                
                if (target) {
                    e.preventDefault();
                    
                    // Close mobile menu if open
                    if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                        navbarToggler.click();
                    }
                    
                    // Smooth scroll to target
                    const headerHeight = header ? header.offsetHeight : 0;
                    const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerHeight;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
        
        // ========================================
        // Active Menu Item Highlight
        // ========================================
        function setActiveMenuItem() {
            const currentPath = window.location.pathname;
            const currentSearch = window.location.search;
            const currentUrl = currentPath + currentSearch;
            
            const menuLinks = document.querySelectorAll('.navbar-nav .nav-link');
            
            menuLinks.forEach(function(link) {
                const linkHref = link.getAttribute('href');
                
                // Remove active class
                link.closest('.nav-item').classList.remove('active');
                
                // Add active class if URL matches
                if (linkHref && (currentUrl.includes(linkHref) || currentSearch.includes(linkHref))) {
                    link.closest('.nav-item').classList.add('active');
                }
            });
        }
        
        // Set active menu on page load
        setActiveMenuItem();
        
        // ========================================
        // Accessibility: ESC key to close menu
        // ========================================
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' || e.keyCode === 27) {
                if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                    navbarToggler.click();
                }
            }
        });
        
    });
    
})();
