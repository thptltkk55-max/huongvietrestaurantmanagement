<?php
$page_title = "Giới thiệu - Nhà hàng Hương Việt";
$page_css = "/huongviet/assets/css/about.css";

require_once __DIR__ . "/../includes/header.php";
require_once __DIR__ . "/../includes/navbar.php";

function aboutImage($file, $alt)
{
    $path = __DIR__ . "/../assets/images/" . $file;

    if (file_exists($path)) {
        echo '<img src="/huongviet/assets/images/' . htmlspecialchars($file, ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($alt, ENT_QUOTES, 'UTF-8') . '">';
    } else {
        echo '<div class="about-no-image">Chưa có ảnh</div>';
    }
}
?>

<section class="about-breadcrumb">
    <div class="about-container">
        <a href="/huongviet/index.php">Trang chủ</a>
        <span>/</span>
        <span>Giới thiệu</span>
    </div>
</section>

<section class="about-page">
    <div class="about-container">
        <h1>QUÁN ĂN GIA ĐÌNH HƯƠNG VIỆT</h1>

        <p class="about-lead">
            Hương Việt - Dịch vụ tiệc cưới hỏi, liên hoan sinh nhật, và các bữa ăn gia đình tuyệt vời tại Bến Cát Bình Dương
        </p>

        <div class="about-content">
            <p class="about-greeting">
                Quán ăn gia đình Hương Việt xin kính chào Quý Khách!
            </p>

            <p>
                Chúng tôi chuyên phục vụ các món ăn gia đình đậm đà hương vị Việt và các món nhậu bình dân cho những buổi tụ tập bạn bè, gia đình. Đặc biệt, Hương Việt còn cung cấp dịch vụ đặt tiệc trọn gói cho các sự kiện như cưới hỏi, sinh nhật, liên hoan và những dịp đặc biệt khác.
            </p>
        </div>

        <div class="about-image-box">
            <?php aboutImage("about_restaurant.jpg", "Quán ăn gia đình Hương Việt"); ?>
        </div>

        <p class="about-image-caption">
            Hình ảnh trước quán và nhân viên quán đang chuẩn bị món ăn
        </p>
    </div>
</section>

<section class="about-section">
    <div class="about-container">
        <h2>Dịch vụ đa dạng và chuyên nghiệp</h2>

        <p>
            Hương Việt cung cấp dịch vụ đặt bàn cho các sự kiện như tiệc liên hoan, sinh nhật, họp mặt gia đình, bạn bè và đồng nghiệp. Đội ngũ nhân viên phục vụ chu đáo, tận tâm, luôn sẵn sàng hỗ trợ khách hàng từ khâu lên ý tưởng, bố trí không gian tiệc đến chuẩn bị món ăn.
        </p>

        <p>
            Chúng tôi không chỉ phục vụ các món ăn ngon mà còn hỗ trợ các dịch vụ như chuẩn bị rạp, bàn ghế, chén ly, âm thanh, ánh sáng, MC, quay phim, chụp ảnh theo nhu cầu của khách hàng.
        </p>

        <div class="about-image-box">
            <?php aboutImage("about_service.jpg", "Dịch vụ tiệc tại Hương Việt"); ?>
        </div>

        <p class="about-image-caption">
            Một số hình ảnh trang trí tiệc cưới, tiệc tất niên tại Hương Việt
        </p>
    </div>
</section>

<section class="about-section">
    <div class="about-container">
        <h2>Thực đơn phong phú và chất lượng</h2>

        <p>
            Quán nổi tiếng với các món ăn gia đình ngon miệng, hấp dẫn, mang đậm hương vị truyền thống Việt Nam. Đặc biệt, các món như cá lóc nhồi thịt, lẩu hải sản, gà nướng, món hấp, món xào và nhiều món đặc sản khác luôn được nhiều thực khách yêu thích.
        </p>

        <div class="about-image-box">
            <?php aboutImage("about_food_quality.jpg", "Thực đơn Hương Việt"); ?>
        </div>

        <p class="about-image-caption">
            Thực đơn đa dạng tùy vào khách hàng yêu cầu
        </p>
    </div>
</section>

<section class="about-section">
    <div class="about-container">
        <h2>Không gian ấm cúng và thân thiện</h2>

        <p>
            Với không gian gần gũi, ấm cúng, Hương Việt tạo cảm giác thân thiện, phù hợp cho các buổi tụ họp gia đình, bạn bè hay đồng nghiệp. Đội ngũ nhân viên nhiệt tình, chuyên nghiệp luôn sẵn sàng phục vụ và đáp ứng mọi nhu cầu của khách hàng.
        </p>

        <div class="about-image-box">
            <?php aboutImage("about_space.jpg", "Không gian Hương Việt"); ?>
        </div>

        <p class="about-image-caption">
            Không gian bàn tiệc lịch sự tại Hương Việt
        </p>
    </div>
</section>

<section class="about-contact-section">
    <div class="about-container">
        <p>
            Với menu phong phú, đa dạng và giá cả hợp lý, chúng tôi cam kết làm Quý Khách hài lòng.
        </p>

        <p>
            <a href="/huongviet/pages/menu.php">Xem menu món ăn của Hương Việt tại đây.</a>
        </p>

        <div class="about-contact-info">
            <p><strong>Liên hệ đặt tiệc:</strong></p>
            <p><strong>Số điện thoại:</strong> 0988659291 - 0983466539</p>
            <p><strong>Email:</strong> quanhuongviet123@gmail.com</p>
            <p><strong>Địa chỉ:</strong> Đường DJ10, khu phố 3B, phường Thới Hòa, TP. Bến Cát, Bình Dương</p>
            <p><strong>Rất hân hạnh được đón tiếp và phục vụ Quý Khách!</strong></p>
        </div>

        <div class="about-share-box">
            <strong>Chia sẻ:</strong>

            <div class="about-share-icons">
                <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                <a href="#" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                <a href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
            </div>
        </div>
    </div>
</section>

<?php
require_once __DIR__ . "/../includes/footer.php";
?>
