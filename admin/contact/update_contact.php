<?php
require '../../config/database.php';

header('Content-Type: application/json');

$id = (int)($_POST['id'] ?? 0);
$status = (int)($_POST['status'] ?? -1);

if ($id <= 0 || !in_array($status, [0,1,2,3])) {
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
    exit;
}

$stmt = $conn->prepare(
    "UPDATE contact_requests SET status = ? WHERE id = ?"
);
$stmt->bind_param("ii", $status, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}
