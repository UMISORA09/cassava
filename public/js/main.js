/**
 * Cassava - Main JavaScript
 */

// Format price to Vietnamese currency
function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND',
        minimumFractionDigits: 0
    }).format(price);
}

// Show notification
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification alert-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 6px;
        z-index: 1000;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Form validation
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validatePhone(phone) {
    const re = /^[\d\s\-\+\(\)]{10,}$/;
    return re.test(phone);
}

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.querySelector('.search-form');
    
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const input = this.querySelector('input[name="search"]');
            if (!input.value.trim()) {
                e.preventDefault();
                showNotification('Vui lòng nhập từ khóa tìm kiếm', 'warning');
            }
        });
    }

    // Contact form validation
    const contactForms = document.querySelectorAll('form[name="contact"]');
    contactForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const phone = this.querySelector('input[name="phone"]');
            
            if (!phone || !phone.value.trim()) {
                e.preventDefault();
                showNotification('Vui lòng nhập số điện thoại', 'error');
                return;
            }

            if (!validatePhone(phone.value)) {
                e.preventDefault();
                showNotification('Số điện thoại không hợp lệ', 'error');
                return;
            }
        });
    });

    // Register form validation
    const registerForms = document.querySelectorAll('form[method="POST"]');
    registerForms.forEach(form => {
        if (form.querySelector('input[name="password_confirm"]')) {
            form.addEventListener('submit', function(e) {
                const email = this.querySelector('input[name="email"]');
                const password = this.querySelector('input[name="password"]');
                const passwordConfirm = this.querySelector('input[name="password_confirm"]');

                if (email && !validateEmail(email.value)) {
                    e.preventDefault();
                    showNotification('Email không hợp lệ', 'error');
                    return;
                }

                if (password && password.value.length < 6) {
                    e.preventDefault();
                    showNotification('Mật khẩu phải có ít nhất 6 ký tự', 'error');
                    return;
                }

                if (password && passwordConfirm && password.value !== passwordConfirm.value) {
                    e.preventDefault();
                    showNotification('Mật khẩu xác nhận không khớp', 'error');
                    return;
                }
            });
        }
    });
});

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href !== '#') {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        }
    });
});

// Mobile menu toggle (if needed)
function toggleMobileMenu() {
    const menu = document.querySelector('.navbar-menu');
    if (menu) {
        menu.classList.toggle('active');
    }
}

// Lazy loading images
if ('IntersectionObserver' in window) {
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                imageObserver.unobserve(img);
            }
        });
    });
    images.forEach(img => imageObserver.observe(img));
}

console.log('Cassava initialized');
