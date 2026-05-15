<?php
require_once __DIR__ . "/../config/database.php";

$page_title = "Thực đơn - Nhà hàng Hương Việt";
$page_css = "/huongviet/assets/css/menu.css";

require_once __DIR__ . "/../includes/header.php";
require_once __DIR__ . "/../includes/navbar.php";

/* Lấy dữ liệu lọc từ URL */
$category_id = 0;
$keyword = "";

if (isset($_GET['category_id']) && $_GET['category_id'] !== "") {
    $category_id = (int) $_GET['category_id'];
}

if (isset($_GET['keyword'])) {
    $keyword = trim($_GET['keyword']);
}

/* Lấy danh sách danh mục */
$sqlCategories = "SELECT * FROM food_categories ORDER BY id ASC";
$resultCategories = mysqli_query($conn, $sqlCategories);

if (!$resultCategories) {
    die("Lỗi truy vấn danh mục: " . mysqli_error($conn));
}

/* Điều kiện lấy món ăn */
$where = "WHERE f.status = 'Còn bán'";

if ($category_id > 0) {
    $where .= " AND f.category_id = $category_id";
}

if ($keyword !== "") {
    $safeKeyword = mysqli_real_escape_string($conn, $keyword);
    $where .= " AND f.food_name LIKE '%$safeKeyword%'";
}

/* Lấy danh sách món ăn */
$sqlFoods = "
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
    LEFT JOIN food_categories c 
        ON f.category_id = c.id
    $where
    ORDER BY f.id DESC
";

$resultFoods = mysqli_query($conn, $sqlFoods);

if (!$resultFoods) {
    die("Lỗi truy vấn món ăn: " . mysqli_error($conn));
}

?>

<section class="menu-page">

    <div class="menu-container">

        <div class="menu-title">

            <h2>MENU</h2>

        </div>

        <div class="menu-layout">

            <aside class="menu-sidebar">

                <div class="sidebar-title">
                    DANH MỤC MENU
                </div>

                <div class="category-list">

                    <a 
                        href="/huongviet/pages/menu.php" 
                        class="<?php echo ($category_id == 0) ? 'active' : ''; ?>"
                    >
                        Tất cả
                    </a>

                    <?php while ($category = mysqli_fetch_assoc($resultCategories)) { ?>

                        <a 
                            href="/huongviet/pages/menu.php?category_id=<?php echo $category['id']; ?>" 
                            class="<?php echo ($category_id == $category['id']) ? 'active' : ''; ?>"
                        >
                            <?php echo htmlspecialchars($category['category_name']); ?>
                        </a>

                    <?php } ?>

                </div>

            </aside>

            <div class="menu-content">

                <!-- Form tìm kiếm -->
                <form class="menu-search" method="GET" action="/huongviet/pages/menu.php">

                    <?php if ($category_id > 0) { ?>

                        <input 
                            type="hidden" 
                            name="category_id" 
                            value="<?php echo $category_id; ?>"
                        >

                    <?php } ?>

                    <input 
                        type="text" 
                        name="keyword" 
                        placeholder="Nhập tên món cần tìm..."
                        value="<?php echo htmlspecialchars($keyword); ?>"
                    >

                    <button type="submit">
                        <i class="bi bi-search"></i>
                        Tìm kiếm
                    </button>

                </form>

                <!-- Danh sách món ăn -->
                <?php if (mysqli_num_rows($resultFoods) > 0) { ?>

                    <div class="menu-product-grid">

                        <?php while ($food = mysqli_fetch_assoc($resultFoods)) { ?>

                            <a 
                                href="/huongviet/pages/food_detail.php?id=<?php echo (int) $food['id']; ?>" 
                                class="menu-product-link"
                            >
                                <div class="menu-product-card">

                                    <div class="menu-product-image">

                                        <?php if (!empty($food['image'])) { ?>

                                            <img 
                                                src="/huongviet/assets/images/<?php echo htmlspecialchars($food['image']); ?>" 
                                                alt="<?php echo htmlspecialchars($food['food_name']); ?>"
                                            >

                                        <?php } else { ?>

                                            <div class="menu-no-image">
                                                Chưa có ảnh
                                            </div>

                                        <?php } ?>

                                    </div>

                                    <div class="menu-product-info">

                                        <h3>
                                            <?php echo htmlspecialchars($food['food_name']); ?>
                                        </h3>

                                        <p class="menu-price">
                                            <?php echo number_format($food['price']); ?> VNĐ
                                        </p>

                                    </div>

                                </div>
                            </a>

                        <?php } ?>

                    </div>

                <?php } else { ?>

                    <div class="menu-empty">
                        Không tìm thấy món ăn phù hợp.
                    </div>

                <?php } ?>

            </div>

        </div>

    </div>

</section>

<?php
require_once __DIR__ . "/../includes/footer.php";
?>
