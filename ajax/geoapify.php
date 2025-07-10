<?php
// GEOAPIFY
    header('Content-Type: application/json');

    $apiKey = '19dc5030f17a4198a54049d88d520208'; // Geoapify API Key
    $type = $_POST['type'] ?? '';
    $address = $_POST['address'] ?? '';
    $lat = $_POST['lat'] ?? '';
    $lon = $_POST['lon'] ?? '';

    $options = [
        'http' => [
            'header' => "User-Agent: MyApp/1.0 (your@email.com)\r\n"
        ]
    ];
    $context = stream_context_create($options);

    // 🔁 REVERSE GEOCODING
    if ($lat && $lon) {
        $url = "https://api.geoapify.com/v1/geocode/reverse?lat={$lat}&lon={$lon}&apiKey={$apiKey}&lang=id";
        $response = file_get_contents($url, false, $context);

        if (!$response) {
            echo json_encode(["status" => "error", "message" => "Gagal reverse geocoding"]);
            exit;
        }

        $data = json_decode($response, true);
        if (!empty($data['features'][0])) {
            $formatted = $data['features'][0]['properties']['formatted'];
            echo json_encode([
                "status" => "success",
                "formatted" => $formatted
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Alamat tidak ditemukan dari lat/lon"]);
        }
        exit;
    }

    // 📍 FORWARD GEOCODING DARI ALAMAT
    if (empty($address)) {
        echo json_encode(["status" => "error", "message" => "Alamat kosong."]);
        exit;
    }

    $alamat = urlencode($address);
    $url = "https://api.geoapify.com/v1/geocode/search?text=$alamat&apiKey=$apiKey&lang=id&limit=1";

    $response = file_get_contents($url, false, $context);
    if (!$response) {
        echo json_encode(["status" => "error", "message" => "Gagal hubungi Geoapify"]);
        exit;
    }

    $data = json_decode($response, true);
    if (!empty($data['features'][0])) {
        $result = $data['features'][0];
        $lat = $result['geometry']['coordinates'][1]; // [lat, lon]
        $lon = $result['geometry']['coordinates'][0];
        $formatted = $result['properties']['formatted'];

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
// END GEOAPIFY
?>