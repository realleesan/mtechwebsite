/**
 * Footer JavaScript
 * NOTE: Handles newsletter form submission and interactions
 */

(function() {
    'use strict';

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initNewsletterForm();
        updateCopyrightYear();
    });

    /**
     * Update Copyright Year
     * NOTE: Tự động cập nhật năm trong copyright text
     */
    function updateCopyrightYear() {
        var currentYear = new Date().getFullYear();
        var copyrightElement = document.querySelector('.pull-left');

        if (copyrightElement) {
            var yearMatch = copyrightElement.innerHTML.match(/\d{4}/);
            if (yearMatch) {
                copyrightElement.innerHTML = copyrightElement.innerHTML.replace(yearMatch[0], currentYear);
            }
        }
    }

    /**
     * Initialize Newsletter Form
     * NOTE: Xử lý form đăng ký newsletter trong footer
     */
    function initNewsletterForm() {
        var form = document.getElementById('footer-newsletter-form');
        
        if (!form) {
            return;
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            var emailInput = form.querySelector('input[name="email"]');
            var responseDiv = form.querySelector('.newsletter-response');
            var email = emailInput.value.trim();

            // Validate email
            if (!email || !isValidEmail(email)) {
                showResponse(responseDiv, 'Please enter a valid email address.', 'error');
                return;
            }

            // NOTE: Thay đổi URL endpoint để xử lý newsletter
            var endpoint = 'api/newsletter/subscribe';
            
            // Submit form data
            submitNewsletter(email, endpoint, responseDiv, emailInput);
        });
    }

    /**
     * Submit Newsletter Subscription
     * @param {string} email - Email address
     * @param {string} endpoint - API endpoint
     * @param {Element} responseDiv - Response container
     * @param {Element} emailInput - Email input field
     */
    function submitNewsletter(email, endpoint, responseDiv, emailInput) {
        var formData = new FormData();
        formData.append('email', email);

        // NOTE: Uncomment khi có API endpoint
        /*
        fetch(endpoint, {
            method: 'POST',
            body: formData
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                showResponse(responseDiv, data.message || 'Thank you for subscribing!', 'success');
                emailInput.value = '';
            } else {
                showResponse(responseDiv, data.message || 'An error occurred. Please try again.', 'error');
            }
        })
        .catch(function(error) {
            console.error('Newsletter error:', error);
            showResponse(responseDiv, 'An error occurred. Please try again.', 'error');
        });
        */

        // Temporary mock response
        showResponse(responseDiv, 'Thank you for subscribing! (Demo mode)', 'success');
        emailInput.value = '';
    }

    /**
     * Show Response Message
     * @param {Element} container - Response container
     * @param {string} message - Message to display
     * @param {string} type - Message type (success/error)
     */
    function showResponse(container, message, type) {
        if (!container) {
            return;
        }

        container.textContent = message;
        container.className = 'newsletter-response ' + type;
        container.style.display = 'block';

        // Hide after 5 seconds
        setTimeout(function() {
            container.style.display = 'none';
        }, 5000);
    }

    /**
     * Validate Email Address
     * @param {string} email - Email to validate
     * @returns {boolean} - True if valid
     */
    function isValidEmail(email) {
        var pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return pattern.test(email);
    }

    /**
     * Smooth Scroll to Top
     * NOTE: Thêm chức năng scroll to top nếu cần
     */
    window.scrollToTop = function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    };

})();
