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
$id_jo =base64_decode($idx);
$pq = mysqli_query($koneksi, "select tr_jo.*, tr_quo.quo_no, m_cust_tr.nama_cust, m_kota_tr.nama_kota as asal, m_kota1.nama_kota as tujuan,
			m_mobil_tr.no_polisi, m_supir_tr.nama_supir
			from 
			tr_jo left join tr_quo_data on tr_jo.id_detil_quo = tr_quo_data.id_detil
			left join tr_quo on tr_quo_data.id_quo = tr_quo.id_quo
			left join m_kota_tr on tr_jo.id_asal = m_kota_tr.id_kota
			left join m_kota_tr as m_kota1 on tr_jo.id_tujuan = m_kota1.id_kota
			left join m_cust_tr on tr_jo.id_cust = m_cust_tr.id_cust
			left join m_mobil_tr on tr_jo.id_mobil = m_mobil_tr.id_mobil
			left join m_supir_tr on tr_jo.id_supir = m_supir_tr.id_supir
				where tr_jo.id_jo  = '$id_jo'  ");
$rq=mysqli_fetch_array($pq);	
$no_jo = $rq['no_jo'];
$no_do = $rq['no_do'];
$ppn = $rq['ppn'];
$pph = $rq['pph'];
$tgl_jo = ConverTgl($rq['tgl_jo']);
$penerima = $rq['penerima'];
$nama_cust = $rq['nama_cust'];
$asal = $rq['asal'];
$tujuan = $rq['tujuan'];
$no_cont = $rq['no_cont'];
$nama_supir = $rq['nama_supir'];
$jenis_mobil = $rq['jenis_mobil'];
$harga = $rq['biaya_kirim'] ;
$hargax = number_format($harga,0);
$ujx = number_format($rq['uj'],0);	
$ritasex = number_format($rq['ritase'],0);	
$total = $total + $harga;

$pdf=new PDF('P','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(false);



//RESI 1

$pdf->Image("img/logo_print.jpg",7,5,25);  
$pdf->SetFont('arial','B',16);
$pdf->setXY(5,10);
$pdf->Cell(199,8,"SALES ORDER",0,1,'C');

$pdf->SetFont('arial','',9);
$pdf->setXY(145,28);
$pdf->Cell(20,5,"Kepada Yth",0,1,'L');
$pdf->SetFont('arial','B',9);
$pdf->setX(145);
$pdf->Cell(3,4,"$nama_cust",0,1,'L');
$pdf->Cell(3,2,"",0,1,'L');
$pdf->SetFont('arial','',9);
$pdf->setX(145);
$pdf->Cell(3,4,"Gudang Penerima:",0,1,'L');
$pdf->setX(145);
$pdf->MultiCell(100,4,"$penerima",0,'L');


$pdf->setXY(8,28);
$pdf->Cell(18,4,"No SJ",0,0,'L');
$pdf->Cell(3,4,":",0,0,'C');
$pdf->Cell(20,4,"$no_jo",0,1,'L');
$pdf->setX(8);
$pdf->Cell(18,4,"No PO/DO",0,0,'L');
$pdf->Cell(3,4,":",0,0,'C');
$pdf->Cell(20,4,"$no_do",0,1,'L');
$pdf->setX(8);
$pdf->Cell(18,4,"Tanggal",0,0,'L');
$pdf->Cell(3,4,":",0,0,'C');
$pdf->Cell(20,4,"$tgl_jo",0,1,'L');

$pdf->setXY(9,57);
$pdf->Cell(10,5,"NO",1,0,'C');
$pdf->Cell(108,5,"KETERANGAN",1,0,'C');
$pdf->Cell(15,5,"QTY",1,0,'C');
$pdf->Cell(27,5,"HARGA",1,0,'C');
$pdf->Cell(30,5,"TOTAL",1,1,'C');



$y=62;
$n++;


$uj_lain = "";
$t1 = "select  tr_jo_uj.*, m_cost_tr.nama_cost from
		tr_jo_uj left join m_cost_tr on tr_jo_uj.id_cost = m_cost_tr.id_cost
			where tr_jo_uj.id_jo = '$id_jo' order by tr_jo_uj.id_uj";
$h1 = mysqli_query($koneksi, $t1); 
while ($d1=mysqli_fetch_array($h1))
{
	$harga = number_format($d1['harga'],0);
	$uj_lain = $uj_lain."$d1[nama_cost] : $harga; ";
}

$data = "$no_cont; $nama_supir; UJ : $ujx; RITASE : $ritasex; $uj_lain";
$datax = strlen($data);		
if($datax <= '38' )
{
	$tinggi = 5;
}else if($datax > '38' && $datax <= '69'){
	$tinggi = 10;	
}else if($datax > '69' ){
	$tinggi = 15;
}

$pdf->setXY(9,$y+1);
$pdf->Cell(10,5,"$n.",0,0,'C');
$pdf->Cell(108,5,"TRUCKING 1 x $jenis_mobil ($asal - $tujuan) $datax",0,0,'L');
$pdf->Cell(15,5,"1",0,0,'C');
$pdf->Cell(27,5,"$hargax",0,0,'R');
$pdf->Cell(30,5,"$hargax",0,0,'R');
$pdf->setXY(19,$y+6);
$pdf->MultiCell(105,3.7,"$data",0,'L');


$pdf->setXY(9,$y);
$pdf->Cell(10,$tinggi,"",1,0,'C');
$pdf->Cell(108,$tinggi,"",1,'L');
$pdf->Cell(15,$tinggi,"",1,0,'C');
$pdf->Cell(27,$tinggi,"",1,0,'R');
$pdf->Cell(30,$tinggi,"",1,1,'R');







/*
$pdf->setXY(9,$y+5);
$pdf->Cell(10,5,"",0,0,'C');
//$pdf->Cell(108,5,"$no_cont; $nama_supir; UJ : $ujx; RITASE : $ritasex; $uj_lain",0,0,'L');
$pdf->MultiCell(110,3.5,"$data",0,'L');
$pdf->Cell(15,5,"",0,0,'C');
$pdf->Cell(27,5,"",0,0,'R');
//$pdf->Cell(30,5,"",0,1,'R');
$total = $total + $harga;
*/


$t1 = "select  tr_jo_biaya.*, m_cost_tr.nama_cost from
		tr_jo_biaya left join m_cost_tr on tr_jo_biaya.id_cost = m_cost_tr.id_cost
			where tr_jo_biaya.id_jo = '$id_jo' order by tr_jo_biaya.id_biaya ";
$h1 = mysqli_query($koneksi, $t1); 
while ($d1=mysqli_fetch_array($h1))
{
	$n++;
	$hargax = number_format($d1['harga'],0);
	$total = $total + $d1['harga'];
	$pdf->setX(9);
	$pdf->Cell(10,5,"$n.",1,0,'C');
	$pdf->Cell(108,5,"$d1[nama_cost]",1,0,'L');
	$pdf->Cell(15,5,"1",1,0,'C');
	$pdf->Cell(27,5,"$hargax",1,0,'R');
	$pdf->Cell(30,5,"$hargax",1,1,'R');
}

$totalx = number_format($total,0);
$pdf->setX(9);
$pdf->Cell(133,4,"",0,0,'C');
$pdf->Cell(27,5,"SUB TOTAL ",1,0,'R');
$pdf->Cell(30,5,"$totalx",1,1,'R');

$nilai_ppn = ($ppn/100) * $total;
$nilai_ppnx = number_format($nilai_ppn,0);
$pdf->setX(9);
$pdf->Cell(133,4,"",0,0,'C');
$pdf->Cell(27,5,"PPN ($ppn%)",1,0,'R');
$pdf->Cell(30,5,"$nilai_ppnx",1,1,'R');

$nilai_pph = ($pph/100) * $total;
$nilai_pphx = number_format($nilai_pph,0);	
$pdf->setX(9);
$pdf->Cell(133,4,"",0,0,'C');
$pdf->Cell(27,5,"WTAX ($pph%)",1,0,'R');
$pdf->Cell(30,5,"$nilai_pphx",1,1,'R');	

$total = $total + $nilai_ppn - $nilai_pph;
$totalx = number_format($total,0);
$pdf->setX(9);
$pdf->Cell(133,4,"",0,0,'C');
$pdf->Cell(27,5,"TOTAL ",1,0,'R');
$pdf->Cell(30,5,"$totalx",1,1,'R');

	
$pdf->Cell(30,15,"",0,1,'R');

$pdf->setX(55);
$pdf->Cell(50,4,"Menyetujui,",0,0,'C');
$pdf->Cell(50,4,"Mengetahui, ",0,1,'C');

$pdf->Cell(30,20,"",0,1,'R');

$pdf->SetFont('arial','U',9);
$pdf->setX(55);
$pdf->Cell(50,4,"Hamdan Wahyu",0,0,'C');
$pdf->Cell(50,4,"",0,1,'C');
		
$pdf->SetFont('arial','',9);
$pdf->setX(55);
$pdf->Cell(50,4,"Manager PTEJ",0,0,'C');
$pdf->Cell(50,4,"GM Logistic",0,1,'C');
		
$sql = "UPDATE tr_jo set tagihan = '$total' where id_jo = '$id_jo'  "; 
$hasil=mysqli_query($koneksi, $sql);	
	
$pdf->Output();

?>