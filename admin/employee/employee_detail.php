<?php
require '../includes/auth.php';
require '../../config/database.php';

if (!isset($_GET['id'])) {
    header("Location: employees.php");
    exit;
}

$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Nhân viên không tồn tại.");
}

$emp = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chi tiết nhân viên</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
.employee-detail-wrapper {
    max-width: 1000px;
    margin: 40px auto;
}

.detail-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0,0,0,.08);
    padding: 35px;
}

/* HEADER */
.detail-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #eee;
    padding-bottom: 18px;
    margin-bottom: 30px;
}

.detail-header h2 {
    margin: 0;
    font-size: 1.6rem;
}

/* GRID HIỂN THỊ NGANG */
.detail-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr); /* 2 cột ngang */
    gap: 22px;
}

/* MỖI Ô THÔNG TIN */
.detail-item {
    background: #f8fafc;
    border-radius: 12px;
    padding: 16px 18px;
    display: flex;
    flex-direction: column;   /* xếp DỌC */
    transition: all 0.25s ease;
    border: 1px solid #eef2f7;
}

/* HOVER */
.detail-item:hover {
    background: #ffffff;
    box-shadow: 0 8px 18px rgba(0,0,0,.08);
    border-color: #e5e7eb;
}

/* LABEL */
.detail-item span {
    color: #64748b;
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .5px;
}

/* VALUE */
.detail-item strong {
    display: block;
    font-size: 17px;
    margin-top: 6px;
    color: #0f172a;
}

/* BADGE */
.badge-active {
    color:#065f46;
    background:#ecfdf5;
    padding:7px 14px;
    border-radius:999px;
    font-weight:600;
}

.badge-hidden {
    color:#475569;
    background:#f1f5f9;
    padding:7px 14px;
    border-radius:999px;
    font-weight:600;
}

/* FOOTER */
.detail-actions {
    margin-top: 35px;
    display: flex;
    justify-content: flex-end;
    gap: 14px;
}

.detail-actions a {
    padding: 11px 20px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: all .2s ease;
}


.btn-back {
    background:#f1f5f9;
    color:#0f172a;
}
.btn-back:hover {
    background:#e2e8f0;
}

.btn-edit {
    background:#2563eb;
    color:#fff;
}
.btn-edit:hover {
    background:#1e40af;
}
</style>
</head>
<body>

<div class="employee-detail-wrapper">
<div class="detail-card">

<div class="detail-header">
<h2><i class="fa-solid fa-id-card"></i> Chi tiết nhân viên</h2>
<span class="<?= $emp['status'] ? 'badge-active' : 'badge-hidden' ?>">
    <?= $emp['status'] ? 'Đang làm việc' : 'Đã nghỉ việc' ?>
</span>
</div>

<div class="detail-grid">
<div class="detail-item"><span>Mã nhân viên</span><strong><?= htmlspecialchars($emp['code']) ?></strong></div>
<div class="detail-item"><span>Họ tên</span><strong><?= htmlspecialchars($emp['full_name']) ?></strong></div>
<div class="detail-item"><span>Email</span><strong><?= htmlspecialchars($emp['email']) ?></strong></div>
<div class="detail-item"><span>Số điện thoại</span><strong><?= htmlspecialchars($emp['phone']) ?></strong></div>
<div class="detail-item"><span>Ngày sinh</span><strong><?= htmlspecialchars($emp['dob']) ?></strong></div>
<div class="detail-item"><span>Chức vụ</span><strong><?= htmlspecialchars($emp['position']) ?></strong></div>
<div class="detail-item"><span>Lương</span><strong><?= number_format($emp['salary']) ?> VNĐ</strong></div>
<div class="detail-item"><span>Ngày vào làm</span><strong><?= htmlspecialchars($emp['hire_date']) ?></strong></div>
</div>

<div class="detail-actions">
<a href="employees.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Quay lại</a>
<a href="employee_edit.php?id=<?= $emp['id'] ?>" class="btn-edit"><i class="fa-solid fa-pen"></i> Sửa</a>
</div>

</div>
</div>

</body>
</html>
