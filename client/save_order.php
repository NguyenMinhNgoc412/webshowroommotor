<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

require_once '../../config/database.php';

$product_id     = intval($_POST['product_id'] ?? 0);
$customer_name  = trim($_POST['customer_name'] ?? '');
$phone          = trim($_POST['phone'] ?? '');
$email          = trim($_POST['email'] ?? '');
$address        = trim($_POST['address'] ?? '');
$note           = trim($_POST['note'] ?? '');

if ($product_id <= 0 || $customer_name === '' || $phone === '') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Thiếu dữ liệu bắt buộc'
    ]);
    exit;
}

$sql = "
    INSERT INTO contact_requests
    (product_id, customer_name, phone, email, address, note, status)
    VALUES (?, ?, ?, ?, ?, ?, 'chưa liên hệ')
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Prepare failed'
    ]);
    exit;
}

$stmt->bind_param(
    "isssss",
    $product_id,
    $customer_name,
    $phone,
    $email,
    $address,
    $note
);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Insert failed'
    ]);
}
