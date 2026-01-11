<?php
require '../includes/auth.php';
require '../../config/database.php';
include '../includes/header.php';

$sql = "
SELECT o.*, p.name AS product_name
FROM contact_requests o
LEFT JOIN products p ON o.product_id = p.id
WHERE o.status IN (0,1,2)
ORDER BY o.created_at DESC
";

$result = $conn->query($sql);
if (!$result) {
    die("SQL Error: " . $conn->error);
}


$orders = [
    '0' => [],
    '1' => [],
    '2' => []
];

while ($row = $result->fetch_assoc()) {
    $orders[$row['status']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quản lý liên hệ</title>
<link rel="stylesheet" href="../assets/css/contact.css">
</head>

<body>

<h2>📞 Quản lý khách hàng liên hệ</h2>

<div class="board">

<!-- CHƯA LIÊN HỆ -->
<div class="column col-new">
<h3>Chưa liên hệ</h3>
<?php foreach ($orders[0] as $o): ?>
    <div class="card">
        <b><?= htmlspecialchars($o['customer_name']) ?></b>
        <?= htmlspecialchars($o['phone']) ?><br>
         <?= htmlspecialchars($o['product_name']) ?><br>
         <?= htmlspecialchars($o['address']) ?><br>
        <?= nl2br(htmlspecialchars($o['note'])) ?><br>
        <small><?= $o['created_at'] ?></small>

        <button class="btn-next"
            onclick="updateStatus(<?= $o['id'] ?>,1)">
            Đã liên hệ
        </button>
        <button class="btn-next"
            onclick="updateStatus(<?= $o['id'] ?>,2)">
            Đã chốt đơn
        </button>
    </div>
<?php endforeach; ?>
</div>

<!-- ĐÃ LIÊN HỆ -->
<div class="column col-contacted">
<h3>Đã liên hệ</h3>
<?php foreach ($orders[1] as $o): ?>
    <div class="card">
        <b><?= htmlspecialchars($o['customer_name']) ?></b>
        <b>SĐT</b><?= htmlspecialchars($o['phone']) ?><br>
        <b>Sản phẩm</b><?= htmlspecialchars($o['product_name']) ?><br>
        <b>Địa chỉ</b><?= htmlspecialchars($o['address']) ?><br>
        <b>Ghi chú</b><?= nl2br(htmlspecialchars($o['note'])) ?><br>
        <small><?= $o['created_at'] ?></small>

        <button class="btn-next"
            onclick="updateStatus(<?= $o['id'] ?>,2)">
            Đã chốt đơn
        </button>
    </div>
<?php endforeach; ?>
</div>

<!-- ĐÃ CHỐT ĐƠN -->
<div class="column col-done">
<h3>Đã chốt đơn</h3>
<?php foreach ($orders[2] as $o): ?>
    <div class="card">
        <b><?= htmlspecialchars($o['customer_name']) ?></b>
        <b>SĐT</b><?= htmlspecialchars($o['phone']) ?><br>
        <b>Sản phẩm</b><?= htmlspecialchars($o['product_name']) ?><br>
        <b>Địa chỉ</b><?= htmlspecialchars($o['address']) ?><br>
        <b>Ghi chú</b><?= nl2br(htmlspecialchars($o['note'])) ?><br>
        <small><?= $o['created_at'] ?></small>

        <button class="btn-next btn-done"
            onclick="updateStatus(<?= $o['id'] ?>, 3)">
             Đã giao
        </button>
    </div>
<?php endforeach; ?>
</div>

</div>

<script>
function updateStatus(id, status) {
    fetch('update_contact.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: `id=${id}&status=${status}`
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            location.reload();
        } else {
            console.error(res.error);
        }
    })
    .catch(err => console.error('Fetch error', err));
}

</script>


</body>
</html>
