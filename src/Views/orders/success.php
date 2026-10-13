<?php
/** 
 * @var array $order;
 * @var array $sepayConfig;
 * @var array|null $expireInfo;
 */

require __DIR__ . '/../layouts/header.php';

$isBankTransfer = strtoupper((string) ($order['payment_method'] ?? '')) === 'BANK_TRANSFER';
$paymentStatus = strtoupper((string) ($order['p_status'] ?? $order['payment_status'] ?? 'UNPAID'));
$orderStatus = strtoupper((string) ($order['order_status'] ?? 'PENDING'));
$isPaid = ($paymentStatus === 'PAID' || $paymentStatus === 'SUCCESS');
$isCancelled = ($orderStatus === 'CANCELLED');
$remainingSeconds = max(0, min(900, (int) ($expireInfo['remaining_seconds'] ?? 0)));
$isExpired = ($expireInfo['is_expired'] ?? false) || ($isCancelled && !$isPaid);

$initMin = (int) floor($remainingSeconds / 60);
$initSec = (int) ($remainingSeconds % 60);

$bankName = $sepayConfig['bank_name'] ?? 'BIDV';
$bankAccount = $sepayConfig['bank_account'] ?? 'SBSEPAYNLVJQGCXLXEN';
$accountHolder = $sepayConfig['account_holder'] ?? 'VO THAI SANG';
$transferCode = 'DH' . (int) $order['id'];
$amount = (int) $order['total_amount'];

// Link QR chuẩn VietQR (sepay)
$qrUrl = 'https://vietqr.app/img?' . http_build_query([
    'bank' => $bankName,
    'acc' => $bankAccount,
    'amount' => $amount,
    'des' => $transferCode,
    'template' => $sepayConfig['template'] ?? 'compact',
]);
?>

<main class="page container">
    <div class="order-success-card">
        <div style="text-align: center;">
            <?php if ($isCancelled): ?>
                <div style="font-size: 52px; margin-bottom: 10px;">❌</div>
                <h1 style="color: #dc2626; font-size: 28px; margin-bottom: 8px;">Đơn hàng đã bị hủy</h1>
                <p style="color: #64748b; font-size: 16px;">
                    Đơn hàng <strong style="color: #0f172a;">#DH<?= (int) $order['id'] ?></strong> đã bị hủy hoặc hết thời hạn thanh toán. Tồn kho sản phẩm đã được hoàn lại.
                </p>
            <?php elseif ($isPaid): ?>
                <div style="font-size: 52px; margin-bottom: 10px;">🎉</div>
                <h1 style="color: #2e7d32; font-size: 28px; margin-bottom: 8px;">Thanh toán thành công!</h1>
                <p style="color: #64748b; font-size: 16px;">
                    Cảm ơn bạn đã mua hàng. Mã đơn hàng của bạn là <strong style="color: #0f172a;">#DH<?= (int) $order['id'] ?></strong>.
                </p>
            <?php else: ?>
                <div style="font-size: 52px; margin-bottom: 10px;">📦</div>
                <h1 style="color: #2e7d32; font-size: 28px; margin-bottom: 8px;">Đặt hàng thành công!</h1>
                <p style="color: #64748b; font-size: 16px;">
                    Cảm ơn bạn đã mua hàng. Mã đơn hàng của bạn là <strong style="color: #0f172a;">#DH<?= (int) $order['id'] ?></strong>.
                </p>
            <?php endif; ?>
        </div>

        <?php if ($isBankTransfer): ?>
            <!-- Khu vực Thanh Toán Chuyển Khoản qua SePay -->
            <div class="sepay-payment-box <?= $isCancelled ? 'box-cancelled' : '' ?>" id="sepay-box">
                <h3 style="color: #0369a1; margin-bottom: 6px; font-size: 20px;">Thanh toán qua Chuyển khoản / Mã QR (SePay)</h3>

                <?php if ($isCancelled): ?>
                    <!-- Trạng thái ĐÃ HỦY / HẾT HẠN -->
                    <div class="payment-status-badge status-cancelled" style="margin-top: 15px;">
                        ❌ Đơn hàng đã hết hạn thanh toán hoặc đã bị hủy
                    </div>
                    <p style="color: #64748b; font-size: 14px; margin-top: 15px;">
                        Mã thanh toán này không còn hiệu lực. Quý khách vui lòng không thực hiện chuyển khoản vào mã đơn này.
                    </p>
                    <div style="margin-top: 20px;">
                        <a href="<?= url('/products') ?>" class="btn" style="display: inline-block; text-decoration: none; padding: 10px 22px;">
                            Đặt mua sản phẩm khác
                        </a>
                    </div>
                <?php elseif ($isPaid): ?>
                    <!-- Trạng thái ĐÃ THANH TOÁN -->
                    <div id="payment-status-container">
                        <div class="payment-status-badge status-paid">
                            ✅ Đã nhận thanh toán thành công!
                        </div>
                    </div>
                    <p style="color: #166534; font-size: 14px; margin-top: 15px;">
                        Mộc Nhiên đã xác nhận thanh toán tự động cho đơn hàng và sẽ tiến hành đóng gói giao sớm nhất.
                    </p>
                <?php else: ?>
                    <!-- Trạng thái CHỜ THANH TOÁN -->
                    <p style="color: #64748b; font-size: 14px; margin-bottom: 16px;">
                        Mở ứng dụng Ngân hàng (MBBank, Vietcombank, Momo, BIDV,...) để quét mã QR bên dưới:
                    </p>

                    <!-- Thẻ đếm ngược thời gian thanh toán rõ ràng -->
                    <div class="payment-countdown-card" id="countdown-card">
                        <div class="countdown-header">
                            <span class="countdown-clock-icon">⏳</span>
                            <span class="countdown-title">Thời gian giữ hàng & thanh toán còn lại:</span>
                        </div>
                        <div class="countdown-digits-wrapper">
                            <div class="countdown-digit-box">
                                <span class="countdown-num" id="countdown-min"><?= sprintf('%02d', $initMin) ?></span>
                                <span class="countdown-unit">phút</span>
                            </div>
                            <span class="countdown-colon">:</span>
                            <div class="countdown-digit-box">
                                <span class="countdown-num" id="countdown-sec"><?= sprintf('%02d', $initSec) ?></span>
                                <span class="countdown-unit">giây</span>
                            </div>
                        </div>
                        <p class="countdown-hint">
                            Đơn hàng sẽ tự động hủy nếu không hoàn tất thanh toán trong thời gian này
                        </p>
                    </div>

                    <div id="payment-status-container">
                        <div class="payment-status-badge status-waiting">
                            <span class="pulse-dot"></span> Đang chờ chuyển khoản... (Tự động xác nhận sau 3 - 5 giây)
                        </div>
                    </div>

                    <!-- Khối Mã QR to và đặt ở giữa -->
                    <div class="qr-container" id="qr-container">
                        <div class="qr-image-wrapper">
                            <img src="<?= htmlspecialchars($qrUrl) ?>" alt="Mã VietQR SePay" class="qr-image" id="qr-img" />
                        </div>
                        <p class="qr-caption">
                            📱 Quét mã QR bằng App Ngân hàng hoặc Ví điện tử để nội dung tự điền chính xác
                        </p>
                    </div>

                    <!-- Bảng thông tin chuyển khoản đặt gọn gàng ở giữa -->
                    <div class="transfer-table-wrapper" id="transfer-table-card">
                        <table class="transfer-info-table" id="transfer-table">
                            <tr>
                                <td>Ngân hàng:</td>
                                <td><?= htmlspecialchars($bankName) ?></td>
                            </tr>
                            <tr>
                                <td>Số tài khoản:</td>
                                <td>
                                    <span><?= htmlspecialchars($bankAccount) ?></span>
                                    <button type="button" class="copy-badge"
                                        onclick="copyText('<?= htmlspecialchars($bankAccount) ?>', this)">
                                        Sao chép
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>Chủ tài khoản:</td>
                                <td><?= htmlspecialchars($accountHolder) ?></td>
                            </tr>
                            <tr>
                                <td>Số tiền:</td>
                                <td style="color: #e53935; font-size: 17px;">
                                    <?= number_format($amount, 0, ',', '.') ?>₫
                                    <button type="button" class="copy-badge" onclick="copyText('<?= $amount ?>', this)">
                                        Sao chép
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>Nội dung chuyển khoản (bắt buộc):</td>
                                <td>
                                    <span
                                        style="background: #fef08a; color: #854d0e; padding: 3px 10px; border-radius: 6px; font-family: monospace; font-size: 16px; font-weight: 700;">
                                        <?= htmlspecialchars($transferCode) ?>
                                    </span>
                                    <button type="button" class="copy-badge"
                                        onclick="copyText('<?= htmlspecialchars($transferCode) ?>', this)">
                                        Sao chép
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Nút Hủy thanh toán -->
                    <div class="cancel-payment-actions" id="cancel-actions-box">
                        <button type="button" class="btn-cancel-payment" onclick="cancelOrderNow(<?= (int) $order['id'] ?>)">
                            ✖ Hủy thanh toán đơn hàng này
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div style="text-align: left; margin-top: 25px; border-top: 1px solid #f1f5f9; padding-top: 20px;">
            <h3 style="font-size: 18px; color: #1e293b; margin-bottom: 15px;">Chi tiết đơn hàng</h3>
            <p><b>Người nhận:</b> <?= htmlspecialchars($order['recipient_name']) ?>
                (<?= htmlspecialchars($order['recipient_phone']) ?>)</p>
            <p><b>Địa chỉ nhận hàng:</b> <?= htmlspecialchars($order['shipping_address']) ?></p>
            <p><b>Phương thức thanh toán:</b>
                <?= htmlspecialchars($order['payment_method']) === 'BANK_TRANSFER' ? 'Chuyển khoản Ngân hàng (SePay)' : htmlspecialchars($order['payment_method']) ?>
            </p>
            <p><b>Trạng thái đơn hàng:</b>
                <?php
                $statusColor = match($order['order_status']) {
                    'CANCELLED' => '#dc2626',
                    'CONFIRMED', 'DELIVERED' => '#2e7d32',
                    default => '#ed6c02',
                };
                ?>
                <span id="order-status-text"
                    style="color: <?= $statusColor ?>; font-weight: bold;"><?= htmlspecialchars($order['order_status']) ?></span>
            </p>

            <h4 style="margin-top: 20px; font-size: 16px;">Sản phẩm đã đặt:</h4>
            <ul style="list-style: none; padding: 0;">
                <?php foreach ($order['items'] as $item): ?>
                    <li
                        style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed #e2e8f0;">
                        <span>
                            <?= htmlspecialchars($item['product_name']) ?>
                            <small style="color: #64748b;">(<?= htmlspecialchars($item['variant_name']) ?>) ×
                                <?= (int) $item['quantity'] ?></small>
                        </span>
                        <b><?= number_format((float) $item['sub_total'], 0, ',', '.') ?>₫</b>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div
                style="display: flex; justify-content: space-between; font-size: 18px; margin-top: 15px; font-weight: bold;">
                <span>Tổng thanh toán:</span>
                <span style="color: #e53935;"><?= number_format((float) $order['total_amount'], 0, ',', '.') ?>₫</span>
            </div>
        </div>

        <div style="text-align: center; margin-top: 30px; display: flex; justify-content: center; gap: 14px; flex-wrap: wrap;">
            <a href="<?= url('/profile?tab=orders') ?>" class="btn btn-outline"
                style="display: inline-block; text-decoration: none; padding: 12px 24px; font-size: 15px; border-radius: 8px;">
                Xem danh sách đơn hàng
            </a>
            <a href="<?= url('/') ?>" class="btn btn-accent"
                style="display: inline-block; text-decoration: none; padding: 12px 28px; font-size: 15px; border-radius: 8px;">
                Tiếp tục mua sắm
            </a>
        </div>
    </div>
</main>

<script>
    function copyText(text, btn) {
        navigator.clipboard.writeText(text).then(() => {
            const originalText = btn.innerHTML;
            btn.innerHTML = '✅ Đã chép!';
            btn.style.background = '#22c55e';
            btn.style.color = '#fff';
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.style.background = '';
                btn.style.color = '';
            }, 2000);
        });
    }

    <?php if ($isBankTransfer && !$isPaid && !$isCancelled): ?>
    (function () {
        const orderId = <?= (int) $order['id'] ?>;
        const checkUrl = '<?= url('/api/orders/check-status?id=') ?>' + orderId;
        const cancelUrl = '<?= url('/api/orders/cancel') ?>';
        let remainingSeconds = <?= $remainingSeconds ?>;
        let pollInterval = null;
        let countdownInterval = null;

        // 1. Cập nhật giao diện Đồng hồ đếm ngược (phút và giây rõ ràng)
        function updateCountdownUI(totalSec) {
            const m = Math.floor(totalSec / 60);
            const s = totalSec % 60;
            const minElem = document.getElementById('countdown-min');
            const secElem = document.getElementById('countdown-sec');
            if (minElem) minElem.textContent = (m < 10 ? '0' : '') + m;
            if (secElem) secElem.textContent = (s < 10 ? '0' : '') + s;

            const countdownCard = document.getElementById('countdown-card');
            if (countdownCard) {
                if (totalSec <= 120 && totalSec > 0) {
                    countdownCard.classList.add('urgent-pulse');
                } else {
                    countdownCard.classList.remove('urgent-pulse');
                }
            }
        }

        if (remainingSeconds > 0) {
            updateCountdownUI(remainingSeconds);

            countdownInterval = setInterval(() => {
                remainingSeconds--;
                if (remainingSeconds <= 0) {
                    clearInterval(countdownInterval);
                    updateCountdownUI(0);
                    handleExpiredUI('Đã hết thời hạn thanh toán (15 phút)');
                } else {
                    updateCountdownUI(remainingSeconds);
                }
            }, 1000);
        } else {
            handleExpiredUI('Đã hết thời hạn thanh toán (15 phút)');
        }

        // 2. Polling kiểm tra trạng thái thanh toán
        pollInterval = setInterval(async () => {
            try {
                const response = await fetch(checkUrl, { cache: 'no-store' });
                if (!response.ok) return;

                const data = await response.json();

                // Trường hợp ĐÃ THANH TOÁN
                if (data.success && (data.is_paid || data.payment_status === 'PAID')) {
                    clearInterval(pollInterval);
                    if (countdownInterval) clearInterval(countdownInterval);

                    const statusContainer = document.getElementById('payment-status-container');
                    if (statusContainer) {
                        statusContainer.innerHTML = `
                            <div class="payment-status-badge status-paid" style="font-size: 18px; padding: 14px 24px;">
                                ✅ XÁC NHẬN THANH TOÁN THÀNH CÔNG!
                            </div>
                        `;
                    }

                    // Ẩn countdown, QR, nút hủy
                    const countdownCard = document.getElementById('countdown-card');
                    if (countdownCard) countdownCard.style.display = 'none';

                    const qrContainer = document.getElementById('qr-container');
                    if (qrContainer) qrContainer.style.display = 'none';

                    const cancelBox = document.getElementById('cancel-actions-box');
                    if (cancelBox) cancelBox.style.display = 'none';

                    const orderStatusText = document.getElementById('order-status-text');
                    if (orderStatusText) {
                        orderStatusText.textContent = data.order_status || 'CONFIRMED';
                        orderStatusText.style.color = '#2e7d32';
                    }
                }

                // Trường hợp ĐƠN HÀNG ĐÃ BỊ HỦY HOẶC HẾT HẠN
                if (data.success && (data.is_cancelled || data.is_expired || data.order_status === 'CANCELLED')) {
                    clearInterval(pollInterval);
                    if (countdownInterval) clearInterval(countdownInterval);
                    handleExpiredUI(data.is_expired ? 'Hết thời hạn thanh toán (15 phút)' : 'Đơn hàng đã bị hủy');
                }
            } catch (err) {
                console.error('Error checking payment status:', err);
            }
        }, 3000);

        // 3. Xử lý giao diện khi Đơn hàng bị hủy / Hết hạn
        function handleExpiredUI(reason) {
            const statusContainer = document.getElementById('payment-status-container');
            if (statusContainer) {
                statusContainer.innerHTML = `
                    <div class="payment-status-badge status-cancelled" style="font-size: 16px; padding: 12px 20px;">
                        ❌ ${reason || 'Đã hết thời hạn thanh toán (15 phút)'}
                    </div>
                `;
            }

            const countdownCard = document.getElementById('countdown-card');
            if (countdownCard) countdownCard.style.display = 'none';

            const qrContainer = document.getElementById('qr-container');
            if (qrContainer) {
                qrContainer.innerHTML = `
                    <p style="color: #dc2626; font-weight: bold; margin-top: 15px; font-size: 16px;">
                        Đơn hàng đã hết hạn hoặc bị hủy. Mã QR không còn hiệu lực.
                    </p>
                `;
            }

            const transferTableCard = document.getElementById('transfer-table-card');
            if (transferTableCard) transferTableCard.style.opacity = '0.35';

            const cancelBox = document.getElementById('cancel-actions-box');
            if (cancelBox) {
                cancelBox.innerHTML = `
                    <a href="<?= url('/products') ?>" class="btn" style="display: inline-block; padding: 12px 26px; text-decoration: none;">
                        Mua lại sản phẩm khác
                    </a>
                `;
            }

            const orderStatusText = document.getElementById('order-status-text');
            if (orderStatusText) {
                orderStatusText.textContent = 'CANCELLED (Đã hủy)';
                orderStatusText.style.color = '#dc2626';
            }
        }

        // 4. Hàm người dùng bấm Hủy thanh toán
        window.cancelOrderNow = async function (id) {
            if (!confirm('Bạn có chắc chắn muốn hủy thanh toán và hủy đơn hàng này không?\nCác sản phẩm sẽ được hoàn trả lại giỏ/kho.')) {
                return;
            }

            try {
                const res = await fetch(cancelUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ order_id: id })
                });

                const data = await res.json();
                if (data.success) {
                    if (pollInterval) clearInterval(pollInterval);
                    if (countdownInterval) clearInterval(countdownInterval);
                    handleExpiredUI('Bạn đã hủy đơn hàng thành công');
                } else {
                    alert(data.message || 'Không thể hủy đơn hàng.');
                }
            } catch (e) {
                console.error('Cancel order error:', e);
                alert('Có lỗi xảy ra khi gửi yêu cầu hủy đơn.');
            }
        };
    })();
    <?php endif; ?>
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>