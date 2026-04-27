/**
 * blog.details.js - JavaScript cho trang Blog Details
 * 
 * Chức năng:
 * - Smooth scroll
 * - Image lightbox (optional)
 * - Sidebar sticky (optional)
 * - Reading progress (optional)
 * - Social share (optional)
 */

(function($) {
    'use strict';

    // ============================================================
    // 1. DOCUMENT READY
    // ============================================================
    $(document).ready(function() {
        
        // Initialize all functions
        initSmoothScroll();
        initImageLightbox();
        initSidebarSticky();
        
    });

    // ============================================================
    // 2. SMOOTH SCROLL FOR ANCHOR LINKS
    // ============================================================
    function initSmoothScroll() {
        $('a[href^="#"]').on('click', function(e) {
            const target = $(this.getAttribute('href'));
            
            if (target.length) {
                e.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 80
                }, 800);
            }
        });
    }

    // ============================================================
    // 3. IMAGE LIGHTBOX (Optional - nếu có ảnh trong content)
    // ============================================================
    function initImageLightbox() {
        // Nếu có thư viện lightbox (magnific popup, fancybox, etc.)
        // Uncomment và customize:
        /*
        $('.blog-text img, .blog_post_item img').each(function() {
            const $img = $(this);
            if (!$img.parent('a').length) {
                $img.wrap('<a href="' + $img.attr('src') + '" class="image-popup"></a>');
            }
        });

        if ($.fn.magnificPopup) {
            $('.image-popup').magnificPopup({
                type: 'image',
                gallery: {
                    enabled: true
                }
            });
        }
        */
    }

    // ============================================================
    // 4. STICKY SIDEBAR (Optional)
    // ============================================================
    function initSidebarSticky() {
        const $sidebar = $('.blog_sidebar_area');
        const $mainContent = $('.blog_left_sidebar');
        
        if ($sidebar.length === 0 || $(window).width() < 992) return;

        $(window).on('scroll', function() {
            const scrollTop = $(window).scrollTop();
            const sidebarTop = $sidebar.offset().top;
            const contentHeight = $mainContent.outerHeight();
            const sidebarHeight = $sidebar.outerHeight();
            
            // Simple sticky logic (có thể cải thiện)
            if (scrollTop > sidebarTop - 100 && contentHeight > sidebarHeight) {
                $sidebar.css({
                    'position': 'sticky',
                    'top': '100px'
                });
            }
        });
    }

    // ============================================================
    // 5. READING PROGRESS BAR (Optional)
    // ============================================================
    function initReadingProgress() {
        // Create progress bar
        const $progressBar = $('<div class="reading-progress"><div class="progress-bar"></div></div>');
        $('body').prepend($progressBar);

        // Add CSS for progress bar
        $('<style>')
            .text(`
                .reading-progress {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 3px;
                    background: #e5e5e5;
                    z-index: 9999;
                }
                .reading-progress .progress-bar {
                    height: 100%;
                    background: #1A3FBF;
                    width: 0%;
                    transition: width 0.1s ease;
                }
            `)
            .appendTo('head');

        $(window).on('scroll', function() {
            const windowHeight = $(window).height();
            const documentHeight = $(document).height();
            const scrollTop = $(window).scrollTop();
            const progress = (scrollTop / (documentHeight - windowHeight)) * 100;
            
            $('.progress-bar').css('width', progress + '%');
        });
    }

    // ============================================================
    // 6. SOCIAL SHARE BUTTONS (Optional)
    // ============================================================
    function initSocialShare() {
        // Facebook Share
        $('.share-facebook').on('click', function(e) {
            e.preventDefault();
            const url = encodeURIComponent(window.location.href);
            window.open('https://www.facebook.com/sharer/sharer.php?u=' + url, 
                       'facebook-share', 'width=580,height=296');
        });

        // Twitter Share
        $('.share-twitter').on('click', function(e) {
            e.preventDefault();
            const url = encodeURIComponent(window.location.href);
            const text = encodeURIComponent(document.title);
            window.open('https://twitter.com/intent/tweet?url=' + url + '&text=' + text, 
                       'twitter-share', 'width=550,height=235');
        });

        // LinkedIn Share
        $('.share-linkedin').on('click', function(e) {
            e.preventDefault();
            const url = encodeURIComponent(window.location.href);
            window.open('https://www.linkedin.com/shareArticle?mini=true&url=' + url, 
                       'linkedin-share', 'width=490,height=530');
        });
    }

    // ============================================================
    // 7. PRINT ARTICLE (Optional)
    // ============================================================
    function initPrintButton() {
        $('.print-article').on('click', function(e) {
            e.preventDefault();
            window.print();
        });
    }

    // ============================================================
    // 8. BACK TO TOP BUTTON (Optional)
    // ============================================================
    function initBackToTop() {
        // Create button
        const $backToTop = $('<button class="back-to-top" title="Back to top">↑</button>');
        $('body').append($backToTop);

        // Add CSS
        $('<style>')
            .text(`
                .back-to-top {
                    position: fixed;
                    bottom: 30px;
                    right: 30px;
                    width: 50px;
                    height: 50px;
                    background: #1A3FBF;
                    color: #fff;
                    border: none;
                    border-radius: 50%;
                    font-size: 24px;
                    cursor: pointer;
                    opacity: 0;
                    visibility: hidden;
                    transition: all 0.3s ease;
                    z-index: 999;
                }
                .back-to-top.show {
                    opacity: 1;
                    visibility: visible;
                }
                .back-to-top:hover {
                    background: #1530A0;
                    transform: translateY(-5px);
                }
            `)
            .appendTo('head');

        // Show/hide on scroll
        $(window).on('scroll', function() {
            if ($(window).scrollTop() > 300) {
                $backToTop.addClass('show');
            } else {
                $backToTop.removeClass('show');
            }
        });

        // Scroll to top on click
        $backToTop.on('click', function() {
            $('html, body').animate({ scrollTop: 0 }, 600);
        });
    }

    // ============================================================
    // 9. COPY LINK BUTTON (Optional)
    // ============================================================
    function initCopyLink() {
        $('.copy-link').on('click', function(e) {
            e.preventDefault();
            
            const url = window.location.href;
            
            // Modern clipboard API
            if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(function() {
                    showNotification('Link copied to clipboard!');
                });
            } else {
                // Fallback for older browsers
                const $temp = $('<input>');
                $('body').append($temp);
                $temp.val(url).select();
                document.execCommand('copy');
                $temp.remove();
                showNotification('Link copied to clipboard!');
            }
        });
    }

    // ============================================================
    // 10. SHOW NOTIFICATION (Helper)
    // ============================================================
    function showNotification(message) {
        const $notification = $('<div class="notification">' + message + '</div>');
        
        $('<style>')
            .text(`
                .notification {
                    position: fixed;
                    bottom: 30px;
                    left: 50%;
                    transform: translateX(-50%);
                    background: #1A3FBF;
                    color: #fff;
                    padding: 15px 30px;
                    border-radius: 5px;
                    font-size: 14px;
                    z-index: 9999;
                    animation: slideUp 0.3s ease;
                }
                @keyframes slideUp {
                    from {
                        opacity: 0;
                        transform: translate(-50%, 20px);
                    }
                    to {
                        opacity: 1;
                        transform: translate(-50%, 0);
                    }
                }
            `)
            .appendTo('head');
        
        $('body').append($notification);
        
        setTimeout(function() {
            $notification.fadeOut(function() {
                $(this).remove();
            });
        }, 3000);
    }

})(jQuery);
