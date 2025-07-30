<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Max-Age: 3600');

$file = 'data.csv';

// Tạo file nếu chưa tồn tại
if (!file_exists($file)) {
    file_put_contents($file, "time,tds,ph\n");
    echo json_encode([]);
    exit;
}

// Đọc dữ liệu từ file CSV
$data = [];
if (($handle = fopen($file, "r")) !== FALSE) {
    $firstLine = fgetcsv($handle); // Bỏ qua header
    
    while (($row = fgetcsv($handle)) !== FALSE) {
        if (count($row) !== 3) continue;
        
        $time = trim($row[0]);
        $tds = is_numeric($row[1]) ? floatval($row[1]) : null;
        $ph = is_numeric($row[2]) ? floatval($row[2]) : null;
        
        if ($tds !== null && $ph !== null) {
            $data[] = [
                'time' => $time,
                'tds' => $tds,
                'ph' => $ph
            ];
        }
    }
    fclose($handle);
}

// Sắp xếp dữ liệu theo thời gian (cũ nhất trước)
usort($data, function($a, $b) {
    return strtotime($a['time']) - strtotime($b['time']);
});

echo json_encode($data);
?>
