<?php
$GEMINI_API_KEY = "AIzaSyCTbwvLgJf9jmSmIV-IT2BHn_U_0A_Y7cM";
/* ===== KẾT NỐI DB ===== */
$conn = new mysqli("localhost", "root", "", "motorbike_showroom", 3307);
$conn->set_charset("utf8");

$userMsg = $_POST['message'] ?? '';
if (!$userMsg) {
    echo json_encode(["error"=>"No message"]);
    exit;
}

/* ===== LẤY DANH SÁCH XE ===== */
$sql = "
SELECT p.name, b.name AS brand, c.name AS category,
       p.price, p.description, p.image
FROM products p
JOIN brands b ON p.brand_id = b.id
JOIN categories c ON p.category_id = c.id
LIMIT 20
";

$result = $conn->query($sql);

$motors = [];
$motorText = "";

while ($row = $result->fetch_assoc()) {
    $motors[] = $row;
    $motorText .= "- {$row['name']} | {$row['brand']} | {$row['category']} | {$row['price']} VND | {$row['description']}\n";
}

/* ===== PROMPT ===== */
$prompt = "Bạn là chatbot tư vấn xe máy cho showroom.
Chỉ sử dụng danh sách xe bên dưới, không được bịa.
Nếu nhắc tới xe nào thì mô tả đúng xe đó.

DANH SÁCH XE:
$motorText

CÂU HỎI KHÁCH:
$userMsg";

/* ===== GỌI GEMINI ===== */
$data = [
    "contents" => [
        ["parts" => [["text" => $prompt]]]
    ]
];

$ch = curl_init("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent");

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "x-goog-api-key: $GEMINI_API_KEY"
    ],
    CURLOPT_POSTFIELDS => json_encode($data)
]);

$response = curl_exec($ch);
curl_close($ch);

/* ===== XỬ LÝ KẾT QUẢ ===== */
$data = json_decode($response, true);
$replyText = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Xin lỗi, tôi chưa hiểu câu hỏi.';

$images = [];
foreach($motors as $m){
    if(stripos($replyText, $m['name']) !== false){
        $images[] = [
            "name" => $m['name'],
            "image" => "../assets/uploads/".$m['image'],
        ];
    }
}

header("Content-Type: application/json; charset=utf-8");
echo json_encode([
    "reply" => $replyText,
    "images" => $images
], JSON_UNESCAPED_UNICODE);