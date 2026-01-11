<?php
include '../../config/database.php';

/* ================= KIỂM TRA ID ================= */
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Không tìm thấy mã hoá đơn.");
}
$id = (int)$_GET['id'];

/* ================= LẤY HOÁ ĐƠN ================= */   
$sql_inv = "
    SELECT i.*, 
           c.full_name AS customer_name, 
           c.phone, 
           c.address
    FROM invoices i
    LEFT JOIN customers c ON i.customer_id = c.id
    WHERE i.id = $id
";
$res_inv = $conn->query($sql_inv);
if (!$res_inv || $res_inv->num_rows == 0) {
    die("Hoá đơn không tồn tại.");
}
$inv = $res_inv->fetch_assoc();

/* ================= LẤY CHI TIẾT ================= */
$items = $conn->query("
    SELECT ii.*, p.name 
    FROM invoice_items ii
    JOIN products p ON ii.product_id = p.id
    WHERE ii.invoice_id = $id
");

/* ================= XỬ LÝ POST ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    /* ===== THANH TOÁN ===== */
    if ($action === 'pay' && $inv['status'] === 'pending') {
        $conn->begin_transaction();
        try {
            $items_process = $conn->query("
                SELECT product_id, quantity 
                FROM invoice_items 
                WHERE invoice_id = $id
            ");

            while ($row = $items_process->fetch_assoc()) {
                $pid = (int)$row['product_id'];
                $qty = (int)$row['quantity'];

                $stock = $conn->query("
                    SELECT quantity 
                    FROM inventory 
                    WHERE product_id = $pid 
                    FOR UPDATE
                ")->fetch_assoc();

                if (!$stock || $stock['quantity'] < $qty) {
                    throw new Exception("Sản phẩm ID $pid không đủ tồn kho.");
                }

                $conn->query("
                    UPDATE inventory 
                    SET quantity = quantity - $qty 
                    WHERE product_id = $pid
                ");

                $stmt = $conn->prepare("
                    INSERT INTO stock_logs (product_id, type, quantity, note)
                    VALUES (?, 'export', ?, ?)
                ");
                $note = "Xuất kho theo hoá đơn #$id";
                $stmt->bind_param("iis", $pid, $qty, $note);
                $stmt->execute();
            }

            $conn->query("UPDATE invoices SET status='paid' WHERE id=$id");
            $conn->commit();

            header("Location: invoices.php?msg=paid");
            exit;

        } catch (Exception $e) {
            $conn->rollback();
            echo "<script>alert('Lỗi: {$e->getMessage()}');</script>";
        }
    }

    /* ===== HUỶ ===== */
    if ($action === 'cancel' && $inv['status'] === 'pending') {
        $conn->query("UPDATE invoices SET status='cancelled' WHERE id=$id");
        header("Location: invoices.php?msg=cancelled");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Hoá đơn #<?= $id ?></title>

<style>
body{
    font-family:"Times New Roman";
    background:#f4f4f4;
    padding:20px
}
.invoice-page{
    background:#fff;
    width:210mm;
    height:297mm;
    margin:auto;
    padding:20px 40px;
    box-sizing:border-box;
}
.header{
    display:flex;
    justify-content:space-between;
    margin-bottom:15px
}
.company{
    width:45%;
    font-size:14px
}
.nation{
    width:45%;
    text-align:center;
    font-size:14px
}
.nation b{
    display:block;
    text-transform:uppercase
}
.title{
    text-align:center;
    margin:20px 0
}
table{
    width:100%;
    border-collapse:collapse;
    margin-top:15px
}
th,td{
    border:1px solid #000;
    padding:8px
}
th{
    background:#f2f2f2;
    text-align:center
}
.sign{
    display:flex;
    justify-content:space-between;
    margin-top:40px;
    text-align:center
}
.sign div{
    width:40%
}
.btn{
    padding:12px 20px;
    border:none;
    color:#fff;
    font-weight:bold;
    border-radius:4px;
    cursor:pointer
}
.btn-pay{background:#28a745}
.btn-cancel{background:#dc3545}
.btn-print{background:#555}
.no-print{text-align:center;margin-bottom:10px}

/* ===== FIX IN 1 TRANG ===== */
@media print {
    body {
        background: white;
        padding: 0;
        margin: 0;
    }

    .no-print {
        display: none;
    }

    .invoice-page {
        page-break-inside: avoid;
        break-inside: avoid;
    }

    table, tr, td, th, .sign {
        page-break-inside: avoid;
        break-inside: avoid;
    }
}
</style>

</head>

<body>

<div class="no-print">
    <a href="invoices.php"><b>← Quay lại danh sách</b></a>
</div>

<div class="invoice-page">

    <!-- HEADER -->
    <div class="header">
        <div class="company">
            <b>CÔNG TY TNHH THƯƠNG MẠI DỊCH VỤ XE MÁY 99</b><br>
            Địa chỉ: 54 Triều Khúc, Hà Nội<br>
            Điện thoại: 0909 123 456
        </div>

        <div class="nation">
            <b>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</b>
            <b>Độc lập - Tự do - Hạnh phúc</b>
            <i>Ngày <?= date('d') ?>, Tháng <?= date('m') ?>, Năm <?= date('Y') ?></i>
        </div>
    </div>

    <!-- TITLE -->
    <div class="title">
        <h2>HOÁ ĐƠN BÁN HÀNG</h2>
        <p>Số: <?= $id ?></p>
    </div>

    <!-- INFO -->
    <p><b>Khách hàng:</b> <?= htmlspecialchars($inv['customer_name']) ?></p>
    <p><b>Điện thoại:</b> <?= htmlspecialchars($inv['phone']) ?></p>
    <p><b>Địa chỉ:</b> <?= htmlspecialchars($inv['address']) ?></p>

    <!-- TABLE -->
    <table>
        <tr>
            <th>STT</th>
            <th>Sản phẩm</th>
            <th>SL</th>
            <th>Đơn giá</th>
            <th>Thành tiền</th>
        </tr>

        <?php $i=1; while($it=$items->fetch_assoc()): ?>
        <tr>
            <td align="center"><?= $i++ ?></td>
            <td><?= htmlspecialchars($it['name']) ?></td>
            <td align="center"><?= $it['quantity'] ?></td>
            <td align="right"><?= number_format($it['price']) ?> đ</td>
            <td align="right"><?= number_format($it['total']) ?> đ</td>
        </tr>
        <?php endwhile; ?>

        <tr>
            <td colspan="4" align="right"><b>TỔNG TIỀN</b></td>
            <td align="right" style="color:red;font-weight:bold">
                <?= number_format($inv['total_amount']) ?> đ
            </td>
        </tr>
    </table>

    <!-- SIGNATURE -->
    <div class="sign">
        <div>
            <b>NGƯỜI MUA HÀNG</b><br>
            <i>(Ký, ghi rõ họ tên)</i><br><br><br>
        </div>
        <div>
            <b>NGƯỜI BÁN HÀNG</b><br>
            <i>(Ký, ghi rõ họ tên)</i><br><br><br>
        </div>
    </div>

</div>

<div class="no-print" style="text-align:center;margin-top:20px">
    <button class="btn btn-print" onclick="window.print()">In hoá đơn</button>

    <?php if ($inv['status'] === 'pending'): ?>
        <form method="post" style="display:inline">
            <input type="hidden" name="action" value="pay">
            <button class="btn btn-pay"
                onclick="return confirm('Xác nhận thanh toán ?')">
                Xác nhận thanh toán
            </button>
        </form>

        <form method="post" style="display:inline">
            <input type="hidden" name="action" value="cancel">
            <button class="btn btn-cancel"
                onclick="return confirm('Huỷ hoá đơn này?')">
                Huỷ hoá đơn
            </button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
