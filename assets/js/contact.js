/**
 * Contact Page JavaScript
 * Xử lý validation và AJAX submit cho form liên hệ
 */

document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    
    if (contactForm) {
        // Form validation và submit
        contactForm.addEventListener('submit', handleFormSubmit);
        
        // Real-time validation
        const inputs = contactForm.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('blur', validateField);
            input.addEventListener('input', clearError);
        });
    }
});

/**
 * Xử lý submit form
 */
function handleFormSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
    
    // Validate all fields
    if (!validateForm(form)) {
        showMessage('Vui lòng kiểm tra lại thông tin nhập.', 'error');
        return;
    }
    
    // Show loading state
    if (submitBtn) {
        submitBtn.classList.add('btn-loading');
        submitBtn.disabled = true;
    }
    
    // Prepare data
    const formData = new FormData(form);
    
    // Submit via AJAX
    fetch('?page=contact&action=submit', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message || 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất.', 'success');
            form.reset();
            clearAllErrors(form);
        } else {
            showMessage(data.message || 'Có lỗi xảy ra. Vui lòng thử lại.', 'error');
            
            // Show field errors if any
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    showFieldError(field, data.errors[field]);
                });
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Có lỗi kết nối. Vui lòng thử lại sau.', 'error');
    })
    .finally(() => {
        // Remove loading state
        if (submitBtn) {
            submitBtn.classList.remove('btn-loading');
            submitBtn.disabled = false;
        }
    });
}

/**
 * Validate toàn bộ form
 */
function validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');
    
    requiredFields.forEach(field => {
        if (!validateField({ target: field })) {
            isValid = false;
        }
    });
    
    // Validate email format
    const emailField = form.querySelector('input[type="email"]');
    if (emailField && emailField.value) {
        if (!isValidEmail(emailField.value)) {
            showFieldError(emailField.name, 'Email không hợp lệ');
            isValid = false;
        }
    }
    
    // Validate phone if provided
    const phoneField = form.querySelector('input[type="tel"]');
    if (phoneField && phoneField.value) {
        if (!isValidPhone(phoneField.value)) {
            showFieldError(phoneField.name, 'Số điện thoại không hợp lệ');
            isValid = false;
        }
    }
    
    return isValid;
}

/**
 * Validate một field
 */
function validateField(e) {
    const field = e.target;
    const value = field.value.trim();
    const fieldName = field.name;
    
    // Check required
    if (field.hasAttribute('required') && !value) {
        showFieldError(fieldName, getFieldLabel(fieldName) + ' là bắt buộc');
        return false;
    }
    
    // Check email format
    if (field.type === 'email' && value) {
        if (!isValidEmail(value)) {
            showFieldError(fieldName, 'Email không hợp lệ');
            return false;
        }
    }
    
    // Check phone format
    if (field.type === 'tel' && value) {
        if (!isValidPhone(value)) {
            showFieldError(fieldName, 'Số điện thoại không hợp lệ');
            return false;
        }
    }
    
    // Check min length for message
    if (fieldName === 'message' && value.length < 10) {
        showFieldError(fieldName, 'Nội dung phải có ít nhất 10 ký tự');
        return false;
    }
    
    // Valid
    clearFieldError(field);
    return true;
}

/**
 * Clear error khi user đang nhập
 */
function clearError(e) {
    const field = e.target;
    if (field.classList.contains('is-invalid')) {
        clearFieldError(field);
    }
}

/**
 * Hiển thị lỗi cho field
 */
function showFieldError(fieldName, message) {
    const field = document.querySelector('[name="' + fieldName + '"]');
    if (!field) return;
    
    field.classList.add('is-invalid');
    field.classList.remove('is-valid');
    
    // Find or create error element
    let errorElement = field.parentElement.querySelector('.invalid-feedback');
    if (!errorElement) {
        errorElement = document.createElement('div');
        errorElement.className = 'invalid-feedback';
        field.parentElement.appendChild(errorElement);
    }
    
    errorElement.textContent = message;
}

/**
 * Clear lỗi của một field
 */
function clearFieldError(field) {
    field.classList.remove('is-invalid');
    field.classList.add('is-valid');
    
    const errorElement = field.parentElement.querySelector('.invalid-feedback');
    if (errorElement) {
        errorElement.textContent = '';
    }
}

/**
 * Clear tất cả lỗi
 */
function clearAllErrors(form) {
    const fields = form.querySelectorAll('.form-control');
    fields.forEach(field => {
        field.classList.remove('is-invalid', 'is-valid');
    });
    
    const errorMessages = form.querySelectorAll('.invalid-feedback');
    errorMessages.forEach(el => el.textContent = '');
}

/**
 * Hiển thị thông báo
 */
function showMessage(message, type) {
    // Remove existing messages
    const existingMessages = document.querySelectorAll('.contact-flash-message');
    existingMessages.forEach(el => el.remove());
    
    // Create message element
    const messageEl = document.createElement('div');
    messageEl.className = 'flash-message flash-' + type + ' contact-flash-message';
    messageEl.innerHTML = `
        <i class="fa fa-${type === 'success' ? 'check' : 'exclamation'}-circle"></i>
        ${message}
        <button class="flash-close" onclick="this.parentElement.remove()">&times;</button>
    `;
    
    // Insert before form
    const form = document.getElementById('contactForm');
    if (form) {
        form.parentElement.insertBefore(messageEl, form);
    }
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        messageEl.remove();
    }, 5000);
}

/**
 * Validate email format
 */
function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Validate phone format (Vietnam)
 */
function isValidPhone(phone) {
    // Support Vietnam phone formats: 03, 05, 07, 08, 09 + 8 digits
    const re = /^(0[3|5|7|8|9])+([0-9]{8})$/;
    // Or international format
    const reIntl = /^\+?[1-9]\d{1,14}$/;
    return re.test(phone.replace(/\s/g, '')) || reIntl.test(phone.replace(/\s/g, ''));
}

/**
 * Get field label
 */
function getFieldLabel(fieldName) {
    const labels = {
        'name': 'Họ tên',
        'email': 'Email',
        'phone': 'Số điện thoại',
        'message': 'Nội dung'
    };
    return labels[fieldName] || fieldName;
}

/**
 * Re-initialize form sau khi có lỗi server
 */
function reinitializeForm() {
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        const inputs = contactForm.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('blur', validateField);
            input.addEventListener('input', clearError);
        });
    }
}

// Export functions for global access
window.ContactPage = {
    validateForm,
    showMessage,
    reinitializeForm
};
