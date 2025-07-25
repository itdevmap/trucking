<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'koneksi.php';
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    $file = $_FILES['excel_file']['tmp_name'];

    if (!file_exists($file)) {
        die("File tidak ditemukan.");
    }

    try {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();

        $no_quotation = $_POST['no_quo'] ?? null;

        // Ambil id_quo dari nomor quotation
        $stmt = $koneksi->prepare("SELECT id_quo FROM t_ware_quo WHERE quo_no = ?");
        $stmt->bind_param("s", $no_quotation);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $id_quo = $row['id_quo'];
            echo "<p><strong>ID Quotation:</strong> $id_quo</p>";
        } else {
            die("<p style='color:red'>ID Quotation untuk '$no_quotation' tidak ditemukan di database.</p>");
        }

        $rows = $sheet->toArray();
        $success = 0;

        echo "<h3>Preview Data dari Excel:</h3>";
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr>
                <th>Kode</th><th>Nama</th><th>Unit</th>
                <th>Panjang</th><th>Lebar</th><th>Tinggi</th>
                <th>Berat</th><th>Volume</th>
            </tr>";

        foreach ($rows as $index => $row) {
            // Lewati 3 baris pertama dan baris ke-4 (header)
            if ($index < 4) continue;

            // Skip baris kosong
            if (empty(trim($row[1]))) continue;

            $kode    = trim($row[1] ?? '');
            $nama    = trim($row[2] ?? '');
            $unit    = trim($row[3] ?? '');
            $panjang = floatval($row[4] ?? 0);
            $lebar   = floatval($row[5] ?? 0);
            $tinggi  = floatval($row[6] ?? 0);
            $berat   = floatval($row[7] ?? 0);
            $vol     = floatval($row[8] ?? 0);

            // Skip baris jika kolom kode berisi teks header
            if (strtoupper($kode) === 'ITEMCODE') continue;

            $masuk = 0;
            $keluar = 0;

            echo "<tr>
                    <td>" . htmlspecialchars($kode) . "</td>
                    <td>" . htmlspecialchars($nama) . "</td>
                    <td>" . htmlspecialchars($unit) . "</td>
                    <td>" . htmlspecialchars($panjang) . "</td>
                    <td>" . htmlspecialchars($lebar) . "</td>
                    <td>" . htmlspecialchars($tinggi) . "</td>
                    <td>" . htmlspecialchars($berat) . "</td>
                    <td>" . htmlspecialchars($vol) . "</td>
                </tr>";

            // Insert ke database
            $insert = $koneksi->prepare("
                INSERT INTO t_ware 
                    (id_quo, kode, nama, unit, panjang, lebar, tinggi, masuk, keluar, berat, vol)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $insert->bind_param(
                "isssddddidd",
                $id_quo, $kode, $nama, $unit,
                $panjang, $lebar, $tinggi,
                $masuk, $keluar,
                $berat, $vol
            );

            if ($insert->execute()) {
                $success++;
            } else {
                echo "<tr><td colspan='8' style='color:red'>Gagal insert: " . htmlspecialchars($insert->error) . "</td></tr>";
            }

            $insert->close();
        }

        echo "</table>";
        echo "<p><strong>Total berhasil disimpan:</strong> $success data.</p>";

        // Tambahkan script untuk reload halaman utama dan tutup popup
        if ($success > 0) {
            echo "<script>
                if (window.opener) {
                    window.opener.location.reload();
                    setTimeout(() => window.close(), 2000);
                }
            </script>";
        }

    } catch (Exception $e) {
        echo "<p style='color:red'>Gagal membaca file Excel: " . htmlspecialchars($e->getMessage()) . "</p>";
    }

} else {
    echo "Form belum mengirim file.";
}
?>
