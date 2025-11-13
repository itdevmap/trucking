<?php
ob_start();
session_start();

// PATCH untuk PHP 7/8 agar kompatibel dengan FPDF lama
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

class PDF extends FPDF{
  function SetDash($black=null, $white=null)
  {
    if($black!==null)
      $s=sprintf('[%.3F %.3F] 0 d',$black*$this->k,$white*$this->k);
    else
      $s='[] 0 d';
    $this->_out($s);
  }	
  function Header(){}
  function Footer()
  {  
    $this->SetTextColor(0,0,0);
    $this->SetY(-15);   
    $this->SetFont('arial','',6);
    $this->SetX(6); 
    // bisa tambahkan print date kalau perlu
  }   
}

$idx = $_GET['id'];	
$id_sewa = base64_decode($idx);

$pq = mysqli_query($koneksi, "SELECT 
  t_ware_sewa.*, 
  m_cust_tr.nama_cust
  FROM t_ware_sewa 
  LEFT JOIN m_cust_tr ON t_ware_sewa.id_cust = m_cust_tr.id_cust
  WHERE t_ware_sewa.id_sewa = '$id_sewa'");

$rq = mysqli_fetch_array($pq);	
$no_sewa = $rq['no_sewa'];
$id_quo  = $rq['id_quo'];
$bln     = $rq['bln'];
$thn     = $rq['thn'];
$tgl_sj  = ConverTgl($rq['tanggal']);
$id_cust = $rq['id_cust'];
$ket     = str_replace("\'","'",$rq['ket']);
$nama_cust = $rq['nama_cust'];
$tgl_keluar = strtotime($rq['tanggal']);

$pdf = new PDF('P','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true);

// HEADER
$pdf->Image("img/logo_print.jpg",7,5,25);  
$pdf->SetFont('arial','B',18);
$pdf->setXY(145,20);
$pdf->Cell(25,8,"SALES ORDER",0,1,'L');

$pdf->SetFont('arial','',7);
$pdf->setX(145);
$pdf->Cell(15,4,"No SO",0,0,'L');
$pdf->Cell(3,4,":",0,0,'L');
$pdf->Cell(3,4,"$no_sewa",0,1,'L');

$pdf->setX(145);
$pdf->Cell(15,4,"Tanggal",0,0,'L');
$pdf->Cell(3,4,":",0,0,'L');
$pdf->Cell(3,4,"$tgl_sj",0,1,'L');

$pdf->setXY(5,27);
$pdf->Cell(190,5,"Kepada Yth:",0,1,'L');
$pdf->SetFont('arial','',8);
$pdf->setX(5);
$pdf->MultiCell(100,4,"$nama_cust",0,1,'L');

$pdf->Cell(190,5,"",0,1,'L');
$pdf->SetFont('arial','',7);

$pdf->setXY(5,42);
$pdf->Cell(7,6,"NO",1,0,'C');
$pdf->Cell(35,6,"KODE BARANG",1,0,'C');
$pdf->Cell(100,6,"NAMA BARANG",1,0,'C');
$pdf->Cell(30,6,"QTY",1,0,'C');
$pdf->Cell(25,6,"CBM",1,1,'C');

// DETAIL
$t1 = "SELECT 
  t_ware_sewa_detail.*,
  t_ware_quo.aging_sewa
  FROM t_ware_sewa_detail
  LEFT JOIN t_ware_sewa ON t_ware_sewa.id_sewa = t_ware_sewa_detail.id_sewa
  LEFT JOIN t_ware_quo ON t_ware_quo.id_quo = t_ware_sewa.id_quo
  WHERE t_ware_sewa_detail.id_sewa = '$id_sewa'";

$h1 = mysqli_query($koneksi, $t1); 
$no = 0;
$fix_cbm = 0;
$max_cbm = 0;
$harga_sewa = 0;

while ($d1 = mysqli_fetch_array($h1)) {
    $no++;
    $tgl_masuk = strtotime($d1['tanggal']);
    $aging = round(($tgl_keluar - $tgl_masuk) / (24*60*60));

    $max_cbm     = $d1['max_cbm'];
    $harga_sewa  = $d1['harga_sewa'];
    $max_cbmx    = number_format($max_cbm, 2);
    $harga_sewax = number_format($harga_sewa, 0);

    if ($aging > $d1['aging_sewa']) {
        $sisa = $d1['qty'];
        $cbm  = $d1['cbm'];
    } else {
        $sisa = 0;
        $cbm  = 0;
    }

    $cbmx = round($cbm, 2);
    $fix_cbm += $cbm;

    $pdf->setX(5);
    $pdf->Cell(7,5,$no,1,0,'C');
    $pdf->Cell(35,5,$d1['itemcode'],1,0,'C');
    $pdf->Cell(100,5,$d1['itemname'],1,0,'L');
    $pdf->Cell(30,5,"$sisa ".$d1['unit'],1,0,'C');
    $pdf->Cell(25,5,number_format($cbmx,2),1,1,'R');
}

// HITUNG TOTAL
$totalx = round($fix_cbm,2);
$sisa_cbm = $fix_cbm - $max_cbm;
if ($sisa_cbm < 0) $sisa_cbm = 0;

$sisa_cbmx = number_format($sisa_cbm,2);
$tagihan = $harga_sewa * $sisa_cbm;
$tagihanx = number_format($tagihan,0);

$pdf->setX(8);
$pdf->Cell(139,5,"",0,0,'L');
$pdf->Cell(30,5,"TOTAL CBM",1,0,'R');
$pdf->Cell(25,5,$totalx,1,1,'R');

$pdf->setX(8);
$pdf->Cell(139,5,"",0,0,'L');
$pdf->Cell(30,5,"MAX CBM",1,0,'R');
$pdf->Cell(25,5,$max_cbmx,1,1,'R');

$pdf->setX(8);
$pdf->Cell(139,5,"",0,0,'L');
$pdf->Cell(30,5,"TAGIHAN CBM",1,0,'R');
$pdf->Cell(25,5,$sisa_cbmx,1,1,'R');

$pdf->setX(8);
$pdf->Cell(139,5,"",0,0,'L');
$pdf->Cell(30,5,"HARGA SEWA",1,0,'R');
$pdf->Cell(25,5,$harga_sewax,1,1,'R');

$pdf->SetFont('arial','B',7);
$pdf->setX(8);
$pdf->Cell(139,5,"",0,0,'L');
$pdf->Cell(30,5,"JUMLAH TAGIHAN",1,0,'R');
$pdf->Cell(25,5,$tagihanx,1,1,'R');

// KETERANGAN
$pdf->SetFont('arial','',7);	
$pdf->setX(5);
$pdf->Cell(10,4,"KETERANGAN:",0,1,'L');
$pdf->setX(5);
$pdf->MultiCell(150,3,$ket,0,'L');

ob_end_clean();
$pdf->Output();
?>
