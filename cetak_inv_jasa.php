<?php
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
        if($black!==null)
            $s=sprintf('[%.3F %.3F] 0 d',$black*$this->k,$white*$this->k);
        else
            $s='[] 0 d';
        $this->_out($s);
    }	
  function Header()
  {  
	
  }
  function Footer()
  {  
	$this->SetTextColor(51, 102, 153);
	$this->SetY(-25);   
    $this->SetFont('arial','BI',11);
    //$this->Cell(190,5,"'Partner of you logistics cargo services'",0,1,'C');
	$this->SetFont('arial','BI',11);
    //$this->Cell(190,5,"www.senopati.co.id",0,0,'C');
	$tanggal_cetak =date("d-m-Y h:i:s");
	$this->SetTextColor(0, 0, 0);
    $this->SetY(-15);   
    $this->SetFont('arial','',6);
	$this->SetX(6); 
    //$this->Cell(0,10,"Print Date : $tanggal_cetak -- $_SESSION[id_user]",0,0,'L');
  }   

}


$idx = $_GET['id'];	
$id_data =base64_decode($idx);
$pq = mysqli_query($koneksi, "select t_ware_data.*, m_cust_tr.nama_cust
		  from 
		  t_ware_data left join m_cust_tr on t_ware_data.id_cust = m_cust_tr.id_cust
		  where t_ware_data.id_data = '$id_data'  ");
$rq=mysqli_fetch_array($pq);	
$no_sj = $rq['no_doc'];
$jenis_sj = $rq['jenis_sj'];
$tgl_sj = ConverTgl($rq['tanggal']);
$id_cust = $rq['id_cust'];
$nama_cust = $rq['nama_cust'];
$gudang = $rq['gudang'];
$id_mobil = $rq['id_mobil'];
$no_polisi = $rq['no_polisi'];
$ketx = str_replace("\'","'",$rq['ket']);
$id_supir = $rq['id_supir'];
$nama_supir = $rq['supir'];
$telp = $rq['telp'];
$supir = $rq['supir'];
$no_do = $rq['no_do'];
	
$pdf=new PDF('P','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(false);



//RESI 1

$pdf->Image("img/logo_print.jpg",7,5,25);  
$pdf->SetFont('arial','B',18);
$pdf->setXY(145,20);
$pdf->Cell(25,8,"SALES ORDER",0,1,'L');

$pdf->SetFont('arial','',7);
$pdf->setX(145);
$pdf->Cell(20,4,"No SJ",0,0,'L');
$pdf->Cell(3,4,":",0,0,'L');
$pdf->Cell(3,4,"$no_sj",0,1,'L');

$pdf->setX(145);
$pdf->Cell(20,4,"Tanggal",0,0,'L');
$pdf->Cell(3,4,":",0,0,'L');
$pdf->Cell(3,4,"$tgl_sj",0,1,'L');

$pdf->setX(145);
$pdf->Cell(20,4,"No. Polisi",0,0,'L');
$pdf->Cell(3,4,":",0,0,'L');
$pdf->Cell(3,4,"$no_polisi",0,1,'L');

$pdf->setX(145);
$pdf->Cell(20,4,"Supir",0,0,'L');
$pdf->Cell(3,4,":",0,0,'L');
$pdf->Cell(3,4,"$nama_supir",0,1,'L');

$pdf->setX(145);
$pdf->Cell(20,4,"No. Telp",0,0,'L');
$pdf->Cell(3,4,":",0,0,'L');
$pdf->Cell(3,4,"$telp",0,1,'L');

$pdf->setXY(5,27);
$pdf->Cell(190,5,"Kepada Yth:",0,1,'L');
$pdf->SetFont('arial','',8);
$pdf->setX(5);
$pdf->MultiCell(100,4,"$nama_cust",0,1,'L');

$pdf->Cell(190,2,"",0,1,'L');

$pdf->setX(5);
$pdf->Cell(100,4,"Gudang Penerima:",0,1,'L');
$pdf->setX(5);
$pdf->Cell(100,4,"$gudang",0,1,'L');

$pdf->Cell(190,5,"",0,1,'L');

$pdf->SetFont('arial','',7);

$pdf->setXY(5,52);
$pdf->Cell(7,6,"NO",1,0,'C');
$pdf->Cell(60,6,"NAMA JASA",1,0,'C');
$pdf->Cell(70,6,"REMARKS",1,0,'C');
$pdf->Cell(15,6,"QTY",1,0,'C');
$pdf->Cell(22,6,"HARGA",1,0,'C');
$pdf->Cell(25,6,"JUMLAH",1,1,'C');

$y=62;
$t1 = "select  t_ware_jasa_biaya.*, m_cost_tr.nama_cost
			from 
			 t_ware_jasa_biaya inner join t_ware_quo_biaya on t_ware_jasa_biaya.id_biaya = t_ware_quo_biaya.id_detil
			left join m_cost_tr on t_ware_quo_biaya.id_biaya = m_cost_tr.id_cost
			where t_ware_jasa_biaya.id_data = '$id_data'  order by  t_ware_jasa_biaya.id_detil";			
$h1 = mysqli_query($koneksi, $t1); 
while ($d1=mysqli_fetch_array($h1))
{
	$n++;
	$hargax = number_format($d1['harga'],0);
	$jumlah = $d1['harga'] * $d1['qty'];
	$jumlahx = number_format($jumlah,0);
	$total = $total + $jumlah;
	$pdf->setX(5);
	$pdf->Cell(7,5,"$n.",1,0,'C');
	$pdf->Cell(60,5,"$d1[nama_cost]",1,0,'L');
	$pdf->Cell(70,5,"$d1[rem]",1,0,'L');	
	$pdf->Cell(15,5,"$d1[qty] $d1[unit]",1,0,'C');
	$pdf->Cell(22,5,"$hargax",1,0,'R');
	$pdf->Cell(25,5,"$jumlahx",1,1,'R');
	
	
	
	
}

$totalx = number_format($total,0);
$pdf->setX(8);
$pdf->Cell(149,5,"",0,0,'L');
$pdf->Cell(22,5,"TOTAL",1,0,'R');
$pdf->Cell(25,5,"$totalx",1,1,'R');
	
$pdf->setX(5);
$pdf->Cell(10,4,"KETERANGAN:",0,1,'L');

$pdf->setX(5);
$pdf->MultiCell(100,3,"$ketx",0,1,'L');
	
$pdf->Output();

?>