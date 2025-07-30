<?php

// $filename = $_GET['file'] ?? '';
// if (!$filename) {
//     die('❌ No file specified.');
// }
// if (!preg_match('/^[a-zA-Z0-9_.-]+$/', $filename)) {
//     die('❌ Invalid file name.');
// }
// $smbServer = '192.168.1.237';
// $smbShare  = 'Doc_System';
// $smbUser   = 'helpdesk';
// $smbPass   = 'P@ssw0rd';
// $smbFolder = 'DOCO/petty_cash';
// $tmpLocal = 'uploads/' . basename($filename);
// $cmd = sprintf(
//     'smbclient //%s/%s %s -U %s%%%s -c "cd %s; get %s %s" 2>&1',
//     $smbServer,
//     $smbShare,
//     escapeshellarg($smbPass),
//     $smbUser,
//     $smbPass,
//     $smbFolder,
//     basename($filename),
//     escapeshellarg($tmpLocal)
// );
// exec($cmd, $output, $status);
// if ($status !== 0 || !file_exists($tmpLocal)) {
//     echo "<pre>";
//     echo "❌ Gagal ambil file dari SMB.\n\n";
//     echo "Perintah: $cmd\n";
//     echo "Status: $status\n\n";
//     echo "Output:\n" . implode("\n", $output);
//     echo "</pre>";
//     exit;
// }
// $finfo = finfo_open(FILEINFO_MIME_TYPE);
// $mimeType = finfo_file($finfo, $tmpLocal);
// finfo_close($finfo);
// header('Content-Description: File Transfer');
// header('Content-Type: ' . $mimeType);
// header('Content-Disposition: inline; filename="' . basename($filename) . '"');
// header('Content-Length: ' . filesize($tmpLocal));
// readfile($tmpLocal);
// unlink($tmpLocal);
// exit;


$filename = $_GET['file'] ?? '';

if (!$filename) {
    die('❌ No file specified.');
}

if (!preg_match('/^[a-zA-Z0-9_.-]+$/', $filename)) {
    die('❌ Invalid file name.');
}

$smbServer = '192.168.1.237';
$smbShare  = 'Doc_System';
$smbUser   = 'helpdesk';
$smbPass   = 'P@ssw0rd';
$smbFolder = 'DOCO/petty_cash';

$cmd = sprintf(
    'smbclient //%s/%s %s -U %s%%%s -c "cd %s; get %s -" 2>&1',
    $smbServer,
    $smbShare,
    escapeshellarg($smbPass),
    $smbUser,
    $smbPass,
    $smbFolder,
    basename($filename)
);

// Open process to read binary output
$descriptorspec = [
    1 => ['pipe', 'w'], // stdout
    2 => ['pipe', 'w']  // stderr
];
$process = proc_open($cmd, $descriptorspec, $pipes);

if (!is_resource($process)) {
    die('❌ Failed to start SMB client.');
}

$content = stream_get_contents($pipes[1]);
$errors  = stream_get_contents($pipes[2]);

fclose($pipes[1]);
fclose($pipes[2]);

$status = proc_close($process);

if ($status !== 0 || !$content) {
    echo "<pre>";
    echo "❌ Gagal ambil file dari SMB.\n\n";
    echo "Perintah: $cmd\n";
    echo "Status: $status\n\n";
    echo "Error Output:\n" . htmlspecialchars($errors);
    echo "</pre>";
    exit;
}

// Tentukan MIME type
$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
$mime = 'application/octet-stream';
switch ($ext) {
    case 'pdf':
        $mime = 'application/pdf';
        break;
    case 'jpg':
    case 'jpeg':
        $mime = 'image/jpeg';
        break;
    case 'png':
        $mime = 'image/png';
        break;
}

// Output file langsung ke browser
header('Content-Type: ' . $mime);
header('Content-Disposition: inline; filename="' . basename($filename) . '"');
header('Content-Length: ' . strlen($content));
echo $content;
exit;
