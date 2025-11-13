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
    
    function NbLines($w, $txt){
        // Hitung jumlah baris yang dibutuhkan MultiCell
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', (string)$txt);
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
        return $nl;
    }

}

$idx    = $_GET['id'] ?? '';
$po     = base64_decode($idx);
$jenis  = $_GET['jenis'] ?? '';


// ========= DATA HEADER =========
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
            m_vendor_tr.alamat,
            m_vendor_tr.payment_term,
            sap_project.kode_project
        FROM tr_po
        LEFT JOIN m_vendor_tr ON m_vendor_tr.id_vendor = tr_po.user_req
        LEFT JOIN sap_project ON sap_project.rowid = tr_po.sap_project
        WHERE tr_po.code_po = '$po'";

// echo $q_header;
// exit;

$result     = mysqli_query($koneksi, $q_header) or die('Query Error: '.mysqli_error($koneksi));
$rq         = mysqli_fetch_array($result);

$no_sap         = $rq['no_sap'];
$code_po        = $rq['code_po'];
$code_pr        = $rq['code_pr'];
$buyer          = $rq['buyer'];
$payment        = $rq['payment'];
$delivery_date  = $rq['delivery_date'];
$now_date  = date('d-m-Y');
$date_at        = $rq['date_at'];
$time_at        = $rq['time_at'];
$nama_vendor    = strtoupper($rq['nama_vendor']);
$kode_project   = $rq['kode_project'];
$remark         = $rq['remark'];
$alamat         = $rq['alamat'];
$payment_term   = $rq['payment_term'];

// ========= DATA DETAIL =========
switch ($jenis) {
    case 'route':
        $q_detail = "";
        break;
    case 'item':
        $q_detail = "SELECT
                    tr_po_detail.qty,
                    tr_po_detail.cur,
                    tr_po_detail.harga,
                    tr_po_detail.total,
                    tr_po_detail.nominal_ppn,
                    tr_po_detail.container,
                    m_cost_tr.itemcode,
                    m_cost_tr.uom,
                    tr_po_detail.description AS sapitemname
                FROM tr_po_detail
                LEFT JOIN m_cost_tr ON m_cost_tr.id_cost = tr_po_detail.item
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


function renderPR($jenis, $pdf, $startY, $no_sap, $code_po, $code_pr, $buyer, $payment, $delivery_date,  $nama_vendor, $kode_project, $date_at, $time_at, $remark, $details,$alamat,$payment_term,$now_date ) {

    $pdf->Image("img/logo_print.jpg",5,5,25);  
    $pdf->SetFont('arial','B',12);
    
    $pdf->setXY(130,10);
    $pdf->SetFont('arial','B',10);
    $pdf->Cell(75, 7, "Purchase Order : $code_po", 1, 1, 'L'); 
    $pdf->setXY(149,17);
    $pdf->SetFont('arial','',10);
    $pdf->Cell(0, 7, "Date : $date_at", 0, 1, 'L'); 

    $pdf->setXY(5,25);
    $pdf->Cell(0, 7, "Vendor", 0, 1, 'L'); 
    $pdf->setXY(20,25);
    $pdf->Cell(10, 7, ":", 0, 1, 'C'); 

    $pdf->SetFont('arial','B',13);
    $pdf->setXY(27,25);
    $pdf->MultiCell(85, 7, "$nama_vendor", 0, 'L');
    $pdf->SetFont('arial','',10);

    // Dapatkan posisi Y terakhir setelah MultiCell vendor
    $lastY = $pdf->GetY();
    $shipY = $lastY + 5; // selisih 5mm dari vendor

    $pdf->setXY(5,$shipY);
    $pdf->Cell(0, 7, "Ship to", 0, 1, 'L'); 
    $pdf->setXY(20,$shipY);
    $pdf->Cell(10, 7, ":", 0, 1, 'C'); 
    $pdf->setXY(27,$shipY);
    $pdf->MultiCell(85, 5, "Pergudangan Margomulyo Permai Blok L No.02 Surabaya, 60183", 0, 'L');


    $pdf->Rect(115, 25, 90, 20);
    $pdf->SetXY(115, 25);
    $pdf->SetFont('arial','B',10);
    $pdf->Cell(90, 7, "Project : $kode_project ", 0, 0, 'L');
    
    $pdf->Rect(115, 47, 90, 25);
    $pdf->SetFont('arial','',9);
    $pdf->SetXY(115, 47);
    $pdf->MultiCell(80, 5, $remark, 0, 'L');
    $pdf->SetXY(115, 62);
    $pdf->Cell(75, 6, "Based on Purchase Request $code_pr", 0, 0, 'L');

    $pdf->SetFont('arial','B',9);
    $pdf->setXY(5,60);
    $pdf->Cell(0, 7, "No. Rekening/Invoice: ", 0, 1, 'L'); 
    $pdf->setXY(5,65);
    $pdf->Cell(0, 7, "$payment - $buyer", 0, 1, 'L'); 

    // ======== TABLE HEADER ========
        $pdf->SetFont('arial','B',10);
        $pdf->setXY(5,75);
        $pdf->Cell(10, 6, "No", 1, 0, 'C'); 
        $pdf->Cell(65, 6, "Nama Barang/Jasa", 1, 0, 'C'); 
        $pdf->Cell(25, 6, "Container", 1, 0, 'C'); 
        $pdf->Cell(15, 6, "Qty", 1, 0, 'C');
        $pdf->Cell(15, 6, "UoM", 1, 0, 'C');
        $pdf->Cell(15, 6, "Cur", 1, 0, 'C');
        $pdf->Cell(25, 6, "Harga", 1, 0, 'C');
        $pdf->Cell(30, 6, "Total", 1, 0, 'C');

        // ======== KOTAK DETAIL ========
        $pdf->SetFont('arial', '', 9);
        $y = 81;
        $no = 1;
        $subtotal = 0;
        $n_ppn = 0;

        foreach ($details as $row) {
            $total_detail = $row['qty'] * $row['harga'];

            $widths = [
                'no'         => 10,
                'nama'       => 65,
                'container'  => 25,
                'qty'        => 15,
                'uom'        => 15,
                'cur'        => 15,
                'harga'      => 25,
                'total'      => 30,
            ];

            $startX = 5;
            $startY = $y;

            $cols = [
                'no'         => $no,
                'nama'       => $row['sapitemname'],
                'container'  => trim($row['container']),
                'qty'        => $row['qty'],
                'uom'        => $row['uom'],
                'cur'        => $row['cur'],
                'harga'      => number_format($row['harga'], 2, ",", "."),
                'total'      => number_format($total_detail, 2, ",", "."),
            ];

            $maxHeight = 6; 
            foreach ($cols as $key => $text) {
                $nb = $pdf->NbLines($widths[$key] - 0, $text);
                $h = $nb * 9; 
                if ($h > $maxHeight) $maxHeight = $h;
            }

            if ($y + $maxHeight > 260) {
                $pdf->AddPage();
                $y = 20;

                $pdf->SetFont('arial','B',10);
                $pdf->setXY(5,$y);
                $pdf->Cell(10, 6, "No", 1, 0, 'C'); 
                $pdf->Cell(65, 6, "Nama Barang/Jasa", 1, 0, 'C'); 
                $pdf->Cell(25, 6, "Container", 1, 0, 'C'); 
                $pdf->Cell(15, 6, "Qty", 1, 0, 'C');
                $pdf->Cell(15, 6, "UoM", 1, 0, 'C');
                $pdf->Cell(15, 6, "Cur", 1, 0, 'C');
                $pdf->Cell(25, 6, "Harga", 1, 0, 'C');
                $pdf->Cell(30, 6, "Total", 1, 0, 'C');
                $pdf->SetFont('arial','',9);
                $y += 6;
                $startY = $y;
            }

            // --- Gambar border tiap kolom ---
            $xRect = $startX;
            foreach ($widths as $w) {
                $pdf->Rect($xRect, $startY, $w, $maxHeight);
                $xRect += $w;
            }

            // --- Tulis isi kolom ---
            $x = $startX;
            foreach ($cols as $key => $text) {
                $w = $widths[$key];
                $align = in_array($key, ['harga','total']) ? 'R' :
                        (in_array($key, ['no','qty','uom','cur']) ? 'C' : 'L');

                // Pisahkan karakter titik/dash agar bisa dibungkus (opsional)
                if ($key == 'nama' || $key == 'container') {
                    $text = preg_replace('/([.-])/', "$1" . chr(8203), $text);
                }

                // Simpan posisi awal cell
                $xBefore = $pdf->GetX();
                $yBefore = $pdf->GetY();

                // MultiCell untuk semua agar rata tengah vertikal
                $pdf->SetXY($x + 2, $startY + 2);
                $pdf->MultiCell($w - 4, 5, $text, 0, $align);

                // Kembalikan posisi ke kanan cell
                $pdf->SetXY($xBefore + $w, $yBefore);

                // Geser X ke kanan
                $x += $w;
            }

            // Pindah Y ke bawah sesuai tinggi baris terbesar
            $y = $startY + $maxHeight;
            $subtotal += $total_detail;
            $n_ppn += $row['nominal_ppn'];
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

    if ($payment_term === "0") {
        $pdf->Cell(5, 7, "Cash Basic", 0, 1, 'L');
    }else {
        $pdf->Cell(5, 7, "$payment_term Days", 0, 1, 'L');
    }

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
    
    $pdf->SetXY(65, 5+$y);
    $pdf->Cell(30, 10, "Dibuat", 1, 1, 'C');
    $pdf->SetXY(95, 5+$y);
    $pdf->Cell(50, 10, "Menyetujui", 1, 1, 'C');
    $pdf->Rect(65, 15+$y, 30, 15);
    $pdf->Rect(95, 15+$y, 50, 15);
    // $pdf->Rect(95, 23+$y, 25, 15);
    // $pdf->Rect(120, 23+$y, 25, 15);

}


renderPR($jenis, $pdf, 20, $no_sap, $code_po, $code_pr, $buyer, $payment, $delivery_date,  $nama_vendor, $kode_project, $date_at, $time_at, $remark, $details, $alamat,$payment_term,$now_date );
ob_end_clean();
$pdf->Output();
