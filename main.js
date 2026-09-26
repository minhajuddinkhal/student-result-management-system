// Static/js/main.js

document.addEventListener('DOMContentLoaded', function () {
    // Bangladeshi Phone Number Real-time Validator
    const phoneInputs = document.querySelectorAll('.bd-phone-input');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function () {
            const val = this.value.trim();
            const pattern = /^01[3-9]\d{8}$/;
            const feedback = this.parentElement.querySelector('.phone-feedback');
            
            if (val.length === 11) {
                if (pattern.test(val)) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                    if (feedback) feedback.textContent = 'Valid 11-digit Bangladeshi mobile number';
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                    if (feedback) feedback.textContent = 'Invalid operator prefix (Must start with 013, 014, 015, 016, 017, 018, 019)';
                }
            } else if (val.length > 0) {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
                if (feedback) feedback.textContent = 'Must be exactly 11 digits (' + val.length + '/11)';
            } else {
                this.classList.remove('is-valid', 'is-invalid');
            }
        });
    });

    // Sidebar toggle for mobile
    const toggleBtn = document.getElementById('sidebarToggle');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            document.querySelector('.sidebar').classList.toggle('show');
        });
    }

    // Auto-dismiss alerts after 6 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) bsAlert.close();
        }, 6000);
    });
});