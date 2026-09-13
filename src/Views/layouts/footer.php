</main>

<footer class="footer">
    <div class="container footer-grid">
        <div class="footer-brand">
            <a class="logo" href="<?= url('/') ?>">
                <svg class="logo-leaf" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17,8C8,10 5.9,16.17 3.82,21.34L5.71,22L6.58,20C9,20.67 11.6,20 13.6,18.4C16.8,15.83 18.6,12 17,8M15.2,16.3C13.8,17.4 12,17.9 10.2,17.5L14.3,13.4C14.7,13 14.7,12.3 14.3,11.9C13.9,11.5 13.2,11.5 12.8,11.9L8.7,16C8.3,14.2 8.8,12.4 9.9,11C11.5,9 13.5,8.2 15.2,8.1C15.9,11 14.5,14 15.2,16.3Z"/>
                </svg>
                <div class="logo-text">
                    <span class="logo-title">MỘC NHIÊN</span>
                    <span class="logo-subtitle">Stationery & More</span>
                </div>
            </a>
            <p class="brand-desc">Văn phòng phẩm cho những ngày nhiều cảm hứng. Nơi bạn tìm thấy những món đồ nhỏ xinh, mộc mạc phục vụ đắc lực cho học tập, làm việc và sáng tạo.</p>
        </div>
        
        <div class="footer-nav-col">
            <h4>MUA SẮM</h4>
            <ul>
                <li><a href="<?= url('/products') ?>">Sản phẩm</a></li>
                <li><a href="<?= url('/products') ?>">Danh mục</a></li>
                <li><a href="<?= url('/products?promotion=today') ?>">Khuyến mãi</a></li>
                <li><a href="<?= url('/') ?>">Best Seller</a></li>
            </ul>
        </div>
        
        <div class="footer-nav-col">
            <h4>HỖ TRỢ</h4>
            <ul>
                <li><a href="<?= url('/contact') ?>">Liên hệ</a></li>
                <li><a href="<?= url('/shipping-policy') ?>">Chính sách giao hàng</a></li>
                <li><a href="<?= url('/returns') ?>">Đổi trả</a></li>
                <li><a href="<?= url('/payment') ?>">Thanh toán</a></li>
                <li><a href="<?= url('/faq') ?>">FAQ</a></li>
            </ul>
        </div>
        
        <div class="footer-nav-col">
            <h4>THEO DÕI</h4>
            <ul class="social-links">
                <li><a href="#" class="social-link">Facebook</a></li>
                <li><a href="#" class="social-link">Instagram</a></li>
                <li><a href="#" class="social-link">TikTok</a></li>
            </ul>
        </div>
    </div>
    <div class="container copyright-bar">
        <p>© 2026 Mộc Nhiên. All rights reserved.</p>
        <span class="copyright-heart">Made with 💚 for stationery lifestyle</span>
    </div>
</footer>

<script src="<?= url('/public/js/app.js') ?>"></script>
</body>

</html>

