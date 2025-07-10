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
    function Header() {  }
    function Footer()
    {  
        $this->SetTextColor(51, 102, 153);
        $this->SetY(-25);   
        $this->SetFont('arial','BI',11);
        $this->SetFont('arial','BI',11);
        $tanggal_cetak = date("d-m-Y h:i:s");
        $this->SetTextColor(0, 0, 0);
        $this->SetY(-15);   
        $this->SetFont('arial','',6);
        $this->SetX(6); 
    }   
}

$idx = $_GET['id'];	
$id_jo = base64_decode($idx);
$pq = mysqli_query($koneksi, 
"SELECT tr_jo.*, 
	tr_quo.quo_no, 
	m_cust_tr.nama_cust, 
	m_kota_tr.nama_kota as asal, 
	m_kota1.nama_kota as tujuan,
	m_mobil_tr.no_polisi, 
	m_supir_tr.nama_supir
FROM tr_jo 
	LEFT JOIN tr_quo_data ON tr_jo.id_detil_quo = tr_quo_data.id_detil
	LEFT JOIN tr_quo ON tr_quo_data.id_quo = tr_quo.id_quo
	LEFT JOIN m_kota_tr ON tr_jo.id_asal = m_kota_tr.id_kota
	LEFT JOIN m_kota_tr as m_kota1 ON tr_jo.id_tujuan = m_kota1.id_kota
	LEFT JOIN m_cust_tr ON tr_jo.id_cust = m_cust_tr.id_cust
	LEFT JOIN m_mobil_tr ON tr_jo.id_mobil = m_mobil_tr.id_mobil
	LEFT JOIN m_supir_tr ON tr_jo.id_supir = m_supir_tr.id_supir
WHERE tr_jo.id_jo = '$id_jo'");

$rq = mysqli_fetch_array($pq);
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
$harga = $rq['biaya_kirim'];
$hargax = number_format($harga,0);
$ujx = number_format($rq['uj'],0);	
$ritasex = number_format($rq['ritase'],0);	
$total = $total + $harga;

$pdf = new PDF('P','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(false);

// HEADER CETAK
$pdf->Image("img/logo_print.jpg",7,5,25);  
$pdf->SetFont('arial','B',16);
$pdf->setXY(5,10);
$pdf->Cell(199,8,"SALES ORDER",0,1,'C');

// Tujuan dan penerima
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

// Info utama
$pdf->setXY(8,28);
$pdf->Cell(18,4,"No SJ",0,0,'L'); $pdf->Cell(3,4,":",0,0,'C'); $pdf->Cell(20,4,"$no_jo",0,1,'L');
$pdf->setX(8); $pdf->Cell(18,4,"No PO/DO",0,0,'L'); $pdf->Cell(3,4,":",0,0,'C'); $pdf->Cell(20,4,"$no_do",0,1,'L');
$pdf->setX(8); $pdf->Cell(18,4,"Tanggal",0,0,'L'); $pdf->Cell(3,4,":",0,0,'C'); $pdf->Cell(20,4,"$tgl_jo",0,1,'L');

// INI HEADER NYAAAA
// HEADER TABEL
$pdf->setXY(9,57);
$pdf->SetFont('arial', 'B', 9);
$pdf->Cell(10, 5, "NO", 1, 0, 'C');
$pdf->Cell(85, 5, "KETERANGAN", 1, 0, 'C');
$pdf->Cell(10, 5, "QTY", 1, 0, 'C');
$pdf->Cell(22, 5, "HARGA", 1, 0, 'C');
$pdf->Cell(20, 5, "PPN", 1, 0, 'C');
$pdf->Cell(20, 5, "WTAX", 1, 0, 'C');
$pdf->Cell(23, 5, "TOTAL", 1, 1, 'C');

$y = 62;
$n = 1;

// KUMPULKAN UJ LAIN
$uj_lain = "";
$q_uj = mysqli_query($koneksi, "
    SELECT tr_jo_uj.*, m_cost_tr.nama_cost
    FROM tr_jo_uj
    LEFT JOIN m_cost_tr ON tr_jo_uj.id_cost = m_cost_tr.id_cost
    WHERE tr_jo_uj.id_jo = '$id_jo'
    ORDER BY tr_jo_uj.id_uj
");
while ($d1 = mysqli_fetch_array($q_uj)) {
    $harga = number_format($d1['harga'], 0);
    $uj_lain .= "{$d1['nama_cost']} : $harga; ";
}

// SIAPKAN DATA BARIS UTAMA
$data_info = "$no_cont; $nama_supir; UJ : $ujx; RITASE : $ritasex; $uj_lain";
$len_data = strlen($data_info);
$tinggi = ($len_data <= 38) ? 5 : (($len_data <= 69) ? 10 : 15);

$ket_text = "TRUCKING 1 x $jenis_mobil ($asal - $tujuan)";
$qty = 1;
$pph_value = $pph;
$wtax_value = "0";
$harga_format = number_format($harga, 0);
$total_format = $harga_format;

// TULIS BARIS KETERANGAN
$pdf->SetFont('arial', '', 9);

// BARIS ISI
$pdf->setXY(9, $y + 1);
$pdf->Cell(10, 5, "$n.", 0, 0, 'C');
$pdf->Cell(85, 5, $ket_text, 0, 0, 'L');
$pdf->Cell(10, 5, $qty, 0, 0, 'C');
$pdf->Cell(22, 5, $harga_format, 0, 0, 'R');
$pdf->Cell(20, 5, $pph_value, 0, 0, 'C');
$pdf->Cell(20, 5, $wtax_value, 0, 0, 'C');
$pdf->Cell(23, 5, $total_format, 0, 0, 'R');

// KETERANGAN TAMBAHAN
$pdf->setXY(19, $y + 6);
$pdf->MultiCell(105, 3.7, $data_info, 0, 'L');

// GARIS BOX
$pdf->setXY(9, $y);
$pdf->Cell(10, $tinggi, "", 1, 0, 'C');
$pdf->Cell(85, $tinggi, "", 1, 0, 'L');
$pdf->Cell(10, $tinggi, "", 1, 0, 'C');
$pdf->Cell(22, $tinggi, "", 1, 0, 'R');
$pdf->Cell(20, $tinggi, "", 1, 0, 'C');
$pdf->Cell(20, $tinggi, "", 1, 0, 'C');
$pdf->Cell(23, $tinggi, "", 1, 1, 'R');

// $t1 = "SELECT tr_jo_biaya.*, tr_jo_biaya.pph as pph_barang, m_cost_tr.nama_cost 
// 		FROM tr_jo_biaya 
//         LEFT JOIN m_cost_tr ON tr_jo_biaya.id_cost = m_cost_tr.id_cost
//         WHERE tr_jo_biaya.id_jo = '$id_jo' ORDER BY tr_jo_biaya.id_biaya";
// $h1 = mysqli_query($koneksi, $t1); 
// while ($d1 = mysqli_fetch_array($h1)) {
//     $n++;
//     $hargax = number_format($d1['harga'], 0);
//     $total += $d1['harga'];

//     $pph_text = ($d1['pph'] != '' && $d1['pph'] != 0) ? $d1['pph'] . '%' : '-';
//     $wtax_text = ($d1['wtax'] != '' && $d1['wtax'] != 0) ? $d1['wtax'] . '%' : '-';

//     $pdf->setX(9);
//     $pdf->Cell(10, 5, "$n.", 1, 0, 'C');
//     $pdf->Cell(85, 5, $d1['nama_cost'], 1, 0, 'L');
//     $pdf->Cell(15, 5, "1", 1, 0, 'C');
//     $pdf->Cell(15, 5, $pph_text, 1, 0, 'C');
//     $pdf->Cell(15, 5, $wtax_text, 1, 0, 'C');
//     $pdf->Cell(25, 5, $hargax, 1, 0, 'R');
//     $pdf->Cell(25, 5, $hargax, 1, 1, 'R');
// }

$t1 = "SELECT tr_jo_biaya.*, tr_jo_biaya.pph as pph_barang, m_cost_tr.nama_cost 
        FROM tr_jo_biaya 
        LEFT JOIN m_cost_tr ON tr_jo_biaya.id_cost = m_cost_tr.id_cost
        WHERE tr_jo_biaya.id_jo = '$id_jo' ORDER BY tr_jo_biaya.id_biaya";
$h1 = mysqli_query($koneksi, $t1); 
$total_wtax_rupiah = 0;
while ($d1 = mysqli_fetch_array($h1)) {
    $n++;
    $harga = $d1['harga'];
    $hargax = number_format($harga, 0);
    $total += $harga;

    $ppn = $d1['pph'];
    $wtax = $d1['wtax'];

    // $pph_text = ($pph != '' && $pph != 0) ? $pph . '%' : '-';

    if ($wtax != '' && $wtax != 0) {
        $wtax_rupiah = ($wtax / 100) * $harga;
        $total_wtax_rupiah += $wtax_rupiah;
		$wtax_rupiah_text = number_format($wtax_rupiah, 0, ',', '.');
        $wtax_text = $wtax . '%';
    } else {
        $wtax_text = '-';
    }

	// ASLINYA PPN SAYA TYPO
    if ($ppn != '' && $ppn != 0) {
        $ppn_rupiah = ($ppn / 100) * $harga;
        $total_ppn_rupiah += $ppn_rupiah;
		$ppn_rupiah_text = number_format($ppn_rupiah, 0, ',', '.');
        $ppn_text = $ppn . '%';
    } else {
        $ppn_text = '-';
    }

    $pdf->setX(9);
    $pdf->Cell(10, 5, "$n.", 1, 0, 'C');
    $pdf->Cell(85, 5, $d1['nama_cost'], 1, 0, 'L');
    $pdf->Cell(10, 5, "1", 1, 0, 'C');
    $pdf->Cell(22, 5, $hargax, 1, 0, 'R');
    // $pdf->Cell(20, 5, $ppn_text, 1, 0, 'C');
    $pdf->Cell(20, 5, $ppn_rupiah_text, 1, 0, 'C');
    // $pdf->Cell(20, 5, $wtax_text, 1, 0, 'C');
    $pdf->Cell(20, 5, $wtax_rupiah_text, 1, 0, 'C');
    $pdf->Cell(23, 5, $hargax, 1, 1, 'R');
}



$totalx = number_format($total,0);
$pdf->setX(9);
$pdf->Cell(127,4,"",0,0,'C');
$pdf->Cell(40,5,"SUB TOTAL ",1,0,'R');
$pdf->Cell(23,5,"$totalx",1,1,'R');

// $nilai_ppn = ($ppn/100) * $total;
// $nilai_ppnx = number_format($nilai_ppn,0);
// $pdf->setX(9);
// $pdf->Cell(135,4,"",0,0,'C');
// $pdf->Cell(30,5,"PPN ($ppn%)",1,0,'R');
// $pdf->Cell(25,5,"$nilai_ppnx",1,1,'R');

// Total PPN
$ppn_total_text = number_format($total_ppn_rupiah, 0);
$pdf->setX(9);
$pdf->Cell(127, 4, "", 0, 0, 'C');
$pdf->Cell(40, 5, "PPN TOTAL", 1, 0, 'R');
$pdf->Cell(23, 5, $ppn_total_text, 1, 1, 'R');

// Total WTAX
$wtax_total_text = number_format($total_wtax_rupiah, 0);
$pdf->setX(9);
$pdf->Cell(127, 4, "", 0, 0, 'C');
$pdf->Cell(40, 5, "WTAX TOTAL", 1, 0, 'R');
$pdf->Cell(23, 5, $wtax_total_text, 1, 1, 'R');

// Hitung total akhir pakai nilai asli (bukan hasil format)
$grand_total = $total + $total_ppn_rupiah - $total_wtax_rupiah;
$totalx = number_format($grand_total, 0);
$pdf->setX(9);
$pdf->Cell(127, 4, "", 0, 0, 'C');
$pdf->Cell(40, 5, "TOTAL", 1, 0, 'R');
$pdf->Cell(23, 5, $totalx, 1, 1, 'R');


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

$sql = "UPDATE tr_jo SET tagihan = '$total' WHERE id_jo = '$id_jo'"; 
$hasil = mysqli_query($koneksi, $sql);	

ob_end_clean();
$pdf->Output();
?>
