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
        $sql = "
            SELECT CONCAT(YEAR(created_at), '-W', LPAD(WEEK(created_at),2,'0')) AS label,
                   COUNT(*) AS total
            FROM customers
            GROUP BY label
            ORDER BY label DESC
            LIMIT 8
        ";
        break;

    case 'year':
        $title = 'Thống kê khách hàng theo năm';
        $sql = "
            SELECT YEAR(created_at) AS label, COUNT(*) AS total
            FROM customers
            GROUP BY label
            ORDER BY label ASC
        ";
        break;

    default: // month
        $title = 'Thống kê khách hàng theo tháng';
        $sql = "
            SELECT DATE_FORMAT(created_at, '%Y-%m') AS label, COUNT(*) AS total
            FROM customers
            GROUP BY label
            ORDER BY label ASC
        ";
}

$result = $conn->query($sql);

$labels = [];
$data   = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = $row['label'];
    $data[]   = (int)$row['total'];
}
?>

<link rel="stylesheet" href="../assets/css/statistics.css">

<div class="content">
    <h1 class="page-header">Thống kê khách hàng theo thời gian</h1>
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

    <!-- CHART -->
    <div class="chart-box">
        <canvas id="customerChart"></canvas>
    </div>
</div>

<!-- LOAD CHART JS (1 LẦN DUY NHẤT) -->
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
                grid: { display: false },
                ticks: {
                    autoSkip: true,
                    maxRotation: 0
                }
            },
            y: {
                beginAtZero: true,
                ticks: { precision: 0 }
            }
        }
    }
});
</script>
