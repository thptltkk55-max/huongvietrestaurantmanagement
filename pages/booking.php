<?php
require_once __DIR__ . "/../config/database.php";

/** @var mysqli $conn */

$page_title = "Đặt bàn online - Nhà hàng Hương Việt";
$page_css = "/huongviet/assets/css/booking.css";

$errors = [];
$success = null;
$customerTypes = ['Khách vãng lai', 'Khách quen', 'Khách VIP'];

$form = [
    'customer_name' => trim($_POST['customer_name'] ?? $_GET['customer_name'] ?? ''),
    'phone' => trim($_POST['phone'] ?? $_GET['phone'] ?? ''),
    'email' => trim($_POST['email'] ?? $_GET['email'] ?? ''),
    'address' => trim($_POST['address'] ?? $_GET['address'] ?? ''),
    'customer_type' => $_POST['customer_type'] ?? $_GET['customer_type'] ?? 'Khách vãng lai',
    'booking_date' => trim($_POST['booking_date'] ?? $_GET['booking_date'] ?? ''),
    'booking_time' => trim($_POST['booking_time'] ?? $_GET['booking_time'] ?? ''),
    'number_of_people' => trim($_POST['number_of_people'] ?? $_GET['number_of_people'] ?? ''),
    'note' => trim($_POST['note'] ?? $_GET['note'] ?? ''),
];

if (!in_array($form['customer_type'], $customerTypes, true)) {
    $form['customer_type'] = 'Khách vãng lai';
}

function findCustomerByPhone(mysqli $conn, string $phone)
{
    $stmt = mysqli_prepare($conn, "SELECT id FROM customers WHERE phone = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $phone);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($result);
}

function findAvailableTable(mysqli $conn, int $people, string $date, string $time)
{
    $sql = "
        SELECT *
        FROM tables_restaurant
        WHERE capacity >= ?
        AND id NOT IN (
            SELECT table_id
            FROM bookings
            WHERE DATE(booking_date) = ?
            AND booking_time = ?
            AND status IN ('Chờ xác nhận', 'Đã xác nhận')
            AND table_id IS NOT NULL
        )
        ORDER BY capacity ASC
        LIMIT 1
    ";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $people, $date, $time);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($result);
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if ($form['customer_name'] === '') {
        $errors[] = "Họ tên không được rỗng.";
    }

    if ($form['phone'] === '') {
        $errors[] = "Số điện thoại không được rỗng.";
    }

    if ($form['booking_date'] === '') {
        $errors[] = "Ngày đặt không được rỗng.";
    }

    if ($form['booking_time'] === '') {
        $errors[] = "Giờ đặt không được rỗng.";
    }

    if (!ctype_digit((string) $form['number_of_people']) || (int) $form['number_of_people'] <= 0) {
        $errors[] = "Số người phải lớn hơn 0.";
    }

    if (empty($errors)) {
        $people = (int) $form['number_of_people'];
        $table = findAvailableTable($conn, $people, $form['booking_date'], $form['booking_time']);

        if (!$table) {
            $errors[] = "Hiện không còn bàn phù hợp vào thời gian này. Vui lòng chọn thời gian khác.";
        } else {
            mysqli_begin_transaction($conn);

            try {
                $customer = findCustomerByPhone($conn, $form['phone']);

                if ($customer) {
                    $customerId = (int) $customer['id'];
                    $stmt = mysqli_prepare(
                        $conn,
                        "UPDATE customers
                         SET customer_name = ?, email = ?, address = ?, customer_type = ?
                         WHERE id = ?"
                    );
                    mysqli_stmt_bind_param(
                        $stmt,
                        "ssssi",
                        $form['customer_name'],
                        $form['email'],
                        $form['address'],
                        $form['customer_type'],
                        $customerId
                    );
                    mysqli_stmt_execute($stmt);
                } else {
                    $stmt = mysqli_prepare(
                        $conn,
                        "INSERT INTO customers (customer_name, phone, email, address, customer_type, created_at)
                         VALUES (?, ?, ?, ?, ?, NOW())"
                    );
                    mysqli_stmt_bind_param(
                        $stmt,
                        "sssss",
                        $form['customer_name'],
                        $form['phone'],
                        $form['email'],
                        $form['address'],
                        $form['customer_type']
                    );
                    mysqli_stmt_execute($stmt);
                    $customerId = mysqli_insert_id($conn);
                }

                $tableId = (int) $table['id'];
                $status = 'Chờ xác nhận';

                $stmt = mysqli_prepare(
                    $conn,
                    "INSERT INTO bookings
                     (customer_id, table_id, booking_date, booking_time, number_of_people, note, status, created_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, NOW())"
                );
                mysqli_stmt_bind_param(
                    $stmt,
                    "iississ",
                    $customerId,
                    $tableId,
                    $form['booking_date'],
                    $form['booking_time'],
                    $people,
                    $form['note'],
                    $status
                );
                mysqli_stmt_execute($stmt);

                mysqli_commit($conn);

                $success = [
                    'customer_name' => $form['customer_name'],
                    'phone' => $form['phone'],
                    'booking_date' => $form['booking_date'],
                    'booking_time' => $form['booking_time'],
                    'number_of_people' => $people,
                    'table_name' => $table['table_name'],
                ];
            } catch (Throwable $e) {
                mysqli_rollback($conn);
                $errors[] = "Không thể lưu đặt bàn. Vui lòng thử lại.";
            }
        }
    }
}

$bookingImagePath = __DIR__ . "/../assets/images/booking_banner.jpg";
$bookingImageUrl = "/huongviet/assets/images/booking_banner.jpg";

require_once __DIR__ . "/../includes/header.php";
require_once __DIR__ . "/../includes/navbar.php";
?>

<section class="booking-breadcrumb">
    <div class="booking-container">
        <a href="/huongviet/index.php">Trang chủ</a>
        <span>/</span>
        <span>Đặt bàn online</span>
    </div>
</section>

<section class="booking-page">
    <div class="booking-container">
        <div class="booking-card">
            <div class="booking-image-box">
                <?php if (file_exists($bookingImagePath)) { ?>
                    <img src="<?php echo htmlspecialchars($bookingImageUrl); ?>" alt="Đặt bàn Hương Việt">
                <?php } else { ?>
                    <div class="booking-image-placeholder">
                        Đặt bàn Hương Việt
                    </div>
                <?php } ?>

                <div class="booking-image-overlay">
                    <h2>ẨM THỰC HƯƠNG VIỆT</h2>
                    <p>Đặt bàn nhanh chóng, giữ chỗ dễ dàng</p>
                    <span>Hotline: 0988659291</span>
                </div>
            </div>

            <div class="booking-form-box">
                <div class="booking-form-header">
                    <div class="booking-form-title">
                        <h1>ĐẶT BÀN ONLINE</h1>
                        <p>Hương Việt sẽ liên hệ xác nhận sau khi bạn gửi thông tin.</p>
                    </div>

                    <a class="booking-home-btn" href="/huongviet/index.php">
                        Về trang chủ
                    </a>
                </div>

                <?php if (!empty($errors)) { ?>
                    <div class="booking-alert error">
                        <?php foreach ($errors as $error) { ?>
                            <div><?php echo htmlspecialchars($error); ?></div>
                        <?php } ?>
                    </div>
                <?php } ?>

                <?php if ($success) { ?>
                    <div class="booking-alert success">
                        <h2>Đặt bàn thành công!</h2>
                        <p>Nhà hàng sẽ liên hệ xác nhận với bạn sớm nhất.</p>
                        <ul>
                            <li>Tên khách: <?php echo htmlspecialchars($success['customer_name']); ?></li>
                            <li>Số điện thoại: <?php echo htmlspecialchars($success['phone']); ?></li>
                            <li>Ngày giờ đặt: <?php echo htmlspecialchars($success['booking_date'] . " " . $success['booking_time']); ?></li>
                            <li>Số người: <?php echo (int) $success['number_of_people']; ?></li>
                            <li>Bàn được đề xuất: <?php echo htmlspecialchars($success['table_name']); ?></li>
                        </ul>
                    </div>
                <?php } ?>

                <form method="POST" action="/huongviet/pages/booking.php" class="booking-form">
                    <div class="booking-form-grid">
                        <div class="form-group">
                            <label>Họ tên khách hàng</label>
                            <input type="text" name="customer_name" value="<?php echo htmlspecialchars($form['customer_name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="text" name="phone" value="<?php echo htmlspecialchars($form['phone']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($form['email']); ?>">
                        </div>

                        <div class="form-group">
                            <label>Địa chỉ</label>
                            <input type="text" name="address" value="<?php echo htmlspecialchars($form['address']); ?>">
                        </div>

                        <div class="form-group">
                            <label>Loại khách</label>
                            <select name="customer_type">
                                <?php foreach ($customerTypes as $type) { ?>
                                    <option value="<?php echo htmlspecialchars($type); ?>" <?php echo $form['customer_type'] === $type ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($type); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Số người</label>
                            <input type="number" min="1" name="number_of_people" value="<?php echo htmlspecialchars($form['number_of_people']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Ngày đặt</label>
                            <input type="date" name="booking_date" value="<?php echo htmlspecialchars($form['booking_date']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Giờ đặt</label>
                            <input type="time" name="booking_time" value="<?php echo htmlspecialchars($form['booking_time']); ?>" required>
                        </div>

                        <div class="form-group full-width">
                            <label>Ghi chú</label>
                            <textarea name="note"><?php echo htmlspecialchars($form['note']); ?></textarea>
                        </div>
                    </div>

                    <div class="booking-actions">
                        <button type="submit" class="booking-submit-btn">
                            Gửi đặt bàn
                        </button>

                        <button type="reset" class="booking-reset-btn">
                            Nhập lại
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php
require_once __DIR__ . "/../includes/footer.php";
?>
