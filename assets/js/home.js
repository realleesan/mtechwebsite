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
        // Section 3
        initTestimonials();
        initLatestNews();
        initHomeContactForm();

        console.log('[Home] Initialized — Sections 1, 2, 3');
    }

    // Run initialization
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // ==========================================
    // SECTION 3: TESTIMONIALS, LATEST NEWS, PROMO, CTA
    // ==========================================

    /**
     * Scroll animation cho testimonial items
     */
    function initTestimonials() {
        const items = document.querySelectorAll('.testimonial_item_width');
        if (!items.length) return;

        if ('IntersectionObserver' in window) {
            const obs = new IntersectionObserver((entries) => {
                entries.forEach((entry, i) => {
                    if (entry.isIntersecting) {
                        setTimeout(() => {
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0)';
                        }, i * 120);
                        obs.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15 });

            items.forEach(item => {
                item.style.opacity = '0';
                item.style.transform = 'translateY(40px)';
                item.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                obs.observe(item);
            });
        }
    }

    /**
     * Scroll animation cho latest news items
     */
    function initLatestNews() {
        const newsItems = document.querySelectorAll('.lt_news_item');
        if (!newsItems.length) return;

        if ('IntersectionObserver' in window) {
            const obs = new IntersectionObserver((entries) => {
                entries.forEach((entry, i) => {
                    if (entry.isIntersecting) {
                        setTimeout(() => {
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateX(0)';
                        }, i * 100);
                        obs.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });

            newsItems.forEach(item => {
                item.style.opacity = '0';
                item.style.transform = 'translateX(30px)';
                item.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                obs.observe(item);
            });
        }
    }

    /**
     * Xử lý form "Drop a Message" trên trang chủ
     * Gửi AJAX đến api/home-contact.php → email đến contact@mtech.com
     */
    function initHomeContactForm() {
        const form = document.getElementById('homeContactForm');
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const btn = form.querySelector('.submit_btn');
            const originalText = btn.textContent;

            // ── Validate required fields ──────────────────────────────────
            let valid = true;
            form.querySelectorAll('[required]').forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#dc3545';
                    valid = false;
                } else {
                    field.style.borderColor = '';
                }
            });

            // Validate email format
            const emailField = form.querySelector('[name="email"]');
            if (emailField && emailField.value.trim()) {
                const emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailField.value.trim());
                if (!emailOk) {
                    emailField.style.borderColor = '#dc3545';
                    valid = false;
                }
            }

            if (!valid) {
                showFormMessage(form, 'Vui lòng điền đầy đủ thông tin hợp lệ.', 'error');
                return;
            }

            // ── Loading state ─────────────────────────────────────────────
            btn.textContent = 'Sending...';
            btn.disabled = true;

            // ── Gửi đến API endpoint ──────────────────────────────────────
            fetch('?page=home&action=contact-submit', {
                method: 'POST',
                body: new FormData(form)
            })
            .then(res => {
                if (!res.ok && res.status !== 422) {
                    throw new Error('Server error: ' + res.status);
                }
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    form.reset();
                    form.querySelectorAll('.form-control').forEach(f => f.style.borderColor = '');
                    showFormMessage(form, data.message || 'Your message has been sent successfully!', 'success');
                } else {
                    // Highlight các field lỗi nếu server trả về
                    if (data.errors) {
                        Object.keys(data.errors).forEach(fieldName => {
                            const field = form.querySelector('[name="' + fieldName + '"]');
                            if (field) field.style.borderColor = '#dc3545';
                        });
                    }
                    showFormMessage(form, data.message || 'Something went wrong. Please try again.', 'error');
                }
            })
            .catch(() => {
                showFormMessage(form, 'Network error. Please try again.', 'error');
            })
            .finally(() => {
                btn.textContent = originalText;
                btn.disabled = false;
            });
        });

        // Reset border khi user gõ lại
        form.querySelectorAll('.form-control').forEach(field => {
            field.addEventListener('input', function() {
                this.style.borderColor = '';
            });
        });
    }

    /**
     * Hiển thị thông báo sau khi submit form
     */
    function showFormMessage(form, message, type) {
        const old = form.querySelector('.form-feedback');
        if (old) old.remove();

        const div = document.createElement('div');
        div.className = 'form-feedback';
        div.style.cssText = [
            'padding: 12px 18px',
            'margin-top: 12px',
            'border-radius: 4px',
            'font-size: 14px',
            'font-family: Open Sans, sans-serif',
            type === 'success'
                ? 'background:#d4edda;color:#155724;border:1px solid #c3e6cb'
                : 'background:#f8d7da;color:#721c24;border:1px solid #f5c6cb'
        ].join(';');
        div.textContent = message;

        form.appendChild(div);
        setTimeout(() => div.remove(), 5000);
    }

})();
