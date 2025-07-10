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
$jenis_cross = $rq['jenis_cross'];
	
$pdf=new PDF('P','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(false);



//RESI 1

$pdf->Image("img/logo_print.jpg",7,5,25);  
$pdf->SetFont('arial','B',18);
$pdf->setXY(145,20);
$pdf->Cell(25,8,"SURAT JALAN",0,1,'L');

$pdf->SetFont('arial','',8);
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

$pdf->setXY(8,27);
$pdf->Cell(190,5,"Kepada Yth:",0,1,'L');
$pdf->SetFont('arial','',8);
$pdf->setX(8);
$pdf->MultiCell(100,4,"$nama_cust",0,1,'L');

$pdf->Cell(190,2,"",0,1,'L');

$pdf->setX(8);
$pdf->Cell(100,4,"Gudang Penerima:",0,1,'L');
$pdf->setX(8);
$pdf->Cell(100,4,"$gudang",0,1,'L');

$pdf->Cell(190,5,"",0,1,'L');

$pdf->setXY(8,52);
$pdf->Cell(10,5,"NO",1,0,'C');
$pdf->Cell(35,5,"KODE BARANG",1,0,'C');
$pdf->Cell(75,5,"NAMA BARANG",1,0,'C');
$pdf->Cell(35,5,"REMARK",1,0,'C');
$pdf->Cell(20,5,"QTY",1,0,'C');
$pdf->Cell(20,5,"VOL",1,1,'C');

$y=57;
$t1 = "select t_ware_data_detil.*, t_ware_data_detil1.no_cont, t_ware_data.tanggal,
			t_ware.nama, t_ware.kode, t_ware.vol, 
			t_ware_data1.tanggal as tgl_sj
			from 
			t_ware_data_detil inner join t_ware_data_detil as t_ware_data_detil1 on 
			t_ware_data_detil.id_detil_masuk = t_ware_data_detil1.id_detil
			left join t_ware_data on t_ware_data_detil1.id_data = t_ware_data.id_data
			left join t_ware on t_ware_data_detil.id_ware = t_ware.id_ware
			left join t_ware_data as t_ware_data1 on t_ware_data_detil.id_data = t_ware_data1.id_data
			where t_ware_data_detil.id_data = '$id_data'  order by  t_ware_data_detil.id_detil";

$h1 = mysqli_query($koneksi, $t1); 
while ($d1=mysqli_fetch_array($h1))
{
	$n++;
	$unit = $d1['unit'];
	$vol = $d1['keluar'] * $d1['vol'];
	$volx = number_format($vol,2);
	$t_qty = $t_qty + $d1['keluar'];
	$t_vol = $t_vol + $vol;
	
	$ket = strlen($d1['nama']);		
	if($ket <= '38' )
	{
		$tinggi = 5;
	}else if($ket > '38' && $ket <= '100'){
		$tinggi = 10;	
	}else if($ket > '61' ){
		$tinggi = 12;
	}
	
	$pdf->setXY(8,$y);
	$pdf->Cell(10,$tinggi,"",1,0,'C');
	$pdf->Cell(35,$tinggi,"",1,0,'C');
	$pdf->Cell(75,$tinggi,"",1,0,'L');
	$pdf->Cell(35,$tinggi,"",1,0,'C');
	$pdf->Cell(20,$tinggi,"",1,0,'R');
	$pdf->Cell(20,$tinggi,"",1,1,'R');
	
	$pdf->SetFont('arial','',8);
	$pdf->setXY(8,$y);
	$pdf->Cell(10,5,"$n.",0,0,'C');
	$pdf->Cell(35,5,"$d1[kode]",0,0,'C');
	$pdf->Cell(75,5,"",0,0,'L');
	$pdf->Cell(35,5,"$d1[rem]",0,0,'C');
	$pdf->Cell(20,5,"$d1[keluar] $d1[unit]",0,0,'R');
	$pdf->Cell(20,5,"$volx M3",0,0,'R');
	
	$pdf->setXY(53,$y+1);
	$pdf->MultiCell(75,3.5,"$d1[nama]",0,'L');
	
	$y=$y+$tinggi;
	
}

$t_volx = number_format($t_vol,2);
$pdf->setXY(8,$y);
$pdf->Cell(10,5,"",0,0,'C');
$pdf->Cell(35,5,"",0,0,'C');
$pdf->Cell(75,5,"",0,0,'L');
$pdf->Cell(35,5,"TOTAL",1,0,'R');
$pdf->Cell(20,5,"$t_qty $unit",1,0,'R');
$pdf->Cell(20,5,"$t_volx M3",1,1,'R');

	
	
$pdf->setX(8);
$pdf->Cell(10,4,"KETERANGAN:",0,1,'L');

$pdf->setX(8);
$pdf->MultiCell(100,3,"$ketx",0,1,'L');

$pdf->Cell(190,5,"",0,1,'L');
	
$pdf->setX(28);
$pdf->Cell(35,5,"DIBUAT OLEH",1,0,'C');
$pdf->Cell(35,5,"SECURITY",1,0,'C');
$pdf->Cell(35,5,"DRIVER",1,0,'C');
$pdf->Cell(35,5,"MENGETAHUI",1,0,'C');
$pdf->Cell(35,5,"PENERIMA",1,1,'C');

$pdf->setX(28);
$pdf->Cell(35,15,"",1,0,'C');
$pdf->Cell(35,15,"",1,0,'C');
$pdf->Cell(35,15,"",1,0,'C');
$pdf->Cell(35,15,"",1,0,'C');
$pdf->Cell(35,15,"",1,1,'C');

ob_end_clean();
$pdf->Output();

?>