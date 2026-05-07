/**
 * Coming Soon Page JavaScript
 * Countdown timer and subscribe form handling
 */

(function() {
    'use strict';

    // ==========================================
    // Countdown Timer Logic
    // ==========================================
    const ComingSoonTimer = {
        targetDate: null,
        intervalId: null,

        init: function(targetTimestamp) {
            // Target timestamp from PHP (milliseconds)
            this.targetDate = new Date(targetTimestamp);
            this.start();
        },

        start: function() {
            this.update(); // Update immediately
            this.intervalId = setInterval(() => this.update(), 1000);
        },

        stop: function() {
            if (this.intervalId) {
                clearInterval(this.intervalId);
            }
        },

        update: function() {
            const now = new Date().getTime();
            const distance = this.targetDate.getTime() - now;

            if (distance < 0) {
                // Countdown finished
                this.stop();
                this.displayZero();
                return;
            }

            // Calculate time units
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // Update display
            this.updateDisplay('.timer__section.days .timer__number', days);
            this.updateDisplay('.timer__section.hours .timer__number', hours);
            this.updateDisplay('.timer__section.minutes .timer__number', minutes);
            this.updateDisplay('.timer__section.seconds .timer__number', seconds);
        },

        updateDisplay: function(selector, value) {
            const element = document.querySelector(selector);
            if (element) {
                // Add leading zero if needed
                const displayValue = value < 10 ? '0' + value : value;
                element.textContent = displayValue;
            }
        },

        displayZero: function() {
            this.updateDisplay('.timer__section.days .timer__number', 0);
            this.updateDisplay('.timer__section.hours .timer__number', 0);
            this.updateDisplay('.timer__section.minutes .timer__number', 0);
            this.updateDisplay('.timer__section.seconds .timer__number', 0);
        }
    };

    // ==========================================
    // Subscribe Form Handler
    // ==========================================
    const SubscribeForm = {
        form: null,
        messageContainer: null,

        init: function() {
            this.form = document.querySelector('.mailchimp');
            if (this.form) {
                this.form.addEventListener('submit', (e) => this.handleSubmit(e));
            }
        },

        handleSubmit: function(e) {
            e.preventDefault();

            const emailInput = this.form.querySelector('input[type="email"]');
            const submitBtn = this.form.querySelector('button[type="submit"]');
            const email = emailInput.value.trim();

            // Validation
            if (!email || !this.isValidEmail(email)) {
                this.showMessage('Vui lòng nhập email hợp lệ.', 'error');
                return;
            }

            // Show loading state
            this.setLoading(submitBtn, true);
            this.hideMessage();

            // Send AJAX request
            this.submitEmail(email, submitBtn);
        },

        isValidEmail: function(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        },

        submitEmail: function(email, submitBtn) {
            const formData = new FormData();
            formData.append('email', email);

            fetch('?page=comingsoon&action=subscribe', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                this.setLoading(submitBtn, false);

                if (data.success) {
                    this.showMessage(data.message, 'success');
                    this.form.querySelector('input[type="email"]').value = '';
                } else {
                    this.showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                this.setLoading(submitBtn, false);
                this.showMessage('Đã có lỗi xảy ra. Vui lòng thử lại sau.', 'error');
                console.error('Subscribe error:', error);
            });
        },

        setLoading: function(button, isLoading) {
            if (isLoading) {
                button.classList.add('btn-loading');
                button.disabled = true;
            } else {
                button.classList.remove('btn-loading');
                button.disabled = false;
            }
        },

        showMessage: function(message, type) {
            if (!this.messageContainer) {
                this.messageContainer = document.createElement('div');
                this.messageContainer.className = 'comingsoon-message';
                this.form.appendChild(this.messageContainer);
            }

            this.messageContainer.textContent = message;
            this.messageContainer.className = 'comingsoon-message ' + type;
        },

        hideMessage: function() {
            if (this.messageContainer) {
                this.messageContainer.className = 'comingsoon-message';
            }
        }
    };

    // ==========================================
    // Initialize on DOM ready
    // ==========================================
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize countdown timer if target date is set
        const timerElement = document.getElementById('timer');
        if (timerElement && window.comingSoonTargetDate) {
            ComingSoonTimer.init(window.comingSoonTargetDate);
        }

        // Initialize subscribe form
        SubscribeForm.init();
    });

    // Expose to global scope for external access if needed
    window.ComingSoonTimer = ComingSoonTimer;
    window.SubscribeForm = SubscribeForm;

})();
