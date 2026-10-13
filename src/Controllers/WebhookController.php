<?php declare(strict_types=1);
namespace App\Controllers;
use App\Models\Order;
use App\Services\SePayService;
use JsonException;
use Throwable;
class WebhookController
{
    public function sepay(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            /* * ============================================================ * 1. LẤY RAW BODY * ============================================================ */
            $payload = file_get_contents('php://input');
            if ($payload === false || trim($payload) === '') {
                $this->respond(400, false, 'Empty request body.');
                return;
            }
            /* * ============================================================ * 2. LẤY HEADER TỪ SEPAY * ============================================================ */
            $signature = (string) ($_SERVER['HTTP_X_SEPAY_SIGNATURE'] ?? '');
            $timestamp = (string) ($_SERVER['HTTP_X_SEPAY_TIMESTAMP'] ?? '');
            if ($signature === '') {
                error_log('SePay Webhook: Missing signature.');
                $this->respond(401, false, 'Missing signature.');
                return;
            }
            if ($timestamp === '') {
                error_log('SePay Webhook: Missing timestamp.');
                $this->respond(401, false, 'Missing timestamp.');
                return;
            }
            /* * ============================================================ * 3. KHỞI TẠO SEPAY SERVICE * ============================================================ */
            $sepay = new SePayService(); /* * ============================================================ * 4. VERIFY TIMESTAMP * ============================================================ */
            if (!$sepay->verifyTimestamp($timestamp)) {
                error_log('SePay Webhook: Invalid or expired timestamp.');
                $this->respond(401, false, 'Invalid or expired timestamp.');
                return;
            }
            /* * ============================================================ * 5. VERIFY HMAC SIGNATURE * ============================================================ */
            if (!$sepay->verifySignature($payload, $timestamp, $signature)) {
                error_log('SePay Webhook: Invalid signature.');
                $this->respond(401, false, 'Invalid signature.');
                return;
            }
            /* * ============================================================ * 6. PARSE JSON * ============================================================ */
            $data = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($data)) {
                $this->respond(400, false, 'Invalid webhook payload.');
                return;
            }
            /* * ============================================================ * 7. LOG WEBHOOK ĐÃ VERIFY * ============================================================ */
            error_log('=== SEPAY VERIFIED WEBHOOK ===');
            error_log('Payload: ' . $payload);
            /* * ============================================================ * 8. LẤY THÔNG TIN GIAO DỊCH * ============================================================ */
            $content = trim((string) ($data['content'] ?? ''));
            $transferAmount = (float) ($data['transferAmount'] ?? $data['amount'] ?? 0);
            $transferType = strtolower(trim((string) ($data['transferType'] ?? '')));
            $referenceCode = (string) ($data['referenceCode'] ?? $data['id'] ?? ('SEPAY_' . time()));
            $transactionId = $data['id'] ?? null;
            /* * ============================================================ * 9. LOG TRANSACTION * ============================================================ */
            error_log('SePay Transaction: ' . 'content=' . $content . ', amount=' . $transferAmount . ', type=' . $transferType . ', reference=' . $referenceCode . ', id=' . ($transactionId ?? 'NULL')); /* * ============================================================ * 10. CHỈ XỬ LÝ TIỀN VÀO * ============================================================ */
            if ($transferType !== 'in') {
                error_log('SePay Webhook: Transaction is not incoming money.');
                $this->respond(200, true, 'Webhook received. Transaction ignored.');
                return;
            }
            /* * ============================================================ * 11. LẤY ORDER ID TỪ NỘI DUNG CHUYỂN KHOẢN * * Hỗ trợ: * * DH5 * DH 5 * dh5 * DH 5 abc * ============================================================ */
            $orderId = $this->extractOrderId($content);
            if ($orderId === null) {
                error_log('SePay Webhook: Cannot find order ID from content: ' . $content);
                /* * Webhook hợp lệ nhưng không tìm thấy Order. * Không trả 500 vì SePay đã gửi request hợp lệ. */
                $this->respond(200, true, 'Webhook received. Order ID not found.');
                return;
            }
            error_log("SePay Webhook: Extracted Order ID = {$orderId}");
            /* * ============================================================ * 12. TÌM ORDER * ============================================================ */
            $orderModel = new Order();
            $order = $orderModel->findById($orderId);
            if (!$order) {
                error_log("SePay Webhook: Order #{$orderId} not found.");
                $this->respond(200, true, 'Webhook received. Order not found.', ['order_id' => $orderId,]);
                return;
            }

            /* * ============================================================ * 13. KIỂM TRA ORDER ĐÃ THANH TOÁN HOẶC ĐÃ HỦY/HẾT HẠN CHƯA * ============================================================ */
            $orderStatus = strtoupper((string) ($order['order_status'] ?? ''));
            if ($orderStatus === 'CANCELLED') {
                error_log("SePay Webhook: Order #{$orderId} was cancelled or expired.");
                $this->respond(200, false, 'Order has already been cancelled or expired.', ['order_id' => $orderId,]);
                return;
            }

            $paymentStatus = strtoupper((string) ($order['payment_status'] ?? ''));
            if ($paymentStatus === 'PAID') {
                error_log("SePay Webhook: Order #{$orderId} " . "was already paid.");
                $this->respond(200, true, 'Order was already paid.', ['order_id' => $orderId,]);
                return;
            }
            /* * ============================================================ * 14. KIỂM TRA SỐ TIỀN * ============================================================ */
            $orderAmount = (float) ($order['total_amount'] ?? 0); /* * Không dùng >=. * * Ví dụ: * * Order = 44,000 * Transfer = 100,000 * * Không được tự động coi là paid. */
            if (abs($transferAmount - $orderAmount) > 0.01) {
                error_log("SePay Webhook: Amount mismatch. " . "Order #{$orderId}, " . "Order amount={$orderAmount}, " . "Transfer amount={$transferAmount}");
                $this->respond(200, false, 'Payment amount does not match order amount.', ['order_id' => $orderId,]);
                return;
            }
            /* * ============================================================ * 15. MARK ORDER AS PAID * ============================================================ */
            $updated = $orderModel->markAsPaid($orderId, $referenceCode, $data);
            if (!$updated) {
                error_log("SePay Webhook: Failed to update " . "Order #{$orderId}.");
                $this->respond(500, false, 'Failed to update order payment.');
                return;
            }
            /* * ============================================================ * 16. SUCCESS * ============================================================ */
            error_log("SePay Webhook: Successfully marked " . "Order #{$orderId} as PAID.");

            // Gửi email thông báo thanh toán thành công
            try {
                $userModel = new \App\Models\User();
                $buyer = $userModel->findById((int) ($order['user_id'] ?? 0));
                if ($buyer && !empty($buyer['email'])) {
                    (new \App\Services\MailService())->sendMailPayment($buyer['email'], 'DH' . $orderId);
                }
            } catch (\Throwable $mailErr) {
                error_log('SePay Webhook sendMailPayment error: ' . $mailErr->getMessage());
            }

            $this->respond(200, true, 'Order payment processed successfully.', ['order_id' => $orderId, 'reference_code' => $referenceCode,]);
        } catch (JsonException $e) {
            error_log('SePay Webhook JSON Error: ' . $e->getMessage());
            $this->respond(400, false, 'Invalid JSON payload.');
        } catch (Throwable $e) { /* * ============================================================ * 17. UNEXPECTED ERROR * ============================================================ */
            error_log('SePay Webhook Error: ' . $e->getMessage());
            error_log('SePay Webhook File: ' . $e->getFile() . ':' . $e->getLine());
            error_log('SePay Webhook Trace: ' . $e->getTraceAsString());
            $this->respond(500, false, 'Internal server error.');
        }
    }
    /** * Lấy Order ID từ content chuyển khoản. * * Hỗ trợ: * * DH5 * DH 5 * dh5 * DH 5 abc */
    private function extractOrderId(string $content): ?int
    {
        if (preg_match('/\bDH\s*(\d+)\b/i', $content, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }
    /** * Trả JSON response. */
    private function respond(int $statusCode, bool $success, string $message, array $extra = []): void
    {
        http_response_code($statusCode);
        echo json_encode(array_merge(['success' => $success, 'message' => $message,], $extra), JSON_UNESCAPED_UNICODE);
    }

    public function ghn(): void
    {

    }
}