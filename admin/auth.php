<?php
require_once __DIR__ . "/../config/database.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn()
{
    return isset($_SESSION['admin_user']) && is_array($_SESSION['admin_user']);
}

function currentUser()
{
    return isLoggedIn() ? $_SESSION['admin_user'] : null;
}

function requireLogin()
{
    if (!isLoggedIn()) {
        header("Location: /huongviet/admin/login.php");
        exit;
    }
}

function requireRoleGroup($groups)
{
    requireLogin();

    if (!is_array($groups)) {
        $groups = [$groups];
    }

    $user = currentUser();

    if (!$user || !in_array($user['role_group'], $groups, true)) {
        http_response_code(403);
        echo "<!doctype html>
<html lang=\"vi\">
<head>
    <meta charset=\"utf-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
    <title>Không có quyền truy cập</title>
    <link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css\" rel=\"stylesheet\">
</head>
<body class=\"bg-light\">
    <div class=\"container py-5\">
        <div class=\"alert alert-danger shadow-sm\">
            Bạn không có quyền truy cập chức năng này.
        </div>
        <a class=\"btn btn-primary\" href=\"/huongviet/admin/dashboard.php\">Về dashboard</a>
    </div>
</body>
</html>";
        exit;
    }
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
