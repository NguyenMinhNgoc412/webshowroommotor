<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/product.css">
    <link rel="stylesheet" href="../assets/css/contact.css">
    <link rel="stylesheet" href="../assets/css/customers.css">
    <!-- ICON -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="dashboard-body">

<div class="admin-wrapper">
    <aside class="sidebar">

        <h2>SHOWROOM<span>99</span></h2>
        <ul>
            <li>
                <a href="dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-house"></i> <span>Dashboard</span>
                </a>
            </li>

            <li>
                <a href="products.php" class="<?= $current_page == 'products.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-motorcycle"></i> <span>Sản phẩm</span>
                </a>
            </li>

            <li>
                <a href="inventory.php" class="<?= $current_page == 'inventory.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-boxes-stacked"></i> <span>Kho</span>
                </a>
            </li>

            <li>
                <a href="invoices.php" class="<?= $current_page == 'invoices.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-file-invoice"></i> <span>Hoá đơn</span>
                </a>
            </li>

            <li>
                <a href="contact.php" class="<?= $current_page == 'contact.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-envelope"></i> <span>Liên hệ</span>
                </a>
            </li>

            <li>
                <a href="customers.php" class="<?= $current_page == 'customers.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-users"></i> <span>Khách hàng</span>
                </a>
            </li>
            <li>
    <a href="statistics_customers.php"
       class="<?= $current_page == 'statistics_customers.php' ? 'active' : '' ?>">
        <i class="fa-solid fa-chart-column"></i>
        <span>Thống kê khách hàng</span>
    </a>
        </li>


            <li style="margin-top:auto; border-top:1px solid rgba(255,255,255,.1); padding-top:10px;">
                <a href="logout.php" style="color:#ffb3b3;">
                    <i class="fa-solid fa-right-from-bracket"></i> <span>Đăng xuất</span>
                </a>
            </li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="admin-header">
            <h1>Tổng quan hệ thống</h1>
            <div class="admin-user">
                <i class="fa-solid fa-circle-user"></i>
                Xin chào
            </div>
        </header>
