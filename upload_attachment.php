<?php

    // // ----------- MUILTIPLE FILE ----------------
    // include("koneksi.php");
    // $smbHost   = "//192.168.1.237/Doc_System";
    // $smbUser   = "helpdesk";
    // $smbPass   = "P@ssw0rd";
    // $smbFolder = "/DOCO/petty_cash/";

    // if (isset($_FILES['file_attachment']) && isset($_POST['id_jo'])) {


    //     $id_jo = mysqli_real_escape_string($koneksi, $_POST['id_jo']);
    //     $query = "SELECT no_cont FROM tr_jo WHERE id_jo = '$id_jo' LIMIT 1";
    //     $result = mysqli_query($koneksi, $query);

    //     if (!$row = mysqli_fetch_assoc($result)) {
    //         echo "❌ Kontainer tidak ditemukan untuk ID JO: $id_jo";
    //         exit;
    //     }

    //     $kontainer = preg_replace('/[^A-Za-z0-9_\-]/', '_', $row['no_cont']);


    //     $files = $_FILES['file_attachment'];
    //     $total = count($files['name']);

    //     for ($i = 0; $i < $total; $i++) {
    //         if ($files['error'][$i] === 0) {
    //             $filename  = basename($files['name'][$i]);
    //             $tmpPath   = $files['tmp_name'][$i];
    //             $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    //             $timestamp = strtotime(date("Y-m-d H:i:s")) . $i;

    //             $imageName = 'foto_so_' . $timestamp . '_' . $kontainer . '.' . $extension;

    //             // Simpan sementara ke lokal
    //             $uploadDir = 'uploads/';
    //             if (!is_dir($uploadDir)) {
    //                 mkdir($uploadDir, 0755, true);
    //             }
    //             $localPath = $uploadDir . $imageName;

    //             if (move_uploaded_file($tmpPath, $localPath)) {
    //                 // Kirim ke SMB
    //                 $escapedLocalPath  = escapeshellarg($localPath);
    //                 $escapedRemotePath = escapeshellarg($smbFolder . $imageName);

    //                 $command = "smbclient '{$smbHost}' -U '{$smbUser}%{$smbPass}' -c 'put {$escapedLocalPath} {$escapedRemotePath}'";
    //                 exec($command, $output, $return_var);

    //                 if ($return_var === 0) {
    //                     // $sqlInsert = "INSERT INTO tr_jo_attachment (id_jo, attachment) VALUES ('$id_jo', '$imageName')";
    //                     // $insert = mysqli_query($koneksi, $sqlInsert);
    //                     // unlink($localPath);

    //                     if (!$insert) {
    //                         echo "Gagal insert ke DB: " . mysqli_error($koneksi) . "<br>";
    //                     }
    //                 } else {
    //                     echo "Gagal kirim file ke SMB: $imageName<br>";
    //                     echo "<pre>" . implode("\n", $output) . "</pre>";
    //                 }

    //                 $sqlInsert = "INSERT INTO tr_jo_attachment (id_jo, attachment) VALUES ('$id_jo', '$imageName')";
    //                 $insert = mysqli_query($koneksi, $sqlInsert);
    //                 // unlink($localPath);
    //             } else {
    //                 echo "Gagal simpan file lokal: $filename<br>";
    //             }
    //         }
    //     }

    //     echo "Semua file diproses.";
    // } else {
    //     echo "File atau ID JO tidak dikirim.";
    // }


    // ----------- SINGLE FILE ----------------
        include("koneksi.php");
        $smbHost   = "//192.168.1.237/Doc_System";
        $smbUser   = "helpdesk";
        $smbPass   = "P@ssw0rd";
        $smbFolder = "/DOCO/petty_cash/";

        $id_jo = mysqli_real_escape_string($koneksi, $_POST['id_jo']);
        $query = "SELECT no_cont FROM tr_jo WHERE id_jo = '$id_jo' LIMIT 1";
        $result = mysqli_query($koneksi, $query);

        if (!$row = mysqli_fetch_assoc($result)) {
            echo "Kontainer tidak ditemukan untuk ID JO: $id_jo";
            exit;
        }

        $kontainer = preg_replace('/[^A-Za-z0-9_\-]/', '_', $row['no_cont']);

        $fileFields = [
            'file_so'     => 'foto_so_',
            'file_sj'     => 'surat_jalan_',
            'file_mutasi' => 'mutasi_rekening_'
        ];

        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $successCount = 0;

        foreach ($fileFields as $field => $prefix) {
            if (isset($_FILES[$field]) && $_FILES[$field]['error'] === 0) {
                $file      = $_FILES[$field];
                $filename  = basename($file['name']);
                $tmpPath   = $file['tmp_name'];
                $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                $timestamp = strtotime(date("Y-m-d H:i:s"));

                $imageName = $prefix . $timestamp . '_' . $kontainer . '.' . $extension;
                $localPath = $uploadDir . $imageName;

                if (move_uploaded_file($tmpPath, $localPath)) {
                    $escapedLocalPath  = escapeshellarg($localPath);
                    $escapedRemotePath = escapeshellarg($smbFolder . $imageName);

                    $command = "smbclient '{$smbHost}' -U '{$smbUser}%{$smbPass}' -c 'put {$escapedLocalPath} {$escapedRemotePath}'";
                    exec($command, $output, $return_var);

                    if ($return_var === 0) {
                        // Insert ke DB
                        $sqlInsert = "INSERT INTO tr_jo_attachment (id_jo, attachment) VALUES ('$id_jo', '$imageName')";
                        $insert = mysqli_query($koneksi, $sqlInsert);

                        if ($insert) {
                            $successCount++;
                            unlink($localPath);
                        } else {
                            echo "Gagal insert DB untuk $field: " . mysqli_error($koneksi) . "<br>";
                        }
                    } else {
                        echo "❌ Gagal kirim file SMB untuk $field: $imageName<br>";
                        echo "<pre>" . implode("\n", $output) . "</pre>";
                    }

                } else {
                    echo "❌ Gagal upload lokal untuk $field: $filename<br>";
                }
            }
        }

        if ($successCount > 0) {
            echo "✅ Berhasil upload dan kirim ke SMB: $successCount file.";
        } else {
            echo "❌ Tidak ada file yang berhasil diproses.";
        }

    // END SINGLE 

?>