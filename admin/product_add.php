<?php
require 'includes/auth.php';
require '../config/database.php';

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

    $imageName = '';

    if (!empty($_FILES['image']['name'])) {

        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];

        if (!in_array($ext, $allowed)) {
            die('Chỉ cho phép ảnh JPG, PNG, WEBP');
        }

        $imageName = time() . '_' . uniqid() . '.' . $ext;

        $uploadPath = __DIR__ . '/../assets/uploads/' . $imageName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            die('Upload ảnh thất bại');
        }
    }

    $stmt = $conn->prepare("
        INSERT INTO products
        (name, brand_id, category_id, price, image, description, specifications, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "siiisssi",
        $name,
        $brand_id,
        $category_id,
        $price,
        $imageName,
        $description,
        $specifications,
        $status
    );
    $stmt->execute();

    header('Location: products.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sản phẩm</title>
    <link rel="stylesheet" href="../assets/css/product_add.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="product-add-wrapper">
    <div class="form-card">
        <div class="form-card-header">
            <h2>Thêm sản phẩm mới</h2>
        </div>

        <form method="post" enctype="multipart/form-data" class="admin-form">
            <div class="form-group">
                <label>Tên xe</label>
                <input type="text" name="name" placeholder="Nhập tên xe máy..." required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Hãng xe</label>
                    <select name="brand_id" required>
                        <option value="">-- Chọn hãng --</option>
                        <?php while ($b = $brands->fetch_assoc()): ?>
                            <option value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Loại xe</label>
                    <select name="category_id" required>
                        <option value="">-- Chọn loại --</option>
                        <?php while ($c = $categories->fetch_assoc()): ?>
                            <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Giá niêm yết (VNĐ)</label>
                    <input type="number" name="price" placeholder="Ví dụ: 25000000" required>
                </div>
                <div class="form-group">
                    <label>Ảnh sản phẩm</label>
                    <input type="file" name="image" required>
                </div>
            </div>

            <div class="form-group">
                <label>Mô tả sản phẩm</label>
                <textarea name="description" placeholder="Giới thiệu sơ lược về xe..."></textarea>
            </div>

            <div class="form-group">
                <label>Thông số kỹ thuật</label>
                <textarea name="specifications" placeholder="Công suất, phanh, lốp, tiêu hao xăng..."></textarea>
            </div>

            <div class="form-group" style="max-width: 300px;">
                <label>Trạng thái</label>
                <select name="status">
                    <option value="1">Hiển thị ngay</option>
                    <option value="0">Tạm lưu (Ẩn)</option>
                </select>
            </div>

            <div class="form-footer">
                <a href="products.php" class="btn btn-return">
                    <i class="fa-solid fa-xmark"></i> Hủy & Quay lại
                </a>
                <button type="submit" class="btn btn-save">
                    <i class="fa-solid fa-check"></i> Lưu dữ liệu
                </button>
            </div>
        </form>
    </div>
</div>

</body>
</html>

