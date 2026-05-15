# Hương Việt Restaurant Management

Website mô phỏng hệ thống quản lý nhà hàng **Ẩm Thực Hương Việt**, được xây dựng bằng **PHP thuần**, **MySQL InnoDB**, **Bootstrap 5**, chạy trên môi trường **XAMPP**.

Dự án gồm giao diện website cho khách hàng và khu vực quản trị dành cho quản lý/nhân viên nhà hàng.

---

## 1. Giới thiệu dự án

**Hương Việt Restaurant Management** là website mô phỏng hoạt động của một nhà hàng, bao gồm:

- Giới thiệu nhà hàng
- Hiển thị thực đơn
- Xem chi tiết món ăn
- Đặt bàn online
- Liên hệ với nhà hàng
- Quản trị món ăn
- Quản lý người dùng
- Quản lý khách hàng
- Quản lý đặt bàn
- Quản lý kho nguyên liệu
- Quản lý order / hóa đơn / thanh toán
- Báo cáo thống kê

Dự án được thực hiện nhằm đáp ứng yêu cầu xây dựng website bằng PHP, có cơ sở dữ liệu MySQL InnoDB và các chức năng quản lý nhà hàng cơ bản.

---

## 2. Công nghệ sử dụng

| Thành phần | Công nghệ |
|---|---|
| Frontend | HTML, CSS, Bootstrap 5, JavaScript |
| Backend | PHP thuần |
| Database | MySQL InnoDB |
| Server local | XAMPP Apache |
| IDE | Visual Studio Code |
| Quản lý mã nguồn | Git, GitHub |

---

## 3. Chức năng website khách hàng

### Trang chủ

- Hiển thị slider banner.
- Hiển thị thực đơn chính.
- Hiển thị món ăn nổi bật.
- Hiển thị phần đặt bàn nhanh.
- Hiển thị phần đôi nét về quán.
- Hiển thị thông tin liên hệ và bản đồ.

### Trang thực đơn

- Hiển thị danh sách món ăn từ database.
- Lọc món ăn theo danh mục.
- Tìm kiếm món ăn theo tên.
- Hiển thị ảnh, tên món, giá và danh mục.

### Trang chi tiết món ăn

- Xem chi tiết thông tin món ăn.
- Hiển thị ảnh, tên món, giá, mô tả, trạng thái.
- Hiển thị các món ăn cùng loại bên dưới.

### Trang đặt bàn

- Khách hàng nhập thông tin đặt bàn.
- Lưu thông tin khách hàng vào database.
- Lưu thông tin đặt bàn vào database.
- Tự động chọn bàn phù hợp theo số người và thời gian đặt.

### Trang liên hệ

- Khách hàng gửi thông tin liên hệ.
- Lưu nội dung liên hệ vào database.
- Admin có thể xem và xử lý liên hệ trong trang quản trị.

### Trang giới thiệu

- Giới thiệu thông tin nhà hàng.
- Giới thiệu dịch vụ, thực đơn, không gian và thông tin liên hệ.

---

## 4. Chức năng quản trị admin

### Đăng nhập admin

- Đăng nhập bằng tài khoản quản trị.
- Sử dụng session để kiểm tra đăng nhập.
- Phân quyền theo nhóm người dùng.

### Quản lý người dùng

- Thêm người dùng.
- Sửa thông tin người dùng.
- Khóa tài khoản người dùng.
- Phân quyền theo vai trò.

Nhóm người dùng gồm:

- Quản lý
- Giám đốc
- Trưởng phòng kế toán
- Trưởng phòng ẩm thực
- Thu ngân
- Phục vụ
- Lao công
- Nhân viên kho
- Đầu bếp

### Quản lý món ăn

- Xem danh sách món ăn.
- Thêm món ăn.
- Sửa món ăn.
- Ngừng bán món ăn.
- Lọc theo danh mục.
- Tìm kiếm món ăn.

### Quản lý đặt bàn

- Xem danh sách đặt bàn.
- Xem chi tiết đặt bàn.
- Xác nhận đặt bàn.
- Hoàn thành đặt bàn.
- Hủy đặt bàn khi còn ở trạng thái chờ xác nhận.

### Quản lý khách hàng

- Xem danh sách khách hàng.
- Thêm khách hàng.
- Sửa thông tin khách hàng.
- Phân loại khách hàng.
- Xem lịch sử đặt bàn của khách.

Loại khách hàng gồm:

- Khách vãng lai
- Khách quen
- Khách VIP

### Quản lý kho nguyên liệu

- Quản lý nguyên liệu.
- Quản lý nhà cung cấp.
- Tạo phiếu nhập kho.
- Cập nhật số lượng tồn kho.
- Cảnh báo nguyên liệu sắp hết.

### Quản lý order / hóa đơn / thanh toán

- Tạo order mới.
- Chọn món ăn cho order.
- Tính tổng tiền.
- Thanh toán hóa đơn.
- Hủy order khi chưa thanh toán.
- In hóa đơn.
- Xem lịch sử hóa đơn.

### Quản lý phục vụ

- Theo dõi trạng thái bàn.
- Gán nhân viên phục vụ.
- Cập nhật trạng thái phục vụ.
- Theo dõi order đang phục vụ.

### Quản lý liên hệ

- Xem danh sách liên hệ từ khách hàng.
- Xem chi tiết nội dung liên hệ.
- Cập nhật trạng thái xử lý.
- Xóa liên hệ chưa xử lý hoặc đang xử lý.

### Báo cáo thống kê

- Báo cáo doanh thu.
- Báo cáo món bán chạy.
- Báo cáo đặt bàn.
- Báo cáo khách hàng.
- Báo cáo kho nguyên liệu.
- Thống kê tổng quan hệ thống.

---

## 5. Cấu trúc thư mục

```plaintext
huongviet/
│
├── admin/
│   ├── bookings/
│   ├── contacts/
│   ├── customers/
│   ├── foods/
│   ├── inventory/
│   ├── orders/
│   ├── reports/
│   ├── service/
│   ├── users/
│   ├── auth.php
│   ├── dashboard.php
│   ├── login.php
│   └── logout.php
│
├── assets/
│   ├── css/
│   │   ├── admin/
│   │   ├── about.css
│   │   ├── booking.css
│   │   ├── contact.css
│   │   ├── food_detail.css
│   │   ├── home.css
│   │   ├── menu.css
│   │   └── style.css
│   │
│   ├── images/
│   └── js/
│
├── classes/
│
├── config/
│   └── database.php
│
├── includes/
│   ├── footer.php
│   ├── header.php
│   └── navbar.php
│
├── pages/
│   ├── about.php
│   ├── booking.php
│   ├── contact.php
│   ├── food_detail.php
│   └── menu.php
│
├── database.sql
├── database_update_admin.sql
├── database_update_booking.sql
├── database_update_contact.sql
├── database_update_customers.sql
├── database_update_inventory.sql
├── database_update_orders.sql
├── database_update_service.sql
├── index.php
└── README.md

---

## 6. Cơ sở dữ liệu

Database sử dụng:

```sql
huongviet
```

Một số bảng chính:

| Bảng | Chức năng |
|---|---|
| roles | Lưu vai trò người dùng |
| users | Lưu tài khoản quản trị / nhân viên |
| food_categories | Lưu danh mục món ăn |
| foods | Lưu món ăn |
| customers | Lưu khách hàng |
| bookings | Lưu thông tin đặt bàn |
| tables_restaurant | Lưu bàn ăn |
| contact_messages | Lưu liên hệ của khách |
| ingredients | Lưu nguyên liệu kho |
| suppliers | Lưu nhà cung cấp |
| import_receipts | Lưu phiếu nhập kho |
| import_details | Lưu chi tiết nhập kho |
| orders | Lưu hóa đơn / order |
| order_details | Lưu chi tiết món trong order |
| service_assignments | Lưu nhiệm vụ phục vụ |

---

## 7. Hướng dẫn cài đặt

### Bước 1: Clone project

```bash
git clone https://github.com/thptltkk55-max/huongvietrestaurantmanagement.git
```

### Bước 2: Copy project vào thư mục XAMPP

Đặt project tại:

```plaintext
C:\xampp\htdocs\huongviet
```

### Bước 3: Khởi động XAMPP

Bật:

```plaintext
Apache
MySQL
```

### Bước 4: Tạo database

Vào phpMyAdmin và tạo database:

```sql
CREATE DATABASE huongviet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Bước 5: Import database

Import file chính:

```plaintext
database.sql
```

Nếu có các file update database, import thêm theo thứ tự:

```plaintext
database_update_admin.sql
database_update_booking.sql
database_update_contact.sql
database_update_customers.sql
database_update_inventory.sql
database_update_orders.sql
database_update_service.sql
```

### Bước 6: Cấu hình database

Kiểm tra file:

```plaintext
config/database.php
```

Cấu hình mặc định:

```php
<?php

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "huongviet";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

?>
```

### Bước 7: Chạy website

Mở trình duyệt:

```plaintext
http://localhost/huongviet
```

---

## 8. Tài khoản admin mẫu

Nếu đã import dữ liệu mẫu, có thể đăng nhập admin bằng:

```plaintext
Username: admin
Password: 123456
```

Hoặc:

```plaintext
Email: admin@huongviet.com
Password: 123456
```

Trang đăng nhập admin:

```plaintext
http://localhost/huongviet/admin/login.php
```

---

## 9. Một số đường dẫn quan trọng

### Website khách hàng

```plaintext
Trang chủ:        http://localhost/huongviet
Thực đơn:        http://localhost/huongviet/pages/menu.php
Đặt bàn:         http://localhost/huongviet/pages/booking.php
Liên hệ:         http://localhost/huongviet/pages/contact.php
Giới thiệu:      http://localhost/huongviet/pages/about.php
```

### Trang quản trị

```plaintext
Đăng nhập admin:       http://localhost/huongviet/admin/login.php
Dashboard:             http://localhost/huongviet/admin/dashboard.php
Quản lý món ăn:         http://localhost/huongviet/admin/foods/index.php
Quản lý đặt bàn:        http://localhost/huongviet/admin/bookings/index.php
Quản lý khách hàng:     http://localhost/huongviet/admin/customers/index.php
Quản lý kho:            http://localhost/huongviet/admin/inventory/index.php
Quản lý hóa đơn:        http://localhost/huongviet/admin/orders/index.php
Báo cáo thống kê:       http://localhost/huongviet/admin/reports/index.php
Quản lý liên hệ:        http://localhost/huongviet/admin/contacts/index.php
Quản lý phục vụ:        http://localhost/huongviet/admin/service/index.php
```

---

## 10. Quy ước đặt tên ảnh món ăn

Ảnh món ăn đặt trong thư mục:

```plaintext
assets/images/
```

Quy ước tên ảnh:

```plaintext
ten_mon_khong_dau_viet_thuong_cach_nhau_bang_dau_gach_duoi.jpg
```

Ví dụ:

```plaintext
ga_nuong.jpg
lau_hai_san.jpg
tom_chien.jpg
ca_loc_nuong.jpg
suon_nuong_mat_ong.jpg
```

---

## 11. Tác giả

Project được thực hiện bởi sinh viên trong quá trình học lập trình web với PHP và MySQL.

---

## 12. Ghi chú

Dự án phục vụ mục đích học tập, mô phỏng hệ thống website và quản lý nhà hàng Hương Việt.

Các chức năng có thể tiếp tục mở rộng:

- Upload ảnh món ăn trực tiếp từ admin
- Gửi email xác nhận đặt bàn
- Thanh toán online
- Phân quyền chi tiết hơn theo từng vai trò
- Xuất báo cáo PDF / Excel
- Biểu đồ thống kê doanh thu