<?php
ob_start();
session_start();

if (!function_exists('set_magic_quotes_runtime')) {
    function set_magic_quotes_runtime($new_setting) {
        return true;
    }
}

require('pdf/code128.php');
include "koneksi.php";
include "session_log.php";
include "lib.php";
include "phpqrcode/qrlib.php";

ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

class PDF extends FPDF {
    function SetDash($black=null, $white=null) {
        if ($black!==null)
            $s=sprintf('[%.3F %.3F] 0 d',$black*$this->k,$white*$this->k);
        else
            $s='[] 0 d';
        $this->_out($s);
    }	
    function Header(){}
    function Footer() {
        $this->SetTextColor(0,0,0);
        $this->SetY(-15);   
        $this->SetFont('arial','',6);
        $this->SetX(6); 
        $this->Cell(0,5,'Printed on: '.date('d/m/Y H:i:s'),0,0,'L');
        $this->Cell(0,5,'Page '.$this->PageNo().'/{nb}',0,0,'R');
    } 
    function NbLines($w, $txt) {
        // Menghitung jumlah baris yang dibutuhkan untuk MultiCell dengan lebar tertentu
        $cw = &$this->CurrentFont['cw'];
        if ($w==0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2*$this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb-1] == "\n")
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

$idx = $_GET['id'];	
$id_sewa = base64_decode($idx);

// ====== HEADER DATA ======
$pq = mysqli_query($koneksi, "SELECT 
  t_ware_sewa.*, 
  m_cust_tr.nama_cust,
  m_cust_tr.tgl_tempo
  FROM t_ware_sewa 
  LEFT JOIN m_cust_tr ON t_ware_sewa.id_cust = m_cust_tr.id_cust
  WHERE t_ware_sewa.id_sewa = '$id_sewa'");

$rq = mysqli_fetch_array($pq);	

$no_ar     = $rq['no_ar'];
$nama_cust = $rq['nama_cust'];
$no_so     = $rq['no_so'];
$tgl_ar    = date("d/m/Y", strtotime($rq['tgl_ar']));
$tempo     = (int)$rq['tgl_tempo'];

if ($tempo == 0) {
    $tgl_jth_tempo = 'Cash Basis';
} else {
    $tgl_jth_tempo = date("d/m/Y", strtotime($rq['tgl_ar']." +".$tempo." days"));
}

// ====== HITUNG FIX CBM ======
$t1 = "SELECT 
            d.*,
            q.aging_sewa,
            s.tanggal as tanggal_sewa
        FROM t_ware_sewa_detail d
        LEFT JOIN t_ware_sewa s ON s.id_sewa = d.id_sewa
        LEFT JOIN t_ware_quo q ON q.id_quo = s.id_quo
        WHERE d.id_sewa = '$id_sewa'";
$h1 = mysqli_query($koneksi, $t1);

$fix_cbm = 0;
while ($d1 = mysqli_fetch_assoc($h1)) {
    $tgl_keluar = strtotime($d1['tanggal_sewa']);
    $tgl_masuk  = strtotime($d1['tanggal']);
    $aging      = round(($tgl_keluar - $tgl_masuk) / (24*60*60));

    $cbm = ($aging > $d1['aging_sewa']) ? $d1['cbm'] : 0;
    $fix_cbm += round($cbm, 2);
}

// ====== DETAIL UTAMA ======
$q_detail = "SELECT 
        t_ware_quo.harga_sewa,
        m_cost_tr.itemcode,
        m_cost_tr.nama_cost,
        t_ware_sewa.ket
    FROM t_ware_sewa
    LEFT JOIN t_ware_quo ON t_ware_quo.id_quo = t_ware_sewa.id_quo
    LEFT JOIN m_cost_tr ON m_cost_tr.id_cost = t_ware_sewa.id_cost
    WHERE t_ware_sewa.id_sewa = '$id_sewa'";
$r_detail = mysqli_query($koneksi, $q_detail);
$d_detail = mysqli_fetch_assoc($r_detail);

$harga_sewa = (float)$d_detail['harga_sewa'];
$total_price = $fix_cbm * $harga_sewa;

// ====== CETAK PDF ======
$pdf = new PDF('P','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 20);

// ===== HEADER PRINT =====
$pdf->Image("img/logo_print.jpg",5,5,25);
$pdf->SetFont('Arial','B',12);
$pdf->setXY(35,10);
$pdf->Cell(0,8,"PT. PLANET EXPRESS TRANSJAYA",0,1,'L');

$pdf->setXY(5,25);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(50, 8, "AR INVOICE : $no_ar", 1, 1, 'C');

$pdf->setXY(100,20);
$pdf->SetFont('Arial','',10);
$pdf->Cell(0, 6, "Kepada Yth.", 0, 1, 'L');
$pdf->SetFont('Arial','B',12);
$pdf->setXY(100,25);
$pdf->Cell(0, 6, "$nama_cust", 0, 1, 'L');

$pdf->setXY(100,35);
$pdf->SetFont('Arial','',10);
$pdf->Cell(0, 6, "Gudang Penerima", 0, 1, 'L');;
$pdf->setXY(100,40);
$pdf->Cell(0, 6, "$nama_cust", 0, 1, 'L');

$pdf->SetFont('Arial','',10);
$pdf->setXY(5,35);
$pdf->Cell(25, 6, "Tgl Invoice", 0, 0, 'L');
$pdf->Cell(4, 6, ":", 0, 0, 'L');
$pdf->Cell(40, 6, "$tgl_ar", 0, 1, 'L');

$pdf->setXY(5,40);
$pdf->Cell(25, 6, "Jatuh Tempo", 0, 0, 'L');
$pdf->Cell(4, 6, ":", 0, 0, 'L');
$pdf->Cell(40, 6, "$tgl_jth_tempo", 0, 1, 'L');

// ===== HEADER TABLE =====
$pdf->Ln(5);
$pdf->SetFont('arial','B',8);
$pdf->setX(5);
$pdf->Cell(7,6,"No",1,0,'C');
$pdf->Cell(35,6,"Kode Barang",1,0,'C');
$pdf->Cell(50,6,"Nama Barang",1,0,'C');
$pdf->Cell(45,6,"Keterangan",1,0,'C');
$pdf->Cell(20,6,"Price/CBM",1,0,'C');
$pdf->Cell(15,6,"CBM",1,0,'C');
$pdf->Cell(25,6,"Total Price",1,1,'C');

// ===== SATU BARIS DETAIL =====
$pdf->SetFont('arial','',8);
$pdf->setX(5);

$cellWidth = [7, 35, 50, 45, 20, 15, 25];
$lineHeight = 4;

// Hitung jumlah baris yang dibutuhkan untuk kolom Keterangan
$nb = $pdf->NbLines($cellWidth[3], $d_detail['ket']);
$rowHeight = $lineHeight * $nb;

// Simpan posisi awal
$x = $pdf->GetX();
$y = $pdf->GetY();

// Cetak kolom satu per satu (dengan tinggi sama)
$pdf->Cell($cellWidth[0], $rowHeight, "1", 1, 0, 'C');
$pdf->Cell($cellWidth[1], $rowHeight, $d_detail['itemcode'], 1, 0, 'C');
$pdf->Cell($cellWidth[2], $rowHeight, $d_detail['nama_cost'], 1, 0, 'L');

// MultiCell khusus untuk keterangan
$pdf->MultiCell($cellWidth[3], $lineHeight, $d_detail['ket'], 1, 'L');

// Posisi setelah MultiCell (kembali ke kanan kolom “ket”)
$pdf->SetXY($x + $cellWidth[0] + $cellWidth[1] + $cellWidth[2] + $cellWidth[3], $y);
$pdf->Cell($cellWidth[4], $rowHeight, number_format($harga_sewa,0,',','.'), 1, 0, 'R');
$pdf->Cell($cellWidth[5], $rowHeight, number_format($fix_cbm,2,',','.'), 1, 0, 'R');
$pdf->Cell($cellWidth[6], $rowHeight, number_format($total_price,0,',','.'), 1, 1, 'R');


// ===== TOTAL DI BAWAH TABEL =====
$pdf->SetFont('arial','B',9);
$pdf->setX(5);
$pdf->Cell(157,8,'Total   ',0,0,'R');
$pdf->Cell(15,8,number_format($fix_cbm,2,',','.'),1,0,'R'); 
$pdf->Cell(25,8,number_format($total_price,0,',','.'),1,1,'R');


$pdf->Ln(-5);
$pdf->SetFont('arial','I',8);
$pdf->setX(5);
$pdf->Cell(0,5,'Keterangan : ',0,1,'L');
$pdf->setX(5);
$pdf->MultiCell(100, 5, $d_detail['ket'], 0, 'L');
$pdf->setX(5);
$pdf->Cell(0,5,'Based On Sales Order ' . $no_so . '',0,1,'L');
$pdf->SetFont('arial','B',9);
$pdf->setX(5);
$pdf->Ln(1);
$pdf->setX(5);
$pdf->MultiCell(67, 5, 'BCA a.n TRI MIRANTI WAHYUNINGASIH ac no 1300490529', 1, 'C');

$pdf->Ln(-15);
$pdf->setX(112);
$pdf->Cell(30,5,'Dibuat Oleh',1,0,'C');
$pdf->Cell(30,5,'Mengetahui',1,0,'C');
$pdf->Cell(30,5,'Penerima',1,1,'C');
$pdf->setX(112);
$pdf->Cell(30,15,'',1,0,'C');
$pdf->Cell(30,15,'',1,0,'C');
$pdf->Cell(30,15,'',1,1,'C');




ob_end_clean();
$pdf->Output();
?>
