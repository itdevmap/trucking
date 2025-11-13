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
$id_data = base64_decode($idx);

// ========== HEADER DATA ==========
$pq = mysqli_query($koneksi, "SELECT 
        t_ware_data.rowid,
        t_ware_data.tanggal,
        t_ware_data.id_cust,
        t_ware_data.gudang,

        t_ware_data.no_ar,
        t_ware_data.tgl_ar,
        t_ware_data.no_so,
        t_ware_data.ket,
        m_cust_tr.nama_cust,
        m_cust_tr.tgl_tempo
    FROM t_ware_data 
    LEFT JOIN m_cust_tr ON m_cust_tr.id_cust = t_ware_data.id_cust
    WHERE t_ware_data.id_data = '$id_data'
    ");
$rq = mysqli_fetch_array($pq);	

$no_ar     = $rq['no_ar'];
$nama_cust = $rq['nama_cust'];
$no_so     = $rq['no_so'];
$ket       = $rq['ket'];
$tgl_ar    = date("d/m/Y", strtotime($rq['tgl_ar']));
$tempo     = (int)$rq['tgl_tempo'];


$rowid     = $rq['rowid'];
$tanggal   = $rq['tanggal'];
$id_cust   = $rq['id_cust'];
$gudang    = $rq['gudang'];

if ($tempo == 0) {
    $tgl_jth_tempo = 'Cash Basis';
} else {
    $tgl_jth_tempo = date("d/m/Y", strtotime($rq['tgl_ar']." +".$tempo." days"));
}


// ========== CETAK PDF ==========
$pdf = new PDF('P','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 20);

// ========== HEADER PRINT ==========
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

// ========== HEADER TABLE ==========
$pdf->Ln(5);
$pdf->SetFont('arial','B',8);
$pdf->setX(5);
$pdf->Cell(7,6,"No",1,0,'C');
$pdf->Cell(35,6,"Kode Barang",1,0,'C');
$pdf->Cell(65,6,"Nama Barang",1,0,'C');
$pdf->Cell(30,6,"Keterangan",1,0,'C');
$pdf->Cell(20,6,"Price/CBM",1,0,'C');
$pdf->Cell(15,6,"CBM",1,0,'C');
$pdf->Cell(25,6,"Total Price",1,1,'C');

$q_ware = "SELECT 
        id_data,
        no_doc 
    FROM t_ware_data 
    WHERE rowid = '$rowid'
        AND tanggal = '$tanggal'
        AND id_cust = '$id_cust'
        AND gudang = '$gudang'
    ";
$r_ware = mysqli_query($koneksi, $q_ware);

$no = 1;

$total_cbm_all = 0;
$total_price_all = 0;

while ($d_ware = mysqli_fetch_assoc($r_ware)) {
    $id_data = $d_ware['id_data'];
    $no_doc  = $d_ware['no_doc'];

    // Tentukan query berdasarkan jenis dokumen
    if (strpos($no_doc, 'SJWH') !== false) {
        $q_detail = "SELECT 
                t_ware_data_detil.*, 
                t_ware_data_detil1.no_cont AS ket, 
                t_ware_data.tanggal,
                t_ware.nama, 
                t_ware.kode, 
                t_ware.vol, 
                t_ware.unit,
                t_ware_data1.tanggal AS tgl_sj, 
                t_ware_quo.aging_sewa,
                t_ware_quo.harga_handling,
                m_cost_tr.nama_cost
            FROM t_ware_data_detil 
            INNER JOIN t_ware_data_detil AS t_ware_data_detil1 
                ON t_ware_data_detil.id_detil_masuk = t_ware_data_detil1.id_detil
            LEFT JOIN t_ware_data 
                ON t_ware_data_detil1.id_data = t_ware_data.id_data
            LEFT JOIN t_ware 
                ON t_ware_data_detil.id_ware = t_ware.id_ware
            LEFT JOIN t_ware_data AS t_ware_data1 
                ON t_ware_data_detil.id_data = t_ware_data1.id_data
            LEFT JOIN t_ware_quo 
                ON t_ware.id_quo = t_ware_quo.id_quo
            LEFT JOIN m_cost_tr ON m_cost_tr.itemcode = t_ware.kode
            WHERE t_ware_data_detil.id_data = '$id_data'
            ORDER BY t_ware_data_detil.id_detil
        ";
    } else {
        $q_detail = "SELECT 
                m_cost_tr.itemcode AS kode,
                m_cost_tr.nama_cost AS nama,
                t_ware_jasa_biaya.qty AS cbm, 
                t_ware_jasa_biaya.harga AS harga_handling
            FROM t_ware_jasa_biaya 
            INNER JOIN t_ware_quo_biaya ON t_ware_jasa_biaya.id_biaya = t_ware_quo_biaya.id_detil
            LEFT JOIN m_cost_tr ON t_ware_quo_biaya.id_biaya = m_cost_tr.id_cost
            WHERE t_ware_jasa_biaya.id_data = '$id_data'
            ORDER BY t_ware_jasa_biaya.id_detil
        ";
    }

    $h1 = mysqli_query($koneksi, $q_detail);

    while ($d1 = mysqli_fetch_assoc($h1)) {
        // Hitung harga
        $harga_sewa = $d1['harga_handling'] ?? 0;
        

        
        if ($harga_sewa > 0) {
            $cbm = isset($d1['cbm']) ? $d1['cbm'] : ($d1['keluar'] * $d1['vol']);
        } else {
            $cbm = 0;
        }

        $total_price = $harga_sewa * round($cbm, 2);
        

        // Tambahkan ke total keseluruhan
        $total_cbm_all += $cbm;
        $total_price_all += $total_price;

        // === CETAK KE PDF ===
        $pdf->SetFont('Arial', '', 8);
        $pdf->setX(5);

        $cellWidth  = [7, 35, 65, 30, 20, 15, 25];
        $lineHeight = 4;

        $text1 = $d1['nama'] ?? '-';
        $text2 = $d1['ket'] ?? '-';
        $nb1 = $pdf->NbLines($cellWidth[2], $text1);
        $nb2 = $pdf->NbLines($cellWidth[3], $text2);
        $nb = max($nb1, $nb2);
        $rowHeight = $lineHeight * $nb;

        $x = $pdf->GetX();
        $y = $pdf->GetY();

        $pdf->Cell($cellWidth[0], $rowHeight, $no++, 1, 0, 'C');
        $pdf->Cell($cellWidth[1], $rowHeight, $d1['kode'], 1, 0, 'C');

        $pdf->MultiCell($cellWidth[2], $lineHeight, $text1, 1, 'L');
        $pdf->SetXY($x + array_sum(array_slice($cellWidth, 0, 3)), $y);

        $pdf->Cell($cellWidth[3], $rowHeight, $text2, 1, 0, 'C');
        $pdf->SetXY($x + array_sum(array_slice($cellWidth, 0, 4)), $y);

        $pdf->Cell($cellWidth[4], $rowHeight, number_format($harga_sewa, 0, ',', '.'), 1, 0, 'R');
        $pdf->Cell($cellWidth[5], $rowHeight, number_format($cbm, 2, ',', '.'), 1, 0, 'R');
        $pdf->Cell($cellWidth[6], $rowHeight, number_format($total_price, 0, ',', '.'), 1, 1, 'R');
    }
}

// === CETAK TOTAL AKHIR ===
$pdf->SetFont('Arial', 'B', 9);
$pdf->setX(5);
$pdf->Cell(157, 8, 'Total   ', 0, 0, 'R');
$pdf->Cell(15, 8, number_format($total_cbm_all, 2, ',', '.'), 1, 0, 'R');
$pdf->Cell(25, 8, number_format($total_price_all, 0, ',', '.'), 1, 1, 'R');



$pdf->Ln(-5);
$pdf->SetFont('arial','I',8);
$pdf->setX(5);
$pdf->Cell(0,5,'Keterangan : ',0,1,'L');
$pdf->setX(5);
$pdf->MultiCell(100, 5, $ket, 0, 'L');
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
