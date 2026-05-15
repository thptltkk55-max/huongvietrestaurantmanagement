<?php
require_once __DIR__ . "/config/database.php";

$page_title = "Nhà hàng Hương Việt";
$page_css = "/huongviet/assets/css/home.css";

require_once __DIR__ . "/includes/header.php";
require_once __DIR__ . "/includes/navbar.php";

$sqlFeatured = "
    SELECT * FROM foods
    WHERE status = 'Còn bán'
    ORDER BY price DESC, id DESC
    LIMIT 8
";

$resultFeatured = mysqli_query($conn, $sqlFeatured);

if (!$resultFeatured) {
    die("Lỗi truy vấn món ăn nổi bật: " . mysqli_error($conn));
}

$featuredFoods = [];

while ($food = mysqli_fetch_assoc($resultFeatured)) {
    $featuredFoods[] = $food;
}

$featuredPages = array_chunk($featuredFoods, 4);

$sqlMainCategories = "
    SELECT * FROM food_categories
    ORDER BY id ASC
";

$resultMainCategories = mysqli_query($conn, $sqlMainCategories);

if (!$resultMainCategories) {
    die("Lỗi truy vấn danh mục thực đơn chính: " . mysqli_error($conn));
}

$mainMenuCategories = [];

while ($category = mysqli_fetch_assoc($resultMainCategories)) {
    $categoryId = (int) $category['id'];

    $sqlMainFoods = "
        SELECT * FROM foods
        WHERE category_id = $categoryId
        AND status = 'Còn bán'
        ORDER BY price DESC, id DESC
        LIMIT 4
    ";

    $resultMainFoods = mysqli_query($conn, $sqlMainFoods);

    if (!$resultMainFoods) {
        die("Lỗi truy vấn món ăn thực đơn chính: " . mysqli_error($conn));
    }

    $category['foods'] = [];

    while ($food = mysqli_fetch_assoc($resultMainFoods)) {
        $category['foods'][] = $food;
    }

    $mainMenuCategories[] = $category;
}

$mainMenuPages = array_chunk($mainMenuCategories, 4);
?>

<!-- Slider Banner -->
<div id="bannerSlider" class="carousel slide" data-bs-ride="carousel">

    <div class="carousel-indicators">

        <button 
            type="button" 
            data-bs-target="#bannerSlider" 
            data-bs-slide-to="0" 
            class="active">
        </button>

        <button 
            type="button" 
            data-bs-target="#bannerSlider" 
            data-bs-slide-to="1">
        </button>

        <button 
            type="button" 
            data-bs-target="#bannerSlider" 
            data-bs-slide-to="2">
        </button>

    </div>

    <div class="carousel-inner">

        <div class="carousel-item active">

            <img 
                src="/huongviet/assets/images/banner1.jpg"
                class="d-block w-100 slider-image"
                alt="Banner 1"
            >

            <div class="carousel-caption">
                <h1>Nhà hàng Hương Việt</h1>
                <p>Ẩm thực Việt Nam truyền thống</p>
            </div>

        </div>

        <div class="carousel-item">

            <img 
                src="/huongviet/assets/images/banner2.jpg"
                class="d-block w-100 slider-image"
                alt="Banner 2"
            >

            <div class="carousel-caption">
                <h1>Món ăn hấp dẫn</h1>
                <p>Không gian sang trọng</p>
            </div>

        </div>

        <div class="carousel-item">

            <img 
                src="/huongviet/assets/images/banner3.jpg"
                class="d-block w-100 slider-image"
                alt="Banner 3"
            >

            <div class="carousel-caption">
                <h1>Đặt bàn online</h1>
                <p>Nhanh chóng và tiện lợi</p>
            </div>

        </div>

    </div>

    <button 
        class="carousel-control-prev" 
        type="button" 
        data-bs-target="#bannerSlider" 
        data-bs-slide="prev">

        <span class="carousel-control-prev-icon"></span>

    </button>

    <button 
        class="carousel-control-next" 
        type="button" 
        data-bs-target="#bannerSlider" 
        data-bs-slide="next">

        <span class="carousel-control-next-icon"></span>

    </button>

</div>

<!-- Thực đơn chính -->
<section class="main-menu-section">

    <div class="home-section-card">

    <div class="main-menu-heading">

        <p>Quán Hương Việt</p>

        <h2>THỰC ĐƠN CHÍNH</h2>

    </div>

    <div class="main-menu-wrapper">

        <button 
            class="main-menu-nav main-menu-prev" 
            type="button"
            onclick="changeMainMenuPage(-1)">

            <i class="bi bi-chevron-left"></i>

        </button>

        <div class="main-menu-book">

            <?php if (!empty($mainMenuPages)) { ?>

                <?php foreach ($mainMenuPages as $pageIndex => $categories) { ?>

                    <?php
                        $leftCategories = array_slice($categories, 0, 2);
                        $rightCategories = array_slice($categories, 2, 2);
                    ?>

                    <div class="main-menu-page <?php echo $pageIndex === 0 ? 'active' : ''; ?>">

                        <div class="book-left-content">

                            <?php foreach ($leftCategories as $category) { ?>

                                <div class="main-menu-category">

                                    <h3>
                                        <?php echo htmlspecialchars($category['category_name']); ?>
                                    </h3>

                                    <ul>

                                        <?php if (!empty($category['foods'])) { ?>

                                            <?php foreach ($category['foods'] as $food) { ?>

                                                <li>
                                                    <span class="main-dish-name">
                                                        <?php echo htmlspecialchars($food['food_name']); ?>
                                                    </span>

                                                    <span class="main-dish-price">
                                                        <?php echo number_format($food['price'], 0, ',', '.') . "đ"; ?>
                                                    </span>
                                                </li>

                                            <?php } ?>

                                        <?php } else { ?>

                                            <li>
                                                <span class="main-dish-name">Chưa có món</span>
                                            </li>

                                        <?php } ?>

                                    </ul>

                                </div>

                            <?php } ?>

                        </div>

                        <div class="book-right-content">

                            <?php foreach ($rightCategories as $category) { ?>

                                <div class="main-menu-category">

                                    <h3>
                                        <?php echo htmlspecialchars($category['category_name']); ?>
                                    </h3>

                                    <ul>

                                        <?php if (!empty($category['foods'])) { ?>

                                            <?php foreach ($category['foods'] as $food) { ?>

                                                <li>
                                                    <span class="main-dish-name">
                                                        <?php echo htmlspecialchars($food['food_name']); ?>
                                                    </span>

                                                    <span class="main-dish-price">
                                                        <?php echo number_format($food['price'], 0, ',', '.') . "đ"; ?>
                                                    </span>
                                                </li>

                                            <?php } ?>

                                        <?php } else { ?>

                                            <li>
                                                <span class="main-dish-name">Chưa có món</span>
                                            </li>

                                        <?php } ?>

                                    </ul>

                                </div>

                            <?php } ?>

                        </div>

                    </div>

                <?php } ?>

            <?php } else { ?>

                <div class="main-menu-page active">

                    <div class="book-left-content">

                        <div class="main-menu-category">

                            <h3>Thực đơn</h3>

                            <ul>
                                <li>
                                    <span class="main-dish-name">Chưa có danh mục</span>
                                </li>
                            </ul>

                        </div>

                    </div>

                    <div class="book-right-content"></div>

                </div>

            <?php } ?>

        </div>

        <button 
            class="main-menu-nav main-menu-next" 
            type="button"
            onclick="changeMainMenuPage(1)">

            <i class="bi bi-chevron-right"></i>

        </button>

    </div>

    </div>

</section>

<script>
    let currentMainMenuPage = 0;

    function changeMainMenuPage(direction) {
        const pages = document.querySelectorAll(".main-menu-page");

        if (pages.length === 0) {
            return;
        }

        const nextPage = currentMainMenuPage + direction;

        if (nextPage < 0 || nextPage >= pages.length) {
            return;
        }

        currentMainMenuPage = nextPage;

        pages.forEach(function (page, index) {
            page.classList.toggle("active", index === currentMainMenuPage);
        });
    }
</script>

<!-- Món ăn nổi bật -->
<section class="featured-section">

    <div class="home-section-card">

    <div class="featured-heading">

        <p>Quán Hương Việt</p>

        <h2>MÓN NỔI BẬT</h2>

    </div>

    <div class="featured-slider" data-featured-slider>

        <button 
            class="featured-nav featured-nav-prev" 
            type="button"
            aria-label="Trang món trước"
            data-featured-prev>

            <i class="bi bi-chevron-left"></i>

        </button>

        <div class="featured-pages">

            <?php if (!empty($featuredPages)) { ?>

                <?php foreach ($featuredPages as $pageIndex => $foods) { ?>

                    <div class="featured-page <?php echo $pageIndex === 0 ? 'active' : ''; ?>">

                        <div class="featured-grid">

                            <?php foreach ($foods as $food) { ?>

                                <article class="featured-card">

                                    <div class="featured-card-image">

                                        <img 
                                            src="/huongviet/assets/images/<?php echo htmlspecialchars($food['image']); ?>" 
                                            alt="<?php echo htmlspecialchars($food['food_name']); ?>"
                                        >

                                    </div>

                                    <h3>
                                        <?php echo htmlspecialchars($food['food_name']); ?>
                                    </h3>

                                </article>

                            <?php } ?>

                        </div>

                    </div>

                <?php } ?>

            <?php } else { ?>

                <p class="featured-empty">
                    Hiện chưa có món ăn nổi bật.
                </p>

            <?php } ?>

        </div>

        <button 
            class="featured-nav featured-nav-next" 
            type="button"
            aria-label="Trang món tiếp theo"
            data-featured-next>

            <i class="bi bi-chevron-right"></i>

        </button>

    </div>

    <a class="featured-more" href="/huongviet/pages/menu.php">
        XEM THÊM
    </a>

    </div>

</section>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const slider = document.querySelector("[data-featured-slider]");

        if (!slider) {
            return;
        }

        const pages = slider.querySelectorAll(".featured-page");
        const prevButton = slider.querySelector("[data-featured-prev]");
        const nextButton = slider.querySelector("[data-featured-next]");
        let currentPage = 0;

        function showPage(pageIndex) {
            if (pages.length === 0) {
                return;
            }

            currentPage = (pageIndex + pages.length) % pages.length;

            pages.forEach(function (page, index) {
                page.classList.toggle("active", index === currentPage);
            });
        }

        if (pages.length <= 1) {
            prevButton.disabled = true;
            nextButton.disabled = true;
        }

        prevButton.addEventListener("click", function () {
            showPage(currentPage - 1);
        });

        nextButton.addEventListener("click", function () {
            showPage(currentPage + 1);
        });
    });
</script>

<!-- Đặt bàn -->
<section class="home-booking-section">

    <div class="home-section-card">

    <div class="home-booking-container">

        <div class="home-booking-image">

            <img 
                src="/huongviet/assets/images/booking_food.jpg" 
                alt="Đặt bàn Hương Việt"
            >

        </div>

        <div class="home-booking-form-box">

            <h2>BOOK BÀN NGAY</h2>

            <p>
                Hãy nhanh tay đặt bàn để được chúng tôi phục vụ tốt nhất nhé!!!
            </p>

            <form action="/huongviet/pages/booking.php" method="GET" class="home-booking-form">

                <input type="text" name="customer_name" placeholder="Họ tên *" required>

                <input type="text" name="phone" placeholder="Điện thoại *" required>

                <input type="email" name="email" placeholder="Email">

                <input type="text" name="address" placeholder="Địa chỉ">

                <input type="date" name="booking_date" placeholder="Ngày đặt">

                <input type="time" name="booking_time" placeholder="Giờ đặt">

                <textarea name="note" placeholder="Nội dung"></textarea>

                <button type="submit">ĐẶT NGAY</button>

            </form>

        </div>

    </div>

    </div>

</section>

<!-- Đôi nét về quán -->
<section class="about-restaurant-section">

    <div class="home-section-card">

    <div class="about-restaurant-container">

        <div class="about-paper-box">

            <p class="about-small-title">Đôi nét về</p>

            <h2>QUÁN ĂN GIA ĐÌNH HƯƠNG VIỆT</h2>

            <p class="about-text">
                Hương Việt Quán chuyên phục vụ các món ăn gia đình, các món nhậu bình dân như lẩu, nướng, chiên, xào, hấp và nhiều món đặc sản Việt Nam. Không gian quán gần gũi, ấm cúng, phù hợp cho gia đình, bạn bè và các buổi liên hoan.
            </p>

            <a href="/huongviet/pages/about.php" class="about-more-btn">XEM THÊM</a>

        </div>

        <div class="about-image-group">

            <div class="about-image about-image-main">

                <img 
                    src="/huongviet/assets/images/about_food_1.jpg" 
                    alt="Món ăn Hương Việt"
                >

            </div>

            <div class="about-image about-image-top">

                <img 
                    src="/huongviet/assets/images/about_food_2.jpg" 
                    alt="Món ăn Hương Việt"
                >

            </div>

            <div class="about-image about-image-bottom">

                <img 
                    src="/huongviet/assets/images/about_food_3.jpg" 
                    alt="Món ăn Hương Việt"
                >

            </div>

        </div>

    </div>

    </div>

</section>

<!-- Footer trang chủ -->
<section class="home-footer-section">

    <div class="home-footer-container">

        <div class="home-footer-main">

            <div class="home-footer-info">

                <h2>ẨM THỰC HƯƠNG VIỆT BẾN CÁT</h2>

                <div class="footer-info-list">

                    <div class="footer-info-item">

                        <div class="footer-info-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>

                        <div class="footer-info-content">
                            <strong>Địa chỉ</strong>
                            <p>Đường DJ 10, KDC Mỹ Phước 3, P. Thới Hòa, TP Hồ Chí Minh</p>
                        </div>

                    </div>

                    <div class="footer-info-item">

                        <div class="footer-info-icon">
                            <i class="bi bi-telephone"></i>
                        </div>

                        <div class="footer-info-content">
                            <strong>Điện thoại</strong>
                            <p>0988659291</p>
                        </div>

                    </div>

                    <div class="footer-info-item">

                        <div class="footer-info-icon">
                            <i class="bi bi-envelope"></i>
                        </div>

                        <div class="footer-info-content">
                            <strong>Email</strong>
                            <p>quanhuongviet123@gmail.com</p>
                        </div>

                    </div>

                </div>

            </div>

            <div class="home-footer-map">

                <?php if (file_exists(__DIR__ . "/assets/images/footer_map.jpg")) { ?>

                    <img 
                        src="/huongviet/assets/images/footer_map.jpg" 
                        alt="Bản đồ nhà hàng Hương Việt"
                    >

                <?php } else { ?>

                    <div class="home-footer-map-empty">
                        Bản đồ đang cập nhật
                    </div>

                <?php } ?>

            </div>

        </div>

        <div class="home-footer-bottom">

            <div class="home-footer-tags">

                <h3>TAGS TỪ KHÓA</h3>

                <div class="footer-tag-list">

                    <a href="/huongviet/pages/menu.php">Món ngon</a>
                    <a href="/huongviet/pages/booking.php">Đặt bàn</a>
                    <a href="/huongviet/pages/menu.php">Ẩm thực Việt</a>
                    <a href="/huongviet/pages/contact.php">Liên hệ</a>

                </div>

            </div>

            <div class="home-footer-social">

                <a href="#" aria-label="Facebook">
                    <i class="bi bi-facebook"></i>
                </a>

                <a href="#" aria-label="Twitter">
                    <i class="bi bi-twitter-x"></i>
                </a>

                <a href="#" aria-label="Instagram">
                    <i class="bi bi-instagram"></i>
                </a>

                <a href="#" aria-label="LinkedIn">
                    <i class="bi bi-linkedin"></i>
                </a>

            </div>

            <p class="home-footer-copyright">
                Bản quyền © NHÀ HÀNG HƯƠNG VIỆT. All rights reserved
            </p>

        </div>

    </div>

</section>

<?php
require_once __DIR__ . "/includes/footer.php";
?>
