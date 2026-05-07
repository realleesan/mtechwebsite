/**
 * Projects Details Page JavaScript
 * Xử lý các tương tác cho trang chi tiết dự án
 */

(function() {
    'use strict';

    // Khởi tạo khi DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        initProjectDetails();
    });

    /**
     * Khởi tạo các chức năng cho trang project details
     */
    function initProjectDetails() {
        // Image zoom/lightbox functionality
        initImageZoom();
        
        // Smooth scroll cho anchor links
        initSmoothScroll();
        
        // Lazy loading cho images
        initLazyLoading();
        
        // Animation on scroll
        initScrollAnimations();
    }

    /**
     * Image zoom/lightbox functionality
     */
    function initImageZoom() {
        const projectImages = document.querySelectorAll('.pr_image img, .project_list .col-lg-4 img');
        
        projectImages.forEach(function(img) {
            img.style.cursor = 'zoom-in';
            img.addEventListener('click', function() {
                openLightbox(this.src, this.alt);
            });
        });
    }

    /**
     * Mở lightbox để xem ảnh
     */
    function openLightbox(src, alt) {
        // Tạo overlay
        const overlay = document.createElement('div');
        overlay.className = 'project-lightbox-overlay';
        overlay.innerHTML = `
            <div class="project-lightbox-content">
                <img src="${src}" alt="${alt}">
                <button class="project-lightbox-close">&times;</button>
            </div>
        `;
        
        // CSS cho lightbox
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;
        
        const content = overlay.querySelector('.project-lightbox-content');
        content.style.cssText = `
            position: relative;
            max-width: 90%;
            max-height: 90%;
        `;
        
        const img = overlay.querySelector('img');
        img.style.cssText = `
            max-width: 100%;
            max-height: 90vh;
            object-fit: contain;
        `;
        
        const closeBtn = overlay.querySelector('.project-lightbox-close');
        closeBtn.style.cssText = `
            position: absolute;
            top: -40px;
            right: 0;
            background: none;
            border: none;
            color: #fff;
            font-size: 40px;
            cursor: pointer;
            line-height: 1;
        `;
        
        // Thêm vào body
        document.body.appendChild(overlay);
        document.body.style.overflow = 'hidden';
        
        // Hiệu ứng fade in
        requestAnimationFrame(function() {
            overlay.style.opacity = '1';
        });
        
        // Đóng lightbox
        function closeLightbox() {
            overlay.style.opacity = '0';
            setTimeout(function() {
                overlay.remove();
                document.body.style.overflow = '';
            }, 300);
        }
        
        closeBtn.addEventListener('click', closeLightbox);
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                closeLightbox();
            }
        });
        
        // Đóng bằng phím ESC
        document.addEventListener('keydown', function escHandler(e) {
            if (e.key === 'Escape') {
                closeLightbox();
                document.removeEventListener('keydown', escHandler);
            }
        });
    }

    /**
     * Smooth scroll cho anchor links
     */
    function initSmoothScroll() {
        const links = document.querySelectorAll('a[href^="#"]');
        
        links.forEach(function(link) {
            link.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    e.preventDefault();
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    /**
     * Lazy loading cho images
     */
    function initLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                            img.classList.add('loaded');
                        }
                        imageObserver.unobserve(img);
                    }
                });
            }, {
                rootMargin: '50px'
            });
            
            document.querySelectorAll('img[data-src]').forEach(function(img) {
                imageObserver.observe(img);
            });
        }
    }

    /**
     * Animation on scroll
     */
    function initScrollAnimations() {
        const animatedElements = document.querySelectorAll('.pr_item, .project_info, .project_description h2');
        
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry, index) {
                    if (entry.isIntersecting) {
                        // Stagger animation
                        setTimeout(function() {
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0)';
                        }, index * 100);
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });
            
            animatedElements.forEach(function(el) {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(el);
            });
        }
    }

    /**
     * Utility: Debounce function
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction() {
            const context = this;
            const args = arguments;
            const later = function() {
                timeout = null;
                func.apply(context, args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    /**
     * Share project functionality
     */
    window.shareProject = function(platform) {
        const url = window.location.href;
        const title = document.title;
        
        const shareUrls = {
            facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`,
            twitter: `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`,
            linkedin: `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`,
            email: `mailto:?subject=${encodeURIComponent(title)}&body=${encodeURIComponent(url)}`
        };
        
        if (shareUrls[platform]) {
            if (platform === 'email') {
                window.location.href = shareUrls[platform];
            } else {
                window.open(shareUrls[platform], '_blank', 'width=600,height=400');
            }
        }
    };

    /**
     * Print project page
     */
    window.printProject = function() {
        window.print();
    };

})();
