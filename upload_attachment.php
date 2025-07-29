<?php

    // // ----------- SINGLE FILE ----------------
    //     include("koneksi.php");

    //     $smbHost   = "//192.168.1.237/Doc_System";
    //     $smbUser   = "helpdesk";
    //     $smbPass   = "P@ssw0rd";
    //     $smbFolder = "/DOCO/petty_cash/";

    //     $id_jo = mysqli_real_escape_string($koneksi, $_POST['id_jo']);
    //     $query = "SELECT no_cont FROM tr_jo WHERE id_jo = '$id_jo' LIMIT 1";
    //     $result = mysqli_query($koneksi, $query);

    //     if (!$row = mysqli_fetch_assoc($result)) {
    //         echo "❌ Kontainer tidak ditemukan untuk ID JO: $id_jo<br>";
    //         exit;
    //     }

    //     $kontainer = preg_replace('/[^A-Za-z0-9_\-]/', '_', $row['no_cont']);

    //     $fileFields = [
    //         'file_so'     => 'foto_so_',
    //         'file_sj'     => 'surat_jalan_',
    //         'file_mutasi' => 'mutasi_rekening_'
    //     ];

    //     $uploadDir = 'uploads/';
    //     if (!is_dir($uploadDir)) {
    //         mkdir($uploadDir, 0755, true);
    //     }

    //     $successCount = 0;

    //     foreach ($fileFields as $field => $prefix) {
    //         echo "<strong>📁 Proses field: $field</strong><br>";

    //         if (!isset($_FILES[$field])) {
    //             echo "❌ Field $field tidak ditemukan di \$_FILES<br>";
    //             continue;
    //         }

    //         $file = $_FILES[$field];
    //         $filename = basename($file['name']);
    //         $tmpPath = $file['tmp_name'];
    //         $errorCode = $file['error'];
    //         $size = $file['size'];

    //         echo "→ Nama file: $filename<br>";
    //         echo "→ Size: $size bytes<br>";
    //         echo "→ Temp path: $tmpPath<br>";
    //         echo "→ Error code: $errorCode<br>";

    //         if ($errorCode !== 0) {
    //             echo "❌ Upload error untuk $field (code: $errorCode)<br>";
    //             continue;
    //         }

    //         if (!is_uploaded_file($tmpPath)) {
    //             echo "❌ File bukan hasil upload: $tmpPath<br>";
    //             continue;
    //         }

    //         $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    //         $timestamp = time();
    //         $imageName = $prefix . $timestamp . '_' . $kontainer . '.' . $extension;
    //         $localPath = $uploadDir . $imageName;

    //         if (move_uploaded_file($tmpPath, $localPath)) {
    //             $escapedLocalPath  = escapeshellarg($localPath);
    //             $escapedRemotePath = escapeshellarg($smbFolder . $imageName);

    //             $command = "smbclient '{$smbHost}' -U '{$smbUser}%{$smbPass}' -c 'put {$escapedLocalPath} {$escapedRemotePath}'";
    //             exec($command, $output, $return_var);

    //             if ($return_var === 0) {
    //                 $sqlInsert = "INSERT INTO tr_jo_attachment (id_jo, attachment) VALUES ('$id_jo', '$imageName')";
    //                 $insert = mysqli_query($koneksi, $sqlInsert);

    //                 if ($insert) {
    //                     $successCount++;
    //                     unlink($localPath);
    //                     echo "✅ Berhasil upload dan insert DB untuk $field<br>";
    //                 } else {
    //                     echo "❌ Gagal insert DB untuk $field: " . mysqli_error($koneksi) . "<br>";
    //                 }
    //             } else {
    //                 echo "❌ Gagal kirim file SMB untuk $field: $imageName<br>";
    //                 echo "<pre>" . implode("\n", $output) . "</pre>";
    //             }
    //         } else {
    //             echo "❌ move_uploaded_file gagal untuk $field: $filename<br>";
    //             echo "→ File exists? " . (file_exists($tmpPath) ? 'Yes' : 'No') . "<br>";
    //             echo "→ is_uploaded_file? " . (is_uploaded_file($tmpPath) ? 'Yes' : 'No') . "<br>";
    //             echo "→ Folder writable? " . (is_writable($uploadDir) ? 'Yes' : 'No') . "<br>";
    //         }

    //         echo "<hr>";
    //     }

    //     if ($successCount > 0) {
    //         echo "<strong>✅ Total berhasil upload: $successCount file.</strong>";
    //     } else {
    //         echo "<strong>❌ Tidak ada file yang berhasil diproses.</strong>";
    //     }

    // // END SINGLE 


    include("koneksi.php");

    if (empty($_POST['id_jo'])) {
        echo "❌ Parameter 'id_jo' tidak ditemukan.";
        exit;
    }

    $id_jo = mysqli_real_escape_string($koneksi, $_POST['id_jo']);

    // Ambil data kontainer
    $query = "SELECT no_cont FROM tr_jo WHERE id_jo = '$id_jo' LIMIT 1";
    $result = mysqli_query($koneksi, $query);
    if (!$row = mysqli_fetch_assoc($result)) {
        echo "❌ Kontainer tidak ditemukan untuk ID JO: $id_jo";
        exit;
    }
    $kontainer = preg_replace('/[^A-Za-z0-9_\-]/', '_', $row['no_cont']);

    // Konfigurasi SMB & lokal
    $smbHost   = "//192.168.1.237/Doc_System";
    $smbUser   = "helpdesk";
    $smbPass   = "P@ssw0rd";
    $smbFolder = "/DOCO/petty_cash/";
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    // Mapping field upload
    $fileMap = [
        'file_so'     => 'foto_so_',
        'file_sj'     => 'surat_jalan_',
        'file_mutasi' => 'mutasi_rekening_'
    ];

    $uploaded = false;

    foreach ($fileMap as $field => $prefix) {
        if (!isset($_FILES[$field])) continue;

        $file = $_FILES[$field];
        $filename = basename($file['name']);
        $tmpPath  = $file['tmp_name'];
        $errorCode = $file['error'];
        $size = $file['size'];

        echo "<strong>📄 Proses upload: $field</strong><br>";
        echo "→ Nama: $filename, Size: $size, Error: $errorCode<br>";

        if ($errorCode !== 0 || !is_uploaded_file($tmpPath)) {
            echo "❌ Gagal upload: " . ($errorCode !== 0 ? "Error code $errorCode" : "File tidak valid") . "<br><hr>";
            continue;
        }

        // Rename file
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $timestamp = time();
        $imageName = $prefix . $timestamp . '_' . $kontainer . '.' . $extension;
        $localPath = $uploadDir . $imageName;

        // Pindahkan file
        if (!move_uploaded_file($tmpPath, $localPath)) {
            echo "❌ Gagal move file ke local: $localPath<br><hr>";
            continue;
        }

        // Kirim ke SMB
        $escapedLocalPath  = escapeshellarg($localPath);
        $escapedRemotePath = escapeshellarg($smbFolder . $imageName);
        $command = "smbclient '{$smbHost}' -U '{$smbUser}%{$smbPass}' -c 'put {$escapedLocalPath} {$escapedRemotePath}'";
        exec($command, $output, $return_var);

        if ($return_var !== 0) {
            echo "❌ Gagal kirim ke SMB: $imageName<br>";
            echo "<pre>" . implode("\n", $output) . "</pre><hr>";
            unlink($localPath);
            continue;
        }

        // Cek duplikat
        $cek_sql = "SELECT COUNT(*) as jml FROM tr_jo_attachment WHERE id_jo = '$id_jo' AND attachment LIKE '{$prefix}%'";
        $cek_q   = mysqli_query($koneksi, $cek_sql);
        $cek     = mysqli_fetch_assoc($cek_q);

        if ($cek['jml'] > 0) {
            echo "⚠️ File dengan jenis '$field' sudah pernah diupload. Lewati insert DB.<br>";
        } else {
            // Simpan ke DB
            $sqlInsert = "INSERT INTO tr_jo_attachment (id_jo, attachment) VALUES ('$id_jo', '$imageName')";
            if (mysqli_query($koneksi, $sqlInsert)) {
                echo "✅ Upload & insert DB berhasil: $imageName<br>";
                $uploaded = true;
            } else {
                echo "❌ Gagal insert DB: " . mysqli_error($koneksi) . "<br>";
            }
        }

        // Hapus file lokal
        unlink($localPath);
        echo "<hr>";
    }

    if (!$uploaded) {
        echo "<strong>❌ Tidak ada file baru yang berhasil diupload.</strong>";
    } else {
        echo "<strong>✅ Upload selesai.</strong>";
    }
?>
