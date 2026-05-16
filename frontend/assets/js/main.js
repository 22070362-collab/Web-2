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
    initAddToCartInterception();
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
        try {
            const rect = btn.getBoundingClientRect();
            createHeartBurst(rect.left + rect.width/2, rect.top + rect.height/2, 'var(--danger)');
        } catch (e) {}
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

function createHeartBurst(cx, cy, color) {
    const count = 6;
    for (let i = 0; i < count; i++) {
        const h = document.createElement('div');
        h.className = 'heart-particle';
        h.style.position = 'fixed';
        h.style.left = (cx - 8) + 'px';
        h.style.top = (cy - 8) + 'px';
        h.style.width = '16px';
        h.style.height = '16px';
        h.style.display = 'flex';
        h.style.alignItems = 'center';
        h.style.justifyContent = 'center';
        h.style.color = color || 'var(--danger)';
        h.style.fontSize = '14px';
        h.style.zIndex = 4000;
        h.style.pointerEvents = 'none';
        h.textContent = '❤';
        document.body.appendChild(h);

        const angle = (Math.PI * 2) * (i / count) + (Math.random() * 0.6 - 0.3);
        const dist = 24 + Math.random() * 24;
        const dx = Math.cos(angle) * dist;
        const dy = Math.sin(angle) * dist - 6;

        const anim = h.animate([
            { transform: 'translate(0px,0px) scale(1)', opacity: 1 },
            { transform: `translate(${dx}px, ${dy}px) scale(0.6)`, opacity: 0 }
        ], { duration: 500 + Math.random() * 200, easing: 'cubic-bezier(0.22,1,0.36,1)', fill: 'forwards' });

        anim.onfinish = () => h.remove();
    }
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
// ADD TO CART (AJAX + FLY-TO-CART ANIMATION)
// =====================================================
function initAddToCartInterception() {
    // Intercept server-side add_to_cart forms (book-detail) and use AJAX + animation
    const addCartInput = document.querySelector('form input[name="action"][value="add_to_cart"]');
    if (!addCartInput) return;

    const form = addCartInput.closest('form');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const params = new URLSearchParams(window.location.search);
        const bookId = params.get('id') || form.querySelector('input[name="book_id"]')?.value;
        const btn = form.querySelector('button');
        addToCartWithAnimation(bookId, btn);
    });
}

function addToCartWithAnimation(bookId, btn) {
    if (!bookId) return;
    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const rentalDaysEl = document.getElementById('rental_days_cart') || document.getElementById('rental_days');
    const rentalDays = rentalDaysEl ? parseInt(rentalDaysEl.value || rentalDaysEl.textContent || '7') : 7;

    const originalContent = btn ? btn.innerHTML : '';
    if (btn) {
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang thêm...';
        btn.disabled = true;
    }

    const imgEl = document.querySelector('.book-detail-image');
    const cartIcon = document.querySelector('.nav-link i.fa-shopping-cart') || document.querySelector('.fa-shopping-cart');
    const cartTarget = cartIcon ? (cartIcon.closest('a') || cartIcon) : null;

    // Build POST payload
    const formData = new FormData();
    formData.append('book_id', bookId);
    formData.append('quantity', 1);
    formData.append('rental_days', rentalDays);

    fetch('../backend/api/api.php?action=add_to_cart', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(async result => {
        if (result && result.success) {
            // Update cart count if present
            const cartCountEl = document.querySelector('.cart-count');
            if (cartCountEl) {
                cartCountEl.textContent = result.count ?? result.cartCount ?? result.cartCount ?? cartCountEl.textContent;
                cartCountEl.classList.add('bump');
                setTimeout(() => cartCountEl.classList.remove('bump'), 400);
            }

            if (!prefersReduced && imgEl && cartTarget) {
                await animateFlyToCart(imgEl, cartTarget);
                // particle burst at cart
                const rect = (cartTarget.getBoundingClientRect && cartTarget.getBoundingClientRect()) || { left: window.innerWidth - 40, top: window.innerHeight - 40, width: 24, height: 24 };
                createParticleBurst(rect.left + rect.width / 2, rect.top + rect.height / 2, getComputedStyle(document.documentElement).getPropertyValue('--accent') || '#F59E0B');
            }

            showToast('Đã thêm vào giỏ hàng', 'success');
        } else {
            showToast(result.message || 'Không thể thêm vào giỏ', 'error');
            if (btn) btn.innerHTML = originalContent;
        }
    })
    .catch(() => {
        showToast('Đã xảy ra lỗi', 'error');
        if (btn) btn.innerHTML = originalContent;
    })
    .finally(() => {
        if (btn) {
            setTimeout(() => {
                btn.innerHTML = originalContent;
                btn.disabled = false;
            }, 600);
        }
    });
}

function animateFlyToCart(imgEl, targetEl) {
    return new Promise((resolve) => {
        try {
            const imgRect = imgEl.getBoundingClientRect();
            const targetRect = targetEl.getBoundingClientRect();

            const clone = imgEl.cloneNode(true);
            clone.style.position = 'fixed';
            clone.style.left = imgRect.left + 'px';
            clone.style.top = imgRect.top + 'px';
            clone.style.width = imgRect.width + 'px';
            clone.style.height = imgRect.height + 'px';
            clone.style.zIndex = 9999;
            clone.style.pointerEvents = 'none';
            clone.style.borderRadius = getComputedStyle(imgEl).borderRadius || '8px';
            clone.style.boxShadow = '0 12px 40px rgba(0,0,0,0.12)';

            document.body.appendChild(clone);

            const dx = (targetRect.left + targetRect.width/2) - (imgRect.left + imgRect.width/2);
            const dy = (targetRect.top + targetRect.height/2) - (imgRect.top + imgRect.height/2);

            // Use Web Animations API if available
            const keyframes = [
                { transform: 'translate(0px, 0px) scale(1) rotate(0deg)', opacity: 1 },
                { transform: `translate(${dx}px, ${dy}px) scale(0.18) rotate(14deg)`, opacity: 0.2 }
            ];

            const timing = { duration: 700, easing: 'cubic-bezier(0.22,1,0.36,1)', fill: 'forwards' };

            if (clone.animate) {
                const anim = clone.animate(keyframes, timing);
                anim.onfinish = () => { clone.remove(); resolve(); };
            } else {
                // Fallback CSS transition
                clone.style.transition = 'transform 700ms cubic-bezier(0.22,1,0.36,1), opacity 700ms ease';
                requestAnimationFrame(() => {
                    clone.style.transform = `translate(${dx}px, ${dy}px) scale(0.18) rotate(14deg)`;
                    clone.style.opacity = '0.2';
                });
                clone.addEventListener('transitionend', () => { clone.remove(); resolve(); }, { once: true });
            }
        } catch (err) {
            resolve();
        }
    });
}

function createParticleBurst(cx, cy, color) {
    const particles = [];
    const count = 8;
    for (let i = 0; i < count; i++) {
        const p = document.createElement('div');
        p.className = 'cart-particle';
        p.style.position = 'fixed';
        p.style.left = (cx - 6) + 'px';
        p.style.top = (cy - 6) + 'px';
        p.style.width = '10px';
        p.style.height = '10px';
        p.style.borderRadius = '50%';
        p.style.background = color || '#F59E0B';
        p.style.zIndex = 4000;
        p.style.pointerEvents = 'none';
        document.body.appendChild(p);

        const angle = (Math.PI * 2) * (i / count) + (Math.random() * 0.6 - 0.3);
        const dist = 30 + Math.random() * 30;
        const dx = Math.cos(angle) * dist;
        const dy = Math.sin(angle) * dist - 8;

        const anim = p.animate([
            { transform: 'translate(0px,0px) scale(1)', opacity: 1 },
            { transform: `translate(${dx}px, ${dy}px) scale(0.2)`, opacity: 0 }
        ], { duration: 600 + Math.random() * 250, easing: 'cubic-bezier(0.22,1,0.36,1)', fill: 'forwards' });

        anim.onfinish = () => p.remove();
        particles.push(p);
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


