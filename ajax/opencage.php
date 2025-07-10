<?php

// OPENCAGE
    header('Content-Type: application/json');

    $apiKey = '835f46493c844d1d9be563136b1a5344';

    $type = $_POST['type'] ?? '';
    $address = $_POST['address'] ?? '';
    $lat = $_POST['lat'] ?? '';
    $lon = $_POST['lon'] ?? '';

    $options = [
        'http' => [
            'header' => "User-Agent: MyApp/1.0 (itdev2.staff.map@gmail.com)\r\n"
        ]
    ];
    $context = stream_context_create($options);


    if ($lat && $lon) {
        $url = "https://api.opencagedata.com/geocode/v1/json?q={$lat}+{$lon}&key=$apiKey&limit=1&language=id";
        $response = file_get_contents($url, false, $context);

        if (!$response) {
            echo json_encode(["status" => "error", "message" => "Gagal reverse geocoding"]);
            exit;
        }

        $data = json_decode($response, true);
        if (!empty($data['results'][0])) {
            $formatted = $data['results'][0]['formatted'];
            echo json_encode([
                "status" => "success",
                "formatted" => $formatted
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Alamat tidak ditemukan dari lat/lon"]);
        }
        exit;
    }

    if (empty($address)) {
        echo json_encode(["status" => "error", "message" => "Alamat kosong."]);
        exit;
    }

    $alamat = urlencode($address);
    $url = "https://api.opencagedata.com/geocode/v1/json?q=$alamat&key=$apiKey&limit=1&language=id";

    $response = file_get_contents($url, false, $context);
    if (!$response) {
        echo json_encode(["status" => "error", "message" => "Gagal hubungi OpenCage"]);
        exit;
    }

    $data = json_decode($response, true);
    if (!empty($data['results'][0])) {
        $result = $data['results'][0];
        $lat = $result['geometry']['lat'];
        $lon = $result['geometry']['lng'];
        $formatted = $result['formatted'];

        $html = "<div style='padding:6px; border-left:4px solid green;'>
                    <strong>Latitude:</strong> $lat<br>
                    <strong>Longitude:</strong> $lon<br>
                    <strong>Alamat Lengkap:</strong><br>$formatted
                </div>";

        echo json_encode([
            "status" => "success",
            "lat" => $lat,
            "lon" => $lon,
            "html" => $html
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Alamat tidak ditemukan."]);
    }
// END OPENCAGE

?>
