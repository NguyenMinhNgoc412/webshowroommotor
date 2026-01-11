<?php
require '../includes/auth.php';
require '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $code = $_POST['code'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $phone = $_POST['phone'];
    $position = $_POST['position'];
    $hire_date = $_POST['hire_date'];
    $salary = $_POST['salary'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("
        INSERT INTO employees
        (code, full_name, email, dob, phone, position, salary, hire_date, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssssssisi",
        $code,
        $full_name,
        $email,
        $dob,
        $phone,
        $position,
        $salary,
        $hire_date,
        $status
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm nhân viên</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}
.product-add-wrapper {
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
    background: #ffffff;
    padding: 25px 30px;
    border-bottom: 1px solid #f1f5f9;
}

.form-card-header h2 {
    font-size: 1.4rem;
    color: #1e293b;
    font-weight: 700;
    margin: 0;
}

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
    letter-spacing: 0.5px;
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
    background: #fff;
}

.admin-form input:focus, 
.admin-form select:focus, 
.admin-form textarea:focus {
    border-color: #e63946;
    box-shadow: 0 0 0 4px rgba(230, 57, 70, 0.1);
    outline: none;
}

.admin-form textarea {
    min-height: 100px;
    resize: vertical;
}

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
    transition: all 0.2s;
    font-size: 0.9rem;
}

.btn-save {
    background: #098864;
    color: #fff;
}

.btn-save:hover {
    background: #054130;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(230, 57, 70, 0.3);
}

.btn-return {
    background: #f1f5f9;
    color: #475569;
    text-decoration: none;
    text-align: center;
}

.btn-return:hover {
    background: #e2e8f0;
}
        
    </style>
</head>
<body>
    <div class="employee-add-wrapper">
    <div class="form-card">
        <div class="form-card-header">
            <h2>Thêm nhân viên mới</h2>
        </div>

        <form method="post" enctype="multipart/form-data" class="admin-form">
            <div class="form-group">
                <label>Mã nhân viên</label>
                <input type="text" name="code" id="code" readonly placeholder="Mã sẽ tự tạo">
            </div>
            <div class="form-group">
                <label>Tên nhân viên</label>
                <input type="text" name="full_name" placeholder="Nhập tên nhân viên..." required>
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="Nhập email..." required>  
            </div>
            <div class="form-group">
                <label>Ngày sinh</label>
                <input type="date" name="dob" required>
            </div>
            <div class="form-group">
                <label>Số điện thoại</label>
                <input type="text" name="phone" placeholder="Nhập số điện thoại..." maxlength="10" pattern="[0-9]{10}" required oninput="this.value=this.value.replace(/[^0-9]/g,'')" >
            </div>
            <div class="form-group">
                <label>Chức vụ</label>
                <select name="position" id="position" required onchange="generateCode()">
                    <option value="">-- Chọn chức vụ --</option>
                    <option value="Giám đốc">Giám đốc</option>
                    <option value="Quản lý">Quản lý</option>
                    <option value="Trưởng phòng kinh doanh">Trưởng phòng kinh doanh</option>
                    <option value="Nhân viên kinh doanh">Nhân viên kinh doanh</option>
                    <option value="Kỹ thuật viên">Kỹ thuật viên</option>
                    <option value="Nhân viên Marketing">Nhân viên Marketing</option>
                    <option value="Nhân viên CSKH">Nhân viên CSKH</option>
                    <option value="Thu ngân">Thu ngân</option>
                    <option value="Kế toán">Kế toán</option>
                    <option value="Bảo vệ">Bảo vệ</option>


                </select>
            </div>
            <div class="form-group">
                <label>Mức lương</label>
                <input type="number" name="salary" placeholder="Nhập mức lương..." required>
            </div>
            <div class="form-group">
                <label>Ngày vào làm</label>
                <input type="date" name="hire_date" required>
            </div>

            <div class="form-group" style="max-width: 300px;">
                <label>Trạng thái</label>
                <select name="status">
                    <option value="1">Đang làm</option>
                    <option value="0">Đã nghỉ việc</option>
                </select>
            </div>

            <div class="form-footer">
                <a href="employees.php" class="btn btn-return">
                    <i class="fa-solid fa-xmark"></i> Hủy & Quay lại
                </a>
                <button type="submit" class="btn btn-save">
                    <i class="fa-solid fa-check"></i> Lưu dữ liệu
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function generateCode() {
    let position = document.getElementById("position").value;
    if(position === ""){
        document.getElementById("code").value = "";
        return;
    }

    // Lấy chữ cái đầu của mỗi từ
    let words = position.split(" ");
    let short = "";

    words.forEach(w => {
        if(w.length > 0){
            short += w[0].toUpperCase();
        }
    });

    // Random 6 số
    let random = Math.floor(100000 + Math.random() * 900000);

    document.getElementById("code").value = short + random;
}
</script>

    
</body>
</html>