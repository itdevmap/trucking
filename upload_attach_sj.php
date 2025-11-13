
<?php
    include("koneksi.php");

    $smbHost   = "//192.168.1.237/Doc_System";
    $smbUser   = "helpdesk";
    $smbPass   = "P@ssw0rd";
    $smbFolder = "/DOCO/petty_cash/";

    $id_sj = mysqli_real_escape_string($koneksi, $_POST['id_sj'] ?? '');

    if ($id_sj === '') {
        exit("❌ id_sj tidak dikirim.");
    }

    // Ambil data kontainer & id_jo
    $query = "SELECT 
                tr_sj.container, tr_jo.id_jo
        FROM tr_sj
        LEFT JOIN tr_jo ON tr_jo.no_jo = tr_sj.no_jo
        WHERE tr_sj.id_sj = '$id_sj'
        LIMIT 1";

    $result = mysqli_query($koneksi, $query);

    if (!$row = mysqli_fetch_assoc($result)) {
        exit("❌ Kontainer tidak ditemukan untuk ID SJ: $id_sj");
    }

    $kontainer  = preg_replace('/[^A-Za-z0-9_\-]/', '_', $row['container']);
    $id_jo      = $row['id_jo'];

    $fileFields = [
        'file_sj' => 'surat_jalan_'
    ];

    $uploadDir = __DIR__ . '/uploads/';

    // Pastikan folder upload tersedia
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
        exit("❌ Gagal membuat folder upload: $uploadDir");
    }
    if (!is_writable($uploadDir)) {
        exit("❌ Folder tidak bisa ditulis: $uploadDir (cek permission)");
    }

    // Helper pesan error upload
    function upload_error_message($code)
    {
        $errors = [
            UPLOAD_ERR_OK => 'Upload sukses.',
            UPLOAD_ERR_INI_SIZE => 'File melebihi batas upload_max_filesize di php.ini.',
            UPLOAD_ERR_FORM_SIZE => 'File melebihi batas MAX_FILE_SIZE dari form HTML.',
            UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian.',
            UPLOAD_ERR_NO_FILE => 'Tidak ada file yang diupload.',
            UPLOAD_ERR_NO_TMP_DIR => 'Temporary folder hilang.',
            UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk.',
            UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh ekstensi PHP.'
        ];
        return $errors[$code] ?? 'Error upload tidak diketahui (code: ' . $code . ')';
    }

    $successCount = 0;
    $messages = [];

    foreach ($fileFields as $field => $prefix) {

        // Jika field tidak dikirim
        if (!isset($_FILES[$field])) {
            $messages[] = "ℹ️ Field '$field' tidak dikirimkan.";
            continue;
        }

        $file = $_FILES[$field];
        $originalName = $file['name'] ?? '';
        $tmpPath      = $file['tmp_name'] ?? '';
        $errorCode    = $file['error'] ?? UPLOAD_ERR_NO_FILE;
        $size         = $file['size'] ?? 0;

        if ($errorCode !== UPLOAD_ERR_OK) {
            $messages[] = "❌ Gagal upload untuk $field ($originalName): " . upload_error_message($errorCode);
            continue;
        }

        if ($tmpPath === '' || !file_exists($tmpPath)) {
            $messages[] = "❌ File sementara tidak ditemukan untuk $field ($originalName). Pastikan enctype='multipart/form-data' dan tmp folder valid.";
            continue;
        }

        if (!is_uploaded_file($tmpPath)) {
            $messages[] = "❌ File bukan hasil upload valid (is_uploaded_file=false) untuk $field ($originalName)";
            continue;
        }

        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $timestamp = time();
        $imageName = $prefix . $timestamp . '_' . $kontainer . ($extension ? '.' . $extension : '');
        $localPath = $uploadDir . $imageName;

        $messages[] = "ℹ️ Memproses $field ($originalName), ukuran: {$size} bytes.";

        // Pindahkan file ke folder lokal
        if (!@move_uploaded_file($tmpPath, $localPath)) {
            $messages[] = "❌ move_uploaded_file gagal untuk $field ($originalName) → $localPath";
            continue;
        }

        // Upload ke SMB
        $escapedLocalPath  = escapeshellarg($localPath);
        $escapedRemotePath = escapeshellarg($smbFolder . $imageName);
        $command = "smbclient '{$smbHost}' -U '{$smbUser}%{$smbPass}' -c 'put {$escapedLocalPath} {$escapedRemotePath}' 2>&1";
        exec($command, $output, $return_var);

        if ($return_var !== 0) {
            $messages[] = "❌ Gagal kirim ke SMB untuk $field ($imageName). Exit code: $return_var.\nOutput:\n" . implode("\n", $output);
            continue;
        }

        $imageNameEsc = mysqli_real_escape_string($koneksi, $imageName);

        $sqlUpdateSJ = "UPDATE tr_sj SET attach_sj = '$imageNameEsc' WHERE id_sj = '$id_sj'";
        // mysqli_query($koneksi, $sqlUpdateSJ);

        // $safePrefixLike = mysqli_real_escape_string($koneksi, $prefix . '%');
        // $check = mysqli_query($koneksi, "SELECT id, attachment 
        //     FROM tr_jo_attachment 
        //     WHERE id_jo = '$id_jo' 
        //     AND attachment LIKE '$safePrefixLike'
        //     LIMIT 1
        // ");

        // if ($exist = mysqli_fetch_assoc($check)) {
        //     // Hapus file lama di SMB
        //     $oldFileEsc = escapeshellarg($smbFolder . $exist['attachment']);
        //     $deleteCmd = "smbclient '{$smbHost}' -U '{$smbUser}%{$smbPass}' -c 'del {$oldFileEsc}' 2>&1";
        //     exec($deleteCmd, $delOutput, $delRet);

        //     $sql = "UPDATE tr_jo_attachment SET attachment = '$imageNameEsc' WHERE id = '{$exist['id']}'";
        // } else {
        //     $sql = "INSERT INTO tr_jo_attachment (id_jo, attachment) VALUES ('$id_jo', '$imageNameEsc')";
        // }

        if (mysqli_query($koneksi, $sqlUpdateSJ)) {
            $successCount++;
            @unlink($localPath);
            $messages[] = "✅ Berhasil upload dan update DB untuk $field → $imageName";
        } else {
            $messages[] = "❌ Gagal simpan DB untuk $field ($imageName): " . mysqli_error($koneksi);
        }
    }

    // Tampilkan hasil
    foreach ($messages as $m) {
        echo nl2br(htmlspecialchars($m)) . "<br>";
    }

    if ($successCount > 0) {
        echo "✅ Total berhasil: $successCount file.";
    } else {
        echo "⚠️ Tidak ada file yang berhasil diproses.";
    }
?>

