    <!-- Newsletter -->
    <section class="newsletter-section">
        <div class="container footer-newsletter">
            <h2 class="footer-cta-title">Đăng ký nhận tin</h2>
            <p class="section-subtitle">Nhận thông báo về sách mới và ưu đãi đặc biệt.</p>
            <form class="newsletter-form" action="#" method="POST">
                <input type="email" placeholder="Nhập email của bạn" required>
                <button type="submit"><i class="fas fa-paper-plane"></i></button>
            </form>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="index.php" class="logo">
                        <div class="logo-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                        MÂY MƠ <span>BOOK</span>
                    </a>
                    <p>Nền tảng thuê sách trực tuyến hàng đầu Việt Nam. Tiếp cận hàng ngàn đầu sách với chi phí hợp lý.</p>
                    <div class="footer-social">
                        <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" title="YouTube"><i class="fab fa-youtube"></i></a>
                        <a href="#" title="TikTok"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
                
                <div>
                    <h4>Quick Links</h4>
                    <div class="footer-links">
                        <a href="index.php"><i class="fas fa-chevron-right"></i> Trang Chủ</a>
                        <a href="books.php"><i class="fas fa-chevron-right"></i> Sách</a>
                        <a href="about.php"><i class="fas fa-chevron-right"></i> Về Chúng Tôi</a>
                        <a href="contact.php"><i class="fas fa-chevron-right"></i> Liên Hệ</a>
                    </div>
                </div>
                
                <div>
                    <h4>Hỗ Trợ</h4>
                    <div class="footer-links">
                        <a href="faq.php"><i class="fas fa-chevron-right"></i> Câu Hỏi Thường Gặp</a>
                        <a href="contact.php"><i class="fas fa-chevron-right"></i> Liên Hệ Hỗ Trợ</a>
                        <a href="#"><i class="fas fa-chevron-right"></i> Chính Sách Đổi Trả</a>
                        <a href="#"><i class="fas fa-chevron-right"></i> Điều Khoản Sử Dụng</a>
                    </div>
                </div>
                
                <div class="footer-contact">
                    <h4>Liên Hệ</h4>
                    <p><i class="fas fa-map-marker-alt"></i> 123 Đường ABC, Quận 1, TP.HCM</p>
                    <p><i class="fas fa-phone"></i> Hotline: 1900 1234</p>
                    <p><i class="fas fa-envelope"></i> support@maymobook.com</p>
                    <p><i class="fas fa-clock"></i> Thứ 2 - Thứ 6: 8:00 - 18:00</p>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> MÂY MƠ BOOK. Tất cả quyền được bảo lưu. | Made with <i class="fas fa-heart" style="color: var(--danger);"></i> in Vietnam</p>
            </div>
        </div>
    </footer>
    
    <!-- Quick View Modal -->
    <div id="quickViewModal" class="modal-overlay">
        <div class="quick-view-modal">
            <button class="modal-close"><i class="fas fa-times"></i></button>
            <div class="quick-view-content">
                <div class="quick-view-image">
                    <img src="" alt="" class="qv-image">
                </div>
                <div class="quick-view-info">
                    <h2 class="qv-title"></h2>
                    <p class="qv-author"></p>
                    <p class="qv-price"></p>
                    <p class="qv-description"></p>
                    <a href="" class="btn btn-primary btn-lg qv-link">
                        <i class="fas fa-book"></i> Xem Chi Tiết
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Toast Container -->
    <div id="toastContainer" class="toast-container"></div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>
