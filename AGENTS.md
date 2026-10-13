# MỘC NHIÊN STATIONERY - AGENTS & DEVELOPER CONTEXT GUIDE

> Tài liệu cung cấp toàn bộ bức tranh kiến trúc, cấu trúc source code, cơ sở dữ liệu, tiến độ triển khai hiện tại và quy chuẩn phát triển (Engineering Rules) cho AI Agents và lập trình viên trước khi phát triển tính năng mới.

---

## 1. TỔNG QUAN DỰ ÁN (PROJECT OVERVIEW)

- **Tên dự án**: Mộc Nhiên Stationery (Cửa hàng văn phòng phẩm xanh)
- **Mục tiêu**: Nền tảng thương mại điện tử chuyên bán văn phòng phẩm, tập vở, bút viết, balo, dụng cụ học tập. Tích hợp thanh toán quét mã tự động (SePay / VietQR), vận chuyển (Giao Hàng Nhanh - GHN), hệ thống gửi email thông báo (SMTP Socket) và quản trị Admin.
- **Môi trường & Tech Stack**:
  - **PHP 8+** (PHP thuần MVC OOP, `strict_types=1`, không dùng framework Laravel/Symfony/CodeIgniter).
  - **Web Server**: Apache / XAMPP, rewrite URL qua `.htaccess` về `index.php`.
  - **Cơ sở dữ liệu**: MySQL / MariaDB (Database: `stationery_shop`), kết nối qua PDO Prepared Statements.
  - **Frontend**: HTML5 semantic, CSS thuần phân tầng theo trang/component, Vanilla JavaScript, Google Fonts (`Outfit` & `Georgia`).

---

## 2. QUY CHUẨN KỸ THUẬT (ENGINEERING RULES)

Khi làm việc trên dự án, AI Agent **bắt buộc tuân thủ 6 nguyên tắc cốt lõi**:

1. **Hiểu rõ trước khi code (Understand Before Coding)**:
   - Luôn đọc và bám sát kiến trúc MVC hiện hữu.
   - Không tự ý cài framework bên ngoài hoặc viết lại các module đang hoạt động ổn định.
   - Tái sử dụng triệt để helper, core classes (`url()`, `Database::connection()`, `Controller::view()`, ...).
2. **Kiến trúc (Architecture)**:
   - **Controllers (`src/Controllers`)**: Tiếp nhận request, validate sơ bộ, gọi model/service, render view hoặc redirect.
   - **Models (`src/Models`)**: Thực thi truy vấn DB bằng PDO, trả về array dữ liệu, không echo HTML.
   - **Views (`src/Views`)**: Chỉ render giao diện, dữ liệu truyền từ controller, output phải được escape (`htmlspecialchars`).
   - **Services (`src/Services`)**: Chứa logic xử lý nghiệp vụ bên thứ ba (Email SMTP, Payment Gateway, Shipping API).
3. **Bảo mật (Security)**:
   - **SQL Injection**: 100% truy vấn có tham số phải dùng PDO prepared statements (`prepare()`, `bindValue()` hoặc `execute($params)`).
   - **XSS Prevention**: Mọi biến in ra view phải dùng `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')`.
   - **CSRF Protection**: Form quan trọng (như POST bài viết, sửa profile, checkout) cần verify CSRF token.
   - **Xác thực & Phân quyền**: Kiểm tra session (`$_SESSION['user_id']`, `$_SESSION['user_role'] === 'admin'`) trước khi xử lý các route cá nhân hoặc admin.
   - **Bảo mật thông tin**: Không hardcode password, secret keys trực tiếp trong mã nguồn nếu có thể cấu hình qua file config, không để lộ exception trace ra frontend.
4. **Quản lý thay đổi (Change Management)**:
   - Chỉ chỉnh sửa các file liên quan đến tính năng được giao.
   - Giữ nguyên tính tương thích ngược (Backward Compatibility). Không chạy lệnh xóa dữ liệu phá hoại.
5. **Kiểm thử & Rà soát (Verification)**:
   - Kiểm tra syntax PHP (`php -l`), bắt lỗi edge cases và kiểm tra tính toàn vẹn của diff trước khi hoàn thành.
6. **Báo cáo hoàn tất (Final Report)**:
   - Tóm tắt rõ ràng: Những gì đã thay đổi, file nào bị tác động và lý do, kết quả kiểm tra và các điểm cần lưu ý.

---

## 3. CẤU TRÚC THƯ MỤC DỰ ÁN (PROJECT STRUCTURE)

```
c:/xampp/htdocs/stationery/
├── .htaccess                   # Rewrite mọi URL về index.php
├── config/
│   └── config.php              # Cấu hình app (base_url), database, mail SMTP, SePay, GHN
├── database/
│   └── stationery_shop (8).sql # File dump cơ sở dữ liệu mẫu
├── index.php                   # Single Entry Point, autoloader, session_start, router dispatch
├── routes/
│   └── web.php                 # Đăng ký danh sách route (GET / POST)
├── public/
│   ├── css/                    # Stylesheets: base.css, layouts.css, catalog.css, checkout.css, ...
│   ├── images/                 # Tài nguyên hình ảnh (products, posts, banners, ...)
│   └── js/
│       └── app.js              # JavaScript chung (toggle mobile menu, search, ...)
├── src/
│   ├── Core/                   # Lớp nền tảng kiến trúc
│   │   ├── Autoloader.php      # Tự động nạp class theo namespace App\
│   │   ├── Controller.php      # Base Controller (kèm header layout injection)
│   │   ├── Database.php        # Kết nối PDO Singleton
│   │   ├── Model.php           # Base Model chứa PDO instance $this->db
│   │   └── Router.php          # Router xử lý dispatch URL và tham số động {slug}, {id}
│   ├── Helpers/
│   │   ├── auth.php            # Helper isLoggedIn(), isAdmin()
│   │   ├── cart.php            # Helper getCartBadgeCount()
│   │   └── url.php             # Helper url($path), baseUrl($path)
│   ├── Controllers/
│   │   ├── Admin/              # Controller quản trị
│   │   │   ├── AdminController.php      # Điều hướng các trang Admin (dashboard, orders, ...)
│   │   │   └── PostControllerAdmin.php  # Quản lý bài viết (CRUD, upload ảnh, sanitize HTML)
│   │   ├── AuthController.php           # Đăng nhập, đăng ký, đăng xuất
│   │   ├── CartController.php           # Giỏ hàng (add, update quantity, remove)
│   │   ├── ErrorController.php          # Bắt lỗi 404 / 500
│   │   ├── HomeController.php           # Trang chủ (best sellers, new products, posts)
│   │   ├── LayoutController.php         # Chuẩn bị dữ liệu dùng chung (Header user, cart count)
│   │   ├── OrderController.php          # Checkout, xử lý tạo đơn, trang thanh toán, check trạng thái
│   │   ├── PostController.php           # Xem bài viết, chi tiết bài viết phía client
│   │   ├── ProductController.php        # Danh sách sản phẩm (lọc, phân trang) & chi tiết sản phẩm
│   │   ├── UserProfileController.php    # Quản lý tài khoản, đổi mật khẩu, xem lịch sử đơn hàng
│   │   └── WebhookController.php        # Xử lý webhook bên ngoài (SePay banking, GHN delivery)
│   ├── Models/
│   │   ├── Auth.php                     # (File placeholder)
│   │   ├── Cart.php                     # Giỏ hàng (hỗ trợ DB shopping_cart & session sync)
│   │   ├── Category.php                 # Danh mục (placeholder, hiện queries nằm ở Product.php)
│   │   ├── Order.php                    # Quản lý orders, order_details, payments, cập nhật thanh toán
│   │   ├── PaymentTransaction.php       # (File placeholder)
│   │   ├── Post.php                     # Truy vấn & quản lý bài viết
│   │   ├── Product.php                  # Sản phẩm, biến thể, lọc giá/danh mục/thương hiệu, phân trang
│   │   └── User.php                     # Xác thực tài khoản, profile, đổi pass
│   ├── Services/
│   │   ├── GhnService.php               # Dịch vụ tích hợp GHN API (đang hoàn thiện)
│   │   ├── MailService.php              # Gửi email SMTP Socket thuần PHP + Template HTML
│   │   └── SePayService.php             # Xác thực chữ ký HMAC-SHA256 từ webhook SePay
│   └── Views/
│       ├── admin/                       # Giao diện quản trị (dashboard, orders, posts, sales, ...)
│       ├── auth/                        # Giao diện đăng nhập, đăng ký
│       ├── cart/                        # Giao diện giỏ hàng
│       ├── errors/                      # Trang 404
│       ├── home/                        # Giao diện trang chủ
│       ├── layouts/                     # Layout dùng chung (header.php, footer.php)
│       ├── orders/                      # Giao diện checkout và trang thanh toán VietQR / COD
│       ├── posts/                       # Giao diện danh sách và chi tiết bài viết
│       ├── products/                    # Giao diện danh mục sản phẩm & chi tiết sản phẩm
│       └── userprofile/                 # Giao diện trang cá nhân & lịch sử đơn hàng
```

---

## 4. BẢN ĐỒ CƠ SỞ DỮ LIỆU (DATABASE SCHEMA)

Cơ sở dữ liệu `stationery_shop` gồm các bảng chính:

1. **`users`**:
   - Khóa chính: `id`.
   - Cột: `role` (ENUM: `'ADMIN'`, `'CUSTOMER'`), `full_name`, `email` (UNIQUE), `password_hash`, `phone_number`, `activated_at`, `banned_at`, `created_at`, `updated_at`.
2. **`categories`**:
   - Cột: `id`, `name`, `slug` (UNIQUE), `description`, `hidden_at`, `created_at`, `updated_at`.
3. **`brands`**:
   - Cột: `id`, `name`, `slug` (UNIQUE), `description`, `hidden_at`, `created_at`, `updated_at`. (VD: Thiên Long, Flexo, LK).
4. **`products`**:
   - Cột: `id`, `category_id`, `brand_id`, `name`, `slug` (UNIQUE), `description`, `price`, `is_best_seller`, `hidden_at`, `created_at`, `updated_at`.
5. **`product_variants`**:
   - Quản lý phân loại hàng (màu sắc, hình ảnh riêng, tồn kho):
   - Cột: `id`, `product_id`, `variant_name`, `color_code`, `stock_quantity`, `sold`, `image_url`, `is_primary` (1/0), `hidden_at`.
6. **`shopping_cart`**:
   - Giỏ hàng lưu DB cho người dùng đã đăng nhập:
   - Cột: `id`, `user_id`, `variant_id`, `quantity`, `created_at`, `updated_at`. (UNIQUE key `user_id`, `variant_id`).
7. **`orders`**:
   - Cột: `id`, `user_id`, `total_amount`, `recipient_name`, `recipient_phone`, `shipping_address`, `order_status` (`PENDING`, `CONFIRMED`, `PROCESSING`, `SHIPPING`, `DELIVERED`, `CANCELLED`), `payment_status` (`UNPAID`, `PAID`, `REFUNDED`), `created_at`, `updated_at`.
8. **`order_details`**:
   - Chi tiết từng món trong đơn: `id`, `order_id`, `variant_id`, `product_name`, `variant_name`, `quantity`, `unit_price`, `sub_total`.
9. **`payments`**:
   - Cột: `id`, `order_id`, `payment_method` (`COD`, `BANK_TRANSFER`, `MOMO`, `VNPAY`), `transaction_id`, `amount`, `payment_status` (`PENDING`, `SUCCESS`, `FAILED`, `CANCELLED`, `REFUNDED`), `gateway_response` (JSON), timestamps.
10. **`ghn_orders`**:
    - Quản lý vận đơn GHN: `id`, `order_id`, `order_code`, `status`, `total_fee`, `cod_amount`, kích thước/trọng lượng, địa chỉ gửi nhận, `raw_response`.
11. **`posts`**:
    - Cột: `id`, `author_id`, `title`, `slug` (UNIQUE), `content`, `post_type` (`ABOUT`, `BLOG`, `NEWS`), `hidden_at`, `published_at`, `img_post`.

---

## 5. BẢN ĐỒ TIẾN ĐỘ & HIỆN TRẠNG DỰ ÁN (CURRENT STATUS)

### ✅ ĐÃ HOÀN THIỆN (COMPLETED FEATURES)
- **MVC Core & Routing**:
  - Tự động nạp class PSR-4 (`Autoloader`).
  - Router hỗ trợ regex matching param động `{slug}`, `{id}`, hỗ trợ prefix base URL (`/stationery`).
  - Base Controller tự động nạp Header dữ liệu (thông tin user hiện tại, số lượng giỏ hàng badge).
  - Kết nối cơ sở dữ liệu PDO tập trung chuẩn bị sẵn (utf8mb4, exception mode).
- **Trang chủ & Danh mục sản phẩm (Catalog & Products)**:
  - Trang chủ (`HomeController`): Khối Best Seller, Sản phẩm mới, Danh mục nổi bật, Bài viết mới nhất.
  - Bộ lọc sản phẩm nâng cao (`ProductController`): Lọc theo danh mục (`category_slug`), thương hiệu (`brand`), khoảng giá (`price_range`), tìm kiếm từ khóa (`keyword`), sắp xếp (`sort`). Phân trang 12 sản phẩm/trang.
  - Trang chi tiết sản phẩm (`ProductController::detail`): Chọn biến thể màu sắc, hiển thị tồn kho, giá bán, số lượng đã bán.
- **Giỏ hàng (Cart)**:
  - Hỗ trợ lưu giỏ hàng linh hoạt: Session (dành cho khách vãng lai) và Database `shopping_cart` (dành cho người đã đăng nhập).
  - Tự động cộng dồn, cập nhật tăng/giảm (+, -), xóa sản phẩm, tính toán tổng tiền.
- **Xác thực người dùng (Auth)**:
  - Đăng ký tài khoản (mã hóa BCRYPT an toàn).
  - Đăng nhập, lưu session user, đăng xuất an toàn.
- **Quản lý tài khoản (User Profile)**:
  - Tab thông tin: Chỉnh sửa Họ tên, Số điện thoại.
  - Tab bảo mật: Đổi mật khẩu kèm kiểm tra mật khẩu hiện tại.
  - Tab lịch sử đơn hàng: Hiển thị toàn bộ đơn hàng của user, trạng thái thanh toán, chi tiết các mặt hàng.
  - Gửi email cảnh báo bảo mật khi cập nhật thông tin cá nhân hoặc đổi mật khẩu.
- **Thanh toán & Đơn hàng (Checkout & Order Placement)**:
  - Form đặt hàng: Nhập tên người nhận, SĐT, địa chỉ giao hàng, lựa chọn phương thức (COD hoặc Chuyển khoản ngân hàng).
  - Giao dịch DB nguyên tử (Transaction): Trừ tồn kho (`stock_quantity`), tăng số lượng bán (`sold`), lưu `orders` + `order_details` + `payments`, làm sạch giỏ hàng.
  - Trang thanh toán thành công (`orders/success`):
    - Chuyển khoản ngân hàng: Tạo mã QR VietQR (BIDV) tự động theo cú pháp nội dung `DH{order_id}` và đúng số tiền đơn hàng.
    - Polling AJAX thời gian thực qua `/api/orders/check-status?id={order_id}` để tự động chuyển trạng thái ngay khi có tiền vào mà không cần reload trang.
- **Tích hợp Webhook SePay (Tự động duyệt tiền vào ngân hàng)**:
  - Endpoint `/webhook/sepay` tiếp nhận IPN từ SePay.
  - Xác thực chữ ký `HTTP_X_SEPAY_SIGNATURE` bằng HMAC SHA-256 với secret key.
  - Kiểm tra timestamp chống tấn công phát lại (Replay Attack).
  - Trích xuất mã đơn hàng regex `DH{id}`, so khớp số tiền chính xác, cập nhật `orders` (`PAID`, `CONFIRMED`) và `payments` (`SUCCESS`).
- **Dịch vụ Email (MailService)**:
  - Viết thuần socket SMTP (hỗ trợ STARTTLS Gmail port 587) không phụ thuộc thư viện nặng như PHPMailer.
  - Template email HTML mang phong cách thương hiệu Mộc Nhiên.
  - Tự động gửi email xác nhận khi thanh toán thành công hoặc cập nhật tài khoản.
- **Quản lý Bài viết Admin (Post Management)**:
  - CRUD bài viết hoàn chỉnh (`PostControllerAdmin`).
  - Tạo slug tự động và xử lý chống trùng slug.
  - Upload hình ảnh đại diện, kiểm tra MIME type (`jpg`, `png`, `webp`), dung lượng giới hạn 5MB.
  - Xử lý lọc thẻ HTML an toàn chống XSS (`sanitizeHtml`) và chống CSRF bằng token session.

---

### ⏳ ĐANG PHÁT TRIỂN / CẦN LÀM TIẾP (IN PROGRESS & PENDING)
1. **Dịch vụ Giao Hàng Nhanh (GHN Integration)**:
   - File `src/Services/GhnService.php` hiện đang rỗng (0 bytes).
   - Hàm `WebhookController::ghn()` chưa có logic xử lý callback từ GHN.
   - Chưa có API tính phí vận chuyển động khi checkout (hiện đơn hàng đang áp dụng freeship/phí cứng).
   - Chưa có tính năng tự động đẩy đơn sang GHN tạo vận đơn sau khi đơn hàng được xác nhận hoặc thanh toán.
2. **Khu vực Quản trị Admin (Admin Modules CRUD)**:
   - Hiện tại chỉ có `PostControllerAdmin` có logic CRUD hoàn chỉnh.
   - Các chức năng quản trị còn lại trong `AdminController`:
     - Quản lý đơn hàng (`/admin/orders`): Mới chỉ render view mẫu, cần code danh sách đơn hàng, xem chi tiết, đổi trạng thái đơn (Pending -> Processing -> Shipping -> Delivered -> Cancelled).
     - Quản lý sản phẩm (`/admin/products`): Chưa có CRUD thêm/sửa/xóa sản phẩm và biến thể sản phẩm.
     - Quản lý danh mục (`/admin/categories`): Chưa có CRUD danh mục.
     - Quản lý khách hàng (`/admin/customers`): Chưa có quản lý user, khóa/mở khóa tài khoản (`banned_at`).
     - Báo cáo doanh thu (`/admin/sales`): Chưa có thống kê doanh thu thực tế từ database.
3. **Phân quyền và Middleware Admin**:
   - Các route `/admin/*` trong `routes/web.php` cần được bảo vệ bằng kiểm tra `isAdmin()`. Nếu chưa đăng nhập hoặc không phải role `ADMIN` thì phải redirect về `/login` hoặc trả về lỗi 403 Forbidden.
4. **Đồng bộ Giỏ hàng khi Login**:
   - Model `Cart::mergeSessionCartToDb()` đã viết sẵn hàm nhưng cần được gọi tự động trong `AuthController::login()` để đồng bộ giỏ hàng từ session của khách vào DB ngay khi đăng nhập.
5. **Cấu hình biến môi trường**:
   - Hiện các credentials (Gmail App Password, SePay Secret, GHN Token) đang nằm trong `config/config.php`. Cần xem xét tách thành file `.env` hoặc cấu hình an toàn khi triển khai môi trường Production.

---

## 6. HƯỚNG DẪN DÀNH CHO AI AGENT KHI LÀM FEATURE MỚI

Khi nhận được yêu cầu phát triển bất kỳ tính năng mới nào:

1. **Bước 1: Tra cứu Schema & Model liên quan**:
   - Xem bảng tương ứng trong file `database/stationery_shop (8).sql`.
   - Xem Model trong `src/Models/` xem đã có phương thức truy vấn cần thiết chưa. Nếu chưa, bổ sung phương thức vào Model với PDO Prepared Statements.
2. **Bước 2: Khai báo Route trong `routes/web.php`**:
   - Sử dụng `$router->get('/path', [ControllerName::class, 'methodName']);` hoặc `$router->post(...)`.
   - Nếu route thuộc admin, đảm bảo controller method có check `isAdmin()`.
3. **Bước 3: Viết Controller**:
   - Tạo hoặc mở Controller trong `src/Controllers/`.
   - Controller kế thừa `App\Core\Controller`.
   - Validate tham số từ `$_GET` hoặc `$_POST`.
   - Gọi Model thực thi logic.
   - Render view qua `$this->view('folder/view_name', $data);` hoặc chuyển hướng bằng `$this->redirect(url('/path'))`.
4. **Bước 4: Viết hoặc Cập nhật View**:
   - Đặt file view trong `src/Views/`.
   - Trong view, include layout hoặc dùng biến truyền từ Controller.
   - Luôn bọc các biến hiển thị trong `htmlspecialchars($value)`.
   - Tạo file CSS tương ứng trong `public/css/` nếu có style mới và truyền tên css vào mảng `$pageCss` của controller.
5. **Bước 5: Kiểm tra chất lượng (Verification)**:
   - Kiểm tra lỗi cú pháp PHP.
   - Thử nghiệm các trường hợp dữ liệu rỗng, nhập sai kiểu dữ liệu, các tình huống biên.
   - Review lại git diff để chắc chắn không làm ảnh hưởng đến các module khác.
