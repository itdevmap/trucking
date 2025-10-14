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

class PDF extends FPDF {
    
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
$idx    = $_GET['no_ar'];	
$no_ar = base64_decode($idx);

// echo "IDX RAW = " . $idx . "<br>";
// echo "NO_AR DECODE = " . $no_ar;
// exit;

$pq = mysqli_query($koneksi, 
    "SELECT 
        tr_jo.id_jo,
        tr_jo.tgl_jo, 
        tr_jo.penerima, 
        tr_jo.print_count, 
        sap_project.kode_project,
        m_cust_tr.nama_cust,
        m_cust_tr.caption,
        m_cust_tr.tgl_tempo
    FROM tr_jo
    LEFT JOIN sap_project ON tr_jo.sap_project = sap_project.rowid
    LEFT JOIN m_cust_tr ON tr_jo.id_cust = m_cust_tr.id_cust
    WHERE tr_jo.no_ar = '$no_ar'"
);

$id_jos = [];
$header = null;
while ($row = mysqli_fetch_assoc($pq)) {
    $id_jos[] = $row['id_jo'];
    if ($header === null) {
        $header = $row;
    }
}

// ================= DATA =================
$tgl_jo_fmt     = date('d/m/Y', strtotime($header['tgl_jo']));
$tempo_hari     = (int) $header['tgl_tempo'];
$tgl_jatuh_tempo_fmt = date('d/m/Y', strtotime($header['tgl_jo'] . " + $tempo_hari days"));
$today          = date('d/m/Y');
$nama_cust      = strtoupper($header['nama_cust']);
$code_cust      = $header['caption'];
$kode_project   = $header['kode_project'];
$penerima       = $header['penerima'];
$print_count    = $header['print_count'];

// ================= Cetak =================
$pdf = new PDF('P','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(false);

// HEADER CETAK
$pdf->Image("img/logo_print.jpg",7,5,25);  
$pdf->SetFont('arial','B',12);
$pdf->setXY(35,10);
$pdf->Cell(0,8,"PT.PLANET EXPRESS TRANSJAYA",0,1,'L');

$pdf->SetFont('arial','',10);
$pdf->setXY(90,10);
$pdf->Cell(0,8,"$today",0,1,'R');

// Label
$pdf->setXY(10,25);
$pdf->SetFont('arial','B',10);
$pdf->Cell(50, 8, "AR INVOICE : $no_ar", 1, 1, 'C'); 

$pdf->setXY(100,20);
$pdf->Cell(0, 10, "Kepada Yth.", 0, 1, 'L'); 

$pdf->setXY(0,15);
$pdf->Cell(0, 10, "$code_cust", 0, 1, 'R'); 

$pdf->setXY(100,25);
$pdf->SetFont('arial','B',12);
$pdf->Cell(0, 10, "$nama_cust", 0, 1, 'L'); 

$pdf->SetFont('arial','',10);
$pdf->setXY(10,35);
$pdf->Cell(0, 10, "Tgl Invoice", 0, 1, 'L'); 
$pdf->setXY(35,35);
$pdf->Cell(0, 10, ":", 0, 1, 'L'); 
$pdf->setXY(40,35);
$pdf->Cell(0, 10, "$tgl_jo_fmt", 0, 1, 'L'); 

$pdf->setXY(10,40);
$pdf->Cell(0, 10, "Jatuh Tempo", 0, 1, 'L'); 
$pdf->setXY(35,40);
$pdf->Cell(0, 10, ":", 0, 1, 'L'); 
$pdf->setXY(40,40);
$pdf->Cell(0, 10, "$tgl_jatuh_tempo_fmt", 0, 1, 'L'); 

$pdf->setXY(100,35);
$pdf->Cell(0, 10, "Seal No", 0, 1, 'L'); 
$pdf->setXY(130,35);
$pdf->Cell(0, 10, ":", 0, 1, 'L'); 

$pdf->setXY(100,40);
$pdf->Cell(0, 10, "Gudang Penerima", 0, 1, 'L'); 
$pdf->setXY(130,40);
$pdf->Cell(0, 10, ":", 0, 1, 'L'); 
$pdf->setXY(135,40);
$pdf->Cell(0, 10, "$penerima", 0, 1, 'L'); 

$pdf->setXY(100,45);
$pdf->Cell(0, 10, "Project", 0, 1, 'L'); 
$pdf->setXY(130,45);
$pdf->Cell(0, 10, ":", 0, 1, 'L'); 
$pdf->setXY(135,45);
$pdf->Cell(0, 10, "$kode_project", 0, 1, 'L'); 

// TABLE HEADER
$pdf->SetFont('arial','B',10);
$pdf->setXY(10,55);
$pdf->Cell(15, 6, "No", 1, 0, 'C'); 
$pdf->Cell(140, 6, "Description", 1, 0, 'C'); 
$pdf->Cell(35, 6, "Price", 1, 1, 'C'); 

// ================= Query detail =================
$id_jo_list = implode(",", $id_jos);
$q_detail = mysqli_query($koneksi, 
        "SELECT 
            tr_sj.container,
            tr_sj.tanggal,
            sap_project.kode_project,
            m_cust_tr.nama_cust,
            tr_jo_detail.jenis_mobil,
            tr_sj.itemname,
            m_supir_tr.nama_supir,
            tr_jo_detail.harga
        FROM tr_sj
        LEFT JOIN tr_jo ON tr_jo.no_jo = tr_sj.no_jo
        LEFT JOIN m_cust_tr ON m_cust_tr.id_cust = tr_jo.id_cust
        LEFT JOIN sap_project ON sap_project.rowid = tr_jo.sap_project
        LEFT JOIN tr_jo_detail ON tr_jo_detail.id_so = tr_jo.id_jo
        LEFT JOIN m_supir_tr ON m_supir_tr.id_supir = tr_sj.id_supir
        WHERE tr_jo.id_jo IN ($id_jo_list)"
);

$biaya = mysqli_query($koneksi, 
        "SELECT 
            CONCAT(m_cost_tr.nama_cost, '-', tr_jo_biaya.remark) AS nama_biaya,
            tr_jo_biaya.harga,
            tr_jo_biaya.pph AS pph,
            (tr_jo_biaya.wtax * tr_jo_biaya.harga / 100) AS wtax,
            tr_jo.no_jo
        FROM tr_jo_biaya
        LEFT JOIN tr_jo ON tr_jo.id_jo = tr_jo_biaya.id_jo
        LEFT JOIN m_cost_tr ON m_cost_tr.id_cost = tr_jo_biaya.id_cost
        WHERE tr_jo_biaya.id_jo IN ($id_jo_list)"
);

$dataGabungan = [];
$pdf->SetFont('arial','',8);
$no       = 1;
$y        = 61;
$subtotal = 0;
$dp       = 0;
$pph      = 0;

while ($row = mysqli_fetch_assoc($q_detail)) {
    $dataGabungan[] = [
        'desc'  => $row['container'] . "#" . $row['nama_cust'] . " " . $tgl_jo_fmt . " " . $kode_project ." 1X" . $row['jenis_mobil'] . " " . $row['itemname'],
        'harga' => $row['harga']
    ];
}

while ($row = mysqli_fetch_assoc($biaya)) {
    $dataGabungan[] = [
        'desc'  => $row['nama_biaya'],
        'harga' => $row['harga'],
        'wtax'  => $row['wtax'] 
    ];
    $pph += $row['wtax'];
}

// ================= CETAK DATA GABUNGAN =================
$pdf->SetFont('arial','',8);
$no       = 1;
$y        = 61;
$subtotal = 0;

foreach ($dataGabungan as $row) {
    $desc       = $row['desc'];
    $harga      = $row['harga'];
    $harga_fmt  = number_format($harga, 0, ',', '.');

    $subtotal += $harga;
    $descHeight = $pdf->GetMultiCellHeight(140, 6, $desc);
    $rowHeight  = max($descHeight, 6);

    $x = 10;
    $pdf->SetXY($x, $y);
    $pdf->Cell(15, $rowHeight, $no, 1, 0, 'C');
    $x += 15;

    $curX = $x;
    $curY = $y;
    $pdf->SetXY($curX, $curY);
    $pdf->MultiCell(140, 6, $desc, 0, 'L');
    $pdf->Rect($curX, $curY, 140, $rowHeight);
    $x += 140;

    $pdf->SetXY($x, $y);
    $pdf->Cell(35, $rowHeight, $harga_fmt, 1, 0, 'R');

    $y  += $rowHeight;
    $no++;
}

// ================= HITUNG TOTAL =================
$subtotal_fmt = number_format($subtotal, 0, ',', '.');
$dp_fmt       = number_format($dp, 0, ',', '.');
$pph_fmt      = number_format($pph, 0, ',', '.');

$total        = $subtotal - $pph;
$total_fmt    = number_format($total, 0, ',', '.');

// ================= AFTER TABLE =================
$cellx = 45;
$setx  = 115;
$setx2 = 162.5;

$pdf->SetFont('arial','B',9);

// SUBTOTAL
$pdf->setXY($setx, 5+$y);
$pdf->Cell($cellx, 5, "SUBTOTAL", 0, 1, 'R');
$pdf->setXY($setx2, 5+$y);
$pdf->Cell(5, 5, ":", 0, 1, 'C');
$pdf->setXY(0, 5+$y);
$pdf->Cell(0, 5, $subtotal_fmt, 0, 1, 'R');

// DOWN PAYMENT
$pdf->setXY($setx, 10+$y);
$pdf->Cell($cellx, 5, "CREDIT MEMO / DOWN PAYMENT", 0, 1, 'R');
$pdf->setXY($setx2, 10+$y);
$pdf->Cell(5, 5, ":", 0, 1, 'C');
$pdf->setXY(0, 10+$y);
$pdf->Cell(0, 5, $dp_fmt, 0, 1, 'R');

// PPH
$pdf->setXY($setx, 15+$y);
$pdf->Cell($cellx, 5, "PPH", 0, 1, 'R');
$pdf->setXY($setx2, 15+$y);
$pdf->Cell(5, 5, ":", 0, 1, 'C');
$pdf->setXY(0, 15+$y);
$pdf->Cell(0, 5, $pph_fmt, 0, 1, 'R');

// TOTAL
$pdf->setXY($setx, 20+$y);
$pdf->Cell($cellx, 5, "TOTAL", 0, 1, 'R');
$pdf->setXY($setx2, 20+$y);
$pdf->Cell(5, 5, ":", 0, 1, 'C');
$pdf->setXY(0, 20+$y);
$pdf->Cell(0, 5, $total_fmt, 0, 1, 'R');

// ================= FOOTER =================
$pdf->setXY(160, 30+$y);
$pdf->Cell($cellx, 5, "Hormat Kami,", 0, 1, 'C');
$pdf->setXY(160, 50+$y);
$pdf->Cell($cellx, 5, "(......................)", 0, 1, 'C');

$pdf->SetFont('arial','',8);
date_default_timezone_set('Asia/Jakarta');
$day_date = date('d.m.Y H:i');

// Print Number row
$pdf->setXY(150, 55+$y);
$pdf->Cell(20, 5, "Print Number", 0, 0, 'L');
$pdf->Cell(5, 5, ":", 0, 0, 'C');
$pdf->SetTextColor(0, 128, 0);
$pdf->Cell(20, 5, $print_count, 0, 1, 'L');
$pdf->SetTextColor(0, 0, 0);

// Print On row
$pdf->setXY(150, 60+$y);
$pdf->Cell(20, 5, "Print On", 0, 0, 'L');
$pdf->Cell(5, 5, ":", 0, 0, 'C');
$pdf->SetTextColor(148, 109, 0);
$pdf->Cell(40, 5, $day_date, 0, 1, 'L');
$pdf->SetTextColor(0, 0, 0);

$footerY += 35+$y;
$boxX = 10;
$boxY = $footerY;
$boxW = 90;
$boxH = 20;
$pdf->Rect($boxX, $boxY, $boxW, $boxH);
$pdf->SetFont('arial','',9);
$pdf->setXY($boxX + 2, $boxY + 3);
$pdf->Cell(0, 5, "Pembayaran Transfer Bank BCA", 0, 1, 'L');
$pdf->setX($boxX + 2);
$pdf->Cell(0, 5, "A/C 1303392525", 0, 1, 'L');
$pdf->setX($boxX + 2);
$pdf->Cell(0, 5, "A/N PT.PLANET EXPRESS TRANSJAYA", 0, 1, 'L');
$footerY += $boxH + 5;


ob_end_clean();
$pdf->Output();
