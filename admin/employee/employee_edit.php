<?php
require '../includes/auth.php';
require '../../config/database.php';

if (!isset($_GET['id'])) {
    header("Location: employees.php");
    exit;
}

$id = (int)$_GET['id'];

$result = $conn->query("SELECT * FROM employees WHERE id=$id");
if ($result->num_rows == 0) {
    header("Location: employees.php");
    exit;
}

$employee = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $position = $_POST['position'];
    $salary = $_POST['salary'];
    $hire_date = $_POST['hire_date'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("
        UPDATE employees
        SET full_name=?, email=?, phone=?, position=?, salary=?, hire_date=?, status=?
        WHERE id=?
    ");

    $stmt->bind_param(
        "ssssdsii",
        $full_name,
        $email,
        $phone,
        $position,
        $salary,
        $hire_date,
        $status,
        $id
    );

    $stmt->execute();
    header('Location: employees.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa nhân viên</title>
    <style>
        
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Arial, sans-serif;
    }
    .product-edit-wrapper {
        padding: 20px;
        background: #f8fafc;
        min-height: 100vh;
    }

    .form-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        max-width: 900px;
        margin: 0 auto;
        overflow: hidden;
    }

    .form-card-header {
        padding: 25px 30px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .form-card-header h2 {
        font-size: 1.4rem;
        color: #1e293b;
        margin: 0;
    }

    /* Phần hiển thị ảnh cũ */
    .current-image-preview {
        margin-bottom: 15px;
        padding: 10px;
        border: 1px dashed #cbd5e1;
        border-radius: 10px;
        display: inline-block;
    }

    .current-image-preview img {
        width: 120px;
        height: 90px;
        object-fit: contain;
        display: block;
        border-radius: 6px;
    }

    .current-image-preview span {
        display: block;
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 5px;
        text-align: center;
    }

    /* Bố cục Form */
    .admin-form {
        padding: 30px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 8px;
        text-transform: uppercase;
    }

    .admin-form input, 
    .admin-form select, 
    .admin-form textarea {
        width: 100%;
        padding: 12px 16px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .admin-form input:focus {
        border-color: #e63946;
        outline: none;
        box-shadow: 0 0 0 4px rgba(230, 57, 70, 0.1);
    }

    /* Nút bấm */
    .form-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 20px 30px;
        background: #fcfcfd;
        border-top: 1px solid #f1f5f9;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        text-decoration: none;
        font-size: 0.9rem;
    }

    .btn-update {
        background: #2563eb;
        color: #fff;
    }

    .btn-update:hover {
        background: #1d4ed8;
    }

    .btn-cancel {
        background: #e63946;
        color: #fff;
    }
    .btn-cancel:hover {
        background: #d62828;
    }
    </style>
</head>
<body>
    <div class="employee-edit-wrapper">
    <div class="form-card">

    <div class="form-card-header">
        <h2><i class="fa-solid fa-pen-to-square"></i> 
            Sửa thông tin: <?= htmlspecialchars($employee['full_name']) ?>
        </h2>
    </div>

    <form method="post" class="admin-form">

    <div class="form-group">
        <label>Tên nhân viên</label>
        <input type="text" name="full_name" value="<?= htmlspecialchars($employee['full_name']) ?>" required>
    </div>

    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($employee['email']) ?>" required>
    </div>

    <div class="form-group">
        <label>Số điện thoại</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($employee['phone']) ?>" 
            maxlength="10" pattern="[0-9]{10}" required 
            oninput="this.value=this.value.replace(/[^0-9]/g,'')">
    </div>

    <div class="form-group">
        <label>Chức vụ</label>
        <select name="position" required>
            <?php
            $positions = [
                "Giám đốc","Quản lý","Trưởng phòng kinh doanh","Nhân viên kinh doanh",
                "Kỹ thuật viên","Nhân viên Marketing","Nhân viên CSKH","Thu ngân","Kế toán"
            ];
            foreach ($positions as $p) {
                $selected = ($employee['position'] == $p) ? "selected" : "";
                echo "<option value='$p' $selected>$p</option>";
            }
            ?>
        </select>
    </div>

    <div class="form-group">
        <label>Lương (VNĐ)</label>
        <input type="number" name="salary" value="<?= $employee['salary'] ?>" required>
    </div>

    <div class="form-group">
        <label>Ngày vào làm</label>
        <input type="date" name="hire_date" value="<?= $employee['hire_date'] ?>" required>
    </div>

    <div class="form-group" style="max-width: 300px;">
        <label>Trạng thái</label>
        <select name="status">
            <option value="1" <?= $employee['status'] ? 'selected' : '' ?>>Đang làm</option>
            <option value="0" <?= !$employee['status'] ? 'selected' : '' ?>>Đã nghỉ</option>
        </select>
    </div>

    <div class="form-footer">
        <a href="employees.php" class="btn btn-cancel">Hủy bỏ</a>
        <button type="submit" class="btn btn-update">
            <i class="fa-solid fa-save"></i> Cập nhật
        </button>
    </div>

    </form>
    </div>
    </div>
</body>
</html>
