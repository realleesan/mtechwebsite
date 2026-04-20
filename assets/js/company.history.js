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
    
    historyItems.forEach(item => {
        // Set initial state
        item.style.opacity = '0';
        item.style.transform = 'translateY(30px)';
        item.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        
        observer.observe(item);
    });
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