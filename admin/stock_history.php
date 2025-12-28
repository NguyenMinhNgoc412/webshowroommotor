<?php
include '../config/database.php';

// 1. Lấy tham số lọc ngày (Mặc định từ đầu tháng đến hiện tại)
$from_date = isset($_GET['from']) ? $_GET['from'] : date('Y-m-01');
$to_date = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d');

// 2. Query dữ liệu (Khớp với cột 'quantity' trong DB của bạn)
$sql = "SELECT l.*, p.name as product_name 
        FROM stock_logs l 
        JOIN products p ON l.product_id = p.id 
        WHERE DATE(l.created_at) BETWEEN ? AND ? 
        ORDER BY l.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $from_date, $to_date);
$stmt->execute();
$result = $stmt->get_result();

// Xử lý xuất Excel dạng bảng có định dạng
if (isset($_GET['export'])) {
    $filename = "Lich_su_kho_" . date('d-m-Y') . ".xls";
    
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=$filename");
    header("Pragma: no-cache");
    header("Expires: 0");

    // Xuất BOM để hiển thị tiếng Việt chuẩn UTF-8
    echo "\xEF\xBB\xBF"; 

    echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>';
    echo '<body>';
    echo '<h3>BÁO CÁO LỊCH SỬ NHẬP XUẤT KHO</h3>';
    echo 'Từ ngày: ' . $_GET['from'] . ' - Đến ngày: ' . $_GET['to'] . '<br><br>';
    
    echo '<table border="1" style="border-collapse:collapse; width:100%;">';
    echo '<thead>
            <tr style="background-color: #4CAF50; color: white; font-weight: bold;">
                <th style="width: 50px;">ID</th>
                <th style="width: 150px;">Thời gian</th>
                <th style="width: 200px;">Sản phẩm</th>
                <th style="width: 100px;">Loại</th>
                <th style="width: 80px;">Số lượng</th>
                <th style="width: 250px;">Ghi chú</th>
            </tr>
          </thead>';
    echo '<tbody>';
    
    // Reset lại kết quả query để chạy từ đầu
    $result->data_seek(0); 
    
    while ($row = $result->fetch_assoc()) {
        $type_text = ($row['type'] == 'import') ? 'Nhập kho' : 'Xuất kho';
        $type_style = ($row['type'] == 'import') ? 'color: green;' : 'color: red;';
        
        echo '<tr>';
        echo '<td style="text-align: center;">' . $row['id'] . '</td>';
        echo '<td style="text-align: center;">' . $row['created_at'] . '</td>';
        echo '<td>' . htmlspecialchars($row['product_name']) . '</td>';
        echo '<td style="text-align: center; ' . $type_style . '">' . $type_text . '</td>';
        echo '<td style="text-align: center;">' . number_format($row['quantity']) . '</td>';
        echo '<td>' . htmlspecialchars($row['note']) . '</td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    echo '</body></html>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch Sử Kho</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; padding: 20px; background-color: #f8f9fa; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .filter-box { margin: 20px 0; padding: 15px; background: #e9ecef; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #dee2e6; padding: 12px; text-align: center; }
        th { background-color: #343a40; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .import { color: #28a745; font-weight: bold; }
        .export { color: #dc3545; font-weight: bold; }
        .btn { padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 14px; }
        .btn-filter { background: #007bff; color: white; }
        .btn-excel { background: #198754; color: white; }
        .btn-back { background: #6c757d; color: white; margin-bottom: 15px; display: inline-block; }
    </style>
</head>
<body>

<div class="container">
    <a href="inventory.php" class="btn btn-back">← Quay lại Kho</a>
    <h2>📜 Lịch sử Nhập / Xuất Kho</h2>
    
    <div class="filter-box">
        <form method="GET">
            Từ ngày: <input type="date" name="from" value="<?= $from_date ?>">
            Đến ngày: <input type="date" name="to" value="<?= $to_date ?>">
            <button type="submit" class="btn btn-filter">Lọc dữ liệu</button>
            <button type="submit" name="export" value="1" class="btn btn-excel">Xuất file Excel</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Thời gian</th>
                <th>Sản phẩm</th>
                <th>Loại</th>
                <th>Số lượng</th>
                <th>Ghi chú</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                    <td style="text-align: left;"><?= htmlspecialchars($row['product_name']) ?></td>
                    <td class="<?= $row['type'] ?>">
                        <?= ($row['type'] == 'import') ? '⬇ Nhập kho' : '⬆ Xuất kho' ?>
                    </td>
                    <td><?= number_format($row['quantity']) ?></td>
                    <td style="text-align: left; font-style: italic; color: #666;">
                        <?= htmlspecialchars($row['note']) ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">Không có dữ liệu trong khoảng thời gian này.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>