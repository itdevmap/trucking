<?php
ob_start();
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

$idx = $_GET['id'] ?? '';
$id_sj = base64_decode($idx);

$query = "SELECT 
            tr_sj.code_sj,
            tr_jo.no_jo,
            tr_jo.tgl_jo,
            m_mobil_tr.no_polisi,
            m_supir_tr.nama_supir, 
            m_supir_tr.telp,
            m_cust_tr.nama_cust, 
            tr_jo.ket,
            tr_jo.penerima,
            tr_sj.itemname,
            tr_sj.berat,
            tr_sj.vol,
            tr_sj.container,
            tr_sj.seal,
            tr_sj.keterangan
        FROM tr_jo
        LEFT JOIN tr_sj ON tr_sj.no_jo = tr_jo.no_jo
        LEFT JOIN m_cust_tr ON tr_jo.id_cust = m_cust_tr.id_cust
        LEFT JOIN m_mobil_tr ON tr_sj.id_mobil = m_mobil_tr.id_mobil
        LEFT JOIN m_supir_tr ON tr_sj.id_supir = m_supir_tr.id_supir
        WHERE tr_sj.id_sj = '$id_sj'";

$result = mysqli_query($koneksi, $query) or die('Query Error: '.mysqli_error($koneksi));
$rq = mysqli_fetch_array($result);

$no_sj       = $rq['code_sj'];
$no_do       = $rq['no_jo'];
$tgl_sj      = ConverTgl($rq['tgl_jo']);
$no_polisi   = $rq['no_polisi'];
$nama_supir  = $rq['nama_supir'];
$telp_supir  = $rq['telp'];
$nama_cust   = $rq['nama_cust'];
$penerima    = $rq['penerima'];
$barang      = $rq['itemname'];
$no_jo       = $rq['no_jo'];
$no_cont     = $rq['container'];
$no_seal     = $rq['seal'];
$nama_barang = str_replace("\'","'",$rq['itemname']);	
$berat       = number_format($rq['berat'],2);
$vol         = number_format($rq['vol'],2);
$ket         = $rq['ket'];

$pdf = new PDF('P','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(false);

function renderSuratJalan($pdf, $startY, $no_sj, $no_do, $tgl_sj, $no_polisi, $nama_supir, $telp_supir, $nama_cust, $penerima, $barang, $berat, $vol, $no_cont, $no_seal, $ket) {


    $pdf->Image("img/logo_print.jpg", 7, $startY - 10, 25);  
    $pdf->SetFont('arial','B',14);
    $pdf->setXY(140, $startY - 5);
    $pdf->Cell(60, 8, "SURAT JALAN", 0, 1, 'L');

    $pdf->SetFont('arial','',9);
    $field = [
        ["No SJ", $no_sj],
        ["No SO", $no_do],
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

    // --- kolom QTY (multi-line) ---
    $x = $pdf->GetX();
    $y = $pdf->GetY();

    $cellHeight = 20;
    $lineHeight = 6;
    $lines      = 2;
    $textHeight = $lines * $lineHeight;
    $startY     = $y + ($cellHeight - $textHeight) / 2;

    $pdf->SetXY($x, $startY);
    $pdf->MultiCell(25, $lineHeight, "$berat KG\n$vol M3", 0, 'C');
    $pdf->SetXY($x + 25, $y);
    $pdf->Rect($x, $y, 25, $cellHeight);



    // lanjut kolom lain
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


renderSuratJalan($pdf, 20, $no_sj, $no_do, $tgl_sj, $no_polisi, $nama_supir, $telp_supir, $nama_cust, $penerima, $barang, $berat, $vol, $no_cont, $no_seal, $ket);
renderSuratJalan($pdf, 155, $no_sj, $no_do, $tgl_sj, $no_polisi, $nama_supir, $telp_supir, $nama_cust, $penerima, $barang, $berat, $vol, $no_cont, $no_seal, $ket);

ob_end_clean();
$pdf->Output();
