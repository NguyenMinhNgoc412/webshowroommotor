<?php
$conn = new mysqli("localhost", "root", "", "motorbike_showroom", 3307);
$conn->set_charset("utf8");

$keyword = $_GET['keyword'] ?? '';

$sql = "SELECT i.*, c.full_name, c.phone
FROM invoices i
JOIN customers c ON i.customer_id = c.id
WHERE i.id LIKE ?
   OR c.full_name LIKE ?
   OR DATE(i.created_at) LIKE ?
   OR i.status LIKE ?
   OR 
   CASE 
       WHEN i.status = 'pending' THEN 'chờ xử lý'
       WHEN i.status = 'paid' THEN 'đã thanh toán'
       WHEN i.status = 'cancelled' THEN 'đã hủy'
   END LIKE ?
ORDER BY i.created_at DESC
";

$stmt = $conn->prepare($sql);
$search = "%$keyword%";
$stmt->bind_param("sssss", $search, $search, $search, $search, $search);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="card">
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
