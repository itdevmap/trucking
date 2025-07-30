<?php
    include("koneksi.php");

    $smbHost   = "//192.168.1.237/Doc_System";
    $smbUser   = "helpdesk";
    $smbPass   = "P@ssw0rd";
    $smbFolder = "/DOCO/petty_cash/";

    $id_jo = mysqli_real_escape_string($koneksi, $_POST['id_jo']);
    $query = "SELECT no_cont FROM tr_jo WHERE id_jo = '$id_jo' LIMIT 1";
    $result = mysqli_query($koneksi, $query);

    if (!$row = mysqli_fetch_assoc($result)) {
        echo "❌ Kontainer tidak ditemukan untuk ID JO: $id_jo";
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
            $timestamp = time();

            $imageName = $prefix . $timestamp . '_' . $kontainer . '.' . $extension;
            $localPath = $uploadDir . $imageName;

            if (move_uploaded_file($tmpPath, $localPath)) {
                $escapedLocalPath  = escapeshellarg($localPath);
                $escapedRemotePath = escapeshellarg($smbFolder . $imageName);

                $command = "smbclient '{$smbHost}' -U '{$smbUser}%{$smbPass}' -c 'put {$escapedLocalPath} {$escapedRemotePath}'";
                exec($command, $output, $return_var);

                if ($return_var === 0) {
                    // Cek apakah ada file sebelumnya dengan prefix yang sama
                    $check = mysqli_query($koneksi, "SELECT id, attachment FROM tr_jo_attachment 
                        WHERE id_jo = '$id_jo' AND attachment LIKE '{$prefix}%' LIMIT 1");

                    if ($exist = mysqli_fetch_assoc($check)) {
                        // Hapus file lama di SMB (opsional)
                        $oldFile = escapeshellarg($smbFolder . $exist['attachment']);
                        $deleteCmd = "smbclient '{$smbHost}' -U '{$smbUser}%{$smbPass}' -c 'del {$oldFile}'";
                        exec($deleteCmd); // optional: tidak perlu dicek jika tak masalah

                        // Update file
                        $sql = "UPDATE tr_jo_attachment SET attachment = '$imageName' WHERE id = '{$exist['id']}'";
                    } else {
                        // Insert baru
                        $sql = "INSERT INTO tr_jo_attachment (id_jo, attachment) VALUES ('$id_jo', '$imageName')";
                    }

                    $run = mysqli_query($koneksi, $sql);
                    if ($run) {
                        $successCount++;
                        unlink($localPath);
                    } else {
                        echo "❌ Gagal simpan DB untuk $field: " . mysqli_error($koneksi) . "<br>";
                    }

                } else {
                    echo "❌ Gagal kirim ke SMB untuk $field: $imageName<br>";
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
        echo "⚠️ Tidak ada file yang berhasil diproses.";
    }
?>
