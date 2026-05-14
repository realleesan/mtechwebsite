/**
 * Admin Header Management JS
 */

document.addEventListener('DOMContentLoaded', function() {
    // Preview Logo Upload
    const logoInput = document.getElementById('logo');
    const logoPreview = document.getElementById('logo-preview-img');

    if (logoInput && logoPreview) {
        logoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    logoPreview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // Auto-fill phone_href from phone
    const phoneInput = document.getElementById('phone');
    const phoneHrefInput = document.getElementById('phone_href');

    if (phoneInput && phoneHrefInput) {
        phoneInput.addEventListener('input', function() {
            // Remove all non-numeric characters for href
            const numericValue = this.value.replace(/\D/g, '');
            phoneHrefInput.value = numericValue;
        });
    }
});
