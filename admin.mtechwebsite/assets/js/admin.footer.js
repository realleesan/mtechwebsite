/**
 * Admin Footer Management JavaScript
 * Scripts for footer management pages
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize footer management functionality
    initFooterManagement();
});

/**
 * Initialize footer management features
 */
function initFooterManagement() {
    // Confirm delete dialog
    initDeleteConfirmation();
    
    // Form validation
    initFormValidation();
    
    // Auto-focus on first input field
    autoFocusFirstInput();
}

/**
 * Initialize delete confirmation dialogs
 */
function initDeleteConfirmation() {
    // Add click event listeners to delete buttons
    const deleteButtons = document.querySelectorAll('[onclick*="confirmDelete"]');
    
    deleteButtons.forEach(button => {
        // Remove inline onclick and add event listener
        const onclickAttr = button.getAttribute('onclick');
        if (onclickAttr) {
            button.removeAttribute('onclick');
            
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Extract ID from the original onclick
                const idMatch = onclickAttr.match(/confirmDelete\((\d+)\)/);
                if (idMatch) {
                    const id = idMatch[1];
                    confirmDelete(id);
                }
            });
        }
    });
}

/**
 * Confirm delete action
 * @param {number|string} id - The ID of the item to delete
 */
function confirmDelete(id) {
    if (confirm('Bạn có chắc chắn muốn xóa liên kết này?')) {
        // Redirect to delete URL
        window.location.href = '/footer/delete/' + id;
    }
}

/**
 * Initialize form validation
 */
function initFormValidation() {
    const forms = document.querySelectorAll('.admin-form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(form)) {
                e.preventDefault();
                return false;
            }
        });
        
        // Add real-time validation
        const inputs = form.querySelectorAll('input[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(input);
            });
            
            input.addEventListener('input', function() {
                // Clear error message when user starts typing
                clearFieldError(input);
            });
        });
    });
}

/**
 * Validate entire form
 * @param {HTMLFormElement} form - The form to validate
 * @returns {boolean} - True if form is valid
 */
function validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('input[required]');
    
    requiredFields.forEach(field => {
        if (!validateField(field)) {
            isValid = false;
        }
    });
    
    return isValid;
}

/**
 * Validate individual field
 * @param {HTMLInputElement} field - The field to validate
 * @returns {boolean} - True if field is valid
 */
function validateField(field) {
    const value = field.value.trim();
    let isValid = true;
    let errorMessage = '';
    
    // Check if required field is empty
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = 'Trường này là bắt buộc';
    }
    
    // URL validation
    if (field.type === 'url' && value) {
        try {
            new URL(value);
        } catch (e) {
            isValid = false;
            errorMessage = 'URL không hợp lệ. Vui lòng nhập URL đầy đủ (http:// hoặc https://)';
        }
    }
    
    // Number validation
    if (field.type === 'number' && value) {
        const num = parseInt(value);
        const min = field.getAttribute('min');
        
        if (min && num < parseInt(min)) {
            isValid = false;
            errorMessage = `Giá trị tối thiểu là ${min}`;
        }
    }
    
    // Show or hide error message
    if (isValid) {
        clearFieldError(field);
    } else {
        showFieldError(field, errorMessage);
    }
    
    return isValid;
}

/**
 * Show field error
 * @param {HTMLInputElement} field - The field with error
 * @param {string} message - Error message
 */
function showFieldError(field, message) {
    clearFieldError(field);
    
    field.classList.add('is-invalid');
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback';
    errorDiv.textContent = message;
    
    field.parentNode.appendChild(errorDiv);
}

/**
 * Clear field error
 * @param {HTMLInputElement} field - The field to clear error from
 */
function clearFieldError(field) {
    field.classList.remove('is-invalid');
    
    const errorDiv = field.parentNode.querySelector('.invalid-feedback');
    if (errorDiv) {
        errorDiv.remove();
    }
}

/**
 * Auto-focus on first input field
 */
function autoFocusFirstInput() {
    const firstInput = document.querySelector('input[type="text"], input[type="url"], input[type="email"]');
    if (firstInput && !firstInput.value) {
        setTimeout(() => {
            firstInput.focus();
        }, 100);
    }
}

/**
 * Utility function to show loading state
 * @param {HTMLButtonElement} button - The button to show loading on
 */
function showButtonLoading(button) {
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Đang xử lý...';
    
    return function restoreButton() {
        button.disabled = false;
        button.innerHTML = originalText;
    };
}

/**
 * Handle AJAX form submission (for future enhancement)
 * @param {HTMLFormElement} form - The form to submit via AJAX
 */
function handleAjaxFormSubmit(form) {
    const submitButton = form.querySelector('button[type="submit"]');
    const restoreButton = showButtonLoading(submitButton);
    
    // This is a placeholder for future AJAX functionality
    // For now, we'll just simulate loading and then submit normally
    setTimeout(() => {
        restoreButton();
        form.submit();
    }, 500);
}

/**
 * Initialize tooltips and other UI components
 */
function initializeUIComponents() {
    // Initialize Bootstrap tooltips if needed
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    if (tooltipTriggerList.length > 0 && window.bootstrap) {
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new window.bootstrap.Tooltip(tooltipTriggerEl));
    }
}

// Initialize UI components when DOM is ready
document.addEventListener('DOMContentLoaded', initializeUIComponents);
