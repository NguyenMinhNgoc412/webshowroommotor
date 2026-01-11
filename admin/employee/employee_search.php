<?php
$conn = new mysqli("localhost", "root", "", "motorbike_showroom", 3307);
$conn->set_charset("utf8");

$keyword = $_GET['keyword'] ?? '';

$sql = "SELECT * FROM employees 
        WHERE code LIKE ? 
        OR full_name LIKE ? 
        OR position LIKE ?";

$stmt = $conn->prepare($sql);
$search = "%$keyword%";
$stmt->bind_param("sss", $search, $search, $search);
$stmt->execute();
$result = $stmt->get_result();
?>

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
        <tr class="clickable-row" data-href="employee_detail.php?id=<?= $row['code'] ?>">
            <td><?= htmlspecialchars($row['code']) ?></td>
            <td><?= htmlspecialchars($row['full_name']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['position']) ?></td>
            <td>
                <span class="status-badge <?= $row['status'] ? 'badge-active' : 'badge-hidden' ?>">
                    <?= $row['status'] ? 'Đang làm' : 'Đã nghỉ việc' ?>
                </span>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
</div>
