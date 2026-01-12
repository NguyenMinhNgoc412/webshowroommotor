<?php
require_once '../includes/auth.php';
require_once '../../config/database.php';
require_once '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>

<link rel="stylesheet" href="../assets/css/admin.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
.dashboard-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:20px;
    margin-bottom:30px
}
.card{
    background:#fff;
    padding:20px;
    border-radius:12px;
    box-shadow:0 4px 12px rgba(0,0,0,.08)
}
.card h4{color:#64748b;font-size:14px}
.card h2{margin-top:8px;color:#0f172a}
.charts{
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:20px
}
.progress{
    height:8px;
    background:#e5e7eb;
    border-radius:4px;
    overflow:hidden
}
.progress-bar{
    height:100%;
    background:#22c55e
}
</style>
</head>

<body class="dashboard-body">
<div class="admin-wrapper">
<main class="main-content">

<h2>📊 Tổng quan hệ thống</h2>

<!-- ===== KPI ===== -->
<div class="dashboard-grid" id="kpi-area"></div>

<!-- ===== BIỂU ĐỒ ===== -->
<div class="charts">
    <div class="card">
        <h4>Doanh thu 7 ngày gần nhất</h4>
        <canvas id="revenueChart"></canvas>
    </div>

    <div class="card">
        <h4>Sản phẩm bán chạy</h4>
        <canvas id="topProductChart"></canvas>
    </div>
</div>

</main>
</div>

<script>
let revenueChart, productChart;

function loadDashboard(){
    fetch('dashboard_data.php')
    .then(res=>res.json())
    .then(data=>{
        renderKPI(data);
        renderRevenueChart(data);
        renderProductChart(data);
    });
}

function renderKPI(d){
    document.getElementById('kpi-area').innerHTML = `
        <div class="card">
            <h4>Doanh thu</h4>
            <h2>${d.revenue.toLocaleString()} đ</h2>
        </div>
        <div class="card">
            <h4>Lợi nhuận</h4>
            <h2 style="color:#22c55e">${d.profit.toLocaleString()} đ</h2>
        </div>
        <div class="card">
            <h4>Giá trị đơn TB</h4>
            <h2>${d.avg_order.toLocaleString()} đ</h2>
        </div>
        <div class="card">
            <h4>Bán chạy nhất</h4>
            <h2>${d.top_product}</h2>
            <div class="progress">
                <div class="progress-bar" style="width:${d.top_percent}%"></div>
            </div>
        </div>
    `;
}

function renderRevenueChart(d){
    if(revenueChart) revenueChart.destroy();
    revenueChart = new Chart(document.getElementById('revenueChart'),{
        type:'line',
        data:{
            labels:d.revenue_days,
            datasets:[{
                label:'Doanh thu',
                data:d.revenue_values,
                fill:true,
                borderWidth:2
            }]
        }
    });
}

function renderProductChart(d){
    if(productChart) productChart.destroy();
    productChart = new Chart(document.getElementById('topProductChart'),{
        type:'pie',
        data:{
            labels:d.product_labels,
            datasets:[{
                data:d.product_values
            }]
        }
    });
}

loadDashboard();
setInterval(loadDashboard,5000); 
</script>

</body>
</html>
