<?php
require_once 'config/database.php';
$where = "WHERE p.status = 1";

if (!empty($_GET['keyword'])) {
    $keyword = mysqli_real_escape_string($conn, $_GET['keyword']);
    $where .= " AND p.name LIKE '%$keyword%'";
}

if (!empty($_GET['brand'])) {
    $brand = (int)$_GET['brand'];
    $where .= " AND p.brand_id = $brand";
}

if (!empty($_GET['category'])) {
    $category = (int)$_GET['category'];
    $where .= " AND p.category_id = $category";
}

if (!empty($_GET['price'])) {
    switch ((int)$_GET['price']) {
        case 1:
            $where .= " AND p.price < 30000000";
            break;
        case 2:
            $where .= " AND p.price BETWEEN 30000000 AND 50000000";
            break;
        case 3:
            $where .= " AND p.price BETWEEN 50000000 AND 80000000";
            break;
        case 4:
            $where .= " AND p.price > 80000000";
            break;
    }
}

$sql = "
    SELECT p.*, b.name AS brand_name, c.name AS category_name
    FROM products p
    LEFT JOIN brands b ON p.brand_id = b.id
    LEFT JOIN categories c ON p.category_id = c.id
    $where
    ORDER BY p.created_at DESC
";

$result = mysqli_query($conn, $sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>99 Motorbike Showroom</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>
    <header class="header">
    <div class="header-inner">
        <img src="assets/images/logo.png" alt="99 Motorbike" class="logo">
        <div class="header-text">
            <h1>99 Motorbike Showroom</h1>
            <p>Uy tín - Chính hãng - Chất lượng</p>
        </div>
    </div>
    </header>

<?php
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
?>

<section class="search-section">
    <form method="GET" action="index.php" class="search-big">
        <input
            type="text"
            name="keyword"
            placeholder="Tìm kiếm xe máy theo tên, hãng, loại..."
            value="<?php echo htmlspecialchars($keyword); ?>"
        >
        <button type="submit">Tìm kiếm</button>
    </form>
</section>

<?php
$brands = mysqli_query($conn, "SELECT * FROM brands");
$categories = mysqli_query($conn, "SELECT * FROM categories");
?>

<nav class="nav-menu">
    <ul>
        <li><a href="index.php">Trang chủ</a></li>

        <li class="dropdown">
            <a href="javascript:void(0)">Hãng xe ▾</a>
            <ul class="dropdown-menu">
                <?php while ($b = mysqli_fetch_assoc($brands)): ?>
                    <li>
                        <a href="index.php?brand=<?php echo $b['id']; ?>">
                            <?php echo $b['name']; ?>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </li>

        <li class="dropdown">
            <a href="javascript:void(0)">Loại xe ▾</a>
            <ul class="dropdown-menu">
                <?php while ($c = mysqli_fetch_assoc($categories)): ?>
                    <li>
                        <a href="index.php?category=<?php echo $c['id']; ?>">
                            <?php echo $c['name']; ?>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </li>

        <li><a href="#footer">Liên hệ</a></li>
    </ul>
</nav>
<section class="banner-slider">
    <div class="slide active" style="background-image:url('assets/images/banner4.jpg')"></div>
    <div class="slide" style="background-image:url('assets/images/banner8.jpg')"></div>
    <div class="slide" style="background-image:url('assets/images/banner3.jpg')"></div>
    <div class="slide" style="background-image:url('assets/images/banner9.png')"></div>
    <div class="slide" style="background-image:url('assets/images/banner10.jpg')"></div>
</section>

    <section class="products">
        <h2>DANH SÁCH XE</h2>

         <div class="product-layout">

        <aside class="filter-sidebar">
            <form method="GET" action="index.php">

                <h3>Lọc sản phẩm</h3>

                <label>Hãng xe</label>
                <select name="brand">
                    <option value="">Tất cả</option>
                    <?php
                    $brands = mysqli_query($conn, "SELECT * FROM brands");
                    while ($b = mysqli_fetch_assoc($brands)):
                    ?>
                        <option value="<?= $b['id'] ?>"
                            <?= ($_GET['brand'] ?? '') == $b['id'] ? 'selected' : '' ?>>
                            <?= $b['name'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label>Loại xe</label>
                <select name="category">
                    <option value="">Tất cả</option>
                    <?php
                    $categories = mysqli_query($conn, "SELECT * FROM categories");
                    while ($c = mysqli_fetch_assoc($categories)):
                    ?>
                        <option value="<?= $c['id'] ?>"
                            <?= ($_GET['category'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                            <?= $c['name'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label>Khoảng giá</label>
                <select name="price">
                    <option value="">Tất cả</option>
                    <option value="1" <?= ($_GET['price'] ?? '') == '1' ? 'selected' : '' ?>>
                        Dưới 30 triệu
                    </option>
                    <option value="2" <?= ($_GET['price'] ?? '') == '2' ? 'selected' : '' ?>>
                        30 – 50 triệu
                    </option>
                    <option value="3" <?= ($_GET['price'] ?? '') == '3' ? 'selected' : '' ?>>
                        50 – 80 triệu
                    </option>
                    <option value="4" <?= ($_GET['price'] ?? '') == '4' ? 'selected' : '' ?>>
                        Trên 80 triệu
                    </option>
                </select>

                <button type="submit">Tìm kiếm</button>

            </form>
        </aside>

        <div class="product-grid">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="product-card">
                    <img src="assets/uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">

                    <h3><?php echo $row['name']; ?></h3>

                    <p class="brand">
                        <?php echo $row['brand_name']; ?> |
                        <?php echo $row['category_name']; ?>
                    </p>

                    <p class="price">
                        <?php echo number_format($row['price']); ?> đ
                    </p>

                    <button class="btn-detail"
                        data-name="<?= htmlspecialchars($row['name']) ?>"
                        data-image="assets/uploads/<?= $row['image'] ?>"
                        data-price="<?= number_format($row['price']) ?> ₫"
                        data-brand="<?= $row['brand_name'] ?>"
                        data-category="<?= $row['category_name'] ?>"
                        data-description="<?= htmlspecialchars($row['description']) ?>"
                        data-specs="<?= htmlspecialchars($row['specifications']) ?>">
                        Xem chi tiết
                    </button>

                    <button class="btn-contact"
                        data-product-id="<?= $row['id'] ?>"
                        data-product-name="<?= htmlspecialchars($row['name']) ?>">
                        Mua xe
                    </button>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Chưa có sản phẩm nào.</p>
        <?php endif; ?>
    </div>
    </section>

    <footer id="footer">
        <div class="footer-container">

        <div class="footer-box">
            <h3>CÔNG TY TNHH THƯƠNG MẠI DỊCH VỤ XE MÁY 99</h3>
            <p><strong>GPDKKD:</strong> 0123456789 do Sở KH&ĐT TP.HN cấp</p>
            <p><strong>Địa chỉ:</strong> 54 phố Triều Khúc thành phố Hà Nội</p>
            <p><strong>Email:</strong> showroomxemay99@gmail.com</p>
            <p><strong>Hotline:</strong> 0909 123 456</p>
        </div>

        <div class="footer-box">
            <h3>THỜI GIAN LÀM VIỆC</h3>
            <p>Thứ 2 – Thứ 7: 8:00 – 18:00</p>
            <p>Chủ nhật: 8:00 – 16:00</p>
            <p>Hỗ trợ tư vấn ngoài giờ qua điện thoại</p>
        </div>

        <div class="footer-box">
            <h3>KẾT NỐI VỚI CHÚNG TÔI</h3>
            <ul class="social-links">
                <li>
                    <a href="https://www.facebook.com/profile.php?id=61585479210060" target="_blank">
                        <i class="fa-brands fa-facebook"></i> Facebook
                    </a>
                </li>

                <li>
                    <a href="#" target="_blank">
                        <i class="fa-brands fa-instagram"></i> Instagram
                    </a>
                </li>

                <li>
                    <a href="#" target="_blank">
                        <i class="fa-brands fa-zalo"></i> Zalo 
                    </a>
                </li>

            </ul>
        </div>

    </div>

    <div class="footer-bottom">
        © <?php echo date('Y'); ?> Showroom Xe Máy 99. All rights reserved.
    </div>
    </footer>
    <div id="overlay"></div>

    <div id="productModal">
        <div class="modal-box detail-modal-box">
            <div class="modal-left">
                <img id="modalImage">
            </div>

            <div class="modal-right">
                <h2 id="modalName"></h2>
                <p><b>Hãng:</b> <span id="modalBrand"></span></p>
                <p><b>Loại:</b> <span id="modalCategory"></span></p>
                <p class="price" id="modalPrice"></p>

                <hr>

                <p><b>Mô tả:</b></p>
                <p id="modalDescription"></p>

                <p><b>Thông số:</b></p>
                <p id="modalSpecs"></p>
            </div>
        </div>
    </div>
    <div id="buyOverlay"></div>
    <div id="buyModal">
        <div class="modal-box buy-modal-box">
            <h2>Đăng ký mua xe</h2>
            <p id="buyProductName" style="margin-bottom:15px;color:#e53935"></p>

            <form id="buyForm">
                <input type="hidden" name="product_id" id="buyProductId">
                <input type="text" name="customer_name" placeholder="Họ và tên" required>
                <input type="text" name="phone" placeholder="Số điện thoại" required>
                <input type="email" name="email" placeholder="Email">
                <input type="text" name="address" placeholder="Địa chỉ">
                <textarea name="note" placeholder="Ghi chú"></textarea>

                <button type="submit" class="btn-contact-submit">
                    Gửi yêu cầu
                </button>
            </form>
        </div>
    </div>

    <script src="assets/js/slider.js"></script>
    <script>
    const overlay = document.getElementById('overlay');
    const modal = document.getElementById('productModal');
    const detailModalBox = document.querySelector('.detail-modal-box');

    document.querySelectorAll('.btn-detail').forEach(btn => {
        btn.onclick = () => {
            document.getElementById('modalName').innerText = btn.dataset.name;
            document.getElementById('modalImage').src = btn.dataset.image;
            document.getElementById('modalPrice').innerText = btn.dataset.price;
            document.getElementById('modalBrand').innerText = btn.dataset.brand;
            document.getElementById('modalCategory').innerText = btn.dataset.category;
            document.getElementById('modalDescription').innerText = btn.dataset.description;
            document.getElementById('modalSpecs').innerText = btn.dataset.specs;
            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') {
                overlay.style.display = 'none';
                    modal.style.display = 'none';
                }
            });
            overlay.style.display = 'block';
            modal.style.display = 'block';
        };
    });

modal.onclick = () => {
    overlay.style.display = 'none';
    modal.style.display = 'none';
};

detailModalBox.onclick = e => {
    e.stopPropagation();
};

</script>
<script>
const buyOverlay = document.getElementById('buyOverlay');
const buyModal = document.getElementById('buyModal');
const buyForm = document.getElementById('buyForm');
const buyModalBox = buyModal.querySelector('.buy-modal-box');

document.querySelectorAll('.btn-contact').forEach(btn => {
    btn.onclick = () => {
        document.getElementById('buyProductId').value = btn.dataset.productId;
        document.getElementById('buyProductName').innerText =
            'Sản phẩm: ' + btn.dataset.productName;

        buyOverlay.style.display = 'block';
        buyModal.style.display = 'block';
    };
});

buyModal.onclick = () => {
    buyOverlay.style.display = 'none';
    buyModal.style.display = 'none';
};

buyModalBox.onclick = e => e.stopPropagation();

buyForm.addEventListener('submit', e => {
    e.preventDefault();

    const formData = new FormData(buyForm);

    fetch('save_order.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        console.log('Server response:', data);

        if (data.success) {
            console.log('✔ Gửi yêu cầu thành công');
            buyModal.style.display = 'none';
            buyOverlay.style.display = 'none';
            buyForm.reset();
        } else {
            console.error('Lỗi:', data.error);
        }
    })
    .catch(err => {
        console.error(' Fetch error:', err);
    });
});

</script>



</body>
</html>