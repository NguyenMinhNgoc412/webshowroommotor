<?php
require_once '../config/database.php';

/* DOANH THU */
$revenue = mysqli_fetch_row(mysqli_query($conn,"
    SELECT IFNULL(SUM(total_amount),0)
    FROM invoices WHERE status='paid'
"))[0];

/* LỢI NHUẬN */
$profit = mysqli_fetch_row(mysqli_query($conn,"
    SELECT IFNULL(SUM((ii.price - p.cost_price) * ii.quantity),0)
    FROM invoice_items ii
    JOIN invoices i ON ii.invoice_id=i.id
    JOIN products p ON ii.product_id=p.id
    WHERE i.status='paid'
"))[0];

/* ĐƠN TB */
$avg = mysqli_fetch_row(mysqli_query($conn,"
    SELECT IFNULL(AVG(total_amount),0)
    FROM invoices WHERE status='paid'
"))[0];

/* TOP PRODUCT */
$top = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT p.name, SUM(ii.quantity) qty
    FROM invoice_items ii
    JOIN invoices i ON ii.invoice_id=i.id
    JOIN products p ON ii.product_id=p.id
    WHERE i.status='paid'
    GROUP BY ii.product_id
    ORDER BY qty DESC
    LIMIT 1
"));

/* BIỂU ĐỒ 7 NGÀY */
$days=[];$values=[];
$q = mysqli_query($conn,"
    SELECT DATE(created_at) d, SUM(total_amount) t
    FROM invoices
    WHERE status='paid'
    GROUP BY d
    ORDER BY d DESC
    LIMIT 7
");
while($r=mysqli_fetch_assoc($q)){
    $days[]=$r['d'];
    $values[]=$r['t'];
}

/* PIE */
$pl=[];$pv=[];
$q2=mysqli_query($conn,"
    SELECT p.name,SUM(ii.quantity) q
    FROM invoice_items ii
    JOIN invoices i ON ii.invoice_id=i.id
    JOIN products p ON ii.product_id=p.id
    WHERE i.status='paid'
    GROUP BY ii.product_id
    LIMIT 5
");
while($r=mysqli_fetch_assoc($q2)){
    $pl[]=$r['name'];
    $pv[]=$r['q'];
}

echo json_encode([
    'revenue'=>$revenue,
    'profit'=>$profit,
    'avg_order'=>$avg,
    'top_product'=>$top['name'] ?? 'N/A',
    'top_percent'=>100,
    'revenue_days'=>array_reverse($days),
    'revenue_values'=>array_reverse($values),
    'product_labels'=>$pl,
    'product_values'=>$pv
]);
