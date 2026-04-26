/**
 * Home Page JavaScript
 * Xử lý các tương tác cho trang chủ
 */

(function() {
    'use strict';

    // ==========================================
    // SECTION 1: HOME BANNER SLIDER
    // ==========================================

    /**
     * Initialize Hero Slider
     */
    function initHomeBannerSlider() {
        const slider = document.getElementById('homeBannerSlider');
        if (!slider) return;

        const slides = slider.querySelectorAll('.slider_item');
        const prevBtn = slider.querySelector('.slider_prev');
        const nextBtn = slider.querySelector('.slider_next');
        let current = 0;
        let isAnimating = false;
        let autoTimer = null;

        // Khởi tạo: hiện caption slide đầu tiên ngay
        const firstCaption = slides[0].querySelector('.slider_caption');
        if (firstCaption) {
            firstCaption.classList.add('caption-in');
        }

        function goTo(index) {
            if (isAnimating) return;
            const next = (index + slides.length) % slides.length;
            if (next === current) return;
            isAnimating = true;

            const currentSlide = slides[current];
            const nextSlide = slides[next];
            const currentCaption = currentSlide.querySelector('.slider_caption');
            const nextCaption = nextSlide.querySelector('.slider_caption');

            // 1. Caption hiện tại: trượt xuống và ẩn
            if (currentCaption) {
                currentCaption.classList.remove('caption-in');
                currentCaption.classList.add('caption-out');
            }

            // 2. Slide mới: đặt z-index cao hơn rồi fade in — tạo cross-blend với slide cũ
            nextSlide.style.zIndex = 2;
            nextSlide.classList.add('active'); // bắt đầu fade in (opacity 0 → 1)

            // 3. Sau 1s (cross-fade xong): dọn dẹp slide cũ
            setTimeout(() => {
                currentSlide.classList.remove('active');
                currentSlide.style.zIndex = '';
                nextSlide.style.zIndex = '';
                if (currentCaption) currentCaption.classList.remove('caption-out');
                current = next;

                // 4. Sau thêm 1s: hiện caption mới
                setTimeout(() => {
                    if (nextCaption) nextCaption.classList.add('caption-in');
                    isAnimating = false;
                }, 1000);

            }, 1000);
        }

        function startAuto() {
            autoTimer = setInterval(() => goTo(current + 1), 6000);
        }

        function resetAuto() {
            clearInterval(autoTimer);
            startAuto();
        }

        if (prevBtn) prevBtn.addEventListener('click', () => { goTo(current - 1); resetAuto(); });
        if (nextBtn) nextBtn.addEventListener('click', () => { goTo(current + 1); resetAuto(); });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') { goTo(current - 1); resetAuto(); }
            if (e.key === 'ArrowRight') { goTo(current + 1); resetAuto(); }
        });

        startAuto();
    }


    // ==========================================
    // SECTION 2: SERVICES & FEATURED PROJECTS
    // ==========================================

    /**
     * Initialize Services Section
     */
    function initServices() {
        const serviceItems = document.querySelectorAll('.service_item');
        
        serviceItems.forEach(item => {
            // Thêm hiệu ứng hover cho service items
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    }

    /**
     * Initialize Featured Projects Section
     */
    function initFeaturedProjects() {
        const featuredItems = document.querySelectorAll('.featured_pr_item');
        
        featuredItems.forEach((item, index) => {
            // Thêm animation delay cho từng item
            item.style.animationDelay = (index * 0.1) + 's';
            
            // Hiệu ứng parallax nhẹ khi scroll
            const img = item.querySelector('img');
            if (img) {
                item.addEventListener('mouseenter', function() {
                    img.style.transform = 'scale(1.1)';
                });
                
                item.addEventListener('mouseleave', function() {
                    img.style.transform = 'scale(1)';
                });
            }
        });
    }

    /**
     * Initialize Quote Section
     */
    function initQuoteSection() {
        const quoteBtn = document.querySelector('.quote_btn');
        
        if (quoteBtn) {
            quoteBtn.addEventListener('click', function(e) {
                // Thêm hiệu ứng ripple
                const ripple = document.createElement('span');
                ripple.style.cssText = `
                    position: absolute;
                    background: rgba(255,255,255,0.3);
                    border-radius: 50%;
                    transform: scale(0);
                    animation: ripple 0.6s linear;
                    pointer-events: none;
                `;
                
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = (e.clientX - rect.left - size/2) + 'px';
                ripple.style.top = (e.clientY - rect.top - size/2) + 'px';
                
                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);
                
                setTimeout(() => ripple.remove(), 600);
            });
        }
    }

    /**
     * Add CSS animation for ripple effect
     */
    function addRippleStyles() {
        if (!document.getElementById('home-ripple-styles')) {
            const style = document.createElement('style');
            style.id = 'home-ripple-styles';
            style.textContent = `
                @keyframes ripple {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        }
    }

    /**
     * Initialize Scroll Animations
     */
    function initScrollAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe service items
        document.querySelectorAll('.service_item').forEach(item => {
            item.style.opacity = '0';
            item.style.transform = 'translateY(30px)';
            item.style.transition = 'all 0.6s ease';
            observer.observe(item);
        });

        // Observe featured project items
        document.querySelectorAll('.featured_pr_item').forEach(item => {
            item.style.opacity = '0';
            item.style.transform = 'scale(0.9)';
            item.style.transition = 'all 0.6s ease';
            observer.observe(item);
        });

        // Add animate-in styles
        const style = document.createElement('style');
        style.textContent = `
            .animate-in {
                opacity: 1 !important;
                transform: translateY(0) scale(1) !important;
            }
        `;
        document.head.appendChild(style);
    }

    /**
     * Handle smooth scroll for anchor links
     */
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
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
     * Lazy load images
     */
    function initLazyLoad() {
        const images = document.querySelectorAll('.service_img img, .featured_pr_item img');
        
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.classList.add('loaded');
                        observer.unobserve(img);
                    }
                });
            }, {
                rootMargin: '50px 0px'
            });

            images.forEach(img => imageObserver.observe(img));
        }
    }

    // ==========================================
    // Initialize on DOM Ready
    // ==========================================
    function init() {
        initHomeBannerSlider();
        initServices();
        initFeaturedProjects();
        initQuoteSection();
        addRippleStyles();
        initScrollAnimations();
        initSmoothScroll();
        initLazyLoad();
        
        console.log('[Home] Initialized Segment 2 - Services & Featured Projects');
    }

    // Run initialization
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // ==========================================
    // SECTION 3: PLACEHOLDER FOR OTHER MEMBERS
    // ==========================================}
    // NOTE: Segment 3 - Để trống cho thành viên khác code

})();
