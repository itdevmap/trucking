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
$id_sewa =base64_decode($idx);
$pq = mysqli_query($koneksi, "select t_ware_sewa.*, m_cust_tr.nama_cust
		  from 
		  t_ware_sewa left join m_cust_tr on t_ware_sewa.id_cust = m_cust_tr.id_cust
		  where t_ware_sewa.id_sewa = '$id_sewa'  ");
$rq=mysqli_fetch_array($pq);	
$no_sewa = $rq['no_sewa'];
$id_quo = $rq['id_quo'];
$bln = $rq['bln'];
$thn = $rq['thn'];
$tgl_sj = ConverTgl($rq['tanggal']);
$id_cust = $rq['id_cust'];
$nama_cust = $rq['nama_cust'];
$ket = str_replace("\'","'",$rq['ket']);
$tgl_keluar = strtotime($rq['tanggal']);
	
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

//$y=62;
$t1 = "select t_ware_data.*, t_ware_data_detil.id_detil, t_ware_data_detil.no_cont, t_ware_data_detil.masuk, t_ware_data_detil.keluar, 
			t_ware.nama, t_ware.kode, t_ware.id_quo, t_ware.vol, t_ware.unit, m_cust_tr.nama_cust, m_lokasi_ware.nama as nama_lokasi,
			t_ware_quo.max_cbm, t_ware_quo.harga_sewa, t_ware_quo.aging_sewa
			from  
			t_ware_data left join t_ware_data_detil on t_ware_data.id_data = t_ware_data_detil.id_data
			left join t_ware on t_ware_data_detil.id_ware = t_ware.id_ware 
			left join t_ware_quo on t_ware.id_quo = t_ware_quo.id_quo
			left join m_cust_tr on t_ware_data.id_cust = m_cust_tr.id_cust
			left join m_lokasi_ware on t_ware_data_detil.id_lokasi = m_lokasi_ware.id_lokasi
			where t_ware.id_quo = '$id_quo'  and t_ware_data.jenis = '0'
			and t_ware_data.status = '1' 
			order by  t_ware_data.id_data";			
$h1 = mysqli_query($koneksi, $t1); 
while ($d1=mysqli_fetch_array($h1))
{
	$n++;
	
	$tgl_masuk = strtotime($d1['tanggal']);
	$aging = $tgl_keluar - $tgl_masuk; 
	$aging = ($aging/24/60/60);
	$aging = round($aging);
			
	$max_cbm = $d1['max_cbm'];
	$harga_sewa = $d1['harga_sewa'];
	$max_cbmx = number_format($d1['max_cbm'],2);
	$harga_sewax = number_format($d1['harga_sewa'],0);
	
	if($aging > $d1['aging_sewa'])
	{
		$sisa  = $d1['masuk'] - $d1['keluar'];
	}else{
		$sisa = 0;
	}
	$cbm = $sisa * $d1['vol'];	
	$cbmx = number_format($cbm,2);
	$total = $total + $cbm;
	
	//if($sisa_cbm >0)
	//{
		$total = $total + $jumlah;
		$pdf->setX(5);
		$pdf->Cell(7,5,"$n",1,0,'C');
		$pdf->Cell(35,5,"$d1[kode]",1,0,'C');
		$pdf->Cell(100,5,"$d1[nama]",1,0,'L');
		$pdf->Cell(30,5,"$sisa $d1[unit]",1,0,'C');
		$pdf->Cell(25,5,"$cbmx",1,1,'R');
	//}
	
}

$totalx = number_format($total,2);
$pdf->setX(8);
$pdf->Cell(139,5,"",0,0,'L');
$pdf->Cell(30,5,"TOTAL CBM",1,0,'R');
$pdf->Cell(25,5,"$totalx",1,1,'R');
	
$pdf->setX(8);
$pdf->Cell(139,5,"",0,0,'L');
$pdf->Cell(30,5,"MAX CBM",1,0,'R');
$pdf->Cell(25,5,"$max_cbmx",1,1,'R');

$sisa_cbm =  $total - $max_cbm;
$sisa_cbmx = number_format($sisa_cbm,2);
$pdf->setX(8);
$pdf->Cell(139,5,"",0,0,'L');
$pdf->Cell(30,5,"TAGIHAN CBM",1,0,'R');
$pdf->Cell(25,5,"$sisa_cbmx",1,1,'R');

$pdf->setX(8);
$pdf->Cell(139,5,"",0,0,'L');
$pdf->Cell(30,5,"HARGA SEWA",1,0,'R');
$pdf->Cell(25,5,"$harga_sewax",1,1,'R');

$pdf->SetFont('arial','B',7);
$tagihan =  $harga_sewa * $sisa_cbm;
$tagihanx = number_format($tagihan,0);	
$pdf->setX(8);
$pdf->Cell(139,5,"",0,0,'L');
$pdf->Cell(30,5,"JUMLAH TAGIHAN",1,0,'R');
$pdf->Cell(25,5,"$tagihanx",1,1,'R');

$pdf->SetFont('arial','',7);	
$pdf->setX(5);
$pdf->Cell(10,4,"KETERANGAN:",0,1,'L');

$pdf->setX(5);
$pdf->MultiCell(10,3,"$ket",0,1,'L');
	
$pdf->Output();

?>