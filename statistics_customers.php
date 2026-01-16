<?php
require 'includes/auth.php';
require '../config/database.php';

$current_page = 'statistics_customers.php';
include 'includes/header.php';

/* ===============================
   XỬ LÝ CHỌN KIỂU THỐNG KÊ
================================ */
$type = $_GET['type'] ?? 'month';

switch ($type) {
    case 'day':
        $title = 'Thống kê khách hàng theo ngày (7 ngày gần nhất)';
        $compareText = 'so với hôm qua';
        $sql = "
            SELECT DATE(created_at) AS label, COUNT(*) AS total
            FROM customers
            WHERE created_at >= CURDATE() - INTERVAL 6 DAY
            GROUP BY DATE(created_at)
            ORDER BY label ASC
        ";
        break;

    case 'week':
        $title = 'Thống kê khách hàng theo tuần (8 tuần gần nhất)';
        $compareText = 'so với tuần qua';
        $sql = "
            SELECT CONCAT(YEAR(created_at), '-W', WEEK(created_at)) AS label,
                   COUNT(*) AS total
            FROM customers
            GROUP BY label
            ORDER BY label DESC
            LIMIT 8
        ";
        break;

    case 'year':
        $title = 'Thống kê khách hàng theo năm';
        $compareText = 'so với năm qua';
        $sql = "
            SELECT YEAR(created_at) AS label, COUNT(*) AS total
            FROM customers
            GROUP BY label
            ORDER BY label ASC
        ";
        break;

    default: // month
        $title = 'Thống kê khách hàng theo tháng';
        $compareText = 'so với tháng qua';
        $sql = "
            SELECT DATE_FORMAT(created_at, '%Y-%m') AS label, COUNT(*) AS total
            FROM customers
            GROUP BY label
            ORDER BY label ASC
        ";
}

/* ===============================
   LẤY DỮ LIỆU
================================ */
$result = $conn->query($sql);

$labels = [];
$data   = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = $row['label'];
    $data[]   = (int)$row['total'];
}

/* ===============================
   TÍNH SO SÁNH TĂNG / GIẢM
================================ */
$trend = 'up';
$percent = 0;
$tooltipText = '';

$countData = count($data);

if ($countData >= 2) {
    $current = $data[$countData - 1];
    $previous = $data[$countData - 2];

    if ($previous > 0) {
        $percent = round((($current - $previous) / $previous) * 100, 1);
    }

    if ($current < $previous) {
        $trend = 'down';
        $percent = abs($percent);
    }

    $tooltipText = "So sánh số khách hàng hiện tại $compareText";
}
?>

<link rel="stylesheet" href="../assets/css/statistics.css">

<div class="content">
    <h1 class="page-header">Thống kê khách hàng</h1>
    <p class="page-desc"><?= $title ?></p>

    <!-- FILTER -->
    <form method="get" class="stat-filter">
        <select name="type" onchange="this.form.submit()">
            <option value="day"   <?= $type=='day'?'selected':'' ?>>Theo ngày</option>
            <option value="week"  <?= $type=='week'?'selected':'' ?>>Theo tuần</option>
            <option value="month" <?= $type=='month'?'selected':'' ?>>Theo tháng</option>
            <option value="year"  <?= $type=='year'?'selected':'' ?>>Theo năm</option>
        </select>
    </form>

    <!-- SO SÁNH -->
    <?php if ($countData >= 2): ?>
        <p class="compare <?= $trend ?>" data-tooltip="<?= $tooltipText ?>">
            <?= $trend == 'up' ? '▲' : '▼' ?>
            <?= $percent ?>% <?= $compareText ?>
        </p>
    <?php endif; ?>

    <!-- BIỂU ĐỒ -->
    <div class="chart-box">
        <canvas id="customerChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('customerChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Số khách hàng',
            data: <?= json_encode($data) ?>,
            backgroundColor: '#3498db'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
                ticks: { autoSkip: true },
                grid: { display: false }
            },
            y: {
                beginAtZero: true,
                ticks: { precision: 0 }
            }
        },
        datasets: {
            bar: {
                barThickness: 22,
                maxBarThickness: 28,
                categoryPercentage: 0.6
            }
        }
    }
});
</script>
