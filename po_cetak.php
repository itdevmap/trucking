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

$idx    = $_GET['id'] ?? '';
$po     = base64_decode($idx);
$jenis  = $_GET['jenis'] ?? '';


// --------------- DATA HEADER ---------------
$q_header = "SELECT 
            tr_po.no_sap,
            tr_po.code_po,
            tr_po.code_pr,
            tr_po.buyer,
            tr_po.payment,
            tr_po.delivery_date,
            tr_po.remark,
            DATE(tr_po.created_at) AS date_at,
            TIME(tr_po.created_at) AS time_at,
            m_vendor_tr.nama_vendor,
            sap_project.kode_project
        FROM tr_po
        LEFT JOIN m_vendor_tr ON m_vendor_tr.id_vendor = tr_po.user_req
        LEFT JOIN sap_project ON sap_project.rowid = tr_po.sap_project
        WHERE tr_po.code_po = '$po'";

$result     = mysqli_query($koneksi, $q_header) or die('Query Error: '.mysqli_error($koneksi));
$rq         = mysqli_fetch_array($result);

$no_sap         = $rq['no_sap'];
$code_po        = $rq['code_po'];
$code_pr        = $rq['code_pr'];
$buyer          = $rq['buyer'];
$payment        = $rq['payment'];
$delivery_date  = $rq['delivery_date'];
$date_at        = $rq['date_at'];
$time_at        = $rq['time_at'];
$nama_vendor    = strtoupper($rq['nama_vendor']);
$kode_project   = $rq['kode_project'];
$remark         = $rq['remark'];


// --------------- DATA DETAIL ---------------
switch ($jenis) {
    case 'route':
        $q_detail = "";
        break;
    case 'item':
        $q_detail = "SELECT
                    tr_po_detail.qty,
                    tr_po_detail.uom,
                    tr_po_detail.cur,
                    tr_po_detail.harga,
                    tr_po_detail.total,
                    tr_po_detail.nominal_ppn,
                    sap_item_tr.sapitemcode,
                    CONCAT(sap_item_tr.sapitemname, ' - ', sap_item_tr.sapitemname) AS sapitemname
                FROM tr_po_detail
                LEFT JOIN sap_item_tr ON sap_item_tr.rowid = tr_po_detail.item
                WHERE tr_po_detail.code_po = '$po'";
        break;
    default:
       $q_detail = "";
        break;
}
$r_detail = mysqli_query($koneksi, $q_detail) or die('Query Error: '.mysqli_error($koneksi));

$details = [];
while ($rd = mysqli_fetch_array($r_detail)) {
    $details[] = $rd;
}

$pdf = new PDF('P','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(false);


function renderPR($jenis, $pdf, $startY, $no_sap, $code_po, $code_pr, $buyer, $payment, $delivery_date,  $nama_vendor, $kode_project, $date_at, $time_at, $remark, $details) {

    $pdf->Image("img/logo_print.jpg",5,5,25);  
    $pdf->SetFont('arial','B',12);
    
    $pdf->setXY(130,10);
    $pdf->SetFont('arial','B',10);
    $pdf->Cell(75, 7, "Purchase Order : $no_sap", 1, 1, 'L'); 
    $pdf->setXY(149,17);
    $pdf->SetFont('arial','',10);
    $pdf->Cell(0, 7, "Date : $delivery_date", 0, 1, 'L'); 

    $pdf->setXY(5,25);
    $pdf->Cell(0, 7, "Pay to", 0, 1, 'L'); 
    $pdf->setXY(20,25);
    $pdf->Cell(10, 7, ":", 0, 1, 'C'); 
    $pdf->SetFont('arial','B',13);
    $pdf->setXY(27,25);
    $pdf->Cell(0, 7, "$nama_vendor", 0, 1, 'L'); 
    $pdf->SetFont('arial','',10);

    $pdf->setXY(5,33);
    $pdf->Cell(0, 7, "Ship to", 0, 1, 'L'); 
    $pdf->setXY(20,33);
    $pdf->Cell(10, 7, ":", 0, 1, 'C'); 
    $pdf->setXY(27,33);
    $pdf->Cell(0, 7, "$remark", 0, 1, 'L'); 

    $pdf->Rect(115, 25, 90, 20);
    $pdf->SetXY(115, 25);
    $pdf->SetFont('arial','B',10);
    $pdf->Cell(90, 7, "Project : $kode_project", 0, 0, 'L');
    
    $pdf->Rect(115, 47, 90, 25);
    $pdf->SetFont('arial','',9);
    $pdf->SetXY(115, 47);
    $pdf->Cell(75, 6, "Project  $code_po", 0, 0, 'L');
    $pdf->SetXY(115, 52);
    $pdf->Cell(75, 6, "No Container ", 0, 0, 'L');
    $pdf->SetXY(115, 57);
    $pdf->Cell(75, 6, "Selesai Pkl $time_at", 0, 0, 'L');
    $pdf->SetXY(115, 62);
    $pdf->Cell(75, 6, "Based on Purchase Request $code_pr", 0, 0, 'L');

    $pdf->SetFont('arial','B',9);
    $pdf->setXY(5,60);
    $pdf->Cell(0, 7, "No. Rekening/Invoice: ", 0, 1, 'L'); 
    $pdf->setXY(5,65);
    $pdf->Cell(0, 7, "$payment - $buyer", 0, 1, 'L'); 

    // ---------- TABLE HEADER ----------
    $pdf->SetFont('arial','B',10);
    $pdf->setXY(5,75);
    $pdf->Cell(10, 6, "No", 1, 0, 'C'); 
    $pdf->Cell(35, 6, "Kode Brg/Jasa", 1, 0, 'C'); 
    $pdf->Cell(55, 6, "Nama Barang/Jasa", 1, 0, 'C'); 
    $pdf->Cell(15, 6, "Qty", 1, 0, 'C');
    $pdf->Cell(15, 6, "UoM", 1, 0, 'C');
    $pdf->Cell(15, 6, "Cur", 1, 0, 'C');
    $pdf->Cell(25, 6, "Harga", 1, 0, 'C');
    $pdf->Cell(30, 6, "Total", 1, 0, 'C');

    // ---------- KOTAK DETAIL ----------
    $pdf->SetFont('arial','',9);
    $y = 81;
    $no = 1;
    $subtotal = 0;
    $n_ppn = 0;
    foreach ($details as $row) {

        $total_detail = $row['qty'] * $row['harga'];

        $startY = $y;
        $pdf->SetXY(50, $y);
        $pdf->MultiCell(55, 6, $row['sapitemname'], 1, 'L'); 
        $afterNameY = $pdf->GetY();
        $cellHeight = $afterNameY - $startY;

        $pdf->SetXY(5, $startY);
        $pdf->Cell(10, $cellHeight, $no, 1, 0, 'C');
        $pdf->Cell(35, $cellHeight, $row['sapitemcode'], 1, 0, 'L');

        $pdf->SetXY(105, $startY);
        $pdf->Cell(15, $cellHeight, $row['qty'], 1, 0, 'C');
        $pdf->Cell(15, $cellHeight, $row['uom'], 1, 0, 'C');
        $pdf->Cell(15, $cellHeight, $row['cur'], 1, 0, 'C');
        $pdf->Cell(25, $cellHeight, number_format($row['harga'],0,",","."), 1, 0, 'R');
        $pdf->Cell(30, $cellHeight, number_format($total_detail,0,",","."), 1, 0, 'R');

        $subtotal += $total_detail;
        $n_ppn += $row['nominal_ppn'];
        $y = $startY + $cellHeight;
        $no++;
    }

    $pdf->SetFont('arial','B',10);
    $pdf->SetXY(5, 3+$y);
    $pdf->Cell(0, 7, "Delivery Date ", 0, 1, 'L');
    $pdf->SetXY(30, 3+$y);
    $pdf->Cell(5, 7, ":", 0, 1, 'L');
    $pdf->SetXY(32, 3+$y);
    $pdf->Cell(5, 7, "$delivery_date", 0, 1, 'L');

    $pdf->SetFont('arial','',10);
    $pdf->SetXY(5, 8+$y);
    $pdf->Cell(0, 8, "Buyer", 0, 1, 'L');
    $pdf->SetXY(30, 8+$y);
    $pdf->Cell(5, 8, ":", 0, 1, 'L');
    $pdf->SetXY(32, 8+$y);
    $pdf->Cell(5, 8, "Puchasing Local", 0, 1, 'L');
    
    $pdf->SetXY(5, 13+$y);
    $pdf->Cell(0, 7, "Payment Term", 0, 1, 'L');
    $pdf->SetXY(30, 13+$y);
    $pdf->Cell(5, 7, ":", 0, 1, 'L');
    $pdf->SetXY(32, 13+$y);
    $pdf->Cell(5, 7, "", 0, 1, 'L');

    $pdf->SetFont('arial','B',10);
    $pdf->Rect(5, 20+$y, 50, 15);
    $pdf->SetXY(5, 20+$y);
    $pdf->Cell(50, 10, "Payment Request Date:", 0, 0, 'C');
    $pdf->SetXY(5, 25+$y);
    $pdf->Cell(50, 10, "", 0, 0, 'C');

    $pdf->SetXY(150, 3+$y);
    $pdf->Cell(0, 7, "Subtotal", 0, 1, 'L');
    $pdf->SetXY(170, 3+$y);
    $pdf->Cell(5, 7, ":", 0, 1, 'L');
    $pdf->SetXY(175, 3+$y);
    $pdf->Cell(30, 7, number_format($subtotal,0,",","."), 1, 1, 'R');

    $pdf->SetXY(150, 13+$y);
    $pdf->Cell(0, 7, "PPN", 0, 1, 'L');
    $pdf->SetXY(170, 13+$y);
    $pdf->Cell(5, 7, ":", 0, 1, 'L');
    $pdf->SetXY(175, 13+$y);
    $pdf->Cell(30, 7, number_format($n_ppn,0,",","."), 1, 1, 'R');

    $pdf->SetXY(150, 23+$y);
    $pdf->Cell(0, 7, "Total", 0, 1, 'L');
    $pdf->SetXY(170, 23+$y);
    $pdf->Cell(5, 7, ":", 0, 1, 'L');
    $pdf->SetXY(175, 23+$y);
    $pdf->Cell(30, 7, number_format($subtotal - $n_ppn,0,",","."), 1, 1, 'R');
    
    $pdf->SetXY(70, 13+$y);
    $pdf->Cell(30, 10, "dibuat", 1, 1, 'C');
    $pdf->SetXY(100, 13+$y);
    $pdf->Cell(30, 10, "Menyetujui", 1, 1, 'C');
    $pdf->Rect(70, 23+$y, 30, 15);
    $pdf->Rect(100, 23+$y, 30, 15);

}


renderPR($jenis, $pdf, 20, $no_sap, $code_po, $code_pr, $buyer, $payment, $delivery_date,  $nama_vendor, $kode_project, $date_at, $time_at, $remark, $details);
ob_end_clean();
$pdf->Output();
