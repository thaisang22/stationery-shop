<?php
/**
 * View Trang Cá Nhân / Profile của User
 * @var array $user
 * @var array $orders
 * @var string|null $successMessage
 * @var string|null $errorMessage
 */

require __DIR__ . '/../layouts/header.php';

$activeTab = $_GET['tab'] ?? 'info';
$initials = mb_strtoupper(mb_substr($user['full_name'] ?? 'U', 0, 1, 'UTF-8'));
?>

<div class="container profile-container">

    <!-- Header Banner Card người dùng -->
    <div class="profile-header-card">
        <div class="profile-user-meta">
            <div class="profile-avatar">
                <?= htmlspecialchars($initials) ?>
            </div>
            <div class="profile-name-group">
                <h1>
                    <?= htmlspecialchars($user['full_name']) ?>
                    <span class="role-badge">
                        <?= ($user['role'] ?? 'CUSTOMER') === 'ADMIN' ? 'QUẢN TRỊ VIÊN' : 'KHÁCH HÀNG' ?>
                    </span>
                </h1>
                <div class="profile-email-text">
                    <?= htmlspecialchars($user['email']) ?>
                    <?php if (!empty($user['phone_number'])): ?>
                        · <?= htmlspecialchars($user['phone_number']) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="profile-joined">
            Thành viên từ <?= date('d/m/Y', strtotime($user['created_at'] ?? 'now')) ?>
        </div>
    </div>

    <!-- Alert Notifications -->
    <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success">
            <div><?= htmlspecialchars($successMessage) ?></div>
        </div>
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-error">
            <div><?= htmlspecialchars($errorMessage) ?></div>
        </div>
    <?php endif; ?>

    <!-- Main Profile Layout Grid -->
    <div class="profile-grid">
        <!-- Sidebar Navigation Tabs -->
        <aside class="profile-sidebar">
            <ul class="nav-tabs-list">
                <li>
                    <button type="button" class="nav-tab-btn <?= $activeTab === 'info' ? 'active' : '' ?>" onclick="switchTab('info')">
                        <span>Thông tin cá nhân</span>
                    </button>
                </li>
                <li>
                    <button type="button" class="nav-tab-btn <?= $activeTab === 'password' ? 'active' : '' ?>" onclick="switchTab('password')">
                        <span>Đổi mật khẩu</span>
                    </button>
                </li>
                <li>
                    <button type="button" class="nav-tab-btn <?= $activeTab === 'orders' ? 'active' : '' ?>" onclick="switchTab('orders')">
                        <span>Lịch sử đơn hàng (<?= count($orders) ?>)</span>
                    </button>
                </li>
                <li>
                    <a href="<?= url('/logout') ?>" class="nav-tab-btn logout-btn" onclick="return confirm('Bạn có chắc chắn muốn đăng xuất?')">
                        <span>Đăng xuất</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content Card -->
        <main class="tab-content-card">

            <!-- TAB 1: THÔNG TIN CÁ NHÂN -->
            <div id="tab-info" class="tab-pane <?= $activeTab === 'info' ? 'active' : '' ?>">
                <h2 class="tab-title">Thông tin cá nhân</h2>
                <p class="tab-subtitle">Cập nhật họ tên và số điện thoại liên lạc của bạn để Mộc Nhiên giao hàng chính xác hơn.</p>

                <form action="<?= url('/profile/update') ?>" method="POST">
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label class="form-label" for="full_name">Họ và tên *</label>
                            <input type="text" id="full_name" name="full_name" class="form-control" 
                                   value="<?= htmlspecialchars($user['full_name']) ?>" required placeholder="Nhập họ và tên đầy đủ">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="email">Địa chỉ Email (Tài khoản)</label>
                            <input type="email" id="email" class="form-control" 
                                   value="<?= htmlspecialchars($user['email']) ?>" readonly disabled>
                            <small style="color: var(--muted); font-size: 12px; margin-top: 4px;">Email đăng ký được cố định để bảo mật tài khoản.</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="phone_number">Số điện thoại</label>
                            <input type="tel" id="phone_number" name="phone_number" class="form-control" 
                                   value="<?= htmlspecialchars($user['phone_number'] ?? '') ?>" placeholder="Ví dụ: 0901234567">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn">
                            Lưu thay đổi thông tin
                        </button>
                    </div>
                </form>
            </div>

            <!-- TAB 2: ĐỔI MẬT KHẨU -->
            <div id="tab-password" class="tab-pane <?= $activeTab === 'password' ? 'active' : '' ?>">
                <h2 class="tab-title">Đổi mật khẩu</h2>
                <p class="tab-subtitle">Đảm bảo tài khoản của bạn luôn an toàn bằng cách cập nhật mật khẩu định kỳ.</p>

                <form action="<?= url('/profile/change-password') ?>" method="POST" style="max-width: 520px;">
                    <div class="form-group">
                        <label class="form-label" for="current_password">Mật khẩu hiện tại *</label>
                        <input type="password" id="current_password" name="current_password" class="form-control" required placeholder="••••••••">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="new_password">Mật khẩu mới *</label>
                        <input type="password" id="new_password" name="new_password" class="form-control" minlength="6" required placeholder="Tối thiểu 6 ký tự">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="confirm_password">Xác nhận mật khẩu mới *</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" minlength="6" required placeholder="Nhập lại mật khẩu mới">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-accent">
                            Cập nhật mật khẩu mới
                        </button>
                    </div>
                </form>
            </div>

            <!-- TAB 3: LỊCH SỬ ĐƠN HÀNG -->
            <div id="tab-orders" class="tab-pane <?= $activeTab === 'orders' ? 'active' : '' ?>">
                <h2 class="tab-title">Lịch sử đơn hàng</h2>
                <p class="tab-subtitle">Theo dõi trạng thái và lịch sử các đơn hàng bạn đã mua tại Mộc Nhiên.</p>

                <?php if (empty($orders)): ?>
                    <div class="empty">
                        <h3 style="color: var(--green-dark); margin-bottom: 8px;">Bạn chưa có đơn hàng nào</h3>
                        <p style="margin-bottom: 20px;">Hãy khám phá các sản phẩm văn phòng phẩm tinh tế tại cửa hàng Mộc Nhiên.</p>
                        <a href="<?= url('/products') ?>" class="btn">
                            Mua sắm ngay
                        </a>
                    </div>
                <?php else: ?>
                    <div class="order-list">
                        <?php foreach ($orders as $order): ?>
                            <?php
                            $statusClass = match ($order['order_status']) {
                                'PENDING'    => 'status-pending',
                                'CONFIRMED'  => 'status-confirmed',
                                'PROCESSING' => 'status-processing',
                                'SHIPPING'   => 'status-shipping',
                                'DELIVERED'  => 'status-delivered',
                                'CANCELLED'  => 'status-cancelled',
                                default      => 'status-pending',
                            };

                            $statusLabel = match ($order['order_status']) {
                                'PENDING'    => 'Chờ xử lý',
                                'CONFIRMED'  => 'Đã xác nhận',
                                'PROCESSING' => 'Đang đóng gói',
                                'SHIPPING'   => 'Đang giao hàng',
                                'DELIVERED'  => 'Đã giao thành công',
                                'CANCELLED'  => 'Đã hủy',
                                default      => $order['order_status'],
                            };

                            $paymentClass = ($order['payment_status'] ?? '') === 'PAID' ? 'payment-paid' : 'payment-unpaid';
                            $paymentLabel = ($order['payment_status'] ?? '') === 'PAID' ? 'Đã thanh toán' : 'Chưa thanh toán';
                            ?>
                            <div class="order-card">
                                <div class="order-header">
                                    <div class="order-code-group">
                                        <span class="order-id">Mã đơn: #DH<?= $order['id'] ?></span>
                                        <span class="order-date">· <?= date('H:i - d/m/Y', strtotime($order['created_at'])) ?></span>
                                    </div>
                                    <div class="order-status-group">
                                        <span class="payment-badge <?= $paymentClass ?>">
                                            <?= $paymentLabel ?> (<?= htmlspecialchars($order['payment_method'] ?? 'COD') ?>)
                                        </span>
                                        <span class="status-badge <?= $statusClass ?>">
                                            <?= $statusLabel ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="order-body">
                                    <div class="order-items-list">
                                        <?php if (!empty($order['items'])): ?>
                                            <?php foreach ($order['items'] as $item): ?>
                                                <div class="order-item-row">
                                                    <div class="item-info">
                                                        <span class="item-title"><?= htmlspecialchars($item['product_name']) ?></span>
                                                        <span class="item-variant">Phân loại: <?= htmlspecialchars($item['variant_name'] ?? 'Mặc định') ?></span>
                                                    </div>
                                                    <div class="item-price-qty">
                                                        <div><strong><?= number_format((float)$item['unit_price'], 0, ',', '.') ?>₫</strong> × <?= (int)$item['quantity'] ?></div>
                                                        <small style="color: var(--muted);">= <?= number_format((float)$item['sub_total'], 0, ',', '.') ?>₫</small>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="order-footer">
                                    <div class="recipient-info">
                                        <strong>Người nhận:</strong> <?= htmlspecialchars($order['recipient_name']) ?> (<?= htmlspecialchars($order['recipient_phone']) ?>)<br>
                                        <strong>Địa chỉ:</strong> <?= htmlspecialchars($order['shipping_address']) ?>
                                    </div>
                                    <div class="order-total" style="text-align: right;">
                                        <div>Tổng tiền: <strong><?= number_format((float)$order['total_amount'], 0, ',', '.') ?>₫</strong></div>
                                        <?php if ($order['order_status'] === 'PENDING' && ($order['payment_status'] ?? '') !== 'PAID'): ?>
                                            <div class="order-actions-group">
                                                <?php if (strtoupper((string)($order['payment_method'] ?? '')) === 'BANK_TRANSFER'): ?>
                                                    <a href="<?= url('/checkout/success?id=' . $order['id']) ?>" class="btn-pay-now">
                                                        Quét mã thanh toán
                                                    </a>
                                                <?php endif; ?>
                                                <form action="<?= url('/orders/cancel') ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng #DH<?= $order['id'] ?> không?\nSố lượng tồn kho sẽ được hoàn trả lại.');" style="margin: 0; display: inline;">
                                                    <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
                                                    <button type="submit" class="btn-cancel-order">
                                                        Hủy đơn
                                                    </button>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

        </main>
    </div>

</div>

<!-- Vanilla JS Chuyển Tab Mượt Mà -->
<script>
function switchTab(tabName) {
    // 1. Thay đổi trạng thái nút Sidebar
    document.querySelectorAll('.nav-tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Tìm nút click tương ứng
    const currentBtn = Array.from(document.querySelectorAll('.nav-tab-btn')).find(btn => {
        return btn.getAttribute('onclick') && btn.getAttribute('onclick').includes("'" + tabName + "'");
    });
    if (currentBtn) {
        currentBtn.classList.add('active');
    }

    // 2. Thay đổi thẻ pane tương ứng
    document.querySelectorAll('.tab-pane').forEach(pane => {
        pane.classList.remove('active');
    });

    const targetPane = document.getElementById('tab-' + tabName);
    if (targetPane) {
        targetPane.classList.add('active');
    }

    // 3. Cập nhật query string trên URL không làm reload trang
    const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?tab=' + tabName;
    window.history.pushState({path: newUrl}, '', newUrl);
}

// Xử lý tự động kích hoạt tab dựa theo parameter URL khi load trang
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    if (tabParam && ['info', 'password', 'orders'].includes(tabParam)) {
        switchTab(tabParam);
    }
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>