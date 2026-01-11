<?php
include '../../config/database.php';
include '../includes/header.php';
include '../includes/auth.php';


// SQL lấy thông tin từ bảng invoices kết hợp bảng customers
$sql = "SELECT i.*, c.full_name, c.phone 
        FROM invoices i 
        JOIN customers c ON i.customer_id = c.id 
        ORDER BY i.id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh Sách Hoá Đơn | Hệ Thống Kho</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600&display=swap" rel="stylesheet">
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
        .card { background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #fdfdfd; color: #888; font-size: 12px; text-transform: uppercase; padding: 15px 20px; border-bottom: 1px solid #eee; text-align: left; }
        td { padding: 18px 20px; border-bottom: 1px solid #f6f6f6; font-size: 14px; }

        /* Hiệu ứng Click cho dòng */
        .clickable-row { cursor: pointer; transition: background 0.2s; }
        .clickable-row:hover { background-color: #f0f4ff !important; }
        
        /* Badges */
        .badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .pending { background: #fff3e0; color: #ef6c00; }
        .paid { background: #e8f5e9; color: #2e7d32; }
        .cancelled { background: #ffebee; color: #c62828; }
        
        .id-label { color: var(--primary); font-weight: 600; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-flex">
        <h1>📑 Quản lý Hoá Đơn</h1>
        <div class="btn-group">
            <a href="invoice_create.php" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Tạo hoá đơn mới
            </a>
        </div>
    </div>
    
        
        <table>
            <thead>
                <tr>
                    <th>Mã HĐ</th>
                    <th>Khách hàng</th>
                    <th>Tổng tiền</th>
                    <th>Ngày tạo</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php if($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr class="clickable-row" data-href="invoice_detail.php?id=<?= $row['id'] ?>">
                        <td><span class="id-label">#<?= $row['id'] ?></span></td>
                        <td>
                            <strong><?= htmlspecialchars($row['full_name']) ?></strong><br>
                            <small style="color:#999"><?= htmlspecialchars($row['phone']) ?></small>
                        </td>
                        <td><strong><?= number_format($row['total_amount']) ?> vnđ</strong></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                        <td>
                            <span class="badge <?= $row['status'] ?>">
                                <?= $row['status'] == 'pending' ? '● Chờ xử lý' : ($row['status']=='paid'?'● Đã thanh toán':'● Đã huỷ') ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const rows = document.querySelectorAll(".clickable-row");
    rows.forEach(row => {
        row.addEventListener("click", () => {
            // Chuyển hướng đến link được lưu trong data-href
            window.location.href = row.dataset.href;
        });
    });
});
</script>

</body>
</html>