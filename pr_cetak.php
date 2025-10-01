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
        if ($black !== null)
            $s = sprintf('[%.3F %.3F] 0 d', $black*$this->k, $white*$this->k);
        else
            $s = '[] 0 d';
        $this->_out($s);
    }	
    function Header() {}
    function Footer()
    {
        $this->SetTextColor(0, 0, 0);
        $this->SetY(-15);   
        $this->SetFont('arial','',6);
        $this->SetX(6); 
    }   
}

$idx    = $_GET['id'] ?? '';
$pr     = base64_decode($idx);
$jenis  = $_GET['jenis'] ?? '';

// --------------- DATA HEADER ---------------
$query = "SELECT 
            tr_pr.id_pr,
            tr_pr.code_pr,
            tr_pr.tgl,
            tr_pr.tgl_pr,
            tr_pr.remark,
            m_cust_tr.nama_cust AS user_req
        FROM tr_pr
        LEFT JOIN m_cust_tr ON m_cust_tr.id_cust = tr_pr.user_req
        WHERE tr_pr.code_pr = '$pr'";

$result = mysqli_query($koneksi, $query) or die('Query Error: '.mysqli_error($koneksi));
$rq = mysqli_fetch_array($result);

$code_pr    = $rq['code_pr'];
$tgl        = ConverTgl($rq['tgl']);
$tgl_pr     = ConverTgl($rq['tgl_pr']);
$user_req   = $rq['user_req'];
$remark     = $rq['remark'];

// --------------- DATA DETAIL ---------------
switch ($jenis) {
    case 'route':
        $queryDetail = "SELECT 
                            tr_pr_detail.description,
                            tr_pr_detail.uom,
                            tr_pr_detail.qty,
                            CONCAT(m_asal.nama_kota, ' - ', m_tujuan.nama_kota) AS item
                        FROM tr_pr_detail
                        LEFT JOIN m_kota_tr AS m_asal ON m_asal.id_kota = tr_pr_detail.origin
                        LEFT JOIN m_kota_tr AS m_tujuan ON m_tujuan.id_kota = tr_pr_detail.destination
                        WHERE tr_pr_detail.code_pr = '$code_pr' AND tr_pr_detail.jenis = '$jenis' ORDER BY tr_pr_detail.id_detail";
        $ket = "Feet";
        break;

    case 'item':
        $queryDetail = "SELECT 
                            sap_item_tr.sapitemcode AS item,
                            tr_pr_detail.description,
                            tr_pr_detail.uom,
                            tr_pr_detail.qty
                        FROM tr_pr_detail
                        LEFT JOIN sap_item_tr ON sap_item_tr.rowid = tr_pr_detail.item
                        WHERE tr_pr_detail.code_pr = '$code_pr' AND tr_pr_detail.jenis = '$jenis' ORDER BY tr_pr_detail.id_detail";
        $ket = "Uom";
        break;
    default:
       $queryDetail = "SELECT 
                            tr_pr_detail.item,
                            tr_pr_detail.description,
                            tr_pr_detail.uom,
                            tr_pr_detail.qty
                        FROM tr_pr_detail
                        WHERE tr_pr_detail.code_pr = '$code_pr' AND tr_pr_detail.jenis = '$jenis' ORDER BY tr_pr_detail.id_detail";
        $ket = "Uom";
        break;
}
$resultDetail = mysqli_query($koneksi, $queryDetail) or die('Query Error: '.mysqli_error($koneksi));

$details = [];
while ($rd = mysqli_fetch_array($resultDetail)) {
    $details[] = $rd;
}

$pdf = new PDF('P','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(false);


function renderPR($jenis, $pdf, $startY, $code_pr, $tgl, $tgl_pr, $user_req, $remark, $details, $ket) {

    $y      = $startY - 5;
    $rowH   = 20;
    $leftW  = 32;
    $pageW  = method_exists($pdf,'GetPageWidth') ? $pdf->GetPageWidth() : $pdf->w;
    $marginL = 10; 
    $marginR = 10;
    $x      = $marginL;
    $rightW = $pageW - $marginL - $marginR - $leftW;

    // ---------- KOTAK KIRI HEADER ----------
    $pdf->SetXY($x, $y);
    $pdf->Cell($leftW, $rowH, '', 1, 0, 'C');
    $logoW = 17;
    $logoH = 12;
    $logoX = $x + ($leftW - $logoW) / 2;
    $logoY = $y + ($rowH - $logoH) / 2;
    $pdf->Image("img/logo_print.jpg", $logoX, $logoY, $logoW, $logoH);

    // ---------- KOTAK KANAN HEADER ----------
    $pdf->Cell($rightW, $rowH, '', 1, 1, 'L'); 
    $pdf->SetFont('arial','B',14);
    $txX = $x + $leftW + 2;
    $txY = $y + 6;
    $txW = $rightW - 4;
    $txH = 6;
    $pdf->SetXY($txX, $txY);
    $pdf->Cell($txW, $txH, "PURCHASE REQUEST No : " . $code_pr, 0, 0, 'R');
    $pdf->SetFont('arial','',9);

    // ---------- KOTAK INFO REQUEST ----------
    $infoY   = $y + $rowH + 0;
    $infoH   = 12;
    $infoW   = $pageW - $marginL - $marginR;

    $leftBoxW  = $infoW * 0.73;
    $rightBoxW = $infoW * 0.27;

    // ---------- KOTAK KIRI ----------
    $pdf->SetXY($marginL, $infoY);
    $pdf->Cell($leftBoxW, $infoH, '', 1, 0, 'L');
    $pdf->SetXY($marginL + 2, $infoY + 3);
    $pdf->Cell($leftBoxW - 4, 5, "Request By : " . $user_req, 0, 0, 'L');

    // ---------- KOTAK KANAN ----------
    $pdf->SetXY($marginL + $leftBoxW, $infoY);
    $pdf->Cell($rightBoxW, $infoH, '', 1, 0, 'L');
    $pdf->SetXY($marginL + $leftBoxW + 2, $infoY + 2);
    $pdf->Cell($rightBoxW - 4, 5, "Date : " . $tgl, 0, 2, 'L');
    $pdf->SetXY($marginL + $leftBoxW + 2, $infoY + 6);
    $pdf->Cell($rightBoxW - 4, 5, "Request Date : " . $tgl_pr, 0, 0, 'L');

    // ---------- KOTAK DETAIL ----------
    $detailY = $infoY + $infoH + 1.5;
    $tableW  = $pageW - $marginL - $marginR;

    $colNo    = 10;
    $colCode  = 50;
    $colUom   = 15;
    $colQty   = 15;
    $colName  = $tableW - ($colNo + $colCode + $colUom + $colQty);

    $pdf->SetFont('arial','B',10);
    $pdf->SetXY($marginL, $detailY);
    $pdf->Cell($colNo,   8, 'No',        1, 0, 'C');

    $pdf->Cell($colCode, 8, ucwords(strtolower($jenis)), 1, 0, 'C');
    $pdf->Cell($colName, 8, 'Description', 1, 0, 'C');
    $pdf->Cell($colUom,  8, $ket,       1, 0, 'C');
    $pdf->Cell($colQty,  8, 'Qty',       1, 1, 'C');

    $pdf->SetFont('arial','',9);
    $no = 1;

    foreach ($details as $row) {
        $x = $marginL;
        $y = $pdf->GetY();

        $nbLines = NbLines($pdf, $colName, $row['description']);
        $rowHeight = 5 * $nbLines;

        $pdf->SetXY($x, $y);
        $pdf->Cell($colNo, $rowHeight, $no++, 1, 0, 'C');
        $x += $colNo;
        $pdf->SetXY($x, $y);
        $pdf->Cell($colCode, $rowHeight, $row['item'], 1, 0, 'L');
        $x += $colCode;
        $pdf->SetXY($x, $y);
        $pdf->MultiCell($colName, 5, $row['description'], 1, 'L');
        $x += $colName;
        $pdf->SetXY($x, $y);
        $pdf->Cell($colUom, $rowHeight, $row['uom'], 1, 0, 'C');
        $x += $colUom;
        $pdf->SetXY($x, $y);
        $pdf->Cell($colQty, $rowHeight, $row['qty'], 1, 1, 'C');
    }

    // ---------- KOTAK DESKRIPSI ----------
    $DescY   = $y + $rowHeight + 1.5;
    $DescH   = 40;
    $DescW   = $pageW - $marginL - $marginR;

    $leftBoxW   = $DescW * 0.6;
    $rightBoxW  = $DescW - $leftBoxW;
    $signBoxW   = $rightBoxW / 3;

    // ---------- KOTAK KIRI (Keterangan) ----------
    $pdf->SetXY($marginL, $DescY);
    $pdf->Cell($leftBoxW, $DescH, '', 1, 0, 'L');

    $pdf->SetXY($marginL + 2, $DescY + 3);
    $pdf->Cell($leftBoxW - 4, 5, "Keterangan :", 0, 0, 'L');

    $pdf->SetXY($marginL + 2, $DescY + 9);
    $pdf->MultiCell($leftBoxW - 4, 5, $remark, 0, 'L');

    // ---------- KOTAK DIMINTA ----------
        $pdf->SetXY($marginL + $leftBoxW, $DescY);
        $pdf->Cell($signBoxW, $DescH, '', 1, 0, 'C');
        $pdf->SetXY($marginL + $leftBoxW, $DescY);
        $pdf->Cell($signBoxW, 7, "Diminta Oleh", 1, 2, 'C');
        $pdf->SetXY($marginL + $leftBoxW, $DescY + $DescH - 15);
        $pdf->MultiCell($signBoxW, 5, $user_req, 0, 'C');

    // ---------- KOTAK MENYETUJUI ----------
    $pdf->SetXY($marginL + $leftBoxW + $signBoxW, $DescY);
    $pdf->Cell($signBoxW, $DescH, '', 1, 0, 'C');
    $pdf->SetXY($marginL + $leftBoxW + $signBoxW, $DescY);
    $pdf->Cell($signBoxW, 7, "Menyetujui", 1, 2, 'C');

    // ---------- KOTAK MENGETAHUI ----------
    $pdf->SetXY($marginL + $leftBoxW + ($signBoxW * 2), $DescY);
    $pdf->Cell($signBoxW, $DescH, '', 1, 0, 'C');
    $pdf->SetXY($marginL + $leftBoxW + ($signBoxW * 2), $DescY);
    $pdf->Cell($signBoxW, 7, "Mengetahui", 1, 2, 'C');

}

// FUNCTION BUAT AUTO LEBARIN 
function NbLines($pdf, $w, $txt) {
    $cw = &$pdf->CurrentFont['cw'];
    if($w == 0)
        $w = $pdf->w - $pdf->rMargin - $pdf->x;
    $wmax = ($w - 2 * $pdf->cMargin) * 1000 / $pdf->FontSize;
    $s = str_replace("\r", '', $txt);
    $nb = strlen($s);
    if($nb > 0 && $s[$nb - 1] == "\n")
        $nb--;
    $sep = -1;
    $i = 0;
    $j = 0;
    $l = 0;
    $nl = 1;
    while($i < $nb) {
        $c = $s[$i];
        if($c == "\n") {
            $i++;
            $sep = -1;
            $j = $i;
            $l = 0;
            $nl++;
            continue;
        }
        if($c == ' ')
            $sep = $i;
        $l += $cw[$c];
        if($l > $wmax) {
            if($sep == -1) {
                if($i == $j)
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

renderPR($jenis, $pdf, 20, $code_pr, $tgl, $tgl_pr, $user_req, $remark, $details,$ket);
ob_end_clean();
$pdf->Output();
