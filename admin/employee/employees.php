<?php
include '../includes/auth.php';
include '../../config/database.php';
include '../includes/header.php';

// Lấy danh sách nhân viên
$sql = "SELECT id, code, full_name, email,dob, phone, position,hire_date, status FROM employees ORDER BY status DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lí nhân viên</title>
    <style>
:root {
            --primary: #d62839;
            --bg: #f8f9fa;
            --text: #2b2d42;
        }

        body { font-family: Arial, sans-serif; background-color: var(--bg); color: var(--text); padding: 20px; }
        .container { max-width: 1100px; margin: auto; }
        
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .btn { padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: 0.3s; display: inline-flex; align-items: center; }
        .btn-primary { background: var(--primary); color: white; }

        /* Bảng Card */
        .card { background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); overflow: hidden;margin-top:20px }
        table { width: 100%; border-collapse: collapse; }
        th { background: #fdfdfd; color: #888; font-size: 12px; text-transform: uppercase; padding: 15px 20px; border-bottom: 1px solid #eee; text-align: left; }
        td { padding: 18px 20px; border-bottom: 1px solid #f6f6f6; font-size: 14px; }

        /* Hiệu ứng Click cho dòng */
        .clickable-row { cursor: pointer; transition: background 0.2s; }
        .clickable-row:hover { background-color: #f0f4ff !important; }
        
        

/* --- BADGES (TRẠNG THÁI) --- */
.status-badge {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.status-badge::before {
    content: "";
    width: 6px;
    height: 6px;
    border-radius: 50%;
}

.badge-active {
    background-color: #ecfdf5;
    color: #065f46;
}
.badge-active::before { background-color: #10b981; }

.badge-hidden {
    background-color: #f1f5f9;
    color: #475569;
}
.badge-hidden::before { background-color: #94a3b8; }

/* --- ACTIONS --- */
.action-buttons {
    display: flex;
    gap: 12px;
}

.action-link {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.2s;
    border: 1px solid var(--border-color);
}

.edit-link { color: #2563eb; background: #eff6ff; }
.edit-link:hover { background: #2563eb; color: white; }

.delete-link { color: #dc2626; background: #fef2f2; }
.delete-link:hover { background: #dc2626; color: white; }
         
    </style>
</head>
<body>
<div class="container">
    <div class="header-flex">
        <h1>📑 Quản lý Nhân Viên</h1>
        <div class="btn-group">
            <a href="employee_add.php" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Thêm nhân viên mới
            </a>
        </div>
    </div>
    <input type="text" id="keyword" placeholder="Tìm theo mã, tên, chức vụ..."
       style="padding:10px;width:300px;border-radius:8px;border:1px solid #ddd;">
<div id="employeeTable">
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Mã NV</th>
                    <th>Họ tên</th>
                    <th>SĐT</th>
                    <th>Chức vụ</th>
                    <th>Trạng thái</th>
                    
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr class="clickable-row" data-href="employee_detail.php?id=<?= $row['id'] ?>">
                <td><?= htmlspecialchars($row['code']) ?></td>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= htmlspecialchars($row['position']) ?></td>
                <td>
                    <span class="status-badge <?= $row['status'] ? 'badge-active' : 'badge-hidden' ?>">
                        <?= $row['status'] ? 'Đang làm' : 'Đã nghỉ việc' ?>
                    </span>
                </td>
                <td>
                    <div class="action-buttons" style="justify-content: flex-end;">
                        <a href="employee_edit.php?id=<?= $row['id'] ?>" class="action-link edit-link" title="Sửa">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <a href="employee_delete.php?id=<?= $row['id'] ?>" class="action-link delete-link" title="Xóa"
                        onclick="return confirm('Xác nhận xóa nhân viên này?')">
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
</div>
<script>
document.addEventListener("click", function(e) {
    if (e.target.closest("a")) return;

    const row = e.target.closest(".clickable-row");
    if (row) {
        window.location.href = row.dataset.href;
    }
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("keyword").addEventListener("keyup", function () {
        let keyword = this.value;

        let xhr = new XMLHttpRequest();
        xhr.open("GET", "employee_search.php?keyword=" + encodeURIComponent(keyword), true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                document.getElementById("employeeTable").innerHTML = xhr.responseText;
            }
        };
        xhr.send();
    });
});
</script>



</body>
</html>