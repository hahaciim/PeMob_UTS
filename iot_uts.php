<?php
// Mengimpor file koneksi
include 'koneksi.php';

// Menyiapkan respons JSON
header('Content-Type: application/json');

// Array untuk menampung data yang akan dikembalikan
$response_data = [
    "suhu_max" => null,
    "humid_max" => null,
    "suhurata" => null,
    "nilai_suhu_max_humid_max" => [],
    "month_year_max" => []
];

// Query untuk mendapatkan suhu maksimum, kelembapan maksimum, dan rata-rata suhu
$summary_query = "SELECT 
                    MAX(suhu) AS suhu_max, 
                    MAX(humid) AS humid_max,
                    AVG(suhu) AS suhurata
                  FROM tb_cuaca";
$summary_result = $connection->query($summary_query);

if ($summary_result && $summary_result->num_rows > 0) {
    $summary_data = $summary_result->fetch_assoc();
    $response_data["suhu_max"] = $summary_data["suhu_max"];
    $response_data["humid_max"] = $summary_data["humid_max"];
    $response_data["suhurata"] = $summary_data["suhurata"];
}

// Query untuk mengambil data yang memiliki suhu maksimum DAN kelembapan maksimum
$data_query = "SELECT id, suhu, humid, lux, ts 
               FROM tb_cuaca 
               WHERE suhu = {$response_data['suhu_max']} 
               AND humid = {$response_data['humid_max']}";
$data_result = $connection->query($data_query);

if ($data_result && $data_result->num_rows > 0) {
    while ($row = $data_result->fetch_assoc()) {
        $response_data["nilai_suhu_max_humid_max"][] = [
            "idx" => $row["id"],
            "suhux" => $row["suhu"],
            "humid" => $row["humid"],
            "kecerahan" => $row["lux"],
            "timestamp" => $row["ts"]
        ];
    }
}

// Query untuk mendapatkan bulan-tahun yang memiliki suhu maksimum DAN kelembapan maksimum
$monthly_query = "SELECT 
                    DATE_FORMAT(ts, '%m-%Y') AS month_year
                  FROM tb_cuaca 
                  WHERE suhu = {$response_data['suhu_max']} 
                  AND humid = {$response_data['humid_max']}
                  GROUP BY month_year";
$monthly_result = $connection->query($monthly_query);

if ($monthly_result && $monthly_result->num_rows > 0) {
    while ($row = $monthly_result->fetch_assoc()) {
        $response_data["month_year_max"][] = [
            "month_year" => $row["month_year"]
        ];
    }
}

// Mengembalikan respons dalam format JSON
echo json_encode($response_data);

// Menutup koneksi database
$connection->close();
?>
