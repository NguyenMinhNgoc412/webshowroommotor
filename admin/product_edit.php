<?php
require 'includes/auth.php';
require '../config/database.php';

$id = (int)$_GET['id'];

$product = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();
$brands = $conn->query("SELECT * FROM brands");
$categories = $conn->query("SELECT * FROM categories");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $brand_id = $_POST['brand_id'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $specifications = $_POST['specifications'];
    $status = $_POST['status'];

    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "assets/uploads/$image");
        $conn->query("UPDATE products SET image='$image' WHERE id=$id");
    }

    $stmt = $conn->prepare("
        UPDATE products
        SET name=?, brand_id=?, category_id=?, price=?, description=?, specifications=?, status=?
        WHERE id=?
    ");
    $stmt->bind_param(
        "siiissii",
        $name,
        $brand_id,
        $category_id,
        $price,
        $description,
        $specifications,
        $status,
        $id
    );
    $stmt->execute();

    header('Location: products.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa sản phẩm</title>
    <link rel="stylesheet" href="../assets/css/product_edit.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="product-edit-wrapper">
    <div class="form-card">
        <div class="form-card-header">
            <h2><i class="fa-solid fa-pen-to-square"></i> Sửa sản phẩm: <?= htmlspecialchars($product['name']) ?></h2>
        </div>

        <form method="post" enctype="multipart/form-data" class="admin-form">
            <div class="form-group">
                <label>Tên xe</label>
                <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Hãng xe</label>
                    <select name="brand_id">
                        <?php while ($b = $brands->fetch_assoc()): ?>
                            <option value="<?= $b['id'] ?>" <?= $b['id']==$product['brand_id']?'selected':'' ?>>
                                <?= $b['name'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Loại xe</label>
                    <select name="category_id">
                        <?php while ($c = $categories->fetch_assoc()): ?>
                            <option value="<?= $c['id'] ?>" <?= $c['id']==$product['category_id']?'selected':'' ?>>
                                <?= $c['name'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Giá bán (VNĐ)</label>
                    <input type="number" name="price" value="<?= $product['price'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Thay đổi hình ảnh (Để trống nếu giữ nguyên)</label>
                    <?php if ($product['image']): ?>
                        <div class="current-image-preview">
                            <img src="../assets/uploads/<?= $product['image'] ?>" alt="Ảnh hiện tại">
                            <span>Ảnh hiện tại</span>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="image">
                </div>
            </div>

            <div class="form-group">
                <label>Mô tả</label>
                <textarea name="description"><?= htmlspecialchars($product['description']) ?></textarea>
            </div>

            <div class="form-group">
                <label>Thông số kỹ thuật</label>
                <textarea name="specifications"><?= htmlspecialchars($product['specifications']) ?></textarea>
            </div>

            <div class="form-group" style="max-width: 300px;">
                <label>Trạng thái hiển thị</label>
                <select name="status">
                    <option value="1" <?= $product['status']?'selected':'' ?>>Đang hiển thị</option>
                    <option value="0" <?= !$product['status']?'selected':'' ?>>Đang ẩn</option>
                </select>
            </div>

            <div class="form-footer">
                <a href="products.php" class="btn btn-cancel">Hủy bỏ</a>
                <button type="submit" class="btn btn-update">
                    <i class="fa-solid fa-save"></i> Cập nhật sản phẩm
                </button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
