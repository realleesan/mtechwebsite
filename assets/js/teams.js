/**
 * Teams Page JavaScript
 * Xử lý validation và AJAX submit cho Question form
 */

document.addEventListener('DOMContentLoaded', function () {
    initQuestionForm();
    initTeamHoverEffects();
});

// ----------------------------------------------------------------
// Question Form
// ----------------------------------------------------------------

function initQuestionForm() {
    const form = document.getElementById('questionForm');
    if (!form) return;

    form.addEventListener('submit', handleQuestionSubmit);

    // Real-time validation
    form.querySelectorAll('.form-control').forEach(function (input) {
        input.addEventListener('blur', validateQuestionField);
        input.addEventListener('input', function () {
            if (input.classList.contains('is-invalid')) {
                clearQuestionFieldError(input);
            }
        });
    });
}

/**
 * Xử lý submit form câu hỏi
 */
function handleQuestionSubmit(e) {
    e.preventDefault();

    const form = e.target;
    const submitBtn = form.querySelector('input[type="submit"], button[type="submit"]');

    if (!validateQuestionForm(form)) {
        return;
    }

    // Loading state
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.value = 'Đang gửi...';
    }

    const formData = new FormData(form);

    fetch('?page=teams&action=submit_question', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(function (response) { return response.json(); })
        .then(function (data) {
            if (data.success) {
                showQuestionMessage(data.message || 'Câu hỏi của bạn đã được gửi thành công!', 'success');
                form.reset();
                clearAllQuestionErrors(form);
            } else {
                showQuestionMessage(data.message || 'Có lỗi xảy ra. Vui lòng thử lại.', 'error');

                if (data.errors) {
                    Object.keys(data.errors).forEach(function (field) {
                        showQuestionFieldError(field, data.errors[field]);
                    });
                }
            }
        })
        .catch(function (error) {
            console.error('Question form error:', error);
            showQuestionMessage('Có lỗi kết nối. Vui lòng thử lại sau.', 'error');
        })
        .finally(function () {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.value = 'Submit now';
            }
        });
}

/**
 * Validate toàn bộ form
 */
function validateQuestionForm(form) {
    var isValid = true;

    form.querySelectorAll('[required]').forEach(function (field) {
        if (!validateQuestionField({ target: field })) {
            isValid = false;
        }
    });

    return isValid;
}

/**
 * Validate một field
 */
function validateQuestionField(e) {
    var field = e.target;
    var value = field.value.trim();
    var name  = field.name;

    if (field.hasAttribute('required') && !value) {
        showQuestionFieldError(name, getQuestionFieldLabel(name) + ' là bắt buộc');
        return false;
    }

    if (field.type === 'email' && value) {
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
            showQuestionFieldError(name, 'Email không hợp lệ');
            return false;
        }
    }

    if (name === 'message' && value && value.length < 10) {
        showQuestionFieldError(name, 'Nội dung phải có ít nhất 10 ký tự');
        return false;
    }

    clearQuestionFieldError(field);
    return true;
}

/**
 * Hiển thị lỗi cho field theo name
 */
function showQuestionFieldError(fieldName, message) {
    var field = document.querySelector('#questionForm [name="' + fieldName + '"]');
    if (!field) return;

    field.classList.add('is-invalid');
    field.classList.remove('is-valid');

    var feedback = field.parentElement.querySelector('.invalid-feedback');
    if (!feedback) {
        feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        field.parentElement.appendChild(feedback);
    }
    feedback.textContent = message;
}

/**
 * Xóa lỗi của một field element
 */
function clearQuestionFieldError(field) {
    field.classList.remove('is-invalid');
    field.classList.add('is-valid');

    var feedback = field.parentElement.querySelector('.invalid-feedback');
    if (feedback) feedback.textContent = '';
}

/**
 * Xóa tất cả lỗi trong form
 */
function clearAllQuestionErrors(form) {
    form.querySelectorAll('.form-control').forEach(function (field) {
        field.classList.remove('is-invalid', 'is-valid');
    });
    form.querySelectorAll('.invalid-feedback').forEach(function (el) {
        el.textContent = '';
    });
}

/**
 * Hiển thị flash message
 */
function showQuestionMessage(message, type) {
    document.querySelectorAll('.question-flash-message').forEach(function (el) { el.remove(); });

    var icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
    var msgEl = document.createElement('div');
    msgEl.className = 'flash-message flash-' + type + ' question-flash-message';
    msgEl.innerHTML =
        '<i class="fa fa-' + icon + '"></i> ' + message +
        '<button class="flash-close" onclick="this.parentElement.remove()">&times;</button>';

    var form = document.getElementById('questionForm');
    if (form) {
        form.parentElement.insertBefore(msgEl, form);
        // Scroll vào vị trí thông báo
        msgEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    setTimeout(function () { msgEl.remove(); }, 6000);
}

/**
 * Label tiếng Việt cho từng field
 */
function getQuestionFieldLabel(name) {
    var labels = {
        email:   'Email',
        subject: 'Tiêu đề',
        message: 'Nội dung câu hỏi'
    };
    return labels[name] || name;
}

// ----------------------------------------------------------------
// Team member hover effects
// ----------------------------------------------------------------

function initTeamHoverEffects() {
    document.querySelectorAll('.team_member').forEach(function (member) {
        member.addEventListener('mouseenter', function () { this.style.zIndex = '10'; });
        member.addEventListener('mouseleave', function () { this.style.zIndex = '1'; });
    });
}
