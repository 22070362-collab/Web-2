<?php
require_once __DIR__ . '/../templates/header.php';
$pageTitle = 'Câu Hỏi Thường Gặp';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="section-header" style="margin-bottom: 0;">
            <div class="section-header-left">
                <div class="section-icon">
                    <i class="fas fa-question-circle"></i>
                </div>
                <div>
                    <h1 style="font-size: 2rem; margin-bottom: 4px;">Câu Hỏi Thường Gặp</h1>
                    <p style="margin: 0; color: var(--text-muted);">Giải đáp thắc mắc của bạn</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Content -->
<section class="section">
    <div class="container">
        <div class="faq-container">
            <!-- General Questions -->
            <div class="faq-section animate-on-scroll">
                <h2 class="faq-section-title">
                    <i class="fas fa-book"></i> Về Dịch Vụ Thuê Sách
                </h2>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Làm thế nào để thuê sách?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Việc thuê sách rất đơn giản:</p>
                        <ol>
                            <li>Đăng ký tài khoản hoặc đăng nhập</li>
                            <li>Tìm kiếm và chọn sách bạn muốn thuê</li>
                            <li>Chọn thời gian thuê (7, 14 hoặc 30 ngày)</li>
                            <li>Thanh toán và nhận sách</li>
                        </ol>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Thời gian thuê tối thiểu là bao lâu?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Thời gian thuê tối thiểu là 7 ngày. Bạn có thể thuê 7, 14 hoặc 30 ngày tùy theo nhu cầu.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Tôi có thể gia hạn thời gian thuê không?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Có, bạn có thể gia hạn thời gian thuê online bất kỳ lúc nào trong thời gian thuê. Chỉ cần vào mục "Sách Đang Thuê" và chọn "Gia Hạn".</p>
                    </div>
                </div>
            </div>
            
            <!-- Payment Questions -->
            <div class="faq-section animate-on-scroll animate-delay-1">
                <h2 class="faq-section-title">
                    <i class="fas fa-credit-card"></i> Thanh Toán
                </h2>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Các phương thức thanh toán được chấp nhận?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Chúng tôi chấp nhận:</p>
                        <ul>
                            <li>Thẻ ATM / Internet Banking</li>
                            <li>Thẻ Visa, MasterCard</li>
                            <li>Ví điện tử (MoMo, ZaloPay, VNPay)</li>
                            <li>Thanh toán khi nhận hàng (COD)</li>
                        </ul>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Tôi có được hoàn tiền không?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Chúng tôi không hoàn tiền cho các đơn thuê đã xác nhận. Tuy nhiên, nếu sách bị lỗi hoặc giao sai, vui lòng liên hệ hotline để được hỗ trợ.</p>
                    </div>
                </div>
            </div>
            
            <!-- Shipping Questions -->
            <div class="faq-section animate-on-scroll animate-delay-2">
                <h2 class="faq-section-title">
                    <i class="fas fa-truck"></i> Giao Hàng
                </h2>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Phí ship như thế nào?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Phí giao hàng:</p>
                        <ul>
                            <li>Miễn phí cho đơn từ 3 cuốn trở lên</li>
                            <li>25,000đ cho đơn 1-2 cuốn nội thành</li>
                            <li>35,000đ cho đơn 1-2 cuốn ngoại thành</li>
                        </ul>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Thời gian giao hàng bao lâu?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Thời gian giao hàng:</p>
                        <ul>
                            <li>Nội thành TP.HCM, Hà Nội: 1-2 ngày</li>
                            <li>Các tỉnh thành khác: 3-5 ngày</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Return Questions -->
            <div class="faq-section animate-on-scroll animate-delay-3">
                <h2 class="faq-section-title">
                    <i class="fas fa-undo"></i> Đổi Trả
                </h2>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Chính sách đổi trả như thế nào?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Chúng tôi áp dụng chính sách đổi trả trong 7 ngày với các điều kiện:</p>
                        <ul>
                            <li>Sách còn nguyên vẹn, không rách, không ghi chép</li>
                            <li>Có hóa đơn mua hàng</li>
                            <li>Không áp dụng cho sách đã quá hạn trả</li>
                        </ul>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Tôi quá hạn trả sách thì sao?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Nếu bạn trả sách quá hạn, phí thuê sẽ được tính thêm theo ngày vượt hạn với mức giá bằng 1.5 lần giá thuê/ngày. Vui lòng trả đúng hạn để tránh phí phát sinh.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Still Have Questions -->
        <div class="faq-contact animate-on-scroll">
            <div class="faq-contact-content">
                <i class="fas fa-headset"></i>
                <h3>Bạn vẫn có thắc mắc?</h3>
                <p>Liên hệ với chúng tôi qua hotline 1900 1234 hoặc gửi email về support@maymobook.com</p>
                <a href="contact.php" class="btn btn-primary">
                    <i class="fas fa-envelope"></i> Liên Hệ Ngay
                </a>
            </div>
        </div>
    </div>
</section>

<style>
.faq-container {
    max-width: 800px;
    margin: 0 auto;
}

.faq-section {
    margin-bottom: 40px;
}

.faq-section-title {
    font-size: 1.25rem;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid var(--blue-primary);
    display: flex;
    align-items: center;
    gap: 12px;
}

.faq-section-title i {
    color: var(--blue-primary);
}

.faq-item {
    background: white;
    border-radius: var(--radius);
    margin-bottom: 12px;
    border: 1px solid var(--border-color);
    overflow: hidden;
}

.faq-question {
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    font-weight: 600;
    color: var(--text-primary);
    transition: var(--transition);
}

.faq-question:hover {
    background: var(--bg-secondary);
}

.faq-question i {
    color: var(--blue-primary);
    transition: var(--transition);
}

.faq-item.active .faq-question i {
    transform: rotate(180deg);
}

.faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.faq-item.active .faq-answer {
    max-height: 500px;
}

.faq-answer p,
.faq-answer ul,
.faq-answer ol {
    padding: 0 20px 16px;
    color: var(--text-secondary);
    line-height: 1.8;
}

.faq-answer ul,
.faq-answer ol {
    margin: 0;
    padding-left: 24px;
}

.faq-answer li {
    margin-bottom: 8px;
}

.faq-contact {
    background: linear-gradient(135deg, var(--blue-dark) 0%, var(--blue-primary) 100%);
    border-radius: var(--radius-xl);
    padding: 48px;
    text-align: center;
    color: white;
    margin-top: 60px;
}

.faq-contact-content i {
    font-size: 3rem;
    margin-bottom: 16px;
}

.faq-contact-content h3 {
    color: white;
    margin-bottom: 8px;
}

.faq-contact-content p {
    opacity: 0.9;
    margin-bottom: 24px;
}

.faq-contact-content .btn {
    background: white;
    color: var(--blue-primary);
}

.faq-contact-content .btn:hover {
    background: var(--bg-secondary);
}

@media (max-width: 768px) {
    .faq-contact {
        padding: 32px 20px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const faqItems = document.querySelectorAll('.faq-question');
    faqItems.forEach(item => {
        item.addEventListener('click', () => {
            const faqItem = item.parentElement;
            const isActive = faqItem.classList.contains('active');
            
            // Close all
            document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('active'));
            
            // Open clicked if wasn't active
            if (!isActive) {
                faqItem.classList.add('active');
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
