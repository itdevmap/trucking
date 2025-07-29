<?php

$filename = $_GET['file'] ?? '';

if (!$filename) {
    die('No file specified.');
}

$smbHost   = "//192.168.1.237/Doc_System";
$smbUser   = "helpdesk";
$smbPass   = "P@ssw0rd";
$smbFolder = "/DOCO/petty_cash/";

$remoteFile = $smbFolder . $filename;

// echo $remoteFile;
// die();

$tmpLocal = sys_get_temp_dir() . '/' . basename($filename);

$cmd = "smbclient {$smbHost} {$smbPass} -U {$smbUser}%{$smbPass} -c \"get '{$remoteFile}' '{$tmpLocal}'\"";
exec($cmd, $output, $status);

if ($status !== 0 || !file_exists($tmpLocal)) {
    echo "Gagal ambil file dari SMB.";
    exit;
}


header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: inline; filename="' . basename($filename) . '"');
header('Content-Length: ' . filesize($tmpLocal));
readfile($tmpLocal);

// Hapus file temp
unlink($tmpLocal);
exit;
