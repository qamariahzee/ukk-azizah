/**
 * Custom JavaScript - Inventaris fakhri
 * UKK Level 3 Web Development
 */

// ========================================
// REALTIME CLOCK
// ========================================
function updateRealtimeClock() {
    const clockElement = document.getElementById('realtime-clock');
    if (clockElement) {
        const now = new Date();
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        };
        clockElement.textContent = now.toLocaleString('id-ID', options);
    }
}

// Update clock setiap detik
setInterval(updateRealtimeClock, 1000);
updateRealtimeClock(); // Initial call

// ========================================
// SWEETALERT HELPERS
// ========================================
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

// Show success toast
function showSuccess(message) {
    Toast.fire({
        icon: 'success',
        title: message
    });
}

// Show error toast
function showError(message) {
    Toast.fire({
        icon: 'error',
        title: message
    });
}

// Confirm delete
function confirmDelete(title, text) {
    return Swal.fire({
        title: title || 'Yakin hapus?',
        text: text || 'Data yang dihapus tidak bisa dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '🗑️ Ya, Hapus!',
        cancelButtonText: 'Batal'
    });
}

// ========================================
// FORMAT HELPERS
// ========================================
// Format number as Rupiah
function formatRupiah(number) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(number);
}

// Format number with thousand separator
function formatNumber(number) {
    return new Intl.NumberFormat('id-ID').format(number);
}

// ========================================
// FORM VALIDATION
// ========================================
// Validate required fields
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;

    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });

    if (!isValid) {
        showError('Mohon lengkapi semua field yang wajib diisi!');
    }

    return isValid;
}

// Remove invalid class on input
document.addEventListener('input', function (e) {
    if (e.target.classList.contains('is-invalid')) {
        e.target.classList.remove('is-invalid');
    }
});

// ========================================
// AUTO-FORMAT INPUT
// ========================================
// Format price input
document.querySelectorAll('input[name="harga"]').forEach(input => {
    input.addEventListener('blur', function () {
        const value = parseFloat(this.value);
        if (!isNaN(value) && value > 0) {
            // Store raw value for form submission
            this.dataset.rawValue = value;
        }
    });
});

// ========================================
// KEYBOARD SHORTCUTS
// ========================================
document.addEventListener('keydown', function (e) {
    // Ctrl + Enter to submit form
    if (e.ctrlKey && e.key === 'Enter') {
        const form = document.querySelector('form');
        if (form) {
            const submitBtn = form.querySelector('[type="submit"]');
            if (submitBtn) {
                submitBtn.click();
            }
        }
    }

    // Escape to go back
    if (e.key === 'Escape') {
        const backBtn = document.querySelector('a[href*="index.php"]');
        if (backBtn) {
            // Don't navigate, just focus the back button
            backBtn.focus();
        }
    }
});

// ========================================
// LOADING STATE
// ========================================
function showLoading(button) {
    if (!button) return;

    button.dataset.originalText = button.innerHTML;
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
    button.disabled = true;
}

function hideLoading(button) {
    if (!button || !button.dataset.originalText) return;

    button.innerHTML = button.dataset.originalText;
    button.disabled = false;
}

// Add loading state to forms
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function (e) {
        const submitBtn = this.querySelector('[type="submit"]');
        if (submitBtn) {
            // Small delay to show loading
            setTimeout(() => showLoading(submitBtn), 100);
        }
    });
});

// ========================================
// INITIALIZATION
// ========================================
document.addEventListener('DOMContentLoaded', function () {
    console.log('🚀 Inventaris fakhri loaded successfully!');

    // Initialize tooltips if Bootstrap is loaded
    if (typeof bootstrap !== 'undefined') {
        const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltips.forEach(el => new bootstrap.Tooltip(el));
    }

    // Auto-focus first input in forms
    const firstInput = document.querySelector('form input:not([type="hidden"]):not([readonly])');
    if (firstInput) {
        firstInput.focus();
    }
});
