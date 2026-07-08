/**
 * Company History Page JavaScript
 * Template: docs/template/about/code/company.history.html
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Company History page functionality
    initCompanyHistory();
});

/**
 * Initialize Company History page
 */
function initCompanyHistory() {
    // Animate history items on scroll
    animateHistoryItems();
    
    // Lazy load images
    lazyLoadImages();

    // Initialize Mobile Slider if on mobile viewport
    initMobileSlider();
}

/**
 * Animate history items when they come into viewport
 */
function animateHistoryItems() {
    const historyItems = document.querySelectorAll('.history_item');
    
    if (!historyItems.length) return;
    
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.2
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    const isMobile = window.innerWidth <= 767;
    historyItems.forEach(item => {
        if (isMobile) {
            item.style.opacity = '1';
            item.style.transform = 'none';
        } else {
            // Set initial state
            item.style.opacity = '0';
            item.style.transform = 'translateY(30px)';
            item.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            
            observer.observe(item);
        }
    });
}

/**
 * Initialize Mobile Slider for Company History
 */
function initMobileSlider() {
    const slider = document.querySelector('.history_info');
    if (!slider) return;

    const wrapper = document.querySelector('.history_items_wrapper');
    const slides = document.querySelectorAll('.history_item');
    const paginationContainer = document.querySelector('.history_slider_pagination');
    if (!wrapper || !slides.length || !paginationContainer) return;

    let currentIndex = 0;
    let isDragging = false;
    let startX = 0;
    let currentTranslate = 0;
    let prevTranslate = 0;
    let animationId = 0;
    let autoTimer = null;
    let isMobile = window.innerWidth <= 767;

    // Create pagination bullets
    paginationContainer.innerHTML = '';
    slides.forEach((_, i) => {
        const bullet = document.createElement('span');
        bullet.className = 'history_bullet' + (i === 0 ? ' active' : '');
        bullet.addEventListener('click', () => {
            goToSlide(i);
            resetAutoplay();
        });
        paginationContainer.appendChild(bullet);
    });

    const bullets = paginationContainer.querySelectorAll('.history_bullet');

    function updatePagination() {
        bullets.forEach((bullet, i) => {
            if (i === currentIndex) {
                bullet.classList.add('active');
            } else {
                bullet.classList.remove('active');
            }
        });
    }

    function getPositionX(e) {
        return e.type.includes('mouse') ? e.pageX : e.touches[0].clientX;
    }

    function setSliderPosition() {
        wrapper.style.transform = `translateX(${currentTranslate}px)`;
    }

    function animation() {
        setSliderPosition();
        if (isDragging) requestAnimationFrame(animation);
    }

    function startDrag(e) {
        if (!isMobile) return;
        isDragging = true;
        startX = getPositionX(e);
        clearInterval(autoTimer);
        
        // Disable transitions during drag for smooth tracking
        wrapper.style.transition = 'none';
        
        animationId = requestAnimationFrame(animation);
        
        // Grab style
        slider.style.cursor = 'grabbing';
    }

    function moveDrag(e) {
        if (!isDragging) return;
        const currentX = getPositionX(e);
        const diff = currentX - startX;
        currentTranslate = prevTranslate + diff;
        
        // Add a slight resistance at the boundaries
        const maxTranslate = 0;
        const minTranslate = -((slides.length - 1) * slider.offsetWidth);
        if (currentTranslate > maxTranslate) {
            currentTranslate = maxTranslate + (currentTranslate - maxTranslate) * 0.3;
        } else if (currentTranslate < minTranslate) {
            currentTranslate = minTranslate + (currentTranslate - minTranslate) * 0.3;
        }
    }

    function endDrag() {
        if (!isDragging) return;
        isDragging = false;
        cancelAnimationFrame(animationId);
        slider.style.cursor = '';

        const movedBy = currentTranslate - prevTranslate;
        const threshold = slider.offsetWidth * 0.15; // 15% threshold

        if (movedBy < -threshold && currentIndex < slides.length - 1) {
            currentIndex += 1;
        } else if (movedBy > threshold && currentIndex > 0) {
            currentIndex -= 1;
        }

        goToSlide(currentIndex);
        startAutoplay();
    }

    function goToSlide(index) {
        currentIndex = index;
        const width = slider.offsetWidth;
        currentTranslate = -currentIndex * width;
        prevTranslate = currentTranslate;
        
        wrapper.style.transition = 'transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
        setSliderPosition();
        updatePagination();
    }

    function nextSlide() {
        if (currentIndex < slides.length - 1) {
            goToSlide(currentIndex + 1);
        } else {
            goToSlide(0);
        }
    }

    function startAutoplay() {
        if (!isMobile) return;
        clearInterval(autoTimer);
        autoTimer = setInterval(nextSlide, 3000);
    }

    function resetAutoplay() {
        clearInterval(autoTimer);
        startAutoplay();
    }

    // Touch and mouse events for dragging
    // Mouse events
    slider.addEventListener('mousedown', startDrag);
    slider.addEventListener('mousemove', moveDrag);
    slider.addEventListener('mouseup', endDrag);
    slider.addEventListener('mouseleave', endDrag);

    // Touch events
    slider.addEventListener('touchstart', startDrag, { passive: true });
    slider.addEventListener('touchmove', moveDrag, { passive: true });
    slider.addEventListener('touchend', endDrag);

    // Handle viewport resize
    function handleResize() {
        const wasMobile = isMobile;
        isMobile = window.innerWidth <= 767;

        if (isMobile) {
            if (!wasMobile) {
                // Just transitioned to mobile
                currentIndex = 0;
                goToSlide(0);
                startAutoplay();
            } else {
                // Adjust slide translation on resize
                goToSlide(currentIndex);
            }
        } else {
            // Desktop
            clearInterval(autoTimer);
            wrapper.style.transform = '';
            wrapper.style.transition = '';
            slider.style.cursor = '';
        }
    }

    window.addEventListener('resize', handleResize);

    // Initial check
    if (isMobile) {
        goToSlide(0);
        startAutoplay();
    }
}

/**
 * Lazy load images for better performance
 */
function lazyLoadImages() {
    const images = document.querySelectorAll('.history_img img, .about_history img');
    
    if (!images.length) return;
    
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.style.opacity = '0';
                img.style.transition = 'opacity 0.5s ease';
                
                img.onload = function() {
                    img.style.opacity = '1';
                };
                
                img.onerror = function() {
                    console.warn('Failed to load image:', img.src);
                    img.style.opacity = '1';
                };
                
                // If image is already loaded
                if (img.complete) {
                    img.style.opacity = '1';
                }
                
                imageObserver.unobserve(img);
            }
        });
    });
    
    images.forEach(img => {
        imageObserver.observe(img);
    });
}

/**
 * Smooth scroll to specific history item
 * @param {string} year - The year to scroll to
 */
function scrollToHistoryItem(year) {
    const historyItems = document.querySelectorAll('.history_item');
    
    historyItems.forEach(item => {
        const dateElement = item.querySelector('.history_date');
        if (dateElement && dateElement.textContent.trim() === year.toString()) {
            item.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Add highlight effect
            item.style.boxShadow = '0 0 20px rgba(255, 107, 53, 0.3)';
            setTimeout(() => {
                item.style.boxShadow = '';
                item.style.transition = 'box-shadow 0.3s ease';
            }, 1000);
        }
    });
}