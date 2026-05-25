/**
 * MÂY MƠ BOOK - Main JavaScript
 * Interactive features and animations
 */

document.addEventListener('DOMContentLoaded', function() {
    initNavbar();
    initBookCarousel();
    initQuickView();
    initWishlist();
    initLazyLoad();
    initToast();
    initAnimations();
});

// =====================================================
// NAVBAR
// =====================================================
function initNavbar() {
    const navbar = document.querySelector('.navbar');
    if (!navbar) return;

    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // Mobile menu toggle
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', () => {
            navbar.classList.toggle('mobile-open');
        });
    }
}

// =====================================================
// BOOK CAROUSEL
// =====================================================
function initBookCarousel() {
    const carousels = document.querySelectorAll('.books-carousel');
    carousels.forEach(carousel => {
        const container = carousel.querySelector('.books-carousel-track');
        const prevBtn = carousel.querySelector('.carousel-prev');
        const nextBtn = carousel.querySelector('.carousel-next');
        
        if (!container || !prevBtn || !nextBtn) return;

        let scrollAmount = 0;
        const cardWidth = container.querySelector('.book-card')?.offsetWidth + 24 || 244;

        prevBtn.addEventListener('click', () => {
            container.scrollBy({ left: -cardWidth * 2, behavior: 'smooth' });
        });

        nextBtn.addEventListener('click', () => {
            container.scrollBy({ left: cardWidth * 2, behavior: 'smooth' });
        });

        // Update button visibility
        container.addEventListener('scroll', () => {
            const maxScroll = container.scrollWidth - container.clientWidth;
            prevBtn.style.opacity = container.scrollLeft > 10 ? '1' : '0.3';
            nextBtn.style.opacity = container.scrollLeft < maxScroll - 10 ? '1' : '0.3';
        });
    });
}

// =====================================================
// QUICK VIEW MODAL
// =====================================================
function initQuickView() {
    const quickViewBtns = document.querySelectorAll('[data-quick-view]');
    const modal = document.getElementById('quickViewModal');
    if (!modal) return;

    quickViewBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const bookId = btn.dataset.bookId;
            openQuickView(bookId);
        });
    });

    // Close modal
    modal.querySelector('.modal-close')?.addEventListener('click', closeQuickView);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeQuickView();
    });
}

function openQuickView(bookId) {
    const modal = document.getElementById('quickViewModal');
    if (!modal) return;

    // Load book data via AJAX
    fetch(`../backend/api/api.php?action=get_book&id=${bookId}`)
        .then(res => res.json())
        .then(book => {
            if (book) {
                modal.querySelector('.qv-image').src = book.cover_image;
                modal.querySelector('.qv-title').textContent = book.title;
                modal.querySelector('.qv-author').textContent = book.author;
                modal.querySelector('.qv-price').textContent = formatCurrency(book.price_per_day);
                modal.querySelector('.qv-description').textContent = book.description || 'Không có mô tả';
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        })
        .catch(err => console.error('Error loading book:', err));
}

function closeQuickView() {
    const modal = document.getElementById('quickViewModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// =====================================================
// WISHLIST
// =====================================================
function initWishlist() {
    const wishlistBtns = document.querySelectorAll('.book-wishlist');
    
    wishlistBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            const bookId = btn.dataset.bookId;
            toggleWishlist(btn, bookId);
        });
    });
}

function toggleWishlist(btn, bookId) {
    const icon = btn.querySelector('i');
    const isActive = btn.classList.contains('active');
    
    if (isActive) {
        btn.classList.remove('active');
        icon.className = 'far fa-heart';
        showToast('Đã xóa khỏi yêu thích', 'info');
    } else {
        btn.classList.add('active');
        icon.className = 'fas fa-heart';
        showToast('Đã thêm vào yêu thích', 'success');
    }
    
    // Save to localStorage
    let wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
    if (isActive) {
        wishlist = wishlist.filter(id => id !== bookId);
    } else {
        wishlist.push(bookId);
    }
    localStorage.setItem('wishlist', JSON.stringify(wishlist));
}

// =====================================================
// LAZY LOADING
// =====================================================
function initLazyLoad() {
    const lazyImages = document.querySelectorAll('img[loading="lazy"]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.classList.add('loaded');
                    observer.unobserve(img);
                }
            });
        });
        
        lazyImages.forEach(img => imageObserver.observe(img));
    } else {
        lazyImages.forEach(img => img.classList.add('loaded'));
    }
}

// =====================================================
// TOAST NOTIFICATIONS
// =====================================================
function initToast() {
    // Auto-dismiss toasts after 3 seconds
    setTimeout(() => {
        document.querySelectorAll('.toast').forEach(toast => {
            toast.classList.add('fade-out');
            setTimeout(() => toast.remove(), 300);
        });
    }, 3000);
}

function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer') || createToastContainer();
    
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;
    
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('fade-out');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'toast-container';
    document.body.appendChild(container);
    return container;
}

// =====================================================
// SCROLL ANIMATIONS
// =====================================================
function initAnimations() {
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        
        animatedElements.forEach(el => observer.observe(el));
    }
}

// =====================================================
// UTILITIES
// =====================================================
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// =====================================================
// BOOK FILTERS & SORT
// =====================================================
function initBookFilters() {
    const filterForm = document.querySelector('.book-filter-form');
    if (!filterForm) return;

    filterForm.addEventListener('change', debounce(() => {
        filterForm.submit();
    }, 500));
}

// =====================================================
// PAGINATION
// =====================================================
function goToPage(page) {
    const url = new URL(window.location);
    url.searchParams.set('page', page);
    window.location.href = url.toString();
}

// =====================================================
// COUPON
// =====================================================
function applyCoupon() {
    const couponInput = document.getElementById('couponCode');
    const code = couponInput?.value.trim();
    if (!code) return;

    fetch('../backend/api/api.php?action=apply_coupon&code=' + encodeURIComponent(code))
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                showToast('Áp dụng mã giảm giá thành công!', 'success');
                // Update total with discount
                const totalEl = document.querySelector('.cart-total');
                if (totalEl) {
                    totalEl.textContent = formatCurrency(result.newTotal);
                }
            } else {
                showToast(result.message || 'Mã giảm giá không hợp lệ', 'error');
            }
        })
        .catch(() => showToast('Đã xảy ra lỗi', 'error'));
}

// =====================================================
// ADD TO CART ANIMATION
// =====================================================
function addToCartWithAnimation(bookId, btn) {
    const originalContent = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang thêm...';
    btn.disabled = true;

    fetch(`../backend/api/api.php?action=add_to_cart&book_id=${bookId}`)
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                btn.innerHTML = '<i class="fas fa-check"></i> Đã thêm!';
                btn.classList.add('btn-success');
                
                // Update cart count
                const cartCount = document.querySelector('.cart-count');
                if (cartCount) {
                    cartCount.textContent = result.cartCount;
                    cartCount.classList.add('bump');
                }
                
                showToast('Đã thêm vào giỏ hàng', 'success');
            } else {
                btn.innerHTML = originalContent;
                showToast(result.message || 'Không thể thêm vào giỏ', 'error');
            }
        })
        .catch(() => {
            btn.innerHTML = originalContent;
            showToast('Đã xảy ra lỗi', 'error');
        });

    setTimeout(() => {
        btn.innerHTML = originalContent;
        btn.disabled = false;
        btn.classList.remove('btn-success');
    }, 2000);
}
