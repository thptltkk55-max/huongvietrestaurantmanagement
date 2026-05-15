<?php
require_once __DIR__ . "/../config/database.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['admin_user'])) {
    header("Location: /huongviet/admin/dashboard.php");
    exit;
}

$error = "";
$login = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? "");
    $password = $_POST['password'] ?? "";

    if ($login === "" || $password === "") {
        $error = "Vui lòng nhập đầy đủ thông tin đăng nhập.";
    } else {
        $sql = "
            SELECT 
                u.id,
                u.role_id,
                u.full_name,
                u.username,
                u.email,
                u.password,
                u.status,
                r.role_name,
                r.role_group
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE (u.username = ? OR u.email = ?)
            LIMIT 1
        ";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ss", $login, $login);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);

            if (
                $user &&
                $user['status'] === 'Hoạt động' &&
                password_verify($password, $user['password'])
            ) {
                session_regenerate_id(true);
                $_SESSION['admin_user'] = [
                    'id' => (int) $user['id'],
                    'role_id' => (int) $user['role_id'],
                    'full_name' => $user['full_name'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'role_name' => $user['role_name'] ?? '',
                    'role_group' => $user['role_group'] ?? '',
                ];

                header("Location: /huongviet/admin/dashboard.php");
                exit;
            }

            $error = "Tài khoản hoặc mật khẩu không đúng, hoặc tài khoản đã bị khóa.";
        } else {
            $error = "Không thể kiểm tra đăng nhập. Vui lòng kiểm tra cấu trúc database.";
        }
    }
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập quản trị - Hương Việt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/base.css?v=<?php echo time(); ?>">
</head>
<body class="bg-light">
    <main class="min-vh-100 d-flex align-items-center py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h1 class="h4 text-center mb-4">Đăng nhập quản trị</h1>

                            <?php if ($error !== "") { ?>
                                <div class="alert alert-danger">
                                    <?php echo htmlspecialchars($error); ?>
                                </div>
                            <?php } ?>

                            <form method="POST" action="/huongviet/admin/login.php">
                                <div class="mb-3">
                                    <label class="form-label">Email hoặc username</label>
                                    <input 
                                        type="text" 
                                        name="login" 
                                        class="form-control" 
                                        value="<?php echo htmlspecialchars($login); ?>"
                                        required
                                    >
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Mật khẩu</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>

                                <button type="submit" class="btn btn-danger w-100">
                                    Đăng nhập
                                </button>
                            </form>

                            <div class="text-center mt-3">
                                <a href="/huongviet/index.php" class="text-decoration-none">
                                    Quay lại trang chủ
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
