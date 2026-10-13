<?php

declare(strict_types=1);

namespace App\Services;

class MailService
{
    private array $config;

    public function __construct(?array $config = null)
    {
        if ($config === null) {
            $configFile = __DIR__ . '/../../config/config.php';
            $allConfig = file_exists($configFile) ? require $configFile : [];
            $this->config = $allConfig['mail'] ?? [
                'driver' => 'smtp',
                'host' => 'smtp.gmail.com',
                'port' => 587,
                'encryption' => 'tls',
                'username' => 'thaisang060704@gmail.com',
                'password' => '',
                'from_address' => 'thaisang060704@gmail.com',
                'from_name' => 'Mộc Nhiên Stationery',
                'smtp_auth' => true,
            ];
        } else {
            $this->config = $config;
        }
    }

    /**
     * Gửi email HTML với phương thức SMTP Socket hoặc mail() PHP fallback
     */
    public function send(string $toEmail, string $subject, string $htmlBody): array
    {
        if (empty($toEmail) || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Địa chỉ email nhận không hợp lệ.'
            ];
        }

        $driver = strtolower($this->config['driver'] ?? 'smtp');

        if ($driver === 'smtp' && !empty($this->config['host'])) {
            $smtpResult = $this->sendViaSmtp($toEmail, $subject, $htmlBody);
            if ($smtpResult['success']) {
                return $smtpResult;
            }
            // Nếu SMTP thất bại, thử fallback qua php mail()
        }
        return $this->sendViaPhpMail($toEmail, $subject, $htmlBody);
    }

    /**
     * Gửi qua SMTP Socket thuần PHP
     */
    private function sendViaSmtp(string $toEmail, string $subject, string $htmlBody): array
    {
        $host = $this->config['host'] ?? 'smtp.gmail.com';
        $port = (int) ($this->config['port'] ?? 587);
        $encryption = strtolower($this->config['encryption'] ?? 'tls');
        $username = $this->config['username'] ?? '';
        $password = $this->config['password'] ?? '';
        $fromEmail = $this->config['from_address'] ?? $username;
        $fromName = $this->config['from_name'] ?? 'Mộc Nhiên Stationery';

        $socketHost = ($encryption === 'ssl') ? "ssl://{$host}" : $host;
        $timeout = 10;

        $socket = @fsockopen($socketHost, $port, $errno, $errstr, $timeout);
        if (!$socket) {
            return [
                'success' => false,
                'message' => "Không thể kết nối đến máy chủ SMTP {$host}:{$port} - {$errstr} ({$errno})"
            ];
        }

        $getResponse = function () use ($socket) {
            $response = '';
            while ($str = fgets($socket, 512)) {
                $response .= $str;
                if (substr($str, 3, 1) === ' ') {
                    break;
                }
            }
            return $response;
        };

        $getResponse(); // Đọc greeting từ server

        fputs($socket, "EHLO " . gethostname() . "\r\n");
        $getResponse();

        if ($encryption === 'tls') {
            fputs($socket, "STARTTLS\r\n");
            $starttlsRes = $getResponse();
            if (strpos($starttlsRes, '220') === false) {
                fclose($socket);
                return [
                    'success' => false,
                    'message' => "STARTTLS thất bại: {$starttlsRes}"
                ];
            }
            stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            fputs($socket, "EHLO " . gethostname() . "\r\n");
            $getResponse();
        }

        // Đăng nhập AUTH LOGIN nếu có username & password
        if (!empty($username) && !empty($password)) {
            fputs($socket, "AUTH LOGIN\r\n");
            $getResponse();
            fputs($socket, base64_encode($username) . "\r\n");
            $getResponse();
            fputs($socket, base64_encode($password) . "\r\n");
            $authRes = $getResponse();

            if (strpos($authRes, '235') === false) {
                fclose($socket);
                return [
                    'success' => false,
                    'message' => "Đăng nhập SMTP thất bại (Sai email hoặc App Password): {$authRes}"
                ];
            }
        }

        fputs($socket, "MAIL FROM: <{$fromEmail}>\r\n");
        $getResponse();

        fputs($socket, "RCPT TO: <{$toEmail}>\r\n");
        $rcptRes = $getResponse();
        if (strpos($rcptRes, '250') === false && strpos($rcptRes, '251') === false) {
            fclose($socket);
            return [
                'success' => false,
                'message' => "SMTP từ chối người nhận: {$rcptRes}"
            ];
        }

        fputs($socket, "DATA\r\n");
        $getResponse();

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <{$fromEmail}>\r\n";
        $headers .= "To: <{$toEmail}>\r\n";
        $headers .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
        $headers .= "Date: " . date('r') . "\r\n";

        $emailData = $headers . "\r\n" . $htmlBody . "\r\n.\r\n";
        fputs($socket, $emailData);
        $dataRes = $getResponse();

        fputs($socket, "QUIT\r\n");
        fclose($socket);

        if (strpos($dataRes, '250') !== false) {
            return [
                'success' => true,
                'message' => 'Gửi email thành công qua SMTP!'
            ];
        }

        return [
            'success' => false,
            'message' => "Lỗi gửi nội dung mail: {$dataRes}"
        ];
    }

    /**
     * Fallback sử dụng hàm mail() mặc định của PHP
     */
    private function sendViaPhpMail(string $toEmail, string $subject, string $htmlBody): array
    {
        $fromEmail = $this->config['from_address'] ?? 'noreply@mocnhien.com';
        $fromName = $this->config['from_name'] ?? 'Mộc Nhiên Stationery';

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <{$fromEmail}>\r\n";

        $encodedSubject = "=?UTF-8?B?" . base64_encode($subject) . "?=";

        $sent = @mail($toEmail, $encodedSubject, $htmlBody, $headers);

        if ($sent) {
            return [
                'success' => true,
                'message' => 'Gửi email thành công qua PHP mail().'
            ];
        }

        return [
            'success' => false,
            'message' => 'Không thể gửi email. Hãy kiểm tra lại cấu hình SMTP server.'
        ];
    }

    // gửi mail thanh toán thành công
    public function sendMailPayment(
        string $toEmail,
        string $order
    ): array {
        $subject = '[Mộc Nhiên] Thanh toán thành công';

        $body = $this->renderEmailTemplate(
            'Thanh toán thành công!',
            '<p>Xin chào!</p>

        <p>
            Mộc Nhiên Stationery xin xác nhận rằng
            đơn hàng của bạn đã được <strong>thanh toán thành công</strong>.
        </p>

        <div style="
            background: #f6f3e9;
            border-left: 4px solid #3f6f4a;
            padding: 15px;
            margin: 15px 0;
            border-radius: 6px;
        ">
            <strong style="color: #2f5237;">
                Thông tin đơn hàng:
            </strong>
            <br>
            - Mã đơn hàng:
            <strong>' . htmlspecialchars($order) . '</strong>
            <br>
            - Trạng thái:
            <strong style="color: #3f6f4a;">
                Đã thanh toán
            </strong>
        </div>

        <p>
            Cảm ơn bạn đã mua sắm tại
            <strong>Mộc Nhiên Stationery</strong>.
        </p>

        <p>
            Đơn hàng của bạn sẽ được xử lý và giao đến địa chỉ
            đã đăng ký trong thời gian sớm nhất.
        </p>

        <p>
            Nếu bạn có bất kỳ câu hỏi nào về đơn hàng,
            vui lòng liên hệ với Mộc Nhiên để được hỗ trợ.
        </p>

        <p>
            Trân trọng,<br>
            <strong>Mộc Nhiên Stationery</strong>
        </p>'
        );

        return $this->send(
            $toEmail,
            $subject,
            $body
        );
    }

    /**
     * Gửi email xác nhận đặt hàng thành công
     */
    public function sendOrderConfirmation(
        string $toEmail,
        int $orderId,
        array $orderData,
        array $cartItems,
        string $paymentMethod
    ): array {
        $subject = "[Mộc Nhiên] Xác nhận đặt hàng thành công #DH{$orderId}";

        $itemsHtml = '<table width="100%" border="0" cellspacing="0" cellpadding="8" style="border-collapse: collapse; margin: 15px 0; font-size: 14px;">';
        $itemsHtml .= '<thead><tr style="background: #eef2eb; color: #2f5237; text-align: left;">
            <th>Sản phẩm</th>
            <th style="text-align: center;">SL</th>
            <th style="text-align: right;">Đơn giá</th>
            <th style="text-align: right;">Thành tiền</th>
        </tr></thead><tbody>';

        foreach ($cartItems as $item) {
            $name = htmlspecialchars($item['name'] ?? $item['product_name'] ?? 'Sản phẩm');
            $variant = htmlspecialchars($item['variant_name'] ?? '');
            $variantText = (!empty($variant) && $variant !== 'Mặc định') ? ' (' . $variant . ')' : '';
            $qty = (int) ($item['quantity'] ?? 1);
            $price = (float) ($item['price'] ?? 0);
            $subtotal = $qty * $price;

            $itemsHtml .= '<tr style="border-bottom: 1px solid #e4e5df;">
                <td><strong>' . $name . '</strong>' . $variantText . '</td>
                <td style="text-align: center;">' . $qty . '</td>
                <td style="text-align: right;">' . number_format($price, 0, ',', '.') . '₫</td>
                <td style="text-align: right; font-weight: 600; color: #276238;">' . number_format($subtotal, 0, ',', '.') . '₫</td>
            </tr>';
        }
        $itemsHtml .= '</tbody></table>';

        $totalFormatted = number_format((float) ($orderData['total_amount'] ?? 0), 0, ',', '.') . '₫';
        $methodText = ($paymentMethod === 'BANK_TRANSFER' || $paymentMethod === 'bank_transfer') 
            ? 'Chuyển khoản trực tuyến (VietQR)' 
            : 'Thanh toán khi nhận hàng (COD)';

        $bodyContent = '
            <p>Xin chào <strong>' . htmlspecialchars($orderData['recipient_name'] ?? 'Quý khách') . '</strong>,</p>
            <p>Cảm ơn bạn đã đặt hàng tại <strong>Mộc Nhiên Stationery</strong>! Đơn hàng của bạn đã được tiếp nhận và đang được chuẩn bị.</p>
            
            <div style="background: #f6f3e9; border-left: 4px solid #3f6f4a; padding: 15px; margin: 15px 0; border-radius: 6px;">
                <p style="margin: 3px 0;"><strong>Mã đơn hàng:</strong> #DH' . $orderId . '</p>
                <p style="margin: 3px 0;"><strong>Số điện thoại:</strong> ' . htmlspecialchars($orderData['recipient_phone'] ?? '') . '</p>
                <p style="margin: 3px 0;"><strong>Địa chỉ nhận hàng:</strong> ' . htmlspecialchars($orderData['shipping_address'] ?? '') . '</p>
                <p style="margin: 3px 0;"><strong>Phương thức:</strong> ' . $methodText . '</p>
            </div>

            <h3 style="color: #2f5237; margin: 20px 0 10px;">Chi tiết đơn hàng:</h3>
            ' . $itemsHtml . '

            <div style="text-align: right; margin-top: 15px; font-size: 16px;">
                <strong>Tổng cộng thanh toán: </strong>
                <span style="color: #276238; font-size: 20px; font-weight: bold;">' . $totalFormatted . '</span>
            </div>

            <p style="margin-top: 25px;">Nếu có bất kỳ thắc mắc nào về đơn hàng, vui lòng phản hồi email này để được hỗ trợ nhanh nhất.</p>
            <p>Trân trọng,<br><strong>Đội ngũ Mộc Nhiên Stationery</strong></p>
        ';

        $htmlBody = $this->renderEmailTemplate("Đặt hàng thành công #DH{$orderId}", $bodyContent);
        return $this->send($toEmail, $subject, $htmlBody);
    }



    //Gửi email thông báo cập nhật tài khoản
    public function sendProfileUpdateAlert(string $toEmail, string $userName): array
    {
        $subject = "[Mộc Nhiên] Thông báo cập nhật thông tin tài khoản";
        $body = $this->renderEmailTemplate(
            "Cập nhật thông tin thành công",
            "<p>Xin chào <strong>" . htmlspecialchars($userName) . "</strong>,</p>
             <p>Chúng tôi xin thông báo rằng thông tin cá nhân/mật khẩu tài khoản của bạn tại <strong>Mộc Nhiên Stationery</strong> vừa được cập nhật thành công.</p>
             <p>Nếu bạn không thực hiện thay đổi này, vui lòng liên hệ ngay với bộ phận hỗ trợ khách hàng của Mộc Nhiên để bảo vệ tài khoản.</p>"
        );

        return $this->send($toEmail, $subject, $body);
    }

    /**
     * Template Email HTML phong cách Mộc Nhiên
     */
    private function renderEmailTemplate(string $title, string $content): string
    {
        return '
        <!DOCTYPE html>
        <html lang="vi">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . htmlspecialchars($title) . '</title>
        </head>
        <body style="margin: 0; padding: 0; background-color: #f6f3e9; font-family: \'Outfit\', Helvetica, Arial, sans-serif; color: #26332d;">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f6f3e9; padding: 30px 10px;">
                <tr>
                    <td align="center">
                        <table width="600" border="0" cellspacing="0" cellpadding="0" style="background: #ffffff; border-radius: 14px; overflow: hidden; box-shadow: 0 10px 25px rgba(47, 82, 55, 0.08);">
                            <!-- Header -->
                            <tr>
                                <td align="center" style="background-color: #3f6f4a; padding: 25px 20px;">
                                    <h1 style="color: #ffffff; margin: 0; font-family: Georgia, serif; font-size: 24px; font-weight: normal; letter-spacing: 1px;">
                                        MỘC NHIÊN
                                    </h1>
                                    <span style="color: #dce7d9; font-size: 12px; text-transform: uppercase; letter-spacing: 2px; display: block; margin-top: 4px;">Stationery & More</span>
                                </td>
                            </tr>

                            <!-- Content -->
                            <tr>
                                <td style="padding: 35px 30px;">
                                    <h2 style="color: #2f5237; font-family: Georgia, serif; font-size: 20px; margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid #dce7d9; padding-bottom: 10px;">
                                        ' . $title . '
                                    </h2>
                                    <div style="font-size: 15px; line-height: 1.6; color: #26332d;">
                                        ' . $content . '
                                    </div>
                                </td>
                            </tr>

                            <!-- Footer -->
                            <tr>
                                <td align="center" style="background-color: #f6f3e9; padding: 20px; font-size: 13px; color: #7a837d; border-top: 1px solid #e4e5df;">
                                    <p style="margin: 0 0 5px 0;">Mộc Nhiên Stationery - Cửa hàng văn phòng phẩm xanh</p>
                                    <p style="margin: 0;">Cảm ơn bạn đã đồng hành cùng Mộc Nhiên!</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>';
    }

}
