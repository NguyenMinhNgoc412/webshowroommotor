<?php
require_once 'includes/auth.php';
require_once '../config/database.php';
include 'includes/header.php';

$current_page = basename($_SERVER['PHP_SELF']);

$totalProducts   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM products"))[0] ?? 0;
$totalBrands     = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM brands"))[0] ?? 0;
$totalCategories = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM categories"))[0] ?? 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="dashboard-body">

<div class="admin-wrapper">  
    <main class="main-content">
        <section class="stats">
            <div class="stat-box">
                <p>Tổng sản phẩm</p>
                <h3><?php echo number_format($totalProducts); ?></h3>
            </div>
            <div class="stat-box">
                <p>Hãng xe đối tác</p>
                <h3><?php echo $totalBrands; ?></h3>
            </div>
            <div class="stat-box">
                <p>Danh mục xe</p>
                <h3><?php echo $totalCategories; ?></h3>
            </div>
        </section>

        <section class="welcome-box">
            <h2>Xin chào quay trở lại!</h2>
            <p>Hệ thống đang hoạt động ổn định. Bạn có thể quản lý danh sách xe máy, cập nhật thông tin hãng xe hoặc phản hồi liên hệ của khách hàng ngay bên dưới.</p>
            <a href="products.php" class="btn">Quản lý xe ngay <i class="fa-solid fa-arrow-right" style="margin-left: 8px;"></i></a>
        </section>

    </main>
</div>

</body>
</html>