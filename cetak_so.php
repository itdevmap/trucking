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
        $this->SetTextColor(0, 0, 0);
        $this->SetY(-15);   
        $this->SetFont('arial','',6);
        $this->SetX(6); 
    }   

    // ============= Tambahan untuk tabel auto height =============
    function Row($data, $widths, $aligns) {
        $nb = 0;
        for ($i=0; $i<count($data); $i++) {
            $nb = max($nb, $this->NbLines($widths[$i], $data[$i]));
        }
        $h = 5 * $nb;

        $this->CheckPageBreak($h);

        for ($i=0; $i<count($data); $i++) {
            $w = $widths[$i];
            $a = isset($aligns[$i]) ? $aligns[$i] : 'L';

            $x = $this->GetX();
            $y = $this->GetY();

            // border cell
            $this->Rect($x, $y, $w, $h);

            // isi cell
            $this->MultiCell($w, 5, $data[$i], 0, $a);

            $this->SetXY($x + $w, $y);
        }

        // pindah ke baris baru
        $this->Ln($h);
    }

    function CheckPageBreak($h)
    {
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt)
    {
        $cw = &$this->CurrentFont['cw'];
        if($w==0)
            $w = $this->w-$this->rMargin-$this->x;
        $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
        $s = str_replace("\r",'',$txt);
        $nb = strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while($i<$nb)
        {
            $c = $s[$i];
            if($c=="\n")
            {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep = $i;
            $l += $cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i = $sep+1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }
}

$idx = $_GET['id'];	
$id_jo = base64_decode($idx);

// ----------- DATA HEADER -----------
$pq = mysqli_query($koneksi, 
"SELECT 
	tr_jo.no_jo,
	tr_jo.tgl_jo,
	tr_jo.penerima,
	m_cust_tr.nama_cust,
    tr_jo_detail.uj,
    tr_jo_detail.ritase
FROM tr_jo
LEFT JOIN tr_quo ON tr_quo.id_quo = tr_jo.id_quo
LEFT JOIN tr_jo_detail ON tr_jo_detail.id_so = tr_jo.id_jo
LEFT JOIN sap_project ON tr_jo.sap_project = sap_project.rowid
LEFT JOIN m_cust_tr ON tr_jo.id_cust = m_cust_tr.id_cust
LEFT JOIN m_kota_tr AS m_asal ON m_asal.id_kota = tr_jo_detail.id_asal
LEFT JOIN m_kota_tr AS m_tujuan ON m_tujuan.id_kota = tr_jo_detail.id_tujuan
LEFT JOIN tr_sj ON tr_sj.no_jo = tr_jo.no_jo
WHERE tr_jo.id_jo = '$id_jo'");

$rq = mysqli_fetch_array($pq);

$no_jo          = $rq['no_jo'];
$no_do          = $rq['no_do'];
$ppn            = $rq['ppn'];
$pph            = $rq['pph'];
$tgl_jo         = ConverTgl($rq['tgl_jo']);
$penerima       = $rq['penerima'];
$nama_cust      = $rq['nama_cust'];
$asal           = $rq['asal'];
$tujuan         = $rq['tujuan'];
$nama_supir     = $rq['nama_supir'] ?? 'Driver';
$jenis_mobil    = $rq['jenis_mobil'];
$harga          = $rq['biaya_kirim'];
$hargax         = number_format($harga,0);
$ujx            = number_format($rq['uj'],0);	
$ritasex        = number_format($rq['ritase'],0);	
$total          = $total + $harga;

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
$pdf->Cell(18,4,"No SO",0,0,'L'); 
$pdf->Cell(3,4,":",0,0,'C'); 
$pdf->Cell(20,4,"$no_jo",0,1,'L');

$pdf->setX(8); 
$pdf->Cell(18,4,"Tanggal",0,0,'L'); 
$pdf->Cell(3,4,":",0,0,'C'); 
$pdf->Cell(20,4,"$tgl_jo",0,1,'L');

// ----------- TABEL HEADER -----------
$widths = [10, 85, 10, 22, 20, 20, 23]; 
$aligns = ['C','L','C','R','C','C','R'];

$pdf->setXY(9,57);
$pdf->SetFont('arial', 'B', 9);
$pdf->Row(["NO","KETERANGAN","QTY","HARGA","PPN","WTAX","TOTAL"], $widths, $aligns);

$pdf->SetFont('arial','',9);
$pdf->setX(9);

// kumpulkan UJ lain
// $uj_lain = "";
// $q_uj = mysqli_query($koneksi,
//     "SELECT tr_jo_uj.*, m_cost_tr.nama_cost
//     FROM tr_jo_uj
//     LEFT JOIN m_cost_tr ON tr_jo_uj.id_cost = m_cost_tr.id_cost
//     WHERE tr_jo_uj.id_jo = '$id_jo'
//     ORDER BY tr_jo_uj.id_uj");
// while ($d1 = mysqli_fetch_array($q_uj)) {
//     $harga = number_format($d1['harga'], 0);
//     $uj_lain .= "{$d1['nama_cost']} : $harga; ";
// }

// $data_info = "$no_cont; $nama_supir; UJ : $ujx; RITASE : $ritasex; $uj_lain";

// $ket_text = "TRUCKING 1 x $jenis_mobil ($asal - $tujuan)";
// $qty = 1;
// $pph_value = $pph ?? 0;
// $wtax_value = "0";
// $harga_format = number_format($harga, 0);
// $total_format = $harga_format;

// baris utama
// $pdf->Row([$n, $ket_text."\n".$data_info, $qty, $harga_format, $pph_value, $wtax_value, $total_format], $widths, $aligns);

// $so_detail = "SELECT 
//     tr_jo_detail.id_asal,
//     tr_jo_detail.id_tujuan,
//     tr_jo_detail.jenis_mobil,
//     tr_jo_detail.harga,
//     GROUP_CONCAT(tr_jo_detail.container ORDER BY tr_jo_detail.container SEPARATOR ', ') AS containers,
//     COUNT(tr_jo_detail.container) AS jml_container,
//     m_asal.nama_kota AS asal,
//     m_tujuan.nama_kota AS tujuan
// FROM tr_jo_detail
// LEFT JOIN tr_jo ON tr_jo.id_jo = tr_jo_detail.id_so
// LEFT JOIN m_kota_tr AS m_asal ON m_asal.id_kota = tr_jo_detail.id_asal
// LEFT JOIN m_kota_tr AS m_tujuan ON m_tujuan.id_kota = tr_jo_detail.id_tujuan
// WHERE tr_jo.id_jo = '$id_jo'
// GROUP BY tr_jo_detail.id_asal, tr_jo_detail.id_tujuan, tr_jo_detail.jenis_mobil";

$so_detail = "SELECT 
    tr_jo_detail.jenis_mobil,
    m_asal.nama_kota AS asal,
    m_tujuan.nama_kota AS tujuan,
    tr_sj.container,
    m_supir_tr.nama_supir,
    tr_jo_detail.uj,
    tr_jo_detail.ritase,
    tr_jo_detail.harga,
    tr_jo_detail.pph AS wtax , 
    CONCAT(
        'TRUCKING 1X ', tr_jo_detail.jenis_mobil, 
        ' (', m_asal.nama_kota, '-', m_tujuan.nama_kota, ') ',
        'Cont ', tr_sj.container,
        '; Driver ', m_supir_tr.nama_supir,
        '; UJ: ', tr_jo_detail.uj,
        '; Ritase: ', tr_jo_detail.ritase,
        ';'
    ) AS keterangan
FROM tr_jo
LEFT JOIN tr_jo_detail ON tr_jo_detail.id_so = tr_jo.id_jo
LEFT JOIN m_kota_tr AS m_asal ON m_asal.id_kota = tr_jo_detail.id_asal
LEFT JOIN m_kota_tr AS m_tujuan ON m_tujuan.id_kota = tr_jo_detail.id_tujuan
INNER JOIN tr_sj ON tr_sj.no_jo = tr_jo.no_jo
LEFT JOIN m_supir_tr ON m_supir_tr.id_supir = tr_sj.id_supir
WHERE tr_jo.id_jo = '$id_jo'";


$data_detail = mysqli_query($koneksi, $so_detail); 
$total_wtax_rupiah = 0;

while ($d1 = mysqli_fetch_array($data_detail)) {
    $n++;
    $harga = $d1['harga'];
    $hargax = number_format($harga, 0);
    $total += $harga;

    $ppn = $d1['pph'];
    $wtax = $d1['wtax'];
    $ppn_rupiah_text = '0';
    $wtax_rupiah_text = '0';
    $pdf->setX(9);

    $jenis_mobil    = $d1['jenis_mobil'];
    $asal           = $d1['asal'];
    $tujuan         = $d1['tujuan'];
    $containers     = $d1['container'];
    $nama_supir     = $d1['nama_supir'];

    $ket_text = "TRUCKING 1X $jenis_mobil ($asal - $tujuan)";
    $data_info = "Cont $containers; Driver : $nama_supir; UJ : $ujx; RITASE : $ritasex; $uj_lain";

    if ($ppn != '' && $ppn != 0) {
        $ppn_rupiah = ($ppn / 100) * $harga;
        $total_ppn_rupiah += $ppn_rupiah;
        $ppn_rupiah_text = number_format($ppn_rupiah, 0, ',', '.');
    }

    if ($wtax != '' && $wtax != 0) {
        $wtax_rupiah = ($wtax / 100) * $harga;
        $total_wtax_rupiah += $wtax_rupiah;
        $wtax_rupiah_text = number_format($wtax_rupiah, 0, ',', '.');
    }

    $pdf->Row([
        $n,
        $ket_text."\n".$data_info,
        1,
        $hargax,
        $ppn_rupiah_text,
        $wtax_rupiah_text,
        $hargax
    ], $widths, $aligns);
}

$t1 = "SELECT 
            tr_jo_biaya.*, 
            tr_jo_biaya.pph AS pph_barang, 
            m_cost_tr.nama_cost 
        FROM tr_jo_biaya 
        LEFT JOIN m_cost_tr ON tr_jo_biaya.id_cost = m_cost_tr.id_cost
        WHERE tr_jo_biaya.id_jo = '$id_jo' 
        ORDER BY tr_jo_biaya.id_biaya";
$h1 = mysqli_query($koneksi, $t1); 
// $total_wtax_rupiah = 0;

while ($d1 = mysqli_fetch_array($h1)) {
    $n++;
    $harga = $d1['harga'];
    $hargax = number_format($harga, 0);
    $total += $harga;

    $ppn = $d1['pph'];
    $wtax = $d1['wtax'];
    $ppn_rupiah_text = '0';
    $wtax_rupiah_text = '0';
    $pdf->setX(9);

    if ($ppn != '' && $ppn != 0) {
        $ppn_rupiah = ($ppn / 100) * $harga;
        $total_ppn_rupiah += $ppn_rupiah;
        $ppn_rupiah_text = number_format($ppn_rupiah, 0, ',', '.');
    }

    if ($wtax != '' && $wtax != 0) {
        $wtax_rupiah = ($wtax / 100) * $harga;
        $total_wtax_rupiah += $wtax_rupiah;
        $wtax_rupiah_text = number_format($wtax_rupiah, 0, ',', '.');
    }

    $pdf->Row([
        $n,
        $d1['nama_cost'],
        1,
        $hargax,
        $ppn_rupiah_text,
        $wtax_rupiah_text,
        $hargax
    ], $widths, $aligns);
}

// subtotal
$totalx = number_format($total,0);
$pdf->setX(9);
$pdf->Cell(127,4,"",0,0,'C');
$pdf->Cell(40,5,"SUB TOTAL ",1,0,'R');
$pdf->Cell(23,5,"$totalx",1,1,'R');

// total PPN
$ppn_total_text = number_format($total_ppn_rupiah, 0);
$pdf->setX(9);
$pdf->Cell(127, 4, "", 0, 0, 'C');
$pdf->Cell(40, 5, "PPN TOTAL", 1, 0, 'R');
$pdf->Cell(23, 5, $ppn_total_text, 1, 1, 'R');

// total WTAX
$wtax_total_text = number_format($total_wtax_rupiah, 0);
$pdf->setX(9);
$pdf->Cell(127, 4, "", 0, 0, 'C');
$pdf->Cell(40, 5, "WTAX TOTAL", 1, 0, 'R');
$pdf->Cell(23, 5, $wtax_total_text, 1, 1, 'R');

// grand total
$grand_total = $total - $total_ppn_rupiah - $total_wtax_rupiah;
$totalx = number_format($grand_total, 0); 
$pdf->setX(9);
$pdf->Cell(127, 4, "", 0, 0, 'C');
$pdf->Cell(40, 5, "TOTAL", 1, 0, 'R');
$pdf->Cell(23, 5, $totalx, 1, 1, 'R');

// tanda tangan
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

// update db
$sql = "UPDATE tr_jo SET tagihan = '$total' WHERE id_jo = '$id_jo'"; 
$hasil = mysqli_query($koneksi, $sql);	

ob_end_clean();
$pdf->Output();
?>
