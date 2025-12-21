<?php
require 'includes/auth.php';
require '../config/database.php';
include 'includes/header.php';

$sql = "
    SELECT p.*, b.name AS brand_name, c.name AS category_name
    FROM products p
    LEFT JOIN brands b ON p.brand_id = b.id
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.id DESC
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản phẩm</title>
    <link rel="stylesheet" href="assets/css/product.css">
</head>
<body>
    <div class="product-page-wrapper">
    <div class="product-header">
        <h2>Quản lý xe máy</h2>
        <a href="product_add.php" class="btn-primary-custom">
            <i class="fa-solid fa-plus"></i> Thêm xe mới
        </a>
    </div>

    <div class="table-card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Thông tin xe</th>
                    <th>Phân loại</th>
                    <th>Giá niêm yết</th>
                    <th>Trạng thái</th>
                    <th style="text-align: right;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>
                        <div class="product-info">
                            <img src="../assets/uploads/<?= $row['image'] ?>" class="product-thumb" alt="xe">
                            <div class="product-name-wrapper">
                                <strong><?= htmlspecialchars($row['name']) ?></strong>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 500; color: var(--text-heading);"><?= $row['brand_name'] ?></div>
                        <div style="font-size: 0.8rem; color: var(--text-light);"><?= $row['category_name'] ?></div>
                    </td>
                    <td>
                        <span style="font-weight: 700; color: var(--primary-color);">
                            <?= number_format($row['price']) ?> <small>đ</small>
                        </span>
                    </td>
                    <td>
                        <span class="status-badge <?= $row['status'] ? 'badge-active' : 'badge-hidden' ?>">
                            <?= $row['status'] ? 'Đang bán' : 'Tạm ẩn' ?>
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons" style="justify-content: flex-end;">
                            <a href="product_edit.php?id=<?= $row['id'] ?>" class="action-link edit-link" title="Sửa">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <a href="product_delete.php?id=<?= $row['id'] ?>" class="action-link delete-link" title="Xóa" onclick="return confirm('Xác nhận xóa xe này?')">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>


