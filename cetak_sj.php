<?php
ob_start(); // ✅ Pastikan buffer diaktifkan
session_start();

require('pdf/code128.php');
include "koneksi.php"; 
include "session_log.php"; 
include "lib.php";
include "phpqrcode/qrlib.php";

ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED); 

if (!function_exists('set_magic_quotes_runtime')) {
    function set_magic_quotes_runtime($new_setting) {
        return true;
    }
}

class PDF extends FPDF
{
    function SetDash($black=null, $white=null)
    {
        if ($black !== null)
            $s = sprintf('[%.3F %.3F] 0 d', $black*$this->k, $white*$this->k);
        else
            $s = '[] 0 d';
        $this->_out($s);
    }	
    function Header() {}
    function Footer()
    {
        $this->SetTextColor(0, 0, 0);
        $this->SetY(-15);   
        $this->SetFont('arial','',6);
        $this->SetX(6); 
    }   
}

// Ambil data
$idx = $_GET['id'] ?? '';
$id_jo = base64_decode($idx);

$query = "SELECT tr_jo.*, tr_quo.quo_no, m_cust_tr.nama_cust, m_kota_tr.nama_kota as asal, m_kota1.nama_kota as tujuan,
        m_mobil_tr.no_polisi, m_supir_tr.nama_supir, m_supir_tr.telp
        FROM tr_jo
        LEFT JOIN tr_quo_data ON tr_jo.id_detil_quo = tr_quo_data.id_detil
        LEFT JOIN tr_quo ON tr_quo_data.id_quo = tr_quo.id_quo
        LEFT JOIN m_kota_tr ON tr_jo.id_asal = m_kota_tr.id_kota
        LEFT JOIN m_kota_tr AS m_kota1 ON tr_jo.id_tujuan = m_kota1.id_kota
        LEFT JOIN m_cust_tr ON tr_jo.id_cust = m_cust_tr.id_cust
        LEFT JOIN m_mobil_tr ON tr_jo.id_mobil = m_mobil_tr.id_mobil
        LEFT JOIN m_supir_tr ON tr_jo.id_supir = m_supir_tr.id_supir
        WHERE tr_jo.id_jo = '$id_jo'";
$result = mysqli_query($koneksi, $query) or die('Query Error: '.mysqli_error($koneksi));
$rq = mysqli_fetch_array($result);

$no_sj       = $rq['no_jo'];
$no_do       = $rq['no_do'];
$tgl_sj      = ConverTgl($rq['tgl_jo']);
$nama_cust   = $rq['nama_cust'];
$no_polisi   = $rq['no_polisi'];
$nama_supir  = $rq['nama_supir'];
$telp_supir  = $rq['telp'];
$penerima    = $rq['penerima'];
$barang      = $rq['barang'];
$no_jo       = $rq['no_jo'];
$no_cont     = $rq['no_cont'];
$no_seal     = $rq['no_seal'];
$nama_barang = str_replace("\'","'",$rq['nama_barang']);	
$berat       = number_format($rq['berat'],2);
$vol         = number_format($rq['vol'],2);
$jenis_sj    = $rq['jenis_sj'];
$ket         = $rq['ket'];

// PDF Setup
$pdf = new PDF('P','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(false);

// Fungsi buat satu blok surat jalan
function renderSuratJalan($pdf, $startY, $no_sj, $no_do, $tgl_sj, $no_polisi, $nama_supir, $telp_supir, $nama_cust, $penerima, $barang, $berat, $vol, $no_cont, $no_seal, $ket) {
    // Header dan Logo
    $pdf->Image("img/logo_print.jpg", 7, $startY - 10, 25);  
    $pdf->SetFont('arial','B',14);
    $pdf->setXY(140, $startY - 5);
    $pdf->Cell(60, 8, "SURAT JALAN", 0, 1, 'L');

    $pdf->SetFont('arial','',9);
    $field = [
        ["No SJ", $no_sj],
        ["No PO/DO", $no_do],
        ["Tanggal", $tgl_sj],
        ["No. Polisi", $no_polisi],
        ["Supir", $nama_supir],
        ["No. Telp", $telp_supir],
    ];
    $pdf->SetXY(140, $startY + 5);
    foreach ($field as $i => $f) {
        $pdf->setX(140);
        $pdf->Cell(25, 5, $f[0], 0, 0, 'L');
        $pdf->Cell(3, 5, ":", 0, 0, 'L');
        $pdf->Cell(60, 5, $f[1], 0, 1, 'L');
    }

    // Kepada Yth
    $pdf->SetXY(8, $startY + 5);
    $pdf->SetFont('arial','',9);
    $pdf->Cell(100,15,"Kepada Yth:", 0, 1, 'L');
    $pdf->setX(8);
    $pdf->Cell(100,5, strtoupper($nama_cust), 0, 1, 'L');
    $pdf->setX(8);
    $pdf->MultiCell(100,5,$penerima,0,1,'L');

    // Tabel Barang
    $tableY = $startY + 40;
    $pdf->SetFont('arial','',9);
    $pdf->SetXY(8,$tableY);
    $pdf->Cell(15,8,"NO",1,0,'C');
    $pdf->Cell(60,8,"NAMA BARANG",1,0,'C');
    $pdf->Cell(25,8,"QTY",1,0,'C');
    $pdf->Cell(30,8,"NO. CONT",1,0,'C');
    $pdf->Cell(25,8,"NO. SEAL",1,0,'C');
    $pdf->Cell(40,8,"KETERANGAN",1,1,'C');

    // Isi Barang
    $pdf->SetX(8);
    $pdf->Cell(15,20,"1",1,0,'C');
    $pdf->Cell(60,20, $barang,1,0,'L');
    $pdf->Cell(25,20, "$berat KG\n$vol M3",1,0,'C');
    $pdf->Cell(30,20, $no_cont,1,0,'C');
    $pdf->Cell(25,20, $no_seal,1,0,'C');
    $pdf->Cell(40,20, $ket,1,1,'L');

    // Tanda Tangan
    $signY = $tableY + 30;
    $pdf->SetXY(8,$signY);
    $pdf->Cell(65,7,"YANG MENYERAHKAN",1,0,'C');
    $pdf->Cell(65,7,"TRUCKING",1,0,'C');
    $pdf->Cell(65,7,"PENERIMA",1,1,'C');

    $pdf->SetX(8);
    $pdf->Cell(65,25,"",1,0,'C');
    $pdf->Cell(65,25,"",1,0,'C');
    $pdf->Cell(65,25,"",1,0,'C');
}


// Cetak 2x (asli & copy)
renderSuratJalan($pdf, 20, $no_sj, $no_do, $tgl_sj, $no_polisi, $nama_supir, $telp_supir, $nama_cust, $penerima, $barang, $berat, $vol, $no_cont, $no_seal, $ket);
renderSuratJalan($pdf, 155, $no_sj, $no_do, $tgl_sj, $no_polisi, $nama_supir, $telp_supir, $nama_cust, $penerima, $barang, $berat, $vol, $no_cont, $no_seal, $ket);

// ✅ Bersihkan buffer sebelum output PDF
ob_end_clean();
$pdf->Output();
