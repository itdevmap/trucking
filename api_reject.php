<?php
include "koneksi.php";
header("Content-Type: application/json");

$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
    exit;
}

if (!$koneksi) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'DB Connection failed']);
    exit;
}

$projectCode = $data['project'] ?? null;

if (!$projectCode) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'project is required']);
    exit;
}

$stmt = mysqli_prepare($koneksi, "SELECT * FROM tr_jo WHERE project_code = ?");
mysqli_stmt_bind_param($stmt, "s", $projectCode);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$joData = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

// ✅ Tambahkan query UPDATE status ke 0
$updateStmt = mysqli_prepare($koneksi, "UPDATE tr_jo SET status = 0 WHERE project_code = ?");
mysqli_stmt_bind_param($updateStmt, "s", $projectCode);
$updateSuccess = mysqli_stmt_execute($updateStmt);

// Ambil jumlah baris yang diubah (opsional)
$affectedRows = mysqli_stmt_affected_rows($updateStmt);
mysqli_stmt_close($updateStmt);

// Tutup koneksi
mysqli_close($koneksi);

// Kirim response
echo json_encode([
    'status' => 'success',
    'project_code' => $projectCode,
    'updated_rows' => $affectedRows,
    'jo_data' => $joData
]);
