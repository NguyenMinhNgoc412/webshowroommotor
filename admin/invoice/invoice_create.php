<?php
include '../../config/database.php';

// 1. Lấy danh sách sản phẩm 
$sql_products = "SELECT p.*, COALESCE(i.quantity, 0) as stock 
                 FROM products p 
                 LEFT JOIN inventory i ON p.id = i.product_id";
$res_prods = $conn->query($sql_products);
$p_list = [];
while($row = $res_prods->fetch_assoc()) { $p_list[] = $row; }

// 2. Xử lý khi nhấn nút LƯU
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $cccd      = $_POST['cccd'];
    $phone     = $_POST['phone'];
    $address   = $_POST['address'];
    $p_ids     = $_POST['product_id']; 
    $qtys      = $_POST['quantity'];   

    $conn->begin_transaction();
    try {
        // Tạo khách hàng mới (ID sẽ tự sinh)
        $stmt_c = $conn->prepare("INSERT INTO customers (full_name, cccd, phone, address) VALUES (?, ?, ?, ?)");
        $stmt_c->bind_param("ssss", $full_name, $cccd, $phone, $address);
        $stmt_c->execute();
        $customer_id = $conn->insert_id; 

        // Tạo hoá đơn mới liên kết với khách hàng (ID hoá đơn tự sinh)
        $stmt_i = $conn->prepare("INSERT INTO invoices (customer_id, total_amount, status) VALUES (?, 0, 'pending')");
        $stmt_i->bind_param("i", $customer_id);
        $stmt_i->execute();
        $invoice_id = $conn->insert_id; 

        $total_bill = 0;
        // Lưu chi tiết các sản phẩm vào invoice_items
        for ($i = 0; $i < count($p_ids); $i++) {
            $pid = intval($p_ids[$i]);
            $qty = intval($qtys[$i]);
            
            // Lấy giá sản phẩm hiện tại
            $res_p = $conn->query("SELECT price FROM products WHERE id = $pid");
            $p_data = $res_p->fetch_assoc();
            $price = $p_data['price'];
            $line_total = $price * $qty;
            $total_bill += $line_total;

            $stmt_it = $conn->prepare("INSERT INTO invoice_items (invoice_id, product_id, quantity, price, total) VALUES (?, ?, ?, ?, ?)");
            $stmt_it->bind_param("iiidd", $invoice_id, $pid, $qty, $price, $line_total);
            $stmt_it->execute();
        }

        // Cập nhật lại tổng tiền cuối cùng vào hoá đơn
        $conn->query("UPDATE invoices SET total_amount = $total_bill WHERE id = $invoice_id");
        
        $conn->commit();

        // CHUYỂN HƯỚNG SANG TRANG CHI TIẾT VỚI ID VỪA SINH
        header("Location: invoice_detail.php?id=" . $invoice_id);
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Lỗi khi tạo hoá đơn: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tạo Hoá Đơn Mới</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body { font-family: Arial, sans-serif; background: #f0f2f5; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .row-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; background: #f8f9fa; padding: 10px; border-bottom: 2px solid #eee; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
        .btn-add { background: #4361ee; color: white; margin-bottom: 10px; }
        .btn-save { background: #0a7e5d; color: white; width: 40%; font-size: 1.1em; margin-top: 20px;margin-left: auto; display: block; }
        #grand-total { font-size: 1.5em; font-weight: bold; color: #e74c3c; text-align: right; margin-top: 15px; }
    </style>
</head>
<body>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Tạo Hoá Đơn Bán Hàng</h2>
        <a href="invoices.php" style="color: #666; text-decoration: none;">← Quay lại</a>
    </div>

    <form method="POST">
        <div class="row-grid">
            <input type="text" name="full_name" placeholder="Tên khách hàng" required>
            <input type="text" name="cccd" placeholder="Số CCCD" required minlength="12" maxlength="12">
            <input type="text" name="phone" placeholder="Số điện thoại" required minlength="10" maxlength="10">
            <input type="text" name="address" placeholder="Địa chỉ" required>
        </div>

        <hr>
        <h3>Danh sách mặt hàng</h3>
        <button type="button" class="btn btn-add" onclick="addRow()">+ Thêm sản phẩm</button>
        
        <table id="item-table">
            <thead>
                <tr>
                    <th width="50%">Sản phẩm</th>
                    <th width="15%">SL</th>
                    <th width="20%">Đơn giá</th>
                    <th width="15%">Xoá</th>
                </tr>
            </thead>
            <tbody id="invoice-items">
                </tbody>
        </table>

        <div id="grand-total">Tổng cộng: 0 đ</div>
        <button type="submit" class="btn btn-save">LƯU VÀ XUẤT HOÁ ĐƠN</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    const products = <?= json_encode($p_list) ?>;

    function addRow() {
        const id = 'row_' + Date.now();
        const html = `
            <tr id="${id}">
                <td>
                    <select name="product_id[]" class="p-select" onchange="updatePrice(this)" required>
                        <option value="">-- Tìm tên sản phẩm --</option>
                        ${products.map(p => `<option value="${p.id}" data-price="${p.price}">${p.name} (Tồn: ${p.stock})</option>`).join('')}
                    </select>
                </td>
                <td><input type="number" name="quantity[]" value="1" min="1" oninput="calculateTotal()" required></td>
                <td><span class="price-display">0</span> đ</td>
                <td><button type="button" onclick="removeRow('${id}')" style="color:red; background:none; border:none; cursor:pointer;">✕ Xoá</button></td>
            </tr>
        `;
        $('#invoice-items').append(html);
        $('.p-select').select2(); // Kích hoạt tìm kiếm
    }

    function removeRow(id) {
        $(`#${id}`).remove();
        calculateTotal();
    }

    function updatePrice(el) {
        const price = $(el).find(':selected').data('price') || 0;
        $(el).closest('tr').find('.price-display').text(new Intl.NumberFormat().format(price));
        calculateTotal();
    }

    function calculateTotal() {
        let total = 0;
        $('#invoice-items tr').each(function() {
            const price = $(this).find('.p-select').find(':selected').data('price') || 0;
            const qty = $(this).find('input[name="quantity[]"]').val() || 0;
            total += (price * qty);
        });
        $('#grand-total').text("Tổng cộng: " + new Intl.NumberFormat().format(total) + " đ");
    }

    // Luôn có 1 dòng khi mở trang
    $(document).ready(() => addRow());
</script>

</body>
</html>