<?php

header('Content-Type: application/json');
include 'koneksi.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['ARNum'], $input['ARPaid'], $input['ARTipeSales'])) {
    echo json_encode(["status" => "error", "message" => "Invalid or incomplete JSON"]);
    exit;
}

$no_ar  = mysqli_real_escape_string($koneksi, $input['ARNum']);
$sales  = $input['ARTipeSales'];
$nom_ar = (int) $input['ARPaid'];

if ($sales == "6") {
    $update = "UPDATE t_ware_data SET nominal_paid = '$nom_ar' WHERE no_ar = '$no_ar'";
    $fetch  = "SELECT no_doc AS doc FROM t_ware_data WHERE no_ar = '$no_ar'";
} else {
    $update = "UPDATE tr_jo SET nominal_paid = '$nom_ar' WHERE no_ar = '$no_ar'";
    $fetch  = "SELECT no_jo AS doc FROM tr_jo WHERE no_ar = '$no_ar'";
}

mysqli_query($koneksi, $update);
$affected_rows = mysqli_affected_rows($koneksi);

$result = mysqli_query($koneksi, $fetch);
$data = mysqli_fetch_assoc($result);
$no_doc = $data['doc'] ?? null;

$raw_data = mysqli_real_escape_string($koneksi, json_encode($input));

if ($affected_rows > 0 && !empty($no_doc)) {
    $log = "INSERT INTO tr_api_logs (docnum, doctype, raw_data, result, `desc`)
            VALUES ('$no_doc', 'API PAYMENT', '$raw_data','SUCCESS', 'SUCCESS')";
    mysqli_query($koneksi, $log);

    echo json_encode([
        "status"  => "success",
        "message" => "Data updated",
        "docnum"  => $no_doc,
        "no_ar"   => $no_ar,
        "paid"    => $nom_ar
    ]);
} else {
    $error_msg = $affected_rows == 0 ? "NO DATA FOUND OR NOT UPDATED" : mysqli_error($koneksi);

    $log = "INSERT INTO tr_api_logs (docnum, doctype, raw_data, result, `desc`)
            VALUES ('$no_ar', 'API PAYMENT', '$raw_data','NO AR PETJ', '$error_msg')";
    mysqli_query($koneksi, $log);

    echo json_encode([
        "status"  => "bukan_ar_petj",
        "message" => "Failed to update",
        "error"   => $error_msg,
        "docnum"  => $no_doc,
        "debug_sql" => $fetch
    ]);
}

exit;
