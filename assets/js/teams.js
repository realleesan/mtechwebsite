/**
 * Teams Page JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Question form handling
    const questionForm = document.querySelector('.question_form');
    
    if (questionForm) {
        const form = questionForm.querySelector('form');
        const submitBtn = questionForm.querySelector('.submit_btn');
        
        if (submitBtn && !form) {
            // If there's no form element, create one
            submitBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                const email = document.getElementById('email');
                const subject = document.getElementById('text');
                const message = document.getElementById('message');
                
                // Validate
                if (!email || !email.value.trim()) {
                    alert('Please enter your email address');
                    if (email) email.focus();
                    return;
                }
                
                if (!subject || !subject.value.trim()) {
                    alert('Please enter a subject');
                    if (subject) subject.focus();
                    return;
                }
                
                if (!message || !message.value.trim()) {
                    alert('Please enter your question');
                    if (message) message.focus();
                    return;
                }
                
                // Email validation
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email.value.trim())) {
                    alert('Please enter a valid email address');
                    email.focus();
                    return;
                }
                
                // Show success message (in real app, would send to server)
                alert('Thank you! Your question has been submitted successfully.');
                
                // Reset form
                if (email) email.value = '';
                if (subject) subject.value = '';
                if (message) message.value = '';
            });
        }
    }
    
    // Team member hover effect enhancement
    const teamMembers = document.querySelectorAll('.team_member');
    
    teamMembers.forEach(member => {
        member.addEventListener('mouseenter', function() {
            this.style.zIndex = '10';
        });
        
        member.addEventListener('mouseleave', function() {
            this.style.zIndex = '1';
        });
    });
    
    // Smooth scroll for any anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
});