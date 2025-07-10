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
$id_jo =base64_decode($idx);
$pq = mysqli_query($koneksi, "select tr_jo.*, tr_quo.quo_no, m_cust_tr.nama_cust, m_kota_tr.nama_kota as asal, m_kota1.nama_kota as tujuan,
			m_mobil_tr.no_polisi, m_supir_tr.nama_supir, m_supir_tr.telp
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

$no_sj = $rq['no_jo'];
$no_do = $rq['no_do'];
$tgl_sj = ConverTgl($rq['tgl_jo']);
$nama_cust = $rq['nama_cust'];
$no_polisi = $rq['no_polisi'];
$nama_supir = $rq['nama_supir'];
$telp_supir = $rq['telp'];
$nama_vendor = $rq['nama_vendor'];
$penerima = $rq['penerima'];
$barang = $rq['barang'];
$no_jo = $rq['no_jo'];
$no_cont = $rq['no_cont'];
$no_seal = $rq['no_seal'];
$nama_barang = str_replace("\'","'",$rq['nama_barang']);	
$berat = number_format($rq['berat'],2);
$vol = number_format($rq['vol'],2);
$jenis_sj = $rq['jenis_sj'];
$ket = $rq['ket'];
	
$pdf=new PDF('P','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(false);



//RESI 1

$pdf->Image("img/logo_print.jpg",7,5,25);  
$pdf->SetFont('arial','B',18);
$pdf->setXY(135,20);
$pdf->Cell(25,8,"SURAT JALAN",0,1,'L');

$pdf->SetFont('arial','',10);
$pdf->setX(135);
$pdf->Cell(20,4,"No SJ",0,0,'L');
$pdf->Cell(3,4,":",0,0,'L');
$pdf->Cell(3,4,"$no_sj",0,1,'L');

$pdf->setX(135);
$pdf->Cell(20,4,"No PO/DO",0,0,'L');
$pdf->Cell(3,4,":",0,0,'L');
$pdf->Cell(3,4,"$no_do",0,1,'L');

$pdf->setX(135);
$pdf->Cell(20,4,"Tanggal",0,0,'L');
$pdf->Cell(3,4,":",0,0,'L');
$pdf->Cell(3,4,"$tgl_sj",0,1,'L');

$pdf->setX(135);
$pdf->Cell(20,4,"No. Polisi",0,0,'L');
$pdf->Cell(3,4,":",0,0,'L');
$pdf->Cell(3,4,"$no_polisi",0,1,'L');

$pdf->setX(135);
$pdf->Cell(20,4,"Supir",0,0,'L');
$pdf->Cell(3,4,":",0,0,'L');
$pdf->Cell(3,4,"$nama_supir",0,1,'L');

$pdf->setX(135);
$pdf->Cell(20,4,"No. Telp",0,0,'L');
$pdf->Cell(3,4,":",0,0,'L');
$pdf->Cell(3,4,"$telp_supir",0,1,'L');

$pdf->setXY(8,27);
$pdf->Cell(190,5,"Kepada Yth:",0,1,'L');
$pdf->SetFont('arial','',10);
$pdf->setX(8);
$pdf->Cell(20,4,"$nama_cust",0,1,'L');
$pdf->setX(8);
$pdf->MultiCell(100,4,"$penerima",0,1,'L');

$pdf->Cell(190,5,"",0,1,'L');

$pdf->setXY(8,57);
$pdf->Cell(15,7,"NO",1,0,'C');
$pdf->Cell(60,7,"NAMA BARANG",1,0,'C');
$pdf->Cell(25,7,"QTY",1,0,'C');
$pdf->Cell(30,7,"NO. CONT",1,0,'C');
$pdf->Cell(25,7,"NO. SEAL",1,0,'C');
$pdf->Cell(40,7,"KETERANGAN",1,1,'C');

$pdf->setX(8);
$pdf->Cell(15,40,"",1,0,'C');
$pdf->Cell(60,40,"",1,0,'C');
$pdf->Cell(25,40,"",1,0,'C');
$pdf->Cell(30,40,"",1,0,'C');
$pdf->Cell(25,40,"",1,0,'C');
$pdf->Cell(40,40,"",1,1,'C');

$pdf->setXY(8,67);
$pdf->Cell(15,4,"1",0,0,'C');

$pdf->setXY(25,67);
$pdf->MultiCell(58,4,"$barang",0,1,'L');


$pdf->setXY(83,67);
$pdf->Cell(25,4,"$berat KG",0,0,'C');
$pdf->setXY(83,72);
$pdf->Cell(25,4,"$vol M3",0,0,'C');

$pdf->setXY(109,67);
$pdf->Cell(28,4,"$no_cont",0,0,'C');

$pdf->setXY(139,67);
$pdf->Cell(22,4,"$no_seal",0,0,'C');

$pdf->setXY(164,67);
$pdf->MultiCell(37,4,"$ket",0,1,'L');

$pdf->setXY(8,107);
$pdf->Cell(65,7,"YANG MENYERAHKAN",1,0,'C');
$pdf->Cell(65,7,"TRUCKING",1,0,'C');
$pdf->Cell(65,7,"PENERIMA",1,1,'C');

$pdf->setX(8);
$pdf->Cell(65,30,"",1,0,'C');
$pdf->Cell(65,30,"",1,0,'C');
$pdf->Cell(65,30,"",1,0,'C');

$pdf->setXY(0,147);
$pdf->Cell(50,1,"..................................................................................................................................................................................................................................................................................................................................",0,0,'L');






//RESI 1

$pdf->Image("img/logo_print.jpg",7,153,25);  
$pdf->SetFont('arial','B',18);
$pdf->setXY(135,168);
$pdf->Cell(25,8,"SURAT JALAN",0,1,'L');

$pdf->SetFont('arial','',10);
$pdf->setX(135);
$pdf->Cell(20,4,"No SJ",0,0,'L');
$pdf->Cell(3,4,":",0,0,'L');
$pdf->Cell(3,4,"$no_sj",0,1,'L');

$pdf->setX(135);
$pdf->Cell(20,4,"No PO/DO",0,0,'L');
$pdf->Cell(3,4,":",0,0,'L');
$pdf->Cell(3,4,"$no_do",0,1,'L');


$pdf->setX(135);
$pdf->Cell(20,4,"Tanggal",0,0,'L');
$pdf->Cell(3,4,":",0,0,'L');
$pdf->Cell(3,4,"$tgl_sj",0,1,'L');

$pdf->setX(135);
$pdf->Cell(20,4,"No. Polisi",0,0,'L');
$pdf->Cell(3,4,":",0,0,'L');
$pdf->Cell(3,4,"$no_polisi",0,1,'L');

$pdf->setX(135);
$pdf->Cell(20,4,"Supir",0,0,'L');
$pdf->Cell(3,4,":",0,0,'L');
$pdf->Cell(3,4,"$nama_supir",0,1,'L');

$pdf->setX(135);
$pdf->Cell(20,4,"No. Telp",0,0,'L');
$pdf->Cell(3,4,":",0,0,'L');
$pdf->Cell(3,4,"$telp_supir",0,1,'L');

$pdf->setXY(8,175);
$pdf->Cell(190,5,"Kepada Yth:",0,1,'L');
$pdf->SetFont('arial','',10);
$pdf->setX(8);
$pdf->Cell(20,4,"$nama_cust",0,1,'L');
$pdf->setX(8);
$pdf->MultiCell(100,4,"$penerima",0,1,'L');

$pdf->Cell(190,5,"",0,1,'L');

$pdf->setXY(8,205);
$pdf->Cell(15,7,"NO",1,0,'C');
$pdf->Cell(60,7,"NAMA BARANG",1,0,'C');
$pdf->Cell(25,7,"QTY",1,0,'C');
$pdf->Cell(30,7,"NO. CONT",1,0,'C');
$pdf->Cell(25,7,"NO. SEAL",1,0,'C');
$pdf->Cell(40,7,"KETERANGAN",1,1,'C');

$pdf->setX(8);
$pdf->Cell(15,40,"",1,0,'C');
$pdf->Cell(60,40,"",1,0,'C');
$pdf->Cell(25,40,"",1,0,'C');
$pdf->Cell(30,40,"",1,0,'C');
$pdf->Cell(25,40,"",1,0,'C');
$pdf->Cell(40,40,"",1,1,'C');

$pdf->setXY(8,215);
$pdf->Cell(15,4,"1",0,0,'C');

$pdf->setXY(25,215);
$pdf->MultiCell(58,4,"$barang",0,1,'L');


$pdf->setXY(83,215);
$pdf->Cell(25,4,"$berat KG",0,0,'C');
$pdf->setXY(83,220);
$pdf->Cell(25,4,"$vol M3",0,0,'C');

$pdf->setXY(109,215);
$pdf->Cell(28,4,"$no_cont",0,0,'C');

$pdf->setXY(139,215);
$pdf->Cell(22,4,"$no_seal",0,0,'C');

$pdf->setXY(164,215);
$pdf->MultiCell(37,4,"$ket",0,1,'L');

$pdf->setXY(8,255);
$pdf->Cell(65,7,"YANG MENYERAHKAN",1,0,'C');
$pdf->Cell(65,7,"TRUCKING",1,0,'C');
$pdf->Cell(65,7,"PENERIMA",1,1,'C');

$pdf->setX(8);
$pdf->Cell(65,30,"",1,0,'C');
$pdf->Cell(65,30,"",1,0,'C');
$pdf->Cell(65,30,"",1,0,'C');


ob_end_clean();
$pdf->Output();

?>