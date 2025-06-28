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

$pdf->Image("img/logo_print.jpg",7,5,25);  
$pdf->SetFont('arial','BU',14);
$pdf->setXY(12,15);
$pdf->Cell(190,8," PURCHASE ORDER ",0,1,'C');

$pdf->SetFont('arial','',9);
$pdf->setXY(8,30);
$pdf->Cell(20,4,"No PO",0,0,'L');
$pdf->Cell(3,4,":",0,0,'L');
$pdf->Cell(3,4,"$no_sj",0,1,'L');
$pdf->setX(8);
$pdf->Cell(20,4,"Tanggal",0,0,'L');
$pdf->Cell(3,4,":",0,0,'L');
$pdf->Cell(3,4,"$tgl_sj",0,1,'L');

$pdf->setXY(120,30);
$pdf->Cell(190,5,"Kepada Yth:",0,1,'L');
$pdf->SetFont('arial','',9);
$pdf->setX(120);
$pdf->MultiCell(100,4,"$nama_cust",0,1,'L');	

$pdf->setXY(8,43);
$pdf->Cell(8,6,"No",1,0,'C');
$pdf->Cell(37,6,"Kode Barang",1,0,'C');
$pdf->Cell(50,6,"Nama Barang",1,0,'C');
$pdf->Cell(15,6,"UoM",1,0,'C');
$pdf->Cell(15,6,"Quantity",1,0,'C');
$pdf->Cell(20,6,"Volume",1,0,'C');
$pdf->Cell(20,6,"Berat",1,0,'C');
$pdf->Cell(30,6,"Remark",1,1,'C');

$y=49;
$t1 = "select t_ware_data_detil.*, t_ware.nama, t_ware.berat, t_ware.vol, t_ware.unit, t_ware.kode, m_lokasi_ware.nama as nama_lokasi
			from 
			t_ware_data_detil inner join t_ware on t_ware_data_detil.id_ware = t_ware.id_ware
			left join m_lokasi_ware on t_ware_data_detil.id_lokasi = m_lokasi_ware.id_lokasi
			where t_ware_data_detil.id_data = '$id_data'  order by  t_ware_data_detil.id_detil";
$h1 = mysqli_query($koneksi, $t1); 
while ($d1=mysqli_fetch_array($h1))
{
	$n++;
	$vol = $d1['masuk'] * $d1['vol'];
	$volx = number_format($vol,2);
	$berat = $d1['masuk'] * $d1['berat'];
	$beratx = number_format($berat,2);
	$qty = number_format($d1['masuk'],0);
	
	$t_qty = $t_qty + $d1['masuk'];
	$t_vol = $t_vol + $vol;
	$t_berat = $t_berat + $berat;
	
	$ket = strlen($d1['nama']);		
	if($ket <= '20' )
	{
		$tinggi = 5;
	}else if($ket > '20' && $ket <= '60'){
		$tinggi = 10;	
	}else if($ket > '61' ){
		$tinggi = 12;
	}
	
	$pdf->setXY(8,$y);
	$pdf->Cell(8,$tinggi,"",1,0,'C');
	$pdf->Cell(37,$tinggi,"",1,0,'C');
	$pdf->Cell(50,$tinggi,"",1,0,'L');
	$pdf->Cell(15,$tinggi,"",1,0,'C');
	$pdf->Cell(15,$tinggi,"",1,0,'C');
	$pdf->Cell(20,$tinggi,"",1,0,'C');
	$pdf->Cell(20,$tinggi,"",1,0,'C');
	$pdf->Cell(30,$tinggi,"",1,0,'C');
	
	$pdf->setXY(8,$y);
	$pdf->Cell(8,6,"$n",0,0,'C');
	$pdf->Cell(37,6,"$d1[kode]",0,0,'C');
	$pdf->Cell(50,6,"",0,0,'C');
	$pdf->Cell(15,6,"$d1[unit]",0,0,'C');
	$pdf->Cell(15,6,"$qty",0,0,'R');
	$pdf->Cell(20,6,"$volx",0,0,'R');
	$pdf->Cell(20,6,"$beratx",0,0,'R');
	$pdf->Cell(30,6,"$d1[no_cont]",0,0,'C');

	$pdf->setXY(53,$y+1);
	$pdf->MultiCell(50,3.5,"$d1[nama]",0,'L');
	
	$y=$y+$tinggi;
}

$t_vol = number_format($t_vol,2);
$t_berat = number_format($t_berat,2);
$t_qty = number_format($t_qty,0);

$pdf->setXY(8,$y);
$pdf->Cell(8,6,"",0,0,'C');
$pdf->Cell(37,6,"",0,0,'C');
$pdf->Cell(50,6,"",0,0,'L');
$pdf->Cell(15,6,"Total ",1,0,'R');
$pdf->Cell(15,6,"$t_qty",1,0,'R');
$pdf->Cell(20,6,"$t_vol",1,0,'R');
$pdf->Cell(20,6,"$t_berat",1,0,'R');
$pdf->Cell(30,6,"",0,1,'C');
	
$pdf->Cell(30,6,"",0,1,'C');	

if($ketx != '')
{
$pdf->setX(8);
$pdf->Cell(8,6,"Keterangan:",0,1,'L');	
$pdf->setX(8);
// $pdf->MultiCell(8,3.5,"$ketx",0,'L');	
$pdf->Cell(30,6,"",0,1,'C');
}

$pdf->setX(22);
$pdf->Cell(30,5,"Dibuat oleh",1,0,'C');	
$pdf->Cell(30,5,"Checker",1,0,'C');	
$pdf->Cell(30,5,"Security",1,0,'C');	
$pdf->Cell(30,5,"Driver",1,0,'C');
$pdf->Cell(30,5,"Mengetahui",1,0,'C');		
$pdf->Cell(30,5,"Penerima",1,1,'C');	

$pdf->setX(22);
$pdf->Cell(30,15,"",1,0,'C');	
$pdf->Cell(30,15,"",1,0,'C');	
$pdf->Cell(30,15,"",1,0,'C');	
$pdf->Cell(30,15,"",1,0,'C');
$pdf->Cell(30,15,"",1,0,'C');		
$pdf->Cell(30,15,"",1,1,'C');	

$pdf->Output();

?>