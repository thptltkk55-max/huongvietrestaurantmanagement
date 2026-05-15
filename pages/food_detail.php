<?php
require_once __DIR__ . "/../config/database.php";
/** @var mysqli $conn */

$page_title = "Chi tiết món ăn - Nhà hàng Hương Việt";
$page_css = "/huongviet/assets/css/food_detail.css";

require_once __DIR__ . "/../includes/header.php";
require_once __DIR__ . "/../includes/navbar.php";

function foodDetailEscape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$food = null;
$categories = [];
$relatedFoods = [];

$categoryResult = mysqli_query($conn, "SELECT * FROM food_categories ORDER BY id ASC");
if ($categoryResult) {
    while ($category = mysqli_fetch_assoc($categoryResult)) {
        $categories[] = $category;
    }
}

if ($id > 0) {
    $stmt = mysqli_prepare($conn, "
        SELECT 
            f.id,
            f.category_id,
            f.food_name,
            f.price,
            f.image,
            f.description,
            f.status,
            c.category_name
        FROM foods f
        LEFT JOIN food_categories c ON f.category_id = c.id
        WHERE f.id = ?
        LIMIT 1
    ");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $food = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

if ($food) {
    $stmtRelated = mysqli_prepare($conn, "
        SELECT id, food_name, price, image, description
        FROM foods
        WHERE category_id = ?
        AND id != ?
        AND status = 'Còn bán'
        ORDER BY id DESC
        LIMIT 4
    ");
    mysqli_stmt_bind_param($stmtRelated, "ii", $food['category_id'], $food['id']);
    mysqli_stmt_execute($stmtRelated);
    $relatedResult = mysqli_stmt_get_result($stmtRelated);

    while ($related = mysqli_fetch_assoc($relatedResult)) {
        $relatedFoods[] = $related;
    }
}
?>

<section class="food-detail-breadcrumb">
    <div class="food-detail-container">
        <a href="/huongviet/index.php">Trang chủ</a>
        <span>/</span>
        <a href="/huongviet/pages/menu.php">Menu</a>
        <span>/</span>
        <span><?php echo $food ? foodDetailEscape($food['food_name']) : 'Chi tiết món ăn'; ?></span>
    </div>
</section>

<section class="food-detail-page">
    <div class="food-detail-container">
        <?php if (!$food) { ?>
            <div class="food-detail-not-found">
                Không tìm thấy món ăn.
            </div>
        <?php } else { ?>
            <div class="food-detail-layout">
                <aside class="food-detail-sidebar">
                    <div class="food-detail-sidebar-title">
                        DANH MỤC MENU
                    </div>

                    <div class="food-detail-category-list">
                        <a href="/huongviet/pages/menu.php">Tất cả</a>

                        <?php foreach ($categories as $category) { ?>
                            <a 
                                href="/huongviet/pages/menu.php?category_id=<?php echo (int) $category['id']; ?>"
                                class="<?php echo ((int) $food['category_id'] === (int) $category['id']) ? 'active' : ''; ?>"
                            >
                                <?php echo foodDetailEscape($category['category_name']); ?>
                            </a>
                        <?php } ?>
                    </div>
                </aside>

                <main class="food-detail-main">
                    <div class="food-detail-box">
                        <div class="food-detail-image">
                            <?php if (!empty($food['image'])) { ?>
                                <img 
                                    src="/huongviet/assets/images/<?php echo foodDetailEscape($food['image']); ?>" 
                                    alt="<?php echo foodDetailEscape($food['food_name']); ?>"
                                >
                            <?php } else { ?>
                                <div class="food-detail-no-image">
                                    Chưa có ảnh
                                </div>
                            <?php } ?>
                        </div>

                        <div class="food-detail-info">
                            <h1><?php echo foodDetailEscape($food['food_name']); ?></h1>

                            <p class="food-detail-category">
                                Danh mục: <?php echo foodDetailEscape($food['category_name'] ?? 'Chưa phân loại'); ?>
                            </p>

                            <p class="food-detail-price">
                                Giá: <?php echo number_format((float) $food['price'], 0, ',', '.') . " VNĐ"; ?>
                            </p>

                            <p class="food-detail-status">
                                Trạng thái: <?php echo foodDetailEscape($food['status']); ?>
                            </p>

                            <p class="food-detail-description">
                                <?php echo nl2br(foodDetailEscape($food['description'] ?: 'Món ăn đang được cập nhật mô tả.')); ?>
                            </p>

                            <a href="/huongviet/pages/booking.php" class="food-detail-booking-btn">
                                Đặt bàn ngay
                            </a>
                        </div>
                    </div>

                    <section class="related-foods-section">
                        <h2>SẢN PHẨM CÙNG LOẠI</h2>

                        <?php if ($relatedFoods) { ?>
                            <div class="related-food-grid">
                                <?php foreach ($relatedFoods as $relatedFood) { ?>
                                    <a class="related-food-card" href="/huongviet/pages/food_detail.php?id=<?php echo (int) $relatedFood['id']; ?>">
                                        <?php if (!empty($relatedFood['image'])) { ?>
                                            <img 
                                                src="/huongviet/assets/images/<?php echo foodDetailEscape($relatedFood['image']); ?>" 
                                                alt="<?php echo foodDetailEscape($relatedFood['food_name']); ?>"
                                            >
                                        <?php } else { ?>
                                            <div class="related-food-no-image">
                                                Chưa có ảnh
                                            </div>
                                        <?php } ?>

                                        <div class="related-food-content">
                                            <h3><?php echo foodDetailEscape($relatedFood['food_name']); ?></h3>
                                            <p class="related-food-price">
                                                <?php echo number_format((float) $relatedFood['price'], 0, ',', '.') . " VNĐ"; ?>
                                            </p>
                                        </div>
                                    </a>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <div class="related-food-empty">
                                Chưa có món ăn cùng loại.
                            </div>
                        <?php } ?>
                    </section>
                </main>
            </div>
        <?php } ?>
    </div>
</section>

<?php
require_once __DIR__ . "/../includes/footer.php";
?>
