<?php
// HIỂN THỊ LỖI (để debug – khi nộp có thể tắt)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'includes/auth.php';
require '../config/database.php';

$current_page = 'customers.php';
include 'includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name = trim($_POST['full_name'] ?? '');
    $cccd      = trim($_POST['cccd'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $address   = trim($_POST['address'] ?? '');

    // ===== KIỂM TRA RỖNG =====
    if ($full_name === '' || $cccd === '' || $phone === '') {
        $error = "❌ Vui lòng nhập đầy đủ thông tin bắt buộc!";
    } else {

        // ===== KIỂM TRA TRÙNG CCCD =====
        $sqlCheck = "SELECT id FROM customers WHERE cccd = ?";
        $stmtCheck = $conn->prepare($sqlCheck);

        if (!$stmtCheck) {
            die("Lỗi prepare CHECK: " . $conn->error);
        }

        $stmtCheck->bind_param("s", $cccd);
        $stmtCheck->execute();
        $stmtCheck->store_result();

        if ($stmtCheck->num_rows > 0) {
            $error = "❌ CCCD đã tồn tại. Vui lòng nhập CCCD khác!";
        } else {

            // ===== INSERT KHÁCH HÀNG =====
            $sqlInsert = "
                INSERT INTO customers (full_name, cccd, phone, address, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ";

            $stmtInsert = $conn->prepare($sqlInsert);

            if (!$stmtInsert) {
                die("Lỗi prepare INSERT: " . $conn->error);
            }

            $stmtInsert->bind_param(
                "ssss",
                $full_name,
                $cccd,
                $phone,
                $address
            );

            if ($stmtInsert->execute()) {
                header("Location: customers.php?msg=added");
                exit;
            } else {
                $error = "❌ Lỗi khi thêm khách hàng: " . $stmtInsert->error;
            }
        }
    }
}
?>

<div class="content">
    <div class="customers-form-box">
        <h2>Thêm khách hàng mới</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="post" class="customers-form">
            <div class="form-group">
                <label>Họ tên</label>
                <input type="text" name="full_name"
                       value="<?= htmlspecialchars($full_name ?? '') ?>"
                       placeholder="Nhập họ tên..." required>
            </div>

            <div class="form-group">
                <label>CCCD (12 số)</label>
                <input type="text" name="cccd"
                       value="<?= htmlspecialchars($cccd ?? '') ?>"
                       placeholder="Số định danh..."
                       maxlength="12"
                       pattern="[0-9]{12}"
                       required>
            </div>

            <div class="form-group">
                <label>Điện thoại</label>
                <input type="text" name="phone"
                       value="<?= htmlspecialchars($phone ?? '') ?>"
                       placeholder="Số điện thoại..." required>
            </div>

            <div class="form-group">
                <label>Địa chỉ</label>
                <input type="text" name="address"
                       value="<?= htmlspecialchars($address ?? '') ?>"
                       placeholder="Địa chỉ cư trú...">
            </div>

            <div class="form-footer">
                <button type="submit" class="btn-save">Lưu khách hàng</button>
                <a href="customers.php" class="btn-cancel">Hủy bỏ</a>
            </div>
        </form>
    </div>
</div>
