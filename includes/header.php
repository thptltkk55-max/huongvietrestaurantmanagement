<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php 
            if (isset($page_title)) {
                echo $page_title;
            } else {
                echo "Nhà hàng Hương Việt";
            }
        ?>
    </title>

    <!-- Bootstrap CSS -->
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link 
        rel="stylesheet" 
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <!-- CSS chính của website -->
    <link 
        rel="stylesheet" 
        href="/huongviet/assets/css/style.css?v=<?php echo time(); ?>"
    >

    <!-- CSS riêng cho từng trang nếu có -->
    <?php if (isset($page_css) && !empty($page_css)) { ?>

        <link 
            rel="stylesheet" 
            href="<?php echo $page_css; ?>?v=<?php echo time(); ?>"
        >

    <?php } ?>

</head>

<body>

<!-- TOP HEADER -->
<div class="top-header">

    <!-- Thông tin nhà hàng -->
    <div class="header-info">

        <p>
            <i class="bi bi-geo-alt-fill"></i>
            Đường DJ 10, KDC Mỹ Phước 3,
            P. Thới Hòa, TP Hồ Chí Minh
        </p>

        <p>
            <i class="bi bi-clock-fill"></i>
            Open: 6:00 AM - 21:00 PM
        </p>

        <p>
            <i class="bi bi-truck"></i>
            Giao hàng tận nơi toàn khu vực
        </p>

    </div>

    <!-- Logo ở giữa -->
    <div class="header-logo">

        <a href="/huongviet/index.php">
            <img 
                src="/huongviet/assets/images/logo.png"
                alt="Logo Hương Việt"
            >
        </a>

        <h1>
            Ẩm Thực Hương Việt Bến Cát
        </h1>

    </div>

    <!-- Hotline -->
    <div class="header-contact">

        <h3>
            Hotline đặt bàn
        </h3>

        <h2>
            <i class="bi bi-telephone-fill"></i>
            0988659291
        </h2>

        <a href="/huongviet/pages/booking.php" class="booking-btn">

            <i class="bi bi-calendar-check"></i>
            Đặt bàn ngay

        </a>

    </div>

</div>