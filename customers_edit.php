<?php
require 'includes/auth.php';
require '../config/database.php';
$current_page = 'customers.php';
include 'includes/header.php';

$id = (int)$_GET['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $cccd = $_POST['cccd'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $sql = "UPDATE customers SET full_name=?, cccd=?, phone=?, address=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $full_name, $cccd, $phone, $address, $id);
    $stmt->execute();
    header("Location: customers.php"); exit;
}

$customer = $conn->query("SELECT * FROM customers WHERE id=$id")->fetch_assoc();
?>

<div class="content">
    <div class="customers-form-box">
        <form method="post" class="customers-form">
            <h2>Sửa thông tin khách hàng</h2>

            <div class="form-group">
                <label>Họ tên</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($customer['full_name']) ?>" required>
            </div>

            <div class="form-group">
                <label>CCCD (12 số)</label>
                <input type="text" name="cccd" value="<?= htmlspecialchars($customer['cccd']) ?>" maxlength="12" pattern="[0-9]{12}" required>
            </div>

            <div class="form-group">
                <label>Điện thoại</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($customer['phone']) ?>" required>
            </div>

            <div class="form-group">
                <label>Địa chỉ</label>
                <input type="text" name="address" value="<?= htmlspecialchars($customer['address']) ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-save">Cập nhật</button>
                <a href="customers.php" class="btn-cancel">Hủy bỏ</a>
            </div>
        </form>
    </div>
</div>
