<?php
include '../config/database.php'; 
include 'includes/header.php'; 
include 'includes/auth.php';   

/** * 1. XỬ LÝ NHẬP KHO 
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['import'])) {
    $p_id = intval($_POST['product_id']);
    $qty = intval($_POST['quantity']);
    $note = $conn->real_escape_string($_POST['note']);

    // Sử dụng Transaction để đảm bảo an toàn dữ liệu
    $conn->begin_transaction();
    try {
        // Cập nhật bảng inventory: Nếu chưa có sản phẩm trong kho thì INSERT, có rồi thì UPDATE
        $sql_inventory = "INSERT INTO inventory (product_id, quantity) 
                          VALUES (?, ?) 
                          ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)";
        $stmt_inv = $conn->prepare($sql_inventory);
        $stmt_inv->bind_param("ii", $p_id, $qty);
        $stmt_inv->execute();

        // Ghi log vào bảng stock_logs (quantity ở đây lưu số dương cho nhập)
        $sql_log = "INSERT INTO stock_logs (product_id, type, quantity, note) VALUES (?, 'import', ?, ?)";
        $stmt_log = $conn->prepare($sql_log);
        $stmt_log->bind_param("iis", $p_id, $qty, $note);
        $stmt_log->execute();
        
        $conn->commit();
        echo "<script>alert('Nhập kho thành công!'); window.location.href='inventory.php';</script>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<div style='color:red;'>Lỗi hệ thống: " . $e->getMessage() . "</div>";
    }
}

/** * 2. LẤY DANH SÁCH SẢN PHẨM VÀ TỒN KHO 
 */
// LEFT JOIN để lấy được cả những sản phẩm chưa từng được nhập kho (tồn = 0)
$sql_select = "SELECT p.id, p.name, p.price, COALESCE(i.quantity, 0) AS stock_quantity 
               FROM products p 
               LEFT JOIN inventory i ON p.id = i.product_id";
$result = $conn->query($sql_select);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Kho</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 20px; background-color: #f4f7f6; }
        h1 { color: #333; }
        .nav-links { margin-bottom: 20px; }
        .nav-links a { text-decoration: none; color: #007bff; font-weight: bold; margin-right: 15px; }
        table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: center; }
        th { background-color: #f8f9fa; color: #333; }
        tr:hover { background-color: #f1f1f1; }
        .btn-import { background: #28a745; color: white; border: none; padding: 7px 15px; cursor: pointer; border-radius: 4px; }
        .btn-import:hover { background: #218838; }
        .stock-badge { padding: 4px 8px; border-radius: 12px; font-weight: bold; }
        .out-of-stock { background: #f8d7da; color: #721c24; }
        .in-stock { background: #d4edda; color: #155724; }
        input { padding: 6px; border: 1px solid #ccc; border-radius: 4px; }
    </style>
</head>
<body>

    <h1>📦 Quản Lý Tồn Kho</h1>
    
    <div class="nav-links">
        <a href="stock_history.php">📜 Xem Lịch Sử Nhập/Xuất</a>
        <a href="invoices.php">📄 Quản Lý Hoá Đơn</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Sản phẩm</th>
                <th>Giá bán</th>
                <th>Tồn kho</th>
                <th>Hành động Nhập kho</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td style="text-align: left; font-weight: 500;"><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= number_format($row['price']) ?> đ</td>
                    <td>
                        <span class="stock-badge <?= $row['stock_quantity'] > 0 ? 'in-stock' : 'out-of-stock' ?>">
                            <?= $row['stock_quantity'] ?>
                        </span>
                    </td>
                    <td>
                        <form method="POST" style="display:flex; gap:8px; justify-content:center;">
                            <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                            <input type="number" name="quantity" placeholder="SL" required min="1" style="width:70px;">
                            <input type="text" name="note" placeholder="Ghi chú (NCC, lô hàng...)" style="width:180px;">
                            <button type="submit" name="import" class="btn-import">Nhập kho</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">Chưa có sản phẩm nào trong hệ thống.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>