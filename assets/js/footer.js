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
     * NOTE: Xử lý form đăng ký newsletter trong footer - CẬP NHẬT MỚI
     */
    function initNewsletterForm() {
        // Tìm form mới với ID newsletterForm
        var form = document.getElementById('newsletterForm');
        
        if (!form) {
            // Fallback: tìm form cũ nếu có
            form = document.getElementById('footer-newsletter-form');
        }

        if (!form) {
            return;
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var emailInput = form.querySelector('input[name="email"]');
            var email = emailInput.value.trim();
            var submitBtn = form.querySelector('button[type="submit"]');

            // Validate email - chặt chẽ hơn
            if (!email || !isValidEmailStrict(email)) {
                showNewsletterMessage('Email không hợp lệ, vui lòng thử lại.', 'error');
                return false;
            }

            // Loading state
            if (submitBtn) {
                submitBtn.disabled = true;
                var img = submitBtn.querySelector('img');
                if (img) {
                    img.style.opacity = '0.5';
                }
            }

            // Submit via AJAX
            submitNewsletter(email, emailInput, submitBtn);
            
            return false;
        });
    }

    /**
     * Submit Newsletter Subscription - CẬP NHẬT MỚI
     * @param {string} email - Email address
     * @param {Element} emailInput - Email input field
     * @param {Element} submitBtn - Submit button
     */
    function submitNewsletter(email, emailInput, submitBtn) {
        var formData = new FormData();
        formData.append('email', email);

        fetch('/newsletter/subscribe', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(response) {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                showNewsletterMessage(data.message || 'Đăng ký thành công!', 'success');
                emailInput.value = '';
            } else {
                showNewsletterMessage(data.message || 'Email không hợp lệ, vui lòng thử lại.', 'error');
            }
        })
        .catch(function(error) {
            console.error('Newsletter error:', error);
            showNewsletterMessage('Email không hợp lệ, vui lòng thử lại.', 'error');
        })
        .finally(function() {
            // Remove loading state
            if (submitBtn) {
                submitBtn.disabled = false;
                var img = submitBtn.querySelector('img');
                if (img) {
                    img.style.opacity = '1';
                }
            }
        });
    }

    /**
     * Show Newsletter Message - POPUP GIỐNG CONTACT FORMS
     * @param {string} message - Message to display
     * @param {string} type - Message type (success/error)
     */
    function showNewsletterMessage(message, type) {
        // Remove existing messages
        var existingMessages = document.querySelectorAll('.newsletter-flash-message');
        existingMessages.forEach(function(el) { 
            el.remove(); 
        });

        // Create message element
        var messageEl = document.createElement('div');
        
        if (type === 'success') {
            // Popup xanh nhạt giống contact.php
            messageEl.className = 'newsletter-flash-message flash-success';
            messageEl.innerHTML = '<i class="fa fa-check-circle"></i> ' + message + 
                                 '<button class="flash-close" onclick="this.parentElement.remove()">&times;</button>';
        } else {
            // Popup hồng nhạt giống home.php
            messageEl.className = 'newsletter-flash-message flash-error';
            messageEl.innerHTML = '<i class="fa fa-exclamation-circle"></i> ' + message;
        }

        // Insert after form
        var form = document.getElementById('newsletterForm');
        if (form) {
            form.parentElement.insertBefore(messageEl, form.nextSibling);
            
            // Scroll to message
            messageEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        // Auto remove after 5 seconds
        setTimeout(function() {
            messageEl.remove();
        }, 5000);
    }

    /**
     * Show Response Message - GIỮ NGUYÊN CHO TƯƠNG THÍCH
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
     * Validate Email Address - GIỮ NGUYÊN
     * @param {string} email - Email to validate
     * @returns {boolean} - True if valid
     */
    function isValidEmail(email) {
        var pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return pattern.test(email);
    }

    /**
     * Validate Email Address - CHẶT CHẼ HỚN
     * @param {string} email - Email to validate
     * @returns {boolean} - True if valid
     */
    function isValidEmailStrict(email) {
        // Email validation chặt chẽ
        var emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!emailRegex.test(email)) {
            return false;
        }

        // Kiểm tra domain extension
        var parts = email.split('@');
        if (parts.length === 2) {
            var domain = parts[1];
            var domainParts = domain.split('.');
            if (domainParts.length < 2 || domainParts[domainParts.length - 1].length < 2) {
                return false;
            }
        }

        return true;
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

    // Export for global access
    window.NewsletterForm = {
        validateEmail: isValidEmailStrict,
        showMessage: showNewsletterMessage
    };

})();