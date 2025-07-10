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
$id_sj =base64_decode($idx);
$pq = mysqli_query($koneksi, "select t_jo_sj_tr.*, m_kota_tr.nama_kota as asal,
		  m_kota1.nama_kota as tujuan, m_mobil_tr.no_polisi, m_supir_tr.nama_supir
		  from 
		  t_jo_sj_tr left join m_kota_tr on t_jo_sj_tr.id_asal = m_kota_tr.id_kota
		  left join m_kota_tr as m_kota1 on t_jo_sj_tr.id_tujuan = m_kota1.id_kota
		  left join m_mobil_tr on t_jo_sj_tr.id_mobil = m_mobil_tr.id_mobil
		  left join m_supir_tr on t_jo_sj_tr.id_supir = m_supir_tr.id_supir
		  where t_jo_sj_tr.id_sj = '$id_sj'  ");
	$rq=mysqli_fetch_array($pq);	
	$no_sj = $rq['no_sj'];
	$tgl_sj = ConverTgl($rq['tgl_sj']);
	$id_asal = $rq['id_asal'];
	$no_cont = $rq['no_cont'];
	$nama_asal = $rq['asal'];
	$id_tujuan = $rq['id_tujuan'];
	$nama_tujuan = $rq['tujuan'];
	$ket = str_replace("\'","'",$rq['ket']);
	$jenis_mobil = $rq['jenis_mobil'];
	$id_mobil = $rq['id_mobil'];
	$no_polisi = $rq['no_polisi'];
	$id_supir = $rq['id_supir'];
	$nama_supir = $rq['nama_supir'];	
	$uj = number_format($rq['uj'],0);
	$ritase = number_format($rq['ritase'],0);

	
$pdf=new PDF('L','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(false);

$pdf->Image("img/logo_print.jpg",7,10,20);  
$pdf->SetFont('arial','B',10);
$pdf->setXY(7,28);
$pdf->Cell(25,5,"SURAT JALAN",0,1,'L');

$pdf->SetFont('arial','',8);
$pdf->setX(7);
$pdf->Cell(20,4,"No SJ",0,0,'L');
$pdf->Cell(3,4,":",0,0,'L');
$pdf->Cell(3,4,"$no_sj",0,1,'L');

$pdf->setX(7);
$pdf->Cell(20,4,"Tanggal",0,0,'L');
$pdf->Cell(3,4,":",0,0,'L');
$pdf->Cell(3,4,"$tgl_sj",0,1,'L');

$pdf->setX(7);
$pdf->Cell(20,4,"No. Polisi",0,0,'L');
$pdf->Cell(3,4,":",0,0,'L');
$pdf->Cell(3,4,"$no_polisi",0,1,'L');
$pdf->setX(7);
$pdf->Cell(20,4,"Supir",0,0,'L');
$pdf->Cell(3,4,":",0,0,'L');
$pdf->Cell(3,4,"$nama_supir",0,1,'L');

$pdf->Cell(190,3,"",0,1,'L');

$pdf->setX(8);
$pdf->Cell(8,6,"NO",1,0,'C');
$pdf->Cell(20,6,"NO ORDER",1,0,'C');
$pdf->Cell(40,6,"NAMA BARANG",1,0,'C');
$pdf->Cell(45,6,"NAMA PENERIMA",1,0,'C');
$pdf->Cell(140,6,"ALAMAT",1,0,'C');
$pdf->Cell(30,6,"PENERIMA",1,1,'C');

$t1="select t_jo_tr.*, m_kota_tr.nama_kota as asal,	m_kota1.nama_kota as tujuan, m_cust_tr.nama_cust
				from 
				t_jo_tr left join m_kota_tr on t_jo_tr.id_asal = m_kota_tr.id_kota
				left join m_kota_tr as m_kota1 on t_jo_tr.id_tujuan = m_kota1.id_kota
				left join m_cust_tr on t_jo_tr.id_cust = m_cust_tr.id_cust
			where  t_jo_tr.id_sj = '$id_sj' order by  t_jo_tr.no_jo";
$h1=mysqli_query($koneksi, $t1);    
while ($d1=mysqli_fetch_array($h1))
{
	$n++;
	$pdf->setX(8);
	$pdf->Cell(8,6,"$n.",1,0,'C');
	$pdf->Cell(20,6,"$d1[no_jo]",1,0,'C');
	$pdf->Cell(40,6,"$d1[nama_barang]",1,0,'L');
	$pdf->Cell(45,6,"$d1[penerima]",1,0,'L');
	$pdf->Cell(140,6,"$d1[alamat_penerima]",1,0,'L');
	$pdf->Cell(30,6,"",1,1,'C');
}

$pdf->Cell(190,3,"",0,1,'L');

$pdf->setX(8);
$pdf->Cell(34,6,"PETUGAS",1,0,'C');
if($jenis_sj == '1')
{
	$pdf->Cell(34,6,"VENDOR",1,1,'C');
}else{
	$pdf->Cell(34,6,"SUPIR",1,1,'C');
}


$pdf->setX(8);
$pdf->Cell(34,20,"",1,0,'C');
$pdf->Cell(34,20,"",1,1,'C');

ob_end_clean();
$pdf->Output();

?>