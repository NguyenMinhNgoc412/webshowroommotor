<?php
require 'includes/auth.php';
require '../config/database.php';

$current_page = 'customers.php';
include 'includes/header.php';

/* ===== XỬ LÝ TÌM KIẾM ===== */
$keyword = $_GET['keyword'] ?? '';
$keyword = trim($keyword);

$where = '';
$params = [];

if ($keyword !== '') {
    $where = "WHERE c.full_name LIKE ? 
              OR c.phone LIKE ? 
              OR c.cccd LIKE ?";
    $kw = "%$keyword%";
    $params = [$kw, $kw, $kw];
}

/* ===== QUERY ===== */
$sql = "
SELECT 
    c.id,
    c.full_name,
    c.cccd,
    c.phone,
    c.address,
    cr_max.contact_status
FROM customers c
LEFT JOIN (
    SELECT phone, MAX(status) AS contact_status
    FROM contact_requests
    GROUP BY phone
) cr_max ON cr_max.phone = c.phone
$where
ORDER BY c.id DESC
";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<div class="content">

    <!-- Tiêu đề -->
    <div class="page-header">
        <h2><i class="fa-solid fa-users"></i> Danh sách khách hàng</h2>
    </div>

    <!-- Thanh tìm kiếm + nút thêm -->
    <div class="toolbar">
        <form method="get" class="search-box">
            <input 
                type="text"
                name="keyword"
                placeholder="Tìm theo họ tên / SĐT / CCCD..."
                value="<?= htmlspecialchars($keyword) ?>"
            >
            <button type="submit">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>

        <a href="customers_add.php" class="btn-add">
             Thêm khách hàng
        </a>
    </div>          

    <!-- Bảng dữ liệu -->
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Họ tên</th>
                <th>CCCD</th>
                <th>Điện thoại</th>
                <th>Địa chỉ</th>
                <th>Trạng thái</th>
                <th class="action-col">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                        // Trạng thái mặc định
                        $status_class = 'status-no';
                        $status_text  = 'Chưa liên hệ';

                        if ($row['contact_status'] !== null) {
                            switch ((int)$row['contact_status']) {
                                case 0:
                                    $status_class = 'status-new';
                                    $status_text  = 'Mới';
                                    break;
                                case 1:
                                    $status_class = 'status-contacted';
                                    $status_text  = 'Đã liên hệ';
                                    break;
                                case 2:
                                    $status_class = 'status-ordered';
                                    $status_text  = 'Chốt đơn';
                                    break;
                                case 3:
                                    $status_class = 'status-done';
                                    $status_text  = 'Hoàn tất';
                                    break;
                            }
                        }
                    ?>
                    <tr>
                        <td>#<?= $row['id'] ?></td>
                        <td><strong><?= htmlspecialchars($row['full_name']) ?></strong></td>
                        <td><?= htmlspecialchars($row['cccd'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td><?= htmlspecialchars($row['address']) ?></td>
                        <td>
                            <span class="status <?= $status_class ?>">
                                <?= $status_text ?>
                            </span>
                        </td>
                        <td class="action-col">
                            <a href="customers_edit.php?id=<?= $row['id'] ?>" class="btn-action btn-edit">
                                Sửa
                            </a>
                            <a href="customers_delete.php?id=<?= $row['id'] ?>" 
                               class="btn-action btn-delete"
                               onclick="return confirm('Xóa khách hàng này?');">
                                 Xóa
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align:center;">Không có khách hàng</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>
