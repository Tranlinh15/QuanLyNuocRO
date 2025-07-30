<?php
header('Content-Type: text/plain');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Max-Age: 3600');
header('Access-Control-Allow-Headers: Content-Type, X-API-KEY');

// Kiểm tra phương thức
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die("ERROR: Method not allowed");
}

// Kiểm tra API Key
$apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
if ($apiKey !== 'WATER_MONITOR_2024') {
    http_response_code(401);
    die("ERROR: Unauthorized");
}

// Validate dữ liệu
$tds = filter_input(INPUT_POST, 'tds', FILTER_VALIDATE_FLOAT);
$ph = filter_input(INPUT_POST, 'ph', FILTER_VALIDATE_FLOAT);
$time = filter_input(INPUT_POST, 'time', FILTER_SANITIZE_STRING) ?? date('H:i:s');

if ($tds === false || $ph === false || $tds === null || $ph === null) {
    http_response_code(400);
    die("ERROR: Invalid data format");
}

// Giới hạn giá trị hợp lý
if ($tds < 0 || $tds > 2000 || $ph < 0 || $ph > 14) {
    http_response_code(400);
    die("ERROR: Value out of range");
}

// Ghi dữ liệu
$file = 'data.csv';
$date = date('Y-m-d');
$entry = "$date $time,$tds,$ph\n";

// Kiểm tra kích thước file trước khi ghi
if (file_exists($file) {
    if (filesize($file) > 10485760) { // 10MB
        http_response_code(507);
        die("ERROR: Storage limit exceeded");
    }
    
    // Kiểm tra xem file có header không
    $firstLine = file_get_contents($file, false, null, 0, 10);
    if (strpos($firstLine, 'time,tds,ph') === false) {
        file_put_contents($file, "time,tds,ph\n", FILE_APPEND | LOCK_EX);
    }
}

if (file_put_contents($file, $entry, FILE_APPEND | LOCK_EX) === false) {
    http_response_code(500);
    die("ERROR: Failed to save data");
}

echo "OK";
?>
