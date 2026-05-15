<?php
require_once __DIR__ . "/../config/database.php";
/** @var mysqli $conn */

$page_title = "Liên hệ - Nhà hàng Hương Việt";
$page_css = "/huongviet/assets/css/contact.css";

$errors = [];
$successMessage = "";
$formData = [
    'full_name' => '',
    'phone' => '',
    'email' => '',
    'address' => '',
    'subject' => '',
    'message' => '',
];

function contactEscape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS contact_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(255) NOT NULL,
        phone VARCHAR(50),
        email VARCHAR(255),
        address VARCHAR(255),
        subject VARCHAR(255),
        message TEXT,
        status VARCHAR(50) DEFAULT 'Chưa xử lý',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_contact'])) {
    foreach ($formData as $key => $value) {
        $formData[$key] = trim($_POST[$key] ?? '');
    }

    if ($formData['full_name'] === '') {
        $errors[] = "Họ tên không được rỗng.";
    }

    if ($formData['email'] !== '' && !filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không đúng định dạng.";
    }

    if (!$errors) {
        $stmt = mysqli_prepare($conn, "
            INSERT INTO contact_messages
            (full_name, phone, email, address, subject, message, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, 'Chưa xử lý', NOW())
        ");

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "ssssss",
                $formData['full_name'],
                $formData['phone'],
                $formData['email'],
                $formData['address'],
                $formData['subject'],
                $formData['message']
            );

            if (mysqli_stmt_execute($stmt)) {
                $successMessage = "Gửi liên hệ thành công! Nhà hàng sẽ phản hồi bạn sớm nhất.";
                $formData = array_fill_keys(array_keys($formData), '');
            } else {
                $errors[] = "Không thể gửi liên hệ lúc này. Vui lòng thử lại sau.";
            }
        } else {
            $errors[] = "Không thể chuẩn bị truy vấn lưu liên hệ.";
        }
    }
}

$mapPath = __DIR__ . "/../assets/images/contact_map.jpg";
$mapUrl = "/huongviet/assets/images/contact_map.jpg";

require_once __DIR__ . "/../includes/header.php";
require_once __DIR__ . "/../includes/navbar.php";
?>

<section class="contact-breadcrumb">
    <div class="contact-container">
        <a href="/huongviet/index.php">Trang chủ</a>
        <span>/</span>
        <span>Liên hệ</span>
    </div>
</section>

<section class="contact-page">
    <div class="contact-container">
        <div class="contact-heading">
            <p>Hương Việt luôn sẵn sàng hỗ trợ bạn</p>
            <h1>LIÊN HỆ</h1>
        </div>

        <div class="contact-card">
            <div class="contact-info-panel">
                <h2>ẨM THỰC HƯƠNG VIỆT BẾN CÁT</h2>

                <p class="contact-intro">
                    Quý khách cần đặt bàn, tư vấn tiệc hoặc góp ý dịch vụ, vui lòng gửi thông tin cho Hương Việt.
                </p>

                <div class="contact-info-list">
                    <div class="contact-info-item">
                        <i class="bi bi-geo-alt-fill"></i>
                        <div>
                            <strong>Địa chỉ</strong>
                            <p>Đường DJ 10, KDC Mỹ Phước 3, P. Thới Hòa, TP. Hồ Chí Minh</p>
                        </div>
                    </div>

                    <div class="contact-info-item">
                        <i class="bi bi-telephone-fill"></i>
                        <div>
                            <strong>Điện thoại</strong>
                            <p>0988659291</p>
                        </div>
                    </div>

                    <div class="contact-info-item">
                        <i class="bi bi-envelope-fill"></i>
                        <div>
                            <strong>Email</strong>
                            <p>quanhuongviet123@gmail.com</p>
                        </div>
                    </div>

                    <div class="contact-info-item">
                        <i class="bi bi-globe"></i>
                        <div>
                            <strong>Website</strong>
                            <p>quangiadinhhuongviet.com</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="contact-form-panel">
                <h2>Gửi thông tin liên hệ</h2>

                <p>
                    Nhà hàng sẽ phản hồi bạn trong thời gian sớm nhất.
                </p>

                <?php if ($successMessage !== '') { ?>
                    <div class="contact-alert success">
                        <?php echo contactEscape($successMessage); ?>
                    </div>
                <?php } ?>

                <?php if ($errors) { ?>
                    <div class="contact-alert error">
                        <?php foreach ($errors as $error) { ?>
                            <div><?php echo contactEscape($error); ?></div>
                        <?php } ?>
                    </div>
                <?php } ?>

                <form method="POST" class="contact-form">
                    <div class="contact-form-grid">
                        <div class="form-group">
                            <label>Họ tên</label>
                            <input type="text" name="full_name" placeholder="Nhập họ tên của bạn" value="<?php echo contactEscape($formData['full_name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="text" name="phone" placeholder="Nhập số điện thoại" value="<?php echo contactEscape($formData['phone']); ?>">
                        </div>

                        <div class="form-group">
                            <label>Địa chỉ</label>
                            <input type="text" name="address" placeholder="Nhập địa chỉ" value="<?php echo contactEscape($formData['address']); ?>">
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" placeholder="Nhập email" value="<?php echo contactEscape($formData['email']); ?>">
                        </div>

                        <div class="form-group full-width">
                            <label>Chủ đề</label>
                            <input type="text" name="subject" placeholder="Bạn muốn liên hệ về vấn đề gì?" value="<?php echo contactEscape($formData['subject']); ?>">
                        </div>

                        <div class="form-group full-width">
                            <label>Nội dung</label>
                            <textarea name="message" placeholder="Nhập nội dung cần liên hệ"><?php echo contactEscape($formData['message']); ?></textarea>
                        </div>
                    </div>

                    <div class="contact-actions">
                        <button type="submit" name="send_contact" class="contact-submit-btn">
                            Gửi liên hệ
                        </button>

                        <button type="reset" class="contact-reset-btn">
                            Nhập lại
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="contact-map-card">
            <div class="contact-map-title">
                <h2>Bản đồ nhà hàng</h2>
                <p>Hương Việt - Quán ăn gia đình tại khu vực Mỹ Phước, Bến Cát</p>
            </div>

            <div class="contact-map">
                <?php if (file_exists($mapPath)) { ?>
                    <img src="<?php echo contactEscape($mapUrl); ?>" alt="Bản đồ nhà hàng Hương Việt">
                <?php } else { ?>
                    <div class="contact-map-empty">
                        Bản đồ đang cập nhật
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</section>

<?php
require_once __DIR__ . "/../includes/footer.php";
?>
