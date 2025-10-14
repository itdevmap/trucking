<?php
ob_start(); 
session_start();
require('pdf/code128.php');
require('pdf/viewpref.php');
require('pdf/FPDF_Protection.php');
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

class PDF extends FPDF_Protection {
    
    function SetDash($black=null, $white=null)
    {
        if($black!==null)
            $s=sprintf('[%.3F %.3F] 0 d',$black*$this->k,$white*$this->k);
        else
            $s='[] 0 d';
        $this->_out($s);
    }	

    function GetMultiCellHeight($w, $h, $txt){
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;

        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl * $h;
    }

    function Header() { }

    function Footer()
    {  
        $this->SetTextColor(0, 0, 0);
        $this->SetY(-15);   
        $this->SetFont('arial','',6);
        $this->SetX(6); 
    }   
}

// ================= Query data header =================
$idx    = $_GET['no_kb'];	
$no_kb = base64_decode($idx);

// echo "IDX RAW = " . $idx . "<br>";
// echo "NO KB DECODE = " . $no_kb;
// exit;

$q_header = mysqli_query($koneksi, 
    "SELECT
        tr_kontrabon.no_kb,
        tr_kontrabon.tgl_kb,
        m_cust_tr.caption,
        m_cust_tr.nama_cust,
        m_cust_tr.alamat,
        m_cust_tr.tgl_tempo
    FROM tr_kontrabon
    LEFT JOIN m_cust_tr ON m_cust_tr.id_cust = tr_kontrabon.id_cust
    WHERE tr_kontrabon.no_kb = '$no_kb'"
);
$d_header = mysqli_fetch_assoc($q_header);
// ================= DATA HEADER =================
$tgl_kb     = $d_header['tgl_kb'];
$caption    = $d_header['caption'];
$nama_cust  = $d_header['nama_cust'];
$alamat     = $d_header['alamat'];
$tgl_tempo  = $d_header['tgl_tempo'];

$pdf = new PDF('P','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(false);

// ================= HEADER CETAK =================
$pdf->Image("img/logo_print.jpg",5,5,25);  
$pdf->SetFont('arial','B',12);
$pdf->setXY(30,10);
$pdf->Cell(0,8,"PT.PLANET EXPRESS TRANSJAYA",0,1,'L');

$pdf->setXY(90,10);
$pdf->Cell(0,8,"REKAPITULASI TAGIHAN CUSTOMER",0,1,'R');
$pdf->setXY(135,15);
$pdf->Cell(50,8,"(KONTRA BON)",0,1,'C');
$pdf->SetFont('arial','',10);
$pdf->setXY(135,23);
$pdf->Cell(50,8,"Tanggal : $tgl_kb",0,1,'C');


$pdf->setXY(5,35);
$pdf->Cell(0,8,"Customer : $caption",0,1,'L');
$pdf->setXY(5,43);
$pdf->SetFont('arial','B',10);
$pdf->MultiCell(70, 5, $nama_cust, 0, 'L');

$pdf->SetFont('arial','',10);
$pdf->setXY(120,35);
$pdf->Cell(0,8,"Alamat :",0,1,'L');
$pdf->setXY(120,43);
$pdf->MultiCell(70, 5, $alamat, 0, 'L');

// ================= TABLE DETAIL =================
$pdf->SetFont('arial','B',10);
$pdf->setXY(5,60);
$pdf->Cell(10, 6, "No", 1, 0, 'C'); 
$pdf->Cell(30, 6, "No Invoice", 1, 0, 'C'); 
$pdf->Cell(40, 6, "Tgl Invoice", 1, 0, 'C'); 
$pdf->Cell(40, 6, "Jatuh tempo", 1, 0, 'C'); 
$pdf->Cell(75, 6, "Container", 1, 1, 'C'); 


// ================= QUERY DETAIL =================
$no_kb = mysqli_real_escape_string($koneksi, $no_kb);

$q_detail = mysqli_query($koneksi,
    "SELECT 
        tr_jo.no_ar,
        tr_jo.tgl_ar,
        tr_jo.print_kb,
        GROUP_CONCAT(tr_jo_detail.container ORDER BY tr_jo_detail.container SEPARATOR ', ') AS containers
    FROM tr_jo
    LEFT JOIN tr_jo_detail ON tr_jo_detail.id_so = tr_jo.id_jo
    WHERE tr_jo.no_kb = '$no_kb'
    GROUP BY tr_jo.no_ar, tr_jo.tgl_ar"
);

if (!$q_detail) {
    die("Query error: " . mysqli_error($koneksi));
}

$tgl_tempo_days = isset($tgl_tempo) ? (int)$tgl_tempo : (int)($d_header['tgl_tempo'] ?? 0);

$results = [];
while ($row = mysqli_fetch_assoc($q_detail)) {
    $no_ar      = $row['no_ar'];
    $tgl_ar     = $tgl_kb;
    $containers = $row['containers'];

    $jth_tempo = null;
    if (!empty($tgl_ar) && $tgl_ar !== '0000-00-00') {
        try {
            $dt = new DateTime($tgl_ar);
            if ($tgl_tempo_days !== 0) {
                $dt->modify("+" . $tgl_tempo_days . " days");
            }
            $jth_tempo = $dt->format('Y-m-d');
        } catch (Exception $e) {
            $jth_tempo = null;
        }

    }

    $row['jth_tempo'] = $jth_tempo;
    $row['jth_tempo_formatted'] = $jth_tempo ? date('d-m-Y', strtotime($jth_tempo)) : '';
    $results[]      = $row;
    $print_count    = $row['print_kb'];
}

$y = 66;
$no = 1;
foreach ($results as $r) {
    $no_ar   = $r['no_ar'];
    $tgl_ar  = $r['tgl_ar'] ? date('d-m-Y', strtotime($r['tgl_ar'])) : '';
    $jth_tempo = $r['jth_tempo_formatted'];
    $containers = $r['containers'];

    $containerHeight = $pdf->GetMultiCellHeight(60, 6, $containers);
    $rowHeight = max(6, $containerHeight);

    $pdf->SetFont('arial','',10);
    $pdf->SetXY(5, $y);
    $pdf->Cell(10, $rowHeight, $no, 1, 0, 'C');
    $pdf->Cell(30, $rowHeight, $no_ar, 1, 0, 'C');
    $pdf->Cell(40, $rowHeight, $tgl_ar, 1, 0, 'C');
    $pdf->Cell(40, $rowHeight, $jth_tempo, 1, 0, 'C');
    $x = $pdf->GetX();
    $curY = $y;
    $pdf->MultiCell(75, 6, $containers, 1, 'L');

    $y += $rowHeight;
    $no++;
    
}

$pdf->SetXY(5 + 30, 5+$y);
$pdf->Cell(30, 6, "TTD SPV Sales", 0, 0, 'C'); 
$pdf->SetXY(50 + 30, 5+$y);
$pdf->Cell(30, 6, "TTD Sales", 0, 0, 'C'); 
$pdf->SetXY(95 + 30, 5+$y);
$pdf->Cell(30, 6, "TTD Customer", 0, 0, 'C'); 

$pdf->SetXY(5 + 30, 35+$y);
$pdf->Cell(30, 6, "(                                )", 0, 0, 'C'); 
$pdf->SetXY(50 + 30, 35+$y);
$pdf->Cell(30, 6, "(                                )", 0, 0, 'C'); 
$pdf->SetXY(95 + 30, 35+$y);
$pdf->Cell(30, 6, "(                                )", 0, 0, 'C'); 

$pdf->SetFont('arial','B',10);
$pdf->SetXY(130, 43+$y);
$pdf->Cell(30, 6, "Nama, Tgl Terima & Stapel", 0, 0, 'C'); 

$pdf->SetFont('arial','B',8);
$pdf->SetXY(5, 50+$y);
$pdf->Cell(0, 6, "Keterangan : ", 0, 0, 'L'); 
$pdf->SetXY(5, 55+$y);
$pdf->Cell(0, 6, "*) Diisi Oleh Customer : ", 0, 0, 'L'); 
$pdf->SetXY(5, 60+$y);
$pdf->Cell(0, 6, "1) Lembar Putih : Sales (diserahkan ke pelanggan setelah semua Invoice yang tercantum Lunas)", 0, 0, 'L'); 
$pdf->SetXY(5, 65+$y);
$pdf->Cell(0, 6, "2) Lembar Merah : Customer (sebagai tanda terima penyerahan Invoice Asli)", 0, 0, 'L'); 
$pdf->SetXY(5, 70+$y);
$pdf->Cell(0, 6, "3) Lembar Kuning : Finance Awal (sebagai bukti penyerahan berkas ke Sales)", 0, 0, 'L'); 
$pdf->SetXY(5, 75+$y);
$pdf->Cell(0, 6, "4) Lembar Hijau : Finance Akhir (diserahkan ke Finance setelah semua Invoice yang tercantum Lunas)", 0, 0, 'L'); 
$pdf->SetXY(5, 80+$y);
$pdf->Cell(0, 6, "# Sebelum semua Invoice lunas, lembar putih dan hijau harus diserahkan ke Finance)", 0, 0, 'L'); 

// Print Number row
$pdf->setXY(150, 55+$y);
$pdf->Cell(20, 5, "Print Number", 0, 0, 'L');
$pdf->Cell(5, 5, ":", 0, 0, 'C');
$pdf->SetTextColor(0, 128, 0);
$pdf->Cell(20, 5, $print_count, 0, 1, 'L');
$pdf->SetTextColor(0, 0, 0);

// Print On row
$day_date = date('d-m-Y H:i:s');
$pdf->setXY(150, 60+$y);
$pdf->Cell(20, 5, "Print On", 0, 0, 'L');
$pdf->Cell(5, 5, ":", 0, 0, 'C');
$pdf->SetTextColor(148, 109, 0);
$pdf->Cell(40, 5, $day_date, 0, 1, 'L');
$pdf->SetTextColor(0, 0, 0);




ob_end_clean();
$pdf->SetProtection(['print', 'copy', 'modify']);
$pdf->Output('cetak_kb.pdf', 'I'); 
