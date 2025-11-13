<?php
    include("koneksi.php");

    // echo "<pre>";
    // print_r($_POST);
    // echo "</pre>";
    // die();

    $smbHost   = "//192.168.1.237/Doc_System";
    $smbUser   = "helpdesk";
    $smbPass   = "P@ssw0rd";
    $smbFolder = "/DOCO/petty_cash/";

    $id_jo = mysqli_real_escape_string($koneksi, $_POST['id_jo'] ?? '');

    if ($id_jo === '') {
        echo "❌ id_jo tidak dikirim.";
        exit;
    }

    $query = "SELECT 
                tr_sj.container 
            FROM tr_jo 
            LEFT JOIN tr_sj ON tr_sj.no_jo = tr_jo.no_jo
            WHERE tr_jo.id_jo = '$id_jo' LIMIT 1";
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

    $uploadDir = __DIR__ . '/uploads/';

    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            die("❌ Gagal membuat folder upload: $uploadDir");
        }
    }
    if (!is_writable($uploadDir)) {
        die("❌ Folder tidak bisa ditulis: $uploadDir (cek permission)");
    }

    function upload_error_message($code) {
        $errors = [
            UPLOAD_ERR_OK => 'There is no error, the file uploaded with success.',
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.'
        ];
        return $errors[$code] ?? 'Unknown upload error code: ' . $code;
    }

    $successCount = 0;
    $messages = [];

    foreach ($fileFields as $field => $prefix) {
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
            $messages[] = "❌ Gagal upload untuk $field ($originalName): " . upload_error_message($errorCode) . " (code:$errorCode)";
            continue;
        }

        if ($tmpPath === '' || !file_exists($tmpPath)) {
            $messages[] = "❌ File sementara tidak ditemukan untuk $field ($originalName). tmp: '$tmpPath'. Pastikan form memakai enctype='multipart/form-data' dan PHP punya tmp folder yang valid.";
            continue;
        }

        if (!is_uploaded_file($tmpPath)) {
            $messages[] = "❌ File di tmp bukan hasil upload valid (is_uploaded_file=false) untuk $field ($originalName). tmp: '$tmpPath'";
            continue;
        }

        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $timestamp = time();
        $imageName = $prefix . $timestamp . '_' . $kontainer . ($extension ? '.' . $extension : '');
        $localPath = $uploadDir . $imageName;

        $ini_upload_max = ini_get('upload_max_filesize');
        $ini_post_max = ini_get('post_max_size');
        $messages[] = "ℹ️ Memproses $field ($originalName) — ukuran: {$size} bytes. PHP upload_max_filesize={$ini_upload_max}, post_max_size={$ini_post_max}.";

        $moved = @move_uploaded_file($tmpPath, $localPath);
        if (!$moved) {
            $errDetails = [];
            $errDetails[] = "move_uploaded_file gagal untuk $field ($originalName). target: $localPath";
            $errDetails[] = "is_writable(uploadDir): " . (is_writable($uploadDir) ? 'yes' : 'no');
            $errDetails[] = "free_disk_space: " . disk_free_space($uploadDir) . " bytes";
            $errDetails[] = "tmp_exists: " . (file_exists($tmpPath) ? 'yes' : 'no');
            $errDetails[] = "tmp_perms: " . sprintf('%o', fileperms($tmpPath) & 0777);
            $errDetails[] = "target_dir_perms: " . sprintf('%o', fileperms($uploadDir) & 0777);
            $messages[] = "❌ " . implode(' | ', $errDetails);
            continue;
        }

        $escapedLocalPath  = escapeshellarg($localPath);
        $escapedRemotePath = escapeshellarg($smbFolder . $imageName);
        $command = "smbclient '{$smbHost}' -U '{$smbUser}%{$smbPass}' -c 'put {$escapedLocalPath} {$escapedRemotePath}' 2>&1";
        exec($command, $output, $return_var);

        if ($return_var !== 0) {
            $messages[] = "❌ Gagal kirim ke SMB untuk $field ($imageName). smbclient exit code: $return_var. Output:\n" . implode("\n", $output);
            continue;
        }

        $imageNameEsc = mysqli_real_escape_string($koneksi, $imageName);
        if ($field === 'file_sj') {

            $q_jo = "SELECT no_jo FROM tr_jo WHERE id_jo = '$id_jo' LIMIT 1";
            $r_jo = mysqli_query($koneksi, $q_jo);

            if ($row_jo = mysqli_fetch_assoc($r_jo)) {
                $no_jo = mysqli_real_escape_string($koneksi, $row_jo['no_jo']);

                $sql = "UPDATE tr_sj 
                        SET attach_sj = '$imageNameEsc'
                        WHERE no_jo = '$no_jo'";
            } else {
                $messages[] = "❌ Tidak ditemukan no_jo untuk id_jo = $id_jo.";
                continue;
            }

        }

        $safePrefixLike = mysqli_real_escape_string($koneksi, $prefix . '%');
        $check = mysqli_query($koneksi, "SELECT id, attachment FROM tr_jo_attachment WHERE id_jo = '$id_jo' AND attachment LIKE '$safePrefixLike' LIMIT 1");

        if ($exist = mysqli_fetch_assoc($check)) {
            $oldFileEsc = escapeshellarg($smbFolder . $exist['attachment']);
            $deleteCmd = "smbclient '{$smbHost}' -U '{$smbUser}%{$smbPass}' -c 'del {$oldFileEsc}' 2>&1";
            exec($deleteCmd, $delOutput, $delRet);
            $sql = "UPDATE tr_jo_attachment SET attachment = '$imageNameEsc' WHERE id = '{$exist['id']}'";
        } else {
            $sql = "INSERT INTO tr_jo_attachment (id_jo, attachment) VALUES ('$id_jo', '$imageNameEsc')";
        }

        if (mysqli_query($koneksi, $sql)) {
            $successCount++;
            @unlink($localPath);
            $messages[] = "✅ Berhasil: $field -> $imageName (dikirim ke SMB dan disimpan di DB).";
        } else {
            $messages[] = "❌ Gagal simpan DB untuk $field ($imageName): " . mysqli_error($koneksi);
        }
    }

    foreach ($messages as $m) {
        echo nl2br(htmlspecialchars($m)) . "<br>";
    }

    if ($successCount > 0) {
        echo "✅ Total berhasil: $successCount file.";
    } else {
        echo "⚠️ Tidak ada file yang berhasil diproses.";
    }
?>
